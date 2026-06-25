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
        $serviceRequest = ServiceRequest::byToken($token)->first();

        if (!$serviceRequest) {
            abort(404, 'Link non valido.');
        }

        return view('services.success', [
            'serviceRequest' => $serviceRequest,
        ]);
    }

    /**
     * Scarica il PDF di riepilogo servizi.
     * GET /servizi/{token}/pdf
     */
    public function downloadPdf(string $token)
    {
        $serviceRequest = ServiceRequest::byToken($token)->first();

        if (!$serviceRequest || !$serviceRequest->isCompleted()) {
            abort(404, 'PDF non disponibile.');
        }

        $pdf = Pdf::loadView('pdf.service-summary', [
            'serviceRequest' => $serviceRequest,
        ]);

        $fileName = 'servizi-' . $serviceRequest->vehicle_name . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Mostra il form di configurazione servizi per un mezzo specifico.
     * GET /servizi/{token}
     */
    public function show(string $token)
    {
        $serviceRequest = ServiceRequest::byToken($token)->first();

        if (!$serviceRequest) {
            abort(404, 'Link non valido o scaduto.');
        }

        // Determina la categoria del mezzo per filtrare i servizi
        $vehicleCategories = config('vehicle_services.vehicle_categories');
        $category = $vehicleCategories[$serviceRequest->vehicle_type] ?? 'aziendali';

        // Filtra le sezioni servizi per questa categoria di mezzo
        $allSections = config('vehicle_services.sections');
        $filteredSections = [];

        foreach ($allSections as $section) {
            $filteredItems = array_filter($section['items'], function ($item) use ($category) {
                return in_array($category, $item['categories']);
            });

            if (!empty($filteredItems)) {
                $section['items'] = array_values($filteredItems);
                $filteredSections[] = $section;
            }
        }

        return view('services.form', [
            'serviceRequest'   => $serviceRequest,
            'sections'         => $filteredSections,
            'alreadyCompleted' => $serviceRequest->isCompleted(),
        ]);
    }

    /**
     * Salva la configurazione servizi selezionata dal commerciale.
     * POST /api/servizi/{token}
     */
    public function store(Request $request, string $token)
    {
        $serviceRequest = ServiceRequest::byToken($token)->first();

        if (!$serviceRequest) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token non valido.',
            ], 404);
        }

        $validated = $request->validate([
            'base_package'       => 'nullable|string',
            'services'           => 'nullable|array',
            'services.*'         => 'string',
            'quantities'         => 'nullable|array',
            'quantities.*'       => 'integer|min:0',
            'vehicle_qty'        => 'nullable|integer|min:1',
            'notes'              => 'nullable|string|max:2000',
        ]);

        // Componi l'array dei servizi selezionati
        $selectedServices = [];

        // Pacchetto base (radio)
        if (!empty($validated['base_package'])) {
            $selectedServices[] = [
                'id'   => $validated['base_package'],
                'name' => $this->getServiceName($validated['base_package']),
                'type' => 'base_package',
            ];
        }

        // Servizi aggiuntivi (checkbox)
        if (!empty($validated['services'])) {
            foreach ($validated['services'] as $serviceId) {
                $entry = [
                    'id'   => $serviceId,
                    'name' => $this->getServiceName($serviceId),
                    'type' => 'addon',
                ];

                // Se ha un campo quantità
                if (isset($validated['quantities'][$serviceId])) {
                    $entry['qty'] = $validated['quantities'][$serviceId];
                }

                $selectedServices[] = $entry;
            }
        }

        // Aggiorna la quantità del mezzo se modificata
        if (!empty($validated['vehicle_qty'])) {
            $serviceRequest->vehicle_qty = $validated['vehicle_qty'];
        }

        // Salva i dati
        $serviceRequest->services = $selectedServices;
        $serviceRequest->notes = $validated['notes'] ?? null;
        $serviceRequest->markAsCompleted();

        // Genera il PDF
        $pdfPath = $this->generatePdf($serviceRequest);

        // Aggiorna HubSpot se disponibile
        if ($serviceRequest->hubspot_deal_id && config('services.hubspot.token')) {
            try {
                $this->hubspot->addServiceNote(
                    $serviceRequest->hubspot_deal_id,
                    $serviceRequest->vehicle_name,
                    $selectedServices,
                    $validated['notes'] ?? null
                );
            } catch (\Exception $e) {
                Log::error("Errore aggiornamento HubSpot servizi: " . $e->getMessage());
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Configurazione servizi salvata con successo.',
        ]);
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
     * Genera il PDF di riepilogo servizi e lo salva nello storage.
     */
    private function generatePdf(ServiceRequest $serviceRequest): string
    {
        $pdf = Pdf::loadView('pdf.service-summary', [
            'serviceRequest' => $serviceRequest,
        ]);

        $fileName = 'servizi-' . $serviceRequest->token . '.pdf';
        $path = 'service-pdfs/' . $fileName;

        \Storage::put($path, $pdf->output());

        Log::info("PDF servizi generato: {$path}");

        return $path;
    }
}
