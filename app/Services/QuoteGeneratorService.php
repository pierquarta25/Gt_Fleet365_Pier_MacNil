<?php

namespace App\Services;

use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\ServiceRequest;

class QuoteGeneratorService
{
    /**
     * Mappatura dei prezzi dal Listino "Bibbia"
     * Formato: ID_SERVIZIO => ['name' => ..., 'price' => ..., 'type' => 'servizio|hardware', 'period' => 'annuale|una tantum']
     */
    protected $pricing = [
        // --- BASE PACKAGES ---
        'base_loc' => [
            'name' => "GT FLEET 365 BASE (LOC)",
            'list_price' => 180.00,
            'discounted_price' => 144.00,
            'offer_price' => 115.20,
            'description' => [
                'Piattaforma Multi Tasking, User Friendly, Multi Utente',
                'Localizzazione - Real Time - Percorsi - Soste via WEB',
                'Stato del mezzo (in sosta, in fermata, in movimento)',
                'Geofencing con gestione POI',
                'Reportistica Avanzata Personalizzata',
                'Notifica in Piattaforma: Crash, Sterzata Brusca, Presa Buca, Frenata Brusca',
                'Km percorsi da GPS'
            ],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 36
        ],
        'plus_loc_sic' => [
            'name' => "GT FLEET 365 PLUS (LOC + SEC)",
            'list_price' => 220.00,
            'discounted_price' => 176.00,
            'offer_price' => 140.80,
            'description' => ['Tutti i servizi BASE', 'Sicurezza avanzata e blocco motore'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 36
        ],
        'gold_loc_tel' => [
            'name' => "GT FLEET 365 GOLD (LOC + TEL)",
            'list_price' => 250.00,
            'discounted_price' => 200.00,
            'offer_price' => 160.00,
            'description' => ['Tutti i servizi BASE', 'Telemetria avanzata'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 36
        ],
        'premium_loc_tel_sic' => [
            'name' => "GT FLEET 365 PREMIUM (LOC + TEL + SEC)",
            'list_price' => 270.00,
            'discounted_price' => 216.00,
            'offer_price' => 172.80,
            'description' => ['Pacchetto completo: Localizzazione, Sicurezza e Telemetria'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 36
        ],
        'plus_trattore' => [
            'name' => "GT FLEET 365 PLUS",
            'list_price' => 245.00,
            'discounted_price' => 196.00,
            'offer_price' => 156.80,
            'description' => ['Servizi specifici per mezzi d\'opera e trattori'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 36
        ],
        'truck_crono_base' => [
            'name' => "GT FLEET 365 TRUCK CRONO BASE (LOC + CRONO)",
            'list_price' => 270.00,
            'discounted_price' => 216.00,
            'offer_price' => 172.80,
            'description' => ['Localizzazione per mezzi pesanti', 'Lettura dati Cronotachigrafo'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 36
        ],
        'truck_crono_plus' => [
            'name' => "GT FLEET 365 TRUCK CRONO PLUS (LOC + SEC + CRONO)",
            'list_price' => 350.00,
            'discounted_price' => 280.00,
            'offer_price' => 224.00,
            'description' => ['Integrazione sicurezza e cronotachigrafo'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 36
        ],
        'truck_crono_tel' => [
            'name' => "GT FLEET 365 TRUCK CRONO TEL (LOC + TEL + CRONO)",
            'list_price' => 430.00,
            'discounted_price' => 344.00,
            'offer_price' => 275.20,
            'description' => ['Telemetria avanzata e cronotachigrafo'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 36
        ],
        'truck_crono_premium' => [
            'name' => "GT FLEET 365 TRUCK CRONO PREMIUM (LOC + TEL + SEC + CRONO)",
            'list_price' => 530.00,
            'discounted_price' => 424.00,
            'offer_price' => 339.20,
            'description' => ['Pacchetto completo per mezzi pesanti'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 36
        ],
        'rimorchi_comodato' => [
            'name' => "GT FLEET 365 RIMORCHI (dispositivo in comodato d'uso)",
            'list_price' => 220.00,
            'discounted_price' => 176.00,
            'offer_price' => 140.80,
            'description' => [
                'Localizzazione - Real Time per rimorchio sganciato',
                'Notifica aggancio/sgancio dalla motrice',
                'Localizzazione a rimorchio fermo (1 dato GPS al giorno)',
                'Localizzazione a rimorchio in movimento (1 dato ogni 10 minuti)'
            ],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 36
        ],
        'rimorchi_vendita' => [
            'name' => "GT FLEET 365 TRAILERS (purchased device)",
            'list_price' => 120.00,
            'discounted_price' => 96.00,
            'offer_price' => 67.20,
            'description' => ['Canone di localizzazione per rimorchi (dispositivo di proprietà)'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 24
        ],
        'asset' => [
            'name' => "GT FLEET 365 ASSET",
            'list_price' => 100.00,
            'discounted_price' => 80.00,
            'offer_price' => 56.00,
            'description' => ['Monitoraggio attrezzature e asset'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 24
        ],
        // --- APP & ADDITIONAL SERVICES ---
        'app_fleet_manager' => [
            'name' => "APP GT FLEET 365 - FLEET MANAGER",
            'list_price' => 39.00,
            'discounted_price' => 31.20,
            'offer_price' => 24.96,
            'description' => [
                'App GT FLEET 365 su store iOS e Android - Fleet Manager',
                'Gestione Sotto Accessi ai Servizi via App per il Cliente',
                'Infomobilità con possibilità di visualizzazione del traffico'
            ],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 36
        ],
        'app_driver' => [
            'name' => "APP GT FLEET 365 - DRIVER",
            'list_price' => 75.00,
            'discounted_price' => 60.00,
            'offer_price' => 48.00,
            'description' => [
                'App GT FLEET 365 DRIVER su store iOS e Android',
                'Inserimento Attività',
                'Gestione Rifornimenti Carburante',
                'Segnalazione Danni e Guasti',
                'Gestione Missioni'
            ],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 36
        ],
        'manutenzioni_scadenze' => [
            'name' => "GT FLEET 365 SERVIZIO MANUTENZIONI - SCADENZE",
            'list_price' => 75.00,
            'discounted_price' => 60.00,
            'offer_price' => 48.00,
            'description' => [
                'Gestione Avvisi Scadenze (Bolli, Assicurazioni, Patenti, Documenti)',
                'Gestione Manutenzioni (per KM, per DATA, per Ore Lavoro Motore)',
                'Gestione Referenti Notifiche'
            ],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 36
        ],
        'formazione_assistenza' => [
            'name' => "FORMAZIONE + ASSISTENZA - GT FLEET 365 WEB E APP",
            'list_price' => 75.00,
            'discounted_price' => 60.00,
            'offer_price' => 48.00,
            'description' => [
                'Numero 6 ore di Formazione in un Anno con Operatore Customer Care',
                'Assistenza di 1° e 2° Livello con Apertura Ticket'
            ],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 36
        ],
        'missioni' => [
            'name' => "GT FLEET 365 MISSIONI: PIANI DI VIAGGIO, ATTIVITA', VISITE",
            'list_price' => 200.00,
            'discounted_price' => 160.00,
            'offer_price' => 128.00,
            'description' => [
                'Predisposizione MISSIONI: Piano di Viaggio, Piano Attività, Piano Visite',
                'Trasmissione MISSIONI dal Web all\'App Driver',
                'Gestione delle MISSIONI attraverso l\'App Driver',
                'Gestione automatica dell MISSIONI (in auto geo fencing)'
            ],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 36
        ],
        'mappe_track' => [
            'name' => "SERVIZIO MAPPE TRUCK",
            'list_price' => 200.00,
            'discounted_price' => 160.00,
            'offer_price' => 128.00,
            'description' => [
                'Funzione Calcolo e Ottimizzazione Percorsi utilizzando Mappe Truck',
                'Gestione Ponti Bassi, Strettoie e Ponti con limitazione di Peso'
            ],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 36
        ],
        'temperatura_controllata' => [
            'name' => "CONTROLLED TEMPERATURE SERVICE",
            'list_price' => 110.00,
            'discounted_price' => 88.00,
            'offer_price' => 70.40,
            'description' => ['Monitoraggio continuo della temperatura in cella'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 36
        ],
        'gestione_portellone' => [
            'name' => "TAILGATE MANAGEMENT SERVICE",
            'list_price' => 111.00,
            'discounted_price' => 88.80,
            'offer_price' => 71.04,
            'description' => ['Sensore di apertura/chiusura portellone'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 36
        ],
        'riconoscimento_driver' => [
            'name' => "DRIVER RECOGNITION SERVICE",
            'list_price' => 111.00,
            'discounted_price' => 88.80,
            'offer_price' => 71.04,
            'description' => ['Identificazione autista tramite chiave o tastierino'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 36
        ],
        'riconoscimento_driver_app' => [
            'name' => "DRIVER RECOGNITION SERVICE WITH APP",
            'list_price' => 140.00,
            'discounted_price' => 112.00,
            'offer_price' => 78.40,
            'description' => ['Identificazione autista tramite Beacon e App'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 24
        ],
        'tempi_guida_realtime' => [
            'name' => "REAL TIME DRIVING TIMES",
            'list_price' => 100.00,
            'discounted_price' => 80.00,
            'offer_price' => 64.00,
            'description' => ['Tempi di guida in tempo reale da scheda tachigrafica'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 36
        ],
        'centrale_operativa_live' => [
            'name' => "LIVE OPERATIONS CENTER (24/7)",
            'list_price' => 200.00,
            'discounted_price' => 160.00,
            'offer_price' => 128.00,
            'description' => ['Centrale operativa di sicurezza H24'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 36
        ],
        'centrale_operativa_ondemand' => [
            'name' => "ON DEMAND OPERATIONS CENTER",
            'list_price' => 100.00,
            'discounted_price' => 80.00,
            'offer_price' => 64.00,
            'description' => ['Attivazione centrale operativa su richiesta'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 36
        ],
        'scorta_digitale' => [
            'name' => "GT FLEET 365 DIGITAL ESCORT",
            'list_price' => 900.00,
            'discounted_price' => 720.00,
            'offer_price' => 576.00,
            'description' => ['Scorta digitale satellitare del mezzo'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 36
        ],
        'app_gt5_app' => [
            'name' => "GT FLEET 365 APP - GT 5.0.APP",
            'list_price' => 50.00,
            'discounted_price' => 40.00,
            'offer_price' => 28.00,
            'description' => ['Applicazione dedicata per gestione avanzata GT 5.0'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 24
        ],
        // --- CHRONOTACHOGRAPH ---
        'carta_aziendale_crono' => [
            'name' => "COMPANY CRONO CARD",
            'list_price' => 188.00,
            'discounted_price' => 150.40,
            'offer_price' => 120.32,
            'description' => ['Hosting carta aziendale presso data center (ogni 25 mezzi)'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 36
        ],
        'crono_ddd_silver' => [
            'name' => "CRONO - DDD MANAGER - SILVER",
            'list_price' => 950.00,
            'discounted_price' => 760.00,
            'offer_price' => 608.00,
            'description' => ['Scarico remoto dati tachigrafo e archiviazione legale'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 36
        ],
        'crono_ddd_gold' => [
            'name' => "CRONO - DDD MANAGER - GOLD",
            'list_price' => 1900.00,
            'discounted_price' => 1520.00,
            'offer_price' => 1216.00,
            'description' => ['Scarico dati, archiviazione e analisi infrazioni'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 36
        ],
        // --- DEVELOPMENT ---
        'sviluppo_digital_transformation' => [
            'name' => "DEVELOPMENT - DIGITAL TRANSFORMATION",
            'list_price' => 1500.00,
            'discounted_price' => 1200.00,
            'offer_price' => 840.00,
            'description' => ['Integrazione API personalizzata e trasformazione digitale'],
            'type' => 'servizio',
            'period' => 'una tantum',
            'contract_months' => 1
        ],
        // --- HARDWARE ---
        'hw_dispositivo_bordo' => [
            'name' => "FORNITURA DISPOSITIVO DI BORDO",
            'list_price' => 250.00,
            'discounted_price' => 200.00,
            'offer_price' => 190.00,
            'description' => ['Dispositivo di localizzazione principale'],
            'type' => 'hardware',
            'period' => 'una tantum',
            'contract_months' => 1
        ],
        'hw_sensore_temperatura' => [
            'name' => "TEMPERATURE SENSOR (Truck or Trailer)",
            'list_price' => 40.00,
            'discounted_price' => 36.00,
            'offer_price' => 36.00,
            'description' => ['Sonda termica per cella frigo'],
            'type' => 'hardware',
            'period' => 'una tantum',
            'contract_months' => 1
        ],
        'hw_sensore_portellone' => [
            'name' => "TAILGATE SENSOR (Van, Truck, Trailer)",
            'list_price' => 40.00,
            'discounted_price' => 36.00,
            'offer_price' => 36.00,
            'description' => ['Sensore magnetico per portellone'],
            'type' => 'hardware',
            'period' => 'una tantum',
            'contract_months' => 1
        ],
        'hw_chiave_dallas' => [
            'name' => "DALLAS KEY",
            'list_price' => 20.00,
            'discounted_price' => 18.00,
            'offer_price' => 18.00,
            'description' => ['Chiave magnetica autista'],
            'type' => 'hardware',
            'period' => 'una tantum',
            'contract_months' => 1
        ],
        'hw_kit_lettore_dallas' => [
            'name' => "DALLAS READER + KEY KIT",
            'list_price' => 27.00,
            'discounted_price' => 25.00,
            'offer_price' => 25.00,
            'description' => ['Lettore cruscotto + Chiave Dallas'],
            'type' => 'hardware',
            'period' => 'una tantum',
            'contract_months' => 1
        ],
        'hw_tastierino' => [
            'name' => "KEYPAD",
            'list_price' => 80.00,
            'discounted_price' => 60.00,
            'offer_price' => 60.00,
            'description' => ['Tastierino numerico per inserimento PIN'],
            'type' => 'hardware',
            'period' => 'una tantum',
            'contract_months' => 1
        ],
        'hw_beacon' => [
            'name' => "BEACON",
            'list_price' => 20.00,
            'discounted_price' => 18.00,
            'offer_price' => 18.00,
            'description' => ['Tag Bluetooth per riconoscimento automatico'],
            'type' => 'hardware',
            'period' => 'una tantum',
            'contract_months' => 1
        ],
        'hw_beacon_passeggero' => [
            'name' => "BEACON (Passenger Recognition)",
            'list_price' => 20.00,
            'discounted_price' => 18.00,
            'offer_price' => 18.00,
            'description' => ['Tag Bluetooth per riconoscimento passeggero'],
            'type' => 'hardware',
            'period' => 'una tantum',
            'contract_months' => 1
        ],
        'hw_pedale_antirapina' => [
            'name' => "ANTI-ROBBERY / PANIC PEDAL",
            'list_price' => 70.00,
            'discounted_price' => 63.00,
            'offer_price' => 63.00,
            'description' => ['Pedale occultato per allarme rapina'],
            'type' => 'hardware',
            'period' => 'una tantum',
            'contract_months' => 1
        ],
        'hw_gost_shadow' => [
            'name' => "GOST - SHADOW DEVICE SUPPLY",
            'list_price' => 350.00,
            'discounted_price' => 320.00,
            'offer_price' => 270.00,
            'description' => ['Dispositivo civetta occultato e autoalimentato'],
            'type' => 'hardware',
            'period' => 'una tantum',
            'contract_months' => 1
        ],
    ];


    /**
     * Prezzo di listino di un pacchetto, o null se sconosciuto.
     */
    public function listPrice(string $id): ?float
    {
        $price = config('vehicle_services.package_prices.' . $id);

        return $price !== null ? (float) $price : null;
    }

    /**
     * Genera il file DOCX e tenta la conversione in PDF.
     * Restituisce il percorso del file finale (PDF se possibile, altrimenti DOCX).
     */
    public function generateQuote($serviceRequests, $token)
    {
        $templatePath = resource_path('templates/template_offerta.docx');

        if (!file_exists($templatePath)) {
            Log::error("Template non trovato in {$templatePath}");
            throw new \Exception("Template Word non trovato.");
        }

        $firstReq    = $serviceRequests->first();
        $companyName = $firstReq->client_data['company'] ?? 'Cliente';

        // --- AGGREGAZIONE SERVIZI (una riga per servizio unico) ---
        $aggregated = [];
        foreach ($serviceRequests as $req) {
            foreach ($req->getServiceGroups() as $group) {
                $groupQty = $group['qty'] ?? ($req->vehicle_qty ?? 1);

                foreach ($group['services'] ?? [] as $srv) {
                    $id              = $srv['id'];
                    $srvQty          = $srv['qty'] ?? 1;
                    $totalQtyForThis = $groupQty * $srvQty;

                    // Stesso servizio con prezzo/sconto diversi (es. gruppi con sconti differenti)
                    // => righe separate, altrimenti il prezzo del primo gruppo verrebbe applicato a tutti.
                    $key = $id . '|' . ($srv['price'] ?? '') . '|' . ($srv['discount'] ?? '') . '|' . ($srv['final_price'] ?? '') . '|' . ($srv['billing'] ?? 'annuale');

                    if (isset($aggregated[$key])) {
                        $aggregated[$key]['totalQty'] += $totalQtyForThis;
                    } else {
                        $aggregated[$key] = [
                            'id'       => $id,
                            'srv'      => $srv,
                            'totalQty' => $totalQtyForThis,
                        ];
                    }
                }
            }
        }

        // --- CALCOLO PREZZI E TOTALI ---
        $totaleServiziAnnuo      = 0;
        $totaleHardwareUnaTantum = 0;
        $rowsData                = [];

        foreach ($aggregated as $entry) {
            $id       = $entry['id'];
            $srv      = $entry['srv'];
            $totalQty = $entry['totalQty'];

            $priceInfo = $this->pricing[$id] ?? [
                'name'             => $srv['name'] ?? $id,
                'list_price'       => 0,
                'discounted_price' => 0,
                'offer_price'      => 0,
                'description'      => [],
                'type'             => 'servizio',
                'period'           => 'annuale',
                'contract_months'  => 24,
            ];

            $listPrice       = isset($srv['price'])       ? (float) $srv['price']       : $priceInfo['list_price'];
            $discount        = isset($srv['discount'])    ? (float) $srv['discount']    : 0;
            $discountedPrice = ($discount > 0)
                ? ($listPrice * (1 - $discount / 100))
                : ($priceInfo['discounted_price'] ?? $listPrice);
            $offerPrice      = isset($srv['final_price']) ? (float) $srv['final_price'] : $priceInfo['offer_price'];

            $contractMonths = $priceInfo['contract_months'] ?? 24;
            $period         = $priceInfo['period']          ?? 'annuale';

            // Pagamento mensile: prezzi presi dal listino (nessun calcolo)
            $monthlyListino = null;
            if ($period === 'annuale' && ($srv['billing'] ?? 'annuale') === 'mensile') {
                $monthlyListino = config('vehicle_services.monthly_prices.' . $id);
                if ($monthlyListino !== null) {
                    $offerPrice = (float) $monthlyListino['year'];
                }
            }
            $type           = $priceInfo['type']            ?? 'servizio';
            $descriptions   = $priceInfo['description']    ?? [];

            if ($monthlyListino !== null) {
                $prezzoMensile        = (float) $monthlyListino['monthly'];
                $totaleAnnualeSingolo = (float) $monthlyListino['year'];
            } else {
                $prezzoMensile        = ($period === 'annuale' && $offerPrice > 0) ? $offerPrice / 12 : 0;
                $totaleAnnualeSingolo = ($period === 'annuale') ? $prezzoMensile * 12 : $offerPrice;
            }

            if ($period === 'annuale') {
                $totaleServiziAnnuo += $totaleAnnualeSingolo * $totalQty;
            } else {
                $totaleHardwareUnaTantum += $offerPrice * $totalQty;
            }

            $euro        = "\xE2\x82\xAC";
            $fmtList     = number_format($listPrice, 2, ',', '.') . ' ' . $euro;
            $fmtDisc     = number_format($discountedPrice, 2, ',', '.') . ' ' . $euro;
            $fmtOffer    = ($offerPrice == 0) ? 'OMAGGIO' : number_format($offerPrice, 2, ',', '.') . ' ' . $euro;
            $fmtMensile  = ($period === 'annuale')
                ? (($offerPrice == 0) ? 'OMAGGIO' : number_format($prezzoMensile, 2, ',', '.') . ' ' . $euro)
                : '-';
            $fmtTotale   = ($offerPrice == 0)
                ? 'OMAGGIO'
                : number_format($totaleAnnualeSingolo, 2, ',', '.') . ' ' . $euro;
            $periodicita = ($period === 'annuale')
                ? ($monthlyListino !== null ? 'Mensile' : 'Annuale')
                : 'Una Tantum';

            $rowsData[] = [
                'name'           => $priceInfo['name'],
                'type'           => $type,
                'descriptions'   => $descriptions,
                'fmtList'        => $fmtList,
                'fmtDisc'        => $fmtDisc,
                'fmtOffer'       => $fmtOffer,
                'period'         => $period,
                'contractMonths' => (string) $contractMonths,
                'qty'            => (string) $totalQty,
                'periodicita'    => $periodicita,
                'fmtMensile'     => $fmtMensile,
                'fmtTotale'      => $fmtTotale,
            ];
        }

        // --- COSTRUZIONE XML OOXML DELLA TABELLA ---
        $tableXml = $this->buildTableXml($rowsData, $totaleServiziAnnuo, $totaleHardwareUnaTantum);

        // --- MANIPOLAZIONE ZIP ---
        $workDir = storage_path('app/private/service-pdfs');
        if (!is_dir($workDir)) {
            mkdir($workDir, 0750, true);
        }
        $uid      = \Illuminate\Support\Str::uuid()->toString();
        $tempDocx = $workDir . '/temp-' . $uid . '.docx';
        copy($templatePath, $tempDocx);

        $zip = new \ZipArchive();
        if ($zip->open($tempDocx) === TRUE) {

            // DOCUMENT.XML
            $docXml = $zip->getFromName('word/document.xml');
            if ($docXml !== false) {
                $docXml = str_replace('Nome Azienda', htmlspecialchars($companyName), $docXml);
                $docXml = str_replace('<w:highlight w:val="yellow"/>', '', $docXml);

                // INIEZIONE TABELLA nella sezione 6 (VALORIZZAZIONE ECONOMICA)
                $docXml = $this->injectTableIntoXml($docXml, $tableXml);

                $zip->addFromString('word/document.xml', $docXml);
            }

            // HEADER
            for ($i = 1; $i <= 5; $i++) {
                $headerName = 'word/header' . $i . '.xml';
                $headerXml  = $zip->getFromName($headerName);
                if ($headerXml !== false) {
                    $headerXml = str_replace('RAGIONE SOCIALE', htmlspecialchars($companyName), $headerXml);
                    $headerXml = str_replace('Emesso da: TEAM Sales', 'Emesso da: GT Fleet 365', $headerXml);
                    $headerXml = str_replace('xx mese 2026', date('d/m/Y'), $headerXml);
                    $headerXml = str_replace('<w:highlight w:val="yellow"/>', '', $headerXml);
                    $zip->addFromString($headerName, $headerXml);
                }
            }

            // FOOTER
            $agentEmail = $firstReq->agent_email ?? null;
            $agent      = $agentEmail ? \App\Models\User::where('email', $agentEmail)->first() : null;
            $agentName  = $agent ? $agent->name : 'Commerciale';

            for ($i = 1; $i <= 5; $i++) {
                $footerName = 'word/footer' . $i . '.xml';
                $footerXml  = $zip->getFromName($footerName);
                if ($footerXml !== false) {
                    $footerXml = str_replace('Nome e Cognome', htmlspecialchars($agentName), $footerXml);
                    $footerXml = str_replace('<w:highlight w:val="yellow"/>', '', $footerXml);
                    $zip->addFromString($footerName, $footerXml);
                }
            }

            $zip->close();
        }

        // --- SALVATAGGIO DOCX FINALE ---
        $fileNameDocx = 'preventivo-' . \Illuminate\Support\Str::slug($companyName) . '-' . $uid . '.docx';
        $pathDocx     = $workDir . '/' . $fileNameDocx;

        rename($tempDocx, $pathDocx);

        // --- CONVERSIONE IN PDF ---
        $fileNamePdf = str_replace('.docx', '.pdf', $fileNameDocx);
        $pathPdf     = $workDir . '/' . $fileNamePdf;
        $outdir      = $workDir;
        $profileDir  = sys_get_temp_dir() . '/lo_' . $uid;

        $cmd       = "timeout 60 soffice -env:UserInstallation=" . escapeshellarg('file://' . $profileDir)
            . " --headless --convert-to pdf --outdir " . escapeshellarg($outdir) . " " . escapeshellarg($pathDocx) . " 2>&1";
        $output    = [];
        $returnVar = 0;
        exec($cmd, $output, $returnVar);

        if (is_dir($profileDir)) {
            \Illuminate\Support\Facades\File::deleteDirectory($profileDir);
        }

        if ($returnVar === 0 && file_exists($pathPdf)) {
            @unlink($pathDocx);
            return $pathPdf;
        }

        Log::warning("Impossibile convertire in PDF con soffice. Output: " . implode("\n", $output));

        return $pathDocx;
    }

    /**
     * Inietta la tabella OOXML nel document.xml in layout VERTICALE,
     * rispettando fedelmente l'impaginazione del documento originale:
     * - Nessun sectPr alterato (nessun accavallamento di pagine, header o footer)
     * - Sostituisce i 34 paragrafi vuoti con la tabella compatta
     * - Page break dopo la tabella per mantenere il Punto 7 sulla pagina corretta
     */
    protected function injectTableIntoXml(string $docXml, string $tableXml): string
    {
        $marker = 'VALORIZZAZIONE ECONOMICA DELLA FORNITURA GT FLEET 365';

        // Cerca la seconda occorrenza (la sezione vera, non l'indice)
        $idx1 = strpos($docXml, $marker);
        if ($idx1 === false) {
            Log::warning('QuoteGenerator: marker VALORIZZAZIONE non trovato nel template.');
            return $docXml;
        }
        $idx2       = strpos($docXml, $marker, $idx1 + 1);
        $searchFrom = ($idx2 !== false) ? $idx2 : $idx1;

        // Fine del </w:p> del paragrafo titolo
        $paraEnd = strpos($docXml, '</w:p>', $searchFrom);
        if ($paraEnd === false) {
            Log::warning('QuoteGenerator: fine paragrafo VALORIZZAZIONE non trovata.');
            return $docXml;
        }
        $insertAfter = $paraEnd + strlen('</w:p>');

        // Trova inizio del paragrafo con CONDIZIONI DI FORNITURA
        $nextMarker = 'CONDIZIONI DI FORNITURA';
        $idxCond    = strpos($docXml, $nextMarker, $insertAfter);

        if ($idxCond === false) {
            Log::warning('QuoteGenerator: marker CONDIZIONI non trovato, tabella inserita senza rimuovere paragrafi.');
            return substr($docXml, 0, $insertAfter) . $tableXml . substr($docXml, $insertAfter);
        }

        // Risale all'inizio del <w:p che contiene CONDIZIONI
        $beforeCond    = substr($docXml, 0, $idxCond);
        $condParaStart = strrpos($beforeCond, '<w:p ');
        if ($condParaStart === false) {
            $condParaStart = strrpos($beforeCond, '<w:p>');
        }
        if ($condParaStart === false || $condParaStart < $insertAfter) {
            $condParaStart = $insertAfter;
        }

        // Page break subito dopo la tabella per mandare le Condizioni di fornitura alla pagina successiva
        $pageBreakAfterTable = '<w:p><w:pPr><w:spacing w:before="0" w:after="0"/></w:pPr><w:r><w:br w:type="page"/></w:r></w:p>';

        $partBefore = substr($docXml, 0, $insertAfter);
        $partAfter  = substr($docXml, $condParaStart);

        // NESSUN sectPr toccato: il layout e le pagine originali rimangono intatti al 100%
        return $partBefore . "\n"
            . $tableXml . "\n"
            . $pageBreakAfterTable . "\n"
            . $partAfter;
    }

    /**
     * Costruisce l'XML OOXML della tabella preventivo in formato VERTICALE (Portrait),
     * esattamente nello stile del listino Excel:
     * - w:tblLayout fixed a 9700 dxa (margine utile A4 Portrait è ~9923 dxa)
     * - Rigorosamente su UN SOLO FOGLIO verticale (nessun overflow di pagina)
     * - Colonne numeriche calibrate con w:noWrap per garantire che tutte le cifre siano ben visibili
     * - Allineamento centrato con margini e rientri azzerati per evitare qualsiasi clipping in Word
     */
    protected function buildTableXml(array $rowsData, float $totaleServizi, float $totaleHardware): string
    {
        // 12 colonne verticali perfettamente proporzionate (somma = 9700 dxa)
        // [0:Codice, 1:Tipo, 2:Descrizione, 3:Listino, 4:Scontato, 5:Offerta, 6:Periodo, 7:Mesi, 8:Qtà, 9:Pagamento, 10:Canone, 11:Tot.Anno]
        $w = [1400, 500, 2000, 700, 700, 750, 550, 400, 400, 750, 750, 800];
        $totalWidth = array_sum($w); // 9700 dxa

        $borders =
            '<w:tblBorders>' .
            '<w:top    w:val="single" w:sz="4" w:space="0" w:color="000000"/>' .
            '<w:left   w:val="single" w:sz="4" w:space="0" w:color="000000"/>' .
            '<w:bottom w:val="single" w:sz="4" w:space="0" w:color="000000"/>' .
            '<w:right  w:val="single" w:sz="4" w:space="0" w:color="000000"/>' .
            '<w:insideH w:val="single" w:sz="4" w:space="0" w:color="000000"/>' .
            '<w:insideV w:val="single" w:sz="4" w:space="0" w:color="000000"/>' .
            '</w:tblBorders>';

        $xml  = '<w:tbl>';
        $xml .= '<w:tblPr>';
        $xml .= '<w:tblW w:w="' . $totalWidth . '" w:type="dxa"/>';
        $xml .= '<w:jc w:val="center"/>';
        $xml .= '<w:tblLayout w:type="fixed"/>';
        $xml .= $borders;
        $xml .= '<w:tblCellMar>';
        $xml .= '<w:top    w:w="25" w:type="dxa"/><w:left   w:w="15" w:type="dxa"/>';
        $xml .= '<w:bottom w:w="25" w:type="dxa"/><w:right  w:w="15" w:type="dxa"/>';
        $xml .= '</w:tblCellMar>';
        $xml .= '</w:tblPr>';

        // Definizione griglia fissa colonne
        $xml .= '<w:tblGrid>';
        foreach ($w as $cw) {
            $xml .= '<w:gridCol w:w="' . $cw . '"/>';
        }
        $xml .= '</w:tblGrid>';

        // --- RIGA INTESTAZIONE (Blu scuro MacNil con Offerta e Canone in Giallo) ---
        $headers = [
            'Codice Servizio',
            'Hw/Srv',
            'Descrizione Prodotto e Servizi',
            'Listino',
            'Scontato',
            'Offerta',
            'Periodo',
            'Mesi',
            'Qtà',
            'Pagamento',
            'Canone Mese',
            'Tot. Anno',
        ];

        $xml .= '<w:tr><w:trPr><w:cantSplit/><w:tblHeader/></w:trPr>';
        foreach ($headers as $i => $hdr) {
            $isYellow = ($i === 5 || $i === 10);
            $xml .= '<w:tc><w:tcPr>';
            $xml .= '<w:tcW w:w="' . $w[$i] . '" w:type="dxa"/>';
            $xml .= '<w:tcMar><w:top w:w="25" w:type="dxa"/><w:left w:w="10" w:type="dxa"/><w:bottom w:w="25" w:type="dxa"/><w:right w:w="10" w:type="dxa"/></w:tcMar>';
            if ($i !== 0 && $i !== 2) {
                $xml .= '<w:noWrap/>';
            }
            $xml .= $isYellow
                ? '<w:shd w:val="clear" w:color="auto" w:fill="FFFF00"/>'
                : '<w:shd w:val="clear" w:color="auto" w:fill="1F3864"/>';
            $xml .= '<w:vAlign w:val="center"/></w:tcPr>';
            $xml .= '<w:p><w:pPr><w:spacing w:before="15" w:after="15"/><w:ind w:left="0" w:right="0" w:firstLine="0" w:hanging="0"/><w:jc w:val="center"/></w:pPr>';
            $xml .= '<w:r><w:rPr><w:b/><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/>';
            $xml .= $isYellow ? '<w:color w:val="000000"/>' : '<w:color w:val="FFFFFF"/>';
            $xml .= '<w:sz w:val="11"/><w:szCs w:val="11"/></w:rPr>'; // 5.5pt bold
            $xml .= '<w:t xml:space="preserve">' . htmlspecialchars($hdr, ENT_XML1, 'UTF-8') . '</w:t>';
            $xml .= '</w:r></w:p></w:tc>';
        }
        $xml .= '</w:tr>';

        // --- RIGHE DATI ---
        foreach ($rowsData as $row) {
            $xml .= '<w:tr><w:trPr><w:cantSplit/></w:trPr>';
            // Col 0: Codice Servizio (allineato a sinistra)
            $xml .= $this->buildCell($w[0],  $row['name'],           false, false, true,  5.5, 'left',  false);
            // Col 1: Tipo Hw/Srv
            $xml .= $this->buildCell($w[1],  $row['type'],           false, false, false, 5,   'center', true);
            // Col 2: Descrizione dettagliata
            $xml .= $this->buildDescriptionCell($w[2], $row['descriptions']);
            // Col 3: Listino (cifre visibili e centrate)
            $xml .= $this->buildCell($w[3],  $row['fmtList'],        false, false, false, 5.5, 'center', true);
            // Col 4: Scontato
            $xml .= $this->buildCell($w[4],  $row['fmtDisc'],        false, false, false, 5.5, 'center', true);
            // Col 5: Offerta (sfondo GIALLO)
            $xml .= $this->buildCell($w[5],  $row['fmtOffer'],       false, true,  true,  5.5, 'center', true);
            // Col 6: Periodo
            $xml .= $this->buildCell($w[6],  $row['period'],         false, false, false, 5,   'center', true);
            // Col 7: Mesi
            $xml .= $this->buildCell($w[7],  $row['contractMonths'], false, false, false, 5.5, 'center', true);
            // Col 8: Qtà
            $xml .= $this->buildCell($w[8],  $row['qty'],            false, false, true,  5.5, 'center', true);
            // Col 9: Modalità Pagamento
            $xml .= $this->buildCell($w[9],  $row['periodicita'],    false, false, false, 5,   'center', true);
            // Col 10: Canone Mese (sfondo GIALLO)
            $xml .= $this->buildCell($w[10], $row['fmtMensile'],     false, true,  true,  5.5, 'center', true);
            // Col 11: Tot. Anno
            $xml .= $this->buildCell($w[11], $row['fmtTotale'],      false, false, true,  5.5, 'center', true);
            $xml .= '</w:tr>';
        }

        // --- RIGA SEPARATRICE ---
        $xml .= '<w:tr><w:trPr><w:cantSplit/></w:trPr>';
        $xml .= '<w:tc><w:tcPr><w:gridSpan w:val="12"/><w:tcW w:w="' . $totalWidth . '" w:type="dxa"/></w:tcPr>';
        $xml .= '<w:p><w:pPr><w:spacing w:before="0" w:after="0"/></w:pPr></w:p></w:tc>';
        $xml .= '</w:tr>';

        $euro = "\xE2\x82\xAC";
        $sumW = $totalWidth - $w[11]; // 9700 - 800 = 8900

        // --- TOTALE CANONI ANNUALI ---
        $xml .= '<w:tr><w:trPr><w:cantSplit/></w:trPr>';
        $xml .= '<w:tc>';
        $xml .= '<w:tcPr><w:gridSpan w:val="11"/><w:tcW w:w="' . $sumW . '" w:type="dxa"/>';
        $xml .= '<w:tcMar><w:top w:w="30" w:type="dxa"/><w:left w:w="30" w:type="dxa"/><w:bottom w:w="30" w:type="dxa"/><w:right w:w="30" w:type="dxa"/></w:tcMar>';
        $xml .= '<w:shd w:val="clear" w:color="auto" w:fill="D9E1F2"/></w:tcPr>';
        $xml .= '<w:p><w:pPr><w:spacing w:before="20" w:after="20"/><w:ind w:left="0" w:right="0"/><w:jc w:val="right"/></w:pPr>';
        $xml .= '<w:r><w:rPr><w:b/><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/>';
        $xml .= '<w:color w:val="000000"/>';
        $xml .= '<w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr>';
        $xml .= '<w:t>TOTALE CANONI ANNUALI</w:t></w:r></w:p></w:tc>';
        $xml .= $this->buildCell($w[11], number_format($totaleServizi, 2, ',', '.') . ' ' . $euro, false, true, true, 6, 'center', true);
        $xml .= '</w:tr>';

        // --- TOTALE HARDWARE UNA TANTUM ---
        $xml .= '<w:tr><w:trPr><w:cantSplit/></w:trPr>';
        $xml .= '<w:tc>';
        $xml .= '<w:tcPr><w:gridSpan w:val="11"/><w:tcW w:w="' . $sumW . '" w:type="dxa"/>';
        $xml .= '<w:tcMar><w:top w:w="30" w:type="dxa"/><w:left w:w="30" w:type="dxa"/><w:bottom w:w="30" w:type="dxa"/><w:right w:w="30" w:type="dxa"/></w:tcMar>';
        $xml .= '<w:shd w:val="clear" w:color="auto" w:fill="D9E1F2"/></w:tcPr>';
        $xml .= '<w:p><w:pPr><w:spacing w:before="20" w:after="20"/><w:ind w:left="0" w:right="0"/><w:jc w:val="right"/></w:pPr>';
        $xml .= '<w:r><w:rPr><w:b/><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/>';
        $xml .= '<w:color w:val="000000"/>';
        $xml .= '<w:sz w:val="12"/><w:szCs w:val="12"/></w:rPr>';
        $xml .= '<w:t>TOTALE HARDWARE UNA TANTUM</w:t></w:r></w:p></w:tc>';
        $xml .= $this->buildCell($w[11], number_format($totaleHardware, 2, ',', '.') . ' ' . $euro, false, true, true, 6, 'center', true);
        $xml .= '</w:tr>';

        $xml .= '</w:tbl>';

        return $xml;
    }

    /**
     * Costruisce una singola cella OOXML con margini dedicati, noWrap opzionale e font nitido.
     */
    protected function buildCell(int $width, string $text, bool $header = false, bool $yellow = false, bool $boldText = false, float $fontSize = 5.5, string $align = 'center', bool $noWrap = true): string
    {
        $szVal  = (int) round($fontSize * 2);
        $isBold = ($header || $boldText) ? '<w:b/>' : '';

        $xml  = '<w:tc>';
        $xml .= '<w:tcPr>';
        $xml .= '<w:tcW w:w="' . $width . '" w:type="dxa"/>';
        $xml .= '<w:tcMar><w:top w:w="20" w:type="dxa"/><w:left w:w="10" w:type="dxa"/><w:bottom w:w="20" w:type="dxa"/><w:right w:w="10" w:type="dxa"/></w:tcMar>';
        if ($noWrap) {
            $xml .= '<w:noWrap/>';
        }
        if ($header) {
            $xml .= '<w:shd w:val="clear" w:color="auto" w:fill="1F3864"/>';
        } elseif ($yellow) {
            $xml .= '<w:shd w:val="clear" w:color="auto" w:fill="FFFF00"/>';
        }
        $xml .= '<w:vAlign w:val="center"/>';
        $xml .= '</w:tcPr>';
        $xml .= '<w:p>';
        $xml .= '<w:pPr><w:spacing w:before="15" w:after="15"/><w:ind w:left="0" w:right="0" w:firstLine="0" w:hanging="0"/><w:jc w:val="' . $align . '"/></w:pPr>';
        $xml .= '<w:r>';
        $xml .= '<w:rPr>';
        $xml .= $isBold;
        $xml .= '<w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/>';
        if ($header) {
            $xml .= '<w:color w:val="FFFFFF"/>';
        } else {
            $xml .= '<w:color w:val="000000"/>';
        }
        $xml .= '<w:sz w:val="' . $szVal . '"/>';
        $xml .= '<w:szCs w:val="' . $szVal . '"/>';
        $xml .= '</w:rPr>';
        $xml .= '<w:t xml:space="preserve">' . htmlspecialchars($text, ENT_XML1, 'UTF-8') . '</w:t>';
        $xml .= '</w:r>';
        $xml .= '</w:p>';
        $xml .= '</w:tc>';

        return $xml;
    }

    /**
     * Costruisce la cella descrizione con singoli paragrafi puliti e distanziati,
     * garantendo che nessun testo si sovrapponga a schermo.
     */
    protected function buildDescriptionCell(int $width, array $descriptions): string
    {
        $xml  = '<w:tc>';
        $xml .= '<w:tcPr>';
        $xml .= '<w:tcW w:w="' . $width . '" w:type="dxa"/>';
        $xml .= '<w:tcMar><w:top w:w="20" w:type="dxa"/><w:left w:w="15" w:type="dxa"/><w:bottom w:w="20" w:type="dxa"/><w:right w:w="15" w:type="dxa"/></w:tcMar>';
        $xml .= '<w:vAlign w:val="center"/>';
        $xml .= '</w:tcPr>';

        if (empty($descriptions)) {
            $xml .= '<w:p><w:pPr><w:spacing w:before="15" w:after="15"/><w:ind w:left="0" w:right="0" w:firstLine="0" w:hanging="0"/></w:pPr></w:p>';
        } else {
            foreach ($descriptions as $desc) {
                $xml .= '<w:p>';
                $xml .= '<w:pPr><w:spacing w:before="8" w:after="8"/><w:ind w:left="0" w:right="0" w:firstLine="0" w:hanging="0"/><w:jc w:val="left"/></w:pPr>';
                $xml .= '<w:r>';
                $xml .= '<w:rPr>';
                $xml .= '<w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/>';
                $xml .= '<w:color w:val="000000"/>';
                $xml .= '<w:sz w:val="10"/><w:szCs w:val="10"/>'; // 5pt
                $xml .= '</w:rPr>';
                $xml .= '<w:t xml:space="preserve">• ' . htmlspecialchars($desc, ENT_XML1, 'UTF-8') . '</w:t>';
                $xml .= '</w:r>';
                $xml .= '</w:p>';
            }
        }

        $xml .= '</w:tc>';
        return $xml;
    }
}


