<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceRequest;
use App\Services\HubSpotService;
use App\Services\QuoteGeneratorService;
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
    public function downloadPdf(string $token, QuoteGeneratorService $quoteGenerator)
    {
        $serviceRequests = $this->getServiceRequests($token);

        if ($serviceRequests->isEmpty() || !$serviceRequests->first()->isCompleted()) {
            abort(404, __('PDF not available.'));
        }

        try {
            // Genera il PDF ufficiale dal template Word usando il QuoteGeneratorService
            $filePath = $quoteGenerator->generateQuote($serviceRequests, $token);
            
            // Il nome del file rifletterà se è stato convertito in PDF o se è ancora un DOCX
            $fileName = basename($filePath);

            return response()->download($filePath, $fileName);
            
        } catch (\Exception $e) {
            Log::error("Errore generazione preventivo dal template DOCX: " . $e->getMessage());
            
            // Fallback sul vecchio metodo DomPDF se qualcosa va storto con il template Word
            $pdf = Pdf::loadView('pdf.service-summary', [
                'serviceRequests' => $serviceRequests,
            ]);

            $company = $serviceRequests->first()->client_data['company'] ?? 'Cliente';
            $fileName = 'servizi-' . \Illuminate\Support\Str::slug($company) . '-' . now()->format('Y-m-d') . '.pdf';

            return $pdf->download($fileName);
        }
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
            'vehicles'                                     => 'required|array',
            'vehicles.*.groups'                            => 'nullable|array',
            'vehicles.*.groups.*.qty'                      => 'nullable|integer|min:1',
            'vehicles.*.groups.*.base_package'             => 'nullable|string',
            'vehicles.*.groups.*.services'                 => 'nullable|array',
            'vehicles.*.groups.*.services.*'               => 'string',
            'vehicles.*.groups.*.quantities'               => 'nullable|array',
            'vehicles.*.groups.*.quantities.*'             => 'integer|min:0',
            'vehicles.*.groups.*.package_price'            => 'nullable|numeric',
            'vehicles.*.groups.*.package_discount'         => 'nullable|numeric',
            'vehicles.*.groups.*.package_final_price'      => 'nullable|numeric',
            'vehicles.*.notes'                             => 'nullable|string|max:2000',
        ]);

        foreach ($serviceRequests as $req) {
            $data = $validated['vehicles'][$req->id] ?? null;
            if (!$data) continue;

            $groups = $data['groups'] ?? [];
            $allGroupsData = [];

            foreach ($groups as $gIdx => $group) {
                $groupServices = [];

                // Pacchetto base (radio)
                if (!empty($group['base_package'])) {
                    $pkg = [
                        'id'   => $group['base_package'],
                        'name' => $this->getServiceName($group['base_package']),
                        'type' => 'base_package',
                    ];
                    if (isset($group['package_price'])) $pkg['price'] = $group['package_price'];
                    if (isset($group['package_discount'])) $pkg['discount'] = $group['package_discount'];
                    if (isset($group['package_final_price'])) $pkg['final_price'] = $group['package_final_price'];
                    $groupServices[] = $pkg;
                }

                // Servizi aggiuntivi (checkbox)
                if (!empty($group['services'])) {
                    foreach ($group['services'] as $serviceId) {
                        $entry = [
                            'id'   => $serviceId,
                            'name' => $this->getServiceName($serviceId),
                            'type' => 'addon',
                        ];
                        if (isset($group['quantities'][$serviceId])) {
                            $entry['qty'] = $group['quantities'][$serviceId];
                        }
                        $groupServices[] = $entry;
                    }
                }

                $allGroupsData[] = [
                    'group_idx' => (int) $gIdx,
                    'qty'       => (int) ($group['qty'] ?? 1),
                    'services'  => $groupServices,
                ];
            }

            // vehicle_qty totale come somma dei gruppi
            $totalQty = array_sum(array_column($allGroupsData, 'qty'));
            if ($totalQty > 0) {
                $req->vehicle_qty = $totalQty;
            }

            $req->services = $allGroupsData;
            $req->notes = $data['notes'] ?? null;
            $req->save(); // Salva services, notes e vehicle_qty nel DB
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
            'message' => __('Service configuration saved successfully.'),
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
                abort(404, __('Invalid or expired link.'));
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
