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
            'list_price' => 200.00,
            'discounted_price' => 160.00,
            'offer_price' => 112.00,
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
            'contract_months' => 24
        ],
        'plus_loc_sic' => [
            'name' => "GT FLEET 365 PLUS (LOC + SEC)",
            'list_price' => 240.00,
            'discounted_price' => 192.00,
            'offer_price' => 134.40,
            'description' => ['Tutti i servizi BASE', 'Sicurezza avanzata e blocco motore'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 24
        ],
        'gold_loc_tel' => [
            'name' => "GT FLEET 365 GOLD (LOC + TEL)",
            'list_price' => 260.00,
            'discounted_price' => 208.00,
            'offer_price' => 145.60,
            'description' => ['Tutti i servizi BASE', 'Telemetria avanzata'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 24
        ],
        'premium_loc_tel_sic' => [
            'name' => "GT FLEET 365 PREMIUM (LOC + TEL + SEC)",
            'list_price' => 300.00,
            'discounted_price' => 240.00,
            'offer_price' => 168.00,
            'description' => ['Pacchetto completo: Localizzazione, Sicurezza e Telemetria'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 24
        ],
        'plus_trattore' => [
            'name' => "GT FLEET 365 PLUS",
            'list_price' => 240.00,
            'discounted_price' => 192.00,
            'offer_price' => 134.40,
            'description' => ['Servizi specifici per mezzi d\'opera e trattori'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 24
        ],
        'truck_crono_base' => [
            'name' => "GT FLEET 365 TRUCK CRONO BASE (LOC + CRONO)",
            'list_price' => 250.00,
            'discounted_price' => 200.00,
            'offer_price' => 140.00,
            'description' => ['Localizzazione per mezzi pesanti', 'Lettura dati Cronotachigrafo'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 24
        ],
        'truck_crono_plus' => [
            'name' => "GT FLEET 365 TRUCK CRONO PLUS (LOC + SEC + CRONO)",
            'list_price' => 290.00,
            'discounted_price' => 232.00,
            'offer_price' => 162.40,
            'description' => ['Integrazione sicurezza e cronotachigrafo'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 24
        ],
        'truck_crono_tel' => [
            'name' => "GT FLEET 365 TRUCK CRONO TEL (LOC + TEL + CRONO)",
            'list_price' => 310.00,
            'discounted_price' => 248.00,
            'offer_price' => 173.60,
            'description' => ['Telemetria avanzata e cronotachigrafo'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 24
        ],
        'truck_crono_premium' => [
            'name' => "GT FLEET 365 TRUCK CRONO PREMIUM (LOC + TEL + SEC + CRONO)",
            'list_price' => 350.00,
            'discounted_price' => 280.00,
            'offer_price' => 196.00,
            'description' => ['Pacchetto completo per mezzi pesanti'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 24
        ],
        'rimorchi_comodato' => [
            'name' => "GT FLEET 365 RIMORCHI (dispositivo in comodato d'uso)",
            'list_price' => 200.00,
            'discounted_price' => 160.00,
            'offer_price' => 112.00,
            'description' => [
                'Localizzazione - Real Time per rimorchio sganciato',
                'Notifica aggancio/sgancio dalla motrice',
                'Localizzazione a rimorchio fermo (1 dato GPS al giorno)',
                'Localizzazione a rimorchio in movimento (1 dato ogni 10 minuti)'
            ],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 24
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
            'offer_price' => 0.00,
            'description' => [
                'App GT FLEET 365 su store iOS e Android - Fleet Manager',
                'Gestione Sotto Accessi ai Servizi via App per il Cliente',
                'Infomobilità con possibilità di visualizzazione del traffico'
            ],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 24
        ],
        'app_driver' => [
            'name' => "APP GT FLEET 365 - DRIVER",
            'list_price' => 100.00,
            'discounted_price' => 80.00,
            'offer_price' => 60.00,
            'description' => [
                'App GT FLEET 365 DRIVER su store iOS e Android',
                'Inserimento Attività',
                'Gestione Rifornimenti Carburante',
                'Segnalazione Danni e Guasti',
                'Gestione Missioni'
            ],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 24
        ],
        'manutenzioni_scadenze' => [
            'name' => "GT FLEET 365 SERVIZIO MANUTENZIONI - SCADENZE",
            'list_price' => 100.00,
            'discounted_price' => 80.00,
            'offer_price' => 0.00,
            'description' => [
                'Gestione Avvisi Scadenze (Bolli, Assicurazioni, Patenti, Documenti)',
                'Gestione Manutenzioni (per KM, per DATA, per Ore Lavoro Motore)',
                'Gestione Referenti Notifiche'
            ],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 24
        ],
        'formazione_assistenza' => [
            'name' => "FORMAZIONE + ASSISTENZA - GT FLEET 365 WEB E APP",
            'list_price' => 100.00,
            'discounted_price' => 80.00,
            'offer_price' => 0.00,
            'description' => [
                'Numero 6 ore di Formazione in un Anno con Operatore Customer Care',
                'Assistenza di 1° e 2° Livello con Apertura Ticket'
            ],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 24
        ],
        'missioni' => [
            'name' => "GT FLEET 365 MISSIONI: PIANI DI VIAGGIO, ATTIVITA', VISITE",
            'list_price' => 200.00,
            'discounted_price' => 160.00,
            'offer_price' => 112.00,
            'description' => [
                'Predisposizione MISSIONI: Piano di Viaggio, Piano Attività, Piano Visite',
                'Trasmissione MISSIONI dal Web all\'App Driver',
                'Gestione delle MISSIONI attraverso l\'App Driver',
                'Gestione automatica dell MISSIONI (in auto geo fencing)'
            ],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 24
        ],
        'mappe_track' => [
            'name' => "SERVIZIO MAPPE TRUCK",
            'list_price' => 200.00,
            'discounted_price' => 160.00,
            'offer_price' => 96.00,
            'description' => [
                'Funzione Calcolo e Ottimizzazione Percorsi utilizzando Mappe Truck',
                'Gestione Ponti Bassi, Strettoie e Ponti con limitazione di Peso'
            ],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 24
        ],
        'temperatura_controllata' => [
            'name' => "CONTROLLED TEMPERATURE SERVICE",
            'list_price' => 150.00,
            'discounted_price' => 120.00,
            'offer_price' => 84.00,
            'description' => ['Monitoraggio continuo della temperatura in cella'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 24
        ],
        'gestione_portellone' => [
            'name' => "TAILGATE MANAGEMENT SERVICE",
            'list_price' => 100.00,
            'discounted_price' => 80.00,
            'offer_price' => 56.00,
            'description' => ['Sensore di apertura/chiusura portellone'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 24
        ],
        'riconoscimento_driver' => [
            'name' => "DRIVER RECOGNITION SERVICE",
            'list_price' => 120.00,
            'discounted_price' => 96.00,
            'offer_price' => 67.20,
            'description' => ['Identificazione autista tramite chiave o tastierino'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 24
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
            'list_price' => 180.00,
            'discounted_price' => 144.00,
            'offer_price' => 100.80,
            'description' => ['Tempi di guida in tempo reale da scheda tachigrafica'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 24
        ],
        'centrale_operativa_live' => [
            'name' => "LIVE OPERATIONS CENTER (24/7)",
            'list_price' => 360.00,
            'discounted_price' => 288.00,
            'offer_price' => 201.60,
            'description' => ['Centrale operativa di sicurezza H24'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 24
        ],
        'centrale_operativa_ondemand' => [
            'name' => "ON DEMAND OPERATIONS CENTER",
            'list_price' => 180.00,
            'discounted_price' => 144.00,
            'offer_price' => 100.80,
            'description' => ['Attivazione centrale operativa su richiesta'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 24
        ],
        'scorta_digitale' => [
            'name' => "GT FLEET 365 DIGITAL ESCORT",
            'list_price' => 250.00,
            'discounted_price' => 200.00,
            'offer_price' => 140.00,
            'description' => ['Scorta digitale satellitare del mezzo'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 24
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
            'list_price' => 120.00,
            'discounted_price' => 96.00,
            'offer_price' => 67.20,
            'description' => ['Hosting carta aziendale presso data center (ogni 25 mezzi)'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 24
        ],
        'crono_ddd_silver' => [
            'name' => "CRONO - DDD MANAGER - SILVER",
            'list_price' => 150.00,
            'discounted_price' => 120.00,
            'offer_price' => 84.00,
            'description' => ['Scarico remoto dati tachigrafo e archiviazione legale'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 24
        ],
        'crono_ddd_gold' => [
            'name' => "CRONO - DDD MANAGER - GOLD",
            'list_price' => 200.00,
            'discounted_price' => 160.00,
            'offer_price' => 112.00,
            'description' => ['Scarico dati, archiviazione e analisi infrazioni'],
            'type' => 'servizio',
            'period' => 'annuale',
            'contract_months' => 24
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
            'list_price' => 80.00,
            'discounted_price' => 64.00,
            'offer_price' => 50.00,
            'description' => ['Sonda termica per cella frigo'],
            'type' => 'hardware',
            'period' => 'una tantum',
            'contract_months' => 1
        ],
        'hw_sensore_portellone' => [
            'name' => "TAILGATE SENSOR (Van, Truck, Trailer)",
            'list_price' => 60.00,
            'discounted_price' => 48.00,
            'offer_price' => 40.00,
            'description' => ['Sensore magnetico per portellone'],
            'type' => 'hardware',
            'period' => 'una tantum',
            'contract_months' => 1
        ],
        'hw_chiave_dallas' => [
            'name' => "DALLAS KEY",
            'list_price' => 15.00,
            'discounted_price' => 12.00,
            'offer_price' => 10.00,
            'description' => ['Chiave magnetica autista'],
            'type' => 'hardware',
            'period' => 'una tantum',
            'contract_months' => 1
        ],
        'hw_kit_lettore_dallas' => [
            'name' => "DALLAS READER + KEY KIT",
            'list_price' => 65.00,
            'discounted_price' => 52.00,
            'offer_price' => 45.00,
            'description' => ['Lettore cruscotto + Chiave Dallas'],
            'type' => 'hardware',
            'period' => 'una tantum',
            'contract_months' => 1
        ],
        'hw_tastierino' => [
            'name' => "KEYPAD",
            'list_price' => 90.00,
            'discounted_price' => 72.00,
            'offer_price' => 65.00,
            'description' => ['Tastierino numerico per inserimento PIN'],
            'type' => 'hardware',
            'period' => 'una tantum',
            'contract_months' => 1
        ],
        'hw_beacon' => [
            'name' => "BEACON",
            'list_price' => 40.00,
            'discounted_price' => 32.00,
            'offer_price' => 25.00,
            'description' => ['Tag Bluetooth per riconoscimento automatico'],
            'type' => 'hardware',
            'period' => 'una tantum',
            'contract_months' => 1
        ],
        'hw_beacon_passeggero' => [
            'name' => "BEACON (Passenger Recognition)",
            'list_price' => 40.00,
            'discounted_price' => 32.00,
            'offer_price' => 25.00,
            'description' => ['Tag Bluetooth per riconoscimento passeggero'],
            'type' => 'hardware',
            'period' => 'una tantum',
            'contract_months' => 1
        ],
        'hw_pedale_antirapina' => [
            'name' => "ANTI-ROBBERY / PANIC PEDAL",
            'list_price' => 120.00,
            'discounted_price' => 96.00,
            'offer_price' => 85.00,
            'description' => ['Pedale occultato per allarme rapina'],
            'type' => 'hardware',
            'period' => 'una tantum',
            'contract_months' => 1
        ],
        'hw_gost_shadow' => [
            'name' => "GOST - SHADOW DEVICE SUPPLY",
            'list_price' => 180.00,
            'discounted_price' => 144.00,
            'offer_price' => 120.00,
            'description' => ['Dispositivo civetta occultato e autoalimentato'],
            'type' => 'hardware',
            'period' => 'una tantum',
            'contract_months' => 1
        ],
    ];

    /**
     * Genera il file DOCX e tenta la conversione in PDF
     * Restituisce il percorso del file finale (PDF se possibile, altrimenti DOCX)
     */
    public function generateQuote($serviceRequests, $token)
    {
        $templatePath = resource_path('templates/template_offerta.docx');
        
        if (!file_exists($templatePath)) {
            Log::error("Template non trovato in {$templatePath}");
            throw new \Exception("Template Word non trovato.");
        }

        $firstReq = $serviceRequests->first();
        $companyName = $firstReq->client_data['company'] ?? 'Cliente';
        $contactName = ($firstReq->client_data['contact'] ?? '') . ' ' . ($firstReq->client_data['lastname'] ?? '');
        
        // --- CREAZIONE TABELLA PREVENTIVO (12 COLONNE) ---
        $tableStyle = [
            'borderSize' => 6,
            'borderColor' => '000000',
            'width' => 5000,
            'unit' => \PhpOffice\PhpWord\SimpleType\TblWidth::PERCENT,
            'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER,
            'cellMargin' => 50
        ];

        $table = new \PhpOffice\PhpWord\Element\Table($tableStyle);

        // Stili
        $headerFont = ['bold' => true, 'name' => 'Arial', 'size' => 7, 'color' => '000000'];
        $cellFont = ['name' => 'Arial', 'size' => 7];
        $cellFontBold = ['name' => 'Arial', 'size' => 7, 'bold' => true];
        $cellStyle = ['valign' => 'center'];
        $yellowCellStyle = ['bgColor' => 'FFFF00', 'valign' => 'center'];

        // Larghezze colonne proporzionali (somma = 5000)
        $w = [500, 300, 1100, 350, 350, 400, 300, 250, 200, 400, 400, 450];

        // --- HEADER (riga fissa, sempre uguale) ---
        $table->addRow(null, ['tblHeader' => true]);
        $headers = [
            'Codice Servizio',
            'Hardware o Servizio',
            'Descrizione Prodotto e Servizi Forniti',
            'Prezzo di Listino al Pubblico',
            'Prezzo Servizio e Fornitura Scontato',
            'Prezzo Servizio o Hardware Offerta LAST MINUTE 30/06/2026',
            'Periodicità Pagamento',
            'Durata Contrattuale in mesi',
            'Quantità servizi',
            'Periodicità Pagamento',
            'Prezzo Servizio o Hardware Offerta LAST MINUTE 30/06/2026',
            'Totale Annuale per Canoni Mensili'
        ];
        foreach ($headers as $i => $header) {
            $style = ($i === 5 || $i === 10) ? $yellowCellStyle : $cellStyle;
            $table->addCell($w[$i], $style)->addText($header, $headerFont);
        }

        // --- AGGREGAZIONE SERVIZI (una riga per servizio unico) ---
        $aggregated = [];
        foreach ($serviceRequests as $req) {
            $vehicleQty = $req->vehicle_qty ?? 1;
            $services = $req->services ?? [];

            foreach ($services as $srv) {
                $id = $srv['id'];
                $srvQty = $srv['qty'] ?? 1;
                $totalQtyForThis = $vehicleQty * $srvQty;

                if (isset($aggregated[$id])) {
                    // Servizio già presente, somma la quantità
                    $aggregated[$id]['totalQty'] += $totalQtyForThis;
                } else {
                    $aggregated[$id] = [
                        'id' => $id,
                        'srv' => $srv,
                        'totalQty' => $totalQtyForThis,
                    ];
                }
            }
        }

        // --- RIGHE DATI (una per servizio unico) ---
        $totaleServiziAnnuo = 0;
        $totaleHardwareUnaTantum = 0;

        foreach ($aggregated as $id => $entry) {
            $srv = $entry['srv'];
            $totalQty = $entry['totalQty'];

            // Dati dal pricing array (solo per info aggiuntive: descrizione, tipo, periodo)
            $priceInfo = $this->pricing[$id] ?? [
                'name' => $srv['name'],
                'list_price' => 0,
                'discounted_price' => 0,
                'offer_price' => 0,
                'description' => [],
                'type' => 'servizio',
                'period' => 'annuale',
                'contract_months' => 24
            ];

            // PREZZI: priorità ai dati inseriti dal commerciale nella configurazione
            $listPrice = isset($srv['price']) ? (float) $srv['price'] : $priceInfo['list_price'];
            $discount = isset($srv['discount']) ? (float) $srv['discount'] : 0;
            $discountedPrice = ($discount > 0) ? ($listPrice - $discount) : ($priceInfo['discounted_price'] ?? $listPrice);
            $offerPrice = isset($srv['final_price']) ? (float) $srv['final_price'] : $priceInfo['offer_price'];
            
            $contractMonths = $priceInfo['contract_months'] ?? 24;
            $period = $priceInfo['period'] ?? 'annuale';
            $type = $priceInfo['type'] ?? 'servizio';

            // Calcoli
            $prezzoMensile = ($period === 'annuale' && $offerPrice > 0) ? $offerPrice / 12 : 0;
            $totaleAnnualeSingolo = ($period === 'annuale') ? $prezzoMensile * 12 : $offerPrice;

            if ($period === 'annuale') {
                $totaleServiziAnnuo += $totaleAnnualeSingolo * $totalQty;
            } else {
                $totaleHardwareUnaTantum += $offerPrice * $totalQty;
            }

            // Formattazione prezzi come da listino MacNil
            $euro = html_entity_decode('&euro;', ENT_COMPAT, 'UTF-8');
            $fmtList = number_format($listPrice, 2, ',', '.') . ' ' . $euro;
            $fmtDisc = $euro . ' ' . number_format($discountedPrice, 2, ',', '.');
            $fmtOffer = ($offerPrice == 0) ? 'OMAGGIO' : $euro . ' ' . number_format($offerPrice, 2, ',', '.');
            $fmtMensile = ($offerPrice == 0) ? 'OMAGGIO' : $euro . ' ' . number_format($prezzoMensile, 2, ',', '.');
            $fmtTotale = ($offerPrice == 0) ? 'OMAGGIO' : $euro . ' ' . number_format($totaleAnnualeSingolo, 2, ',', '.');

            $table->addRow();

            // Col 1: Codice Servizio
            $table->addCell($w[0], $cellStyle)->addText($priceInfo['name'], $cellFont);

            // Col 2: Hardware o Servizio
            $table->addCell($w[1], $cellStyle)->addText($type, $cellFont);

            // Col 3: Descrizione (con bullet list)
            $descCell = $table->addCell($w[2], $cellStyle);
            $descriptions = $priceInfo['description'] ?? [];
            foreach ($descriptions as $desc) {
                $descCell->addListItem(
                    $desc,
                    0,
                    ['name' => 'Arial', 'size' => 6],
                    ['listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_BULLET_FILLED]
                );
            }

            // Col 4: Prezzo Listino
            $table->addCell($w[3], $cellStyle)->addText($fmtList, $cellFont);

            // Col 5: Prezzo Scontato
            $table->addCell($w[4], $cellStyle)->addText($fmtDisc, $cellFont);

            // Col 6: Prezzo Offerta (GIALLO)
            $table->addCell($w[5], $yellowCellStyle)->addText($fmtOffer, $cellFontBold);

            // Col 7: Periodicita
            $table->addCell($w[6], $cellStyle)->addText($period, $cellFont);

            // Col 8: Durata Contrattuale
            $table->addCell($w[7], $cellStyle)->addText((string) $contractMonths, $cellFont);

            // Col 9: Quantita
            $table->addCell($w[8], $cellStyle)->addText((string) $totalQty, $cellFont);

            // Col 10: Periodicita Pagamento
            $periodicita = ($period === 'annuale') ? 'Canone Mensile con Pagamento Bimestrale Anticipato' : 'Una Tantum';
            $table->addCell($w[9], $cellStyle)->addText($periodicita, ['name' => 'Arial', 'size' => 6]);

            // Col 11: Prezzo Mensile Offerta (GIALLO)
            $table->addCell($w[10], $yellowCellStyle)->addText($fmtMensile, $cellFontBold);

            // Col 12: Totale Annuale
            $table->addCell($w[11], $cellStyle)->addText($fmtTotale, $cellFontBold);
        }

        // --- RIGHE TOTALI ---
        // Riga vuota di separazione
        $table->addRow();
        $table->addCell(5000, ['gridSpan' => 12])->addText('', $cellFont);

        // Totale Canoni Annuali
        $table->addRow();
        $table->addCell(4500, ['gridSpan' => 11])->addText('TOTALE CANONI ANNUALI', $cellFontBold);
        $table->addCell($w[11])->addText(number_format($totaleServiziAnnuo, 2, ',', '.'), $cellFontBold);

        // Totale Hardware
        $table->addRow();
        $table->addCell(4500, ['gridSpan' => 11])->addText('TOTALE HARDWARE UNA TANTUM', $cellFontBold);
        $table->addCell($w[11])->addText(number_format($totaleHardwareUnaTantum, 2, ',', '.'), $cellFontBold);

        // --- PRE-ELABORAZIONE ZIP ARCHIVE ---
        $tempDocx = storage_path('app/public/temp-' . time() . '.docx');
        if (!file_exists(storage_path('app/public'))) {
            mkdir(storage_path('app/public'), 0755, true);
        }
        copy($templatePath, $tempDocx);

        $zip = new \ZipArchive();
        if ($zip->open($tempDocx) === TRUE) {
            // Sostituisci in document.xml
            $docXml = $zip->getFromName('word/document.xml');
            if ($docXml !== false) {
                // Sostituisce il nome azienda
                $docXml = str_replace('Nome Azienda', htmlspecialchars($companyName), $docXml);
                
                // Rimuove eventuali evidenziazioni gialle
                $docXml = str_replace('<w:highlight w:val="yellow"/>', '', $docXml);
                
                // Inserisce il macro-placeholder per la tabella sotto la seconda occorrenza (quella nel corpo)
                $search = 'VALORIZZAZIONE ECONOMICA DELLA FORNITURA GT FLEET 365';
                $replace = 'VALORIZZAZIONE ECONOMICA DELLA FORNITURA GT FLEET 365</w:t></w:r></w:p><w:p><w:r><w:t>${quote_table}</w:t></w:r></w:p><w:p><w:r><w:t>';
                
                $pos = strpos($docXml, $search); // Prima occorrenza (nell'Indice)
                if ($pos !== false) {
                    $pos2 = strpos($docXml, $search, $pos + strlen($search)); // Seconda occorrenza (nel Corpo)
                    if ($pos2 !== false) {
                        $docXml = substr_replace($docXml, $replace, $pos2, strlen($search));
                    }
                }
                
                $zip->addFromString('word/document.xml', $docXml);
            }
            
            // Sostituisci negli header (per RAGIONE SOCIALE, emesso da, data)
            for ($i = 1; $i <= 5; $i++) {
                $headerName = 'word/header' . $i . '.xml';
                $headerXml = $zip->getFromName($headerName);
                if ($headerXml !== false) {
                    $headerXml = str_replace('RAGIONE SOCIALE', htmlspecialchars($companyName), $headerXml);
                    $headerXml = str_replace('Emesso da: TEAM Sales', htmlspecialchars('Emesso da: GT Fleet 365'), $headerXml);
                    $headerXml = str_replace('xx mese 2026', date('d/m/Y'), $headerXml);
                    
                    // Rimuove eventuali evidenziazioni gialle
                    $headerXml = str_replace('<w:highlight w:val="yellow"/>', '', $headerXml);
                    
                    $zip->addFromString($headerName, $headerXml);
                }
            }

            // Ottieni il commerciale
            $agentEmail = $firstReq->agent_email ?? null;
            $agent = $agentEmail ? \App\Models\User::where('email', $agentEmail)->first() : null;
            $agentName = $agent ? $agent->name : 'Commerciale';

            // Sostituisci nei footer (per Nome e Cognome)
            for ($i = 1; $i <= 5; $i++) {
                $footerName = 'word/footer' . $i . '.xml';
                $footerXml = $zip->getFromName($footerName);
                if ($footerXml !== false) {
                    $footerXml = str_replace('Nome e Cognome', htmlspecialchars($agentName), $footerXml);
                    
                    // Rimuove eventuali evidenziazioni gialle
                    $footerXml = str_replace('<w:highlight w:val="yellow"/>', '', $footerXml);
                    
                    $zip->addFromString($footerName, $footerXml);
                }
            }
            $zip->close();
        }

        // --- INSERIMENTO TABELLA CON TEMPLATE PROCESSOR ---
        $templateProcessor = new TemplateProcessor($tempDocx);
        
        // Questo setComplexBlock andrà a sostituire la riga ${quote_table} che abbiamo iniettato
        $templateProcessor->setComplexBlock('quote_table', $table);

        // Salva il file DOCX finale
        $fileNameDocx = 'preventivo-' . \Illuminate\Support\Str::slug($companyName) . '-' . time() . '.docx';
        $pathDocx = storage_path('app/public/service-pdfs/' . $fileNameDocx);
        
        if (!file_exists(storage_path('app/public/service-pdfs'))) {
            mkdir(storage_path('app/public/service-pdfs'), 0755, true);
        }

        $templateProcessor->saveAs($pathDocx);
        
        // Pulisce il temp file
        @unlink($tempDocx);

        // PROVA CONVERSIONE IN PDF
        $fileNamePdf = str_replace('.docx', '.pdf', $fileNameDocx);
        $pathPdf = storage_path('app/public/service-pdfs/' . $fileNamePdf);
        $outdir = storage_path('app/public/service-pdfs');

        $cmd = "soffice --headless --convert-to pdf --outdir " . escapeshellarg($outdir) . " " . escapeshellarg($pathDocx) . " 2>&1";
        $output = [];
        $returnVar = 0;
        exec($cmd, $output, $returnVar);

        if ($returnVar === 0 && file_exists($pathPdf)) {
            return $pathPdf;
        }

        Log::warning("Impossibile convertire in PDF con soffice. Output: " . implode("\n", $output));
        
        return $pathDocx;
    }
}
