<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceRequest;
use App\Services\HubSpotService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ServiceFormController extends Controller
{
    protected $hubspot;

    public function __construct(HubSpotService $hubspot)
    {
        $this->hubspot = $hubspot;
    }

    /**
     * Mostra la pagina di successo dopo la compilazione.
     * GET /servizi/{token}/successo
     */
    public function success(string $token)
    {
        $serviceRequests = $this->getServiceRequests($token);

        return view('services.success', [
            'serviceRequests' => $serviceRequests,
            'groupToken' => $token,
        ]);
    }

    /**
     * Scarica il PDF di riepilogo servizi.
     * GET /servizi/{token}/pdf
     */
    public function downloadPdf(string $token)
    {
        $serviceRequests = $this->getServiceRequests($token);

        if ($serviceRequests->isEmpty() || !$serviceRequests->first()->isCompleted()) {
            abort(404, 'PDF non disponibile.');
        }

        $pdf = Pdf::loadView('pdf.service-summary', [
            'serviceRequests' => $serviceRequests,
        ]);

        // Se c'è l'azienda cliente usa quella per il nome file
        $company = $serviceRequests->first()->client_data['company'] ?? 'Cliente';
        $fileName = 'servizi-' . \Illuminate\Support\Str::slug($company) . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Mostra il form di configurazione servizi a schede.
     * GET /servizi/{token}
     */
    public function show(string $token)
    {
        $serviceRequests = $this->getServiceRequests($token);

        $vehiclesData = [];
        $allSections = config('vehicle_services.sections');
        $vehicleCategories = config('vehicle_services.vehicle_categories');

        foreach ($serviceRequests as $req) {
            $category = $vehicleCategories[$req->vehicle_type] ?? 'aziendali';
            $filteredSections = [];

            foreach ($allSections as $section) {
                $filteredItems = array_filter($section['items'], function ($item) use ($category) {
                    return in_array($category, $item['categories']);
                });

                if (!empty($filteredItems)) {
                    $sectionCopy = $section;
                    $sectionCopy['items'] = array_values($filteredItems);
                    $filteredSections[] = $sectionCopy;
                }
            }

            $vehiclesData[] = [
                'model'    => $req,
                'sections' => $filteredSections,
            ];
        }

        return view('services.form', [
            'vehiclesData'     => $vehiclesData,
            'groupToken'       => $token,
            'alreadyCompleted' => $serviceRequests->first()->isCompleted(),
        ]);
    }

    /**
     * Salva la configurazione per tutti i mezzi.
     * POST /api/servizi/{token}
     */
    public function store(Request $request, string $token)
    {
        $serviceRequests = $this->getServiceRequests($token);

        $validated = $request->validate([
            'vehicles'                        => 'required|array',
            'vehicles.*.base_package'         => 'nullable|string',
            'vehicles.*.services'             => 'nullable|array',
            'vehicles.*.services.*'           => 'string',
            'vehicles.*.quantities'           => 'nullable|array',
            'vehicles.*.quantities.*'         => 'integer|min:0',
            'vehicles.*.vehicle_qty'          => 'nullable|integer|min:1',
            'vehicles.*.notes'                => 'nullable|string|max:2000',
        ]);

        foreach ($serviceRequests as $req) {
            $data = $validated['vehicles'][$req->id] ?? null;
            if (!$data) continue;

            $selectedServices = [];

            // Pacchetto base (radio)
            if (!empty($data['base_package'])) {
                $selectedServices[] = [
                    'id'   => $data['base_package'],
                    'name' => $this->getServiceName($data['base_package']),
                    'type' => 'base_package',
                ];
            }

            // Servizi aggiuntivi (checkbox)
            if (!empty($data['services'])) {
                foreach ($data['services'] as $serviceId) {
                    $entry = [
                        'id'   => $serviceId,
                        'name' => $this->getServiceName($serviceId),
                        'type' => 'addon',
                    ];

                    if (isset($data['quantities'][$serviceId])) {
                        $entry['qty'] = $data['quantities'][$serviceId];
                    }

                    $selectedServices[] = $entry;
                }
            }

            if (!empty($data['vehicle_qty'])) {
                $req->vehicle_qty = $data['vehicle_qty'];
            }

            $req->services = $selectedServices;
            $req->notes = $data['notes'] ?? null;
            $req->markAsCompleted();
        }

        // Ricarichiamo le collection aggiornate per il PDF
        $serviceRequests = $this->getServiceRequests($token);

        // Genera il PDF Globale
        $this->generatePdf($serviceRequests, $token);

        // Aggiorna HubSpot se disponibile (usiamo il primo record per recuperare il deal)
        $firstReq = $serviceRequests->first();
        if ($firstReq && $firstReq->hubspot_deal_id && config('services.hubspot.token')) {
            try {
                $this->hubspot->addServiceNote($firstReq->hubspot_deal_id, $serviceRequests);
            } catch (\Exception $e) {
                Log::error("Errore aggiornamento HubSpot servizi globali: " . $e->getMessage());
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Configurazione servizi salvata con successo.',
        ]);
    }

    /**
     * Helper: Recupera le service request da group_token o token (fallback).
     */
    private function getServiceRequests(string $token)
    {
        $requests = ServiceRequest::where('group_token', $token)->get();
        if ($requests->isEmpty()) {
            $single = ServiceRequest::where('token', $token)->first();
            if ($single) {
                $requests = collect([$single]);
            } else {
                abort(404, 'Link non valido o scaduto.');
            }
        }
        return $requests;
    }

    /**
     * Trova il nome del servizio dato il suo ID dalla config.
     */
    private function getServiceName(string $serviceId): string
    {
        $sections = config('vehicle_services.sections');

        foreach ($sections as $section) {
            foreach ($section['items'] as $item) {
                if ($item['id'] === $serviceId) {
                    return $item['name'];
                }
            }
        }

        return $serviceId;
    }

    /**
     * Genera il PDF globale.
     */
    private function generatePdf($serviceRequests, string $token): string
    {
        $pdf = Pdf::loadView('pdf.service-summary', [
            'serviceRequests' => $serviceRequests,
        ]);

        $fileName = 'servizi-group-' . $token . '.pdf';
        $path = 'service-pdfs/' . $fileName;

        Storage::put($path, $pdf->output());

        Log::info("PDF globale servizi generato: {$path}");

        return $path;
    }
}
