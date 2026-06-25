<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HubSpotService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.hubapi.com/crm/v3/objects';

    public function __construct()
    {
        $this->apiKey = config('services.hubspot.token');
    }

    // Crea un contatto e il relativo deal associato su HubSpot
    public function createLead(array $client, array $vehicles)
    {
        $contactId = $this->upsertContact($client);

        $vehicleSummary = "Richiesta Mezzi:\n";
        foreach ($vehicles as $name => $qty) {
            if ($qty > 0) {
                $vehicleSummary .= "- $name: $qty\n";
            }
        }

        $drivers = $client['drivers'] ?? 0;
        $vehicleSummary .= "\nNumero Autisti: $drivers\n";

        return $this->createDeal($contactId, $client['company'], $vehicleSummary);
    }

    // Crea o recupera un contatto tramite email
    private function upsertContact($client)
    {
        $payload = [
            'properties' => [
                'email' => $client['email'],
                'firstname' => $client['contact'],
                'lastname' => $client['lastname'] ?? '',
                'company' => $client['company'],
                'phone' => $client['phone'] ?? '',
            ]
        ];

        $response = Http::withToken($this->apiKey)
            ->post("{$this->baseUrl}/contacts", $payload);

        if ($response->successful()) {
            return $response->json()['id'];
        }

        // Se il contatto esiste già (409 Conflict), lo cerchiamo per email
        if ($response->status() === 409) {
            $search = Http::withToken($this->apiKey)
                ->post("{$this->baseUrl}/contacts/search", [
                    'filterGroups' => [[
                        'filters' => [[
                            'propertyName' => 'email',
                            'operator' => 'EQ',
                            'value' => $client['email']
                        ]]
                    ]]
                ]);
            return $search->json()['results'][0]['id'] ?? null;
        }

        return null;
    }

    // Crea il deal e lo associa al contatto
    private function createDeal($contactId, $company, $summary)
    {
        $payload = [
            'properties' => [
                'dealname' => "GT Fleet 365: $company",
                'dealstage' => 'appointmentscheduled',
                'pipeline' => 'default',
                'description' => $summary
            ]
        ];

        $response = Http::withToken($this->apiKey)
            ->post("{$this->baseUrl}/deals", $payload);

        if ($response->successful()) {
            $dealId = $response->json()['id'];
            
            if ($contactId) {
                Http::withToken($this->apiKey)
                    ->put("{$this->baseUrl}/deals/$dealId/associations/contact/$contactId/deal_to_contact");
            }
            
            return $dealId;
        }

        return null;
    }

    /**
     * Aggiunge una nota al Deal con i servizi selezionati per tutti i mezzi.
     */
    public function addServiceNote(string $dealId, $serviceRequests): void
    {
        $body = "📋 Riepilogo Servizi Configurati per Preventivo\n\n";

        foreach ($serviceRequests as $req) {
            if (empty($req->services)) {
                continue;
            }

            $body .= "🚛 {$req->vehicle_name} (Qtà: {$req->vehicle_qty})\n";

            foreach ($req->services as $service) {
                $line = "  ✔ {$service['name']}";
                if (!empty($service['qty'])) {
                    $line .= " (×{$service['qty']})";
                }
                $body .= $line . "\n";
            }

            if ($req->notes) {
                $body .= "  📝 Note: {$req->notes}\n";
            }
            $body .= "\n";
        }

        // Crea una nota (engagement) tramite API v3
        $notePayload = [
            'properties' => [
                'hs_timestamp' => now()->toIso8601String(),
                'hs_note_body' => $body,
            ],
            'associations' => [
                [
                    'to' => ['id' => $dealId],
                    'types' => [
                        [
                            'associationCategory' => 'HUBSPOT_DEFINED',
                            'associationTypeId' => 214, // note_to_deal
                        ]
                    ]
                ]
            ]
        ];

        $response = Http::withToken($this->apiKey)
            ->post("{$this->baseUrl}/notes", $notePayload);

        if (!$response->successful()) {
            Log::warning("Impossibile creare nota servizi su HubSpot per deal {$dealId}: " . $response->body());
        }
    }
}

