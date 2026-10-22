<?php

/**
 * Mappatura dei servizi disponibili per ogni categoria di mezzo.
 *
 * Ogni mezzo del catalogo viene associato a una categoria (vehicle_category).
 * Il form servizi mostrerà solo i servizi della categoria corrispondente.
 *
 * Struttura:
 * - 'vehicle_categories': mappa vehicle_id → categoria
 * - 'services': servizi raggruppati per sezione, con flag per categoria
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Mappatura Veicolo → Categoria
    |--------------------------------------------------------------------------
    */
    'vehicle_categories' => [
        // Mezzi Aziendali
        'auto'                    => 'aziendali',
        'auto_fringe'             => 'aziendali',
        'auto_elettrica'          => 'aziendali',
        'auto_elettrica_fringe'   => 'aziendali',
        'auto_ibrida'             => 'aziendali',
        'auto_ibrida_fringe'      => 'aziendali',
        'furgone'                 => 'aziendali',
        'furgone_cassonato'       => 'aziendali',
        'moto'                    => 'aziendali',
        'carro_funebre'           => 'aziendali',

        // Mezzi Frigo
        'furgone_frigo'           => 'frigo',
        'furgone_multitemp'       => 'frigo',
        'motrice_frigo'           => 'frigo_pesante',
        'motrice_isotermica'      => 'frigo_pesante',
        'rimorchio_frigo'         => 'frigo_rimorchio',
        'semirimorchio_frigo'     => 'frigo_rimorchio',
        'semirimorchio_isotermico' => 'frigo_rimorchio',

        // Mezzi Pesanti
        'bus_alunni'              => 'pesanti',
        'bisarca'                 => 'pesanti',
        'bus_privato'             => 'pesanti',
        'bus_pubblico'            => 'pesanti',
        'motrice_cisterna'        => 'pesanti',
        'motrice_gru'             => 'pesanti',
        'motrice_container'       => 'pesanti',
        'motrice_rimorchio'       => 'pesanti',
        'motrice_telonata'        => 'pesanti',
        'spazzatrice'             => 'pesanti',
        'trattore_stradale'       => 'pesanti',
        'veicolo_rifiuti'         => 'pesanti',

        // Rimorchi e Semirimorchi
        'rimorchio_motrici'       => 'rimorchi',
        'semirimorchio'           => 'rimorchi',
        'semirimorchio_telonato'  => 'rimorchi',
        'semirimorchio_container' => 'rimorchi',
        'cassa_mobile'            => 'rimorchi',
        'cassone_scarrabile'      => 'rimorchi',
        'container'               => 'rimorchi',

        // Trattori Agricoli & Mezzi D'Opera
        'trattore_agricolo'       => 'opera',
        'trattore_compatto'       => 'opera',
        'betoniera'               => 'opera',
        'mezzi_cantiere'          => 'opera',
        'mezzi_magazzino'         => 'opera',
        'mezzi_movimento_terra'   => 'opera',
        'mezzi_cave'              => 'opera',
        'minidumper'              => 'opera',
        'motocarriola'            => 'opera',
        'muletto'                 => 'opera',
        'piattaforma_furgone'     => 'opera',
        'piattaforma_gomma'       => 'opera',

        // Asset
        'golf_cart'               => 'asset',
        'cucina_mobile'           => 'asset',
        'gommone'                 => 'asset',
        'gruppo_elettrogeno'      => 'asset',
        'bagno_chimico'           => 'asset',
        'attrezzatura_cantiere'   => 'asset',
        'mezzo_portuale'          => 'asset',
    ],

    /*
    |--------------------------------------------------------------------------
    | Servizi Raggruppati per Sezione
    |--------------------------------------------------------------------------
    |
    | Ogni servizio ha:
    | - 'name': nome visualizzato nel form
    | - 'type': 'radio' (pacchetto base, uno solo) o 'checkbox'
    | - 'input': 'check' (solo selezione) o 'qty' (con campo quantità)
    | - 'categories': array di categorie di mezzi per cui è visibile
    |
    */
    'sections' => [

        [
            'title' => 'Package',
            'icon'  => '📡',
            'type'  => 'radio', // Solo uno selezionabile
            'items' => [
                [
                    'id'   => 'base_loc',
                    'name' => 'GT FLEET 365 BASE (LOC)',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante', 'opera'],
                ],
                [
                    'id'   => 'plus_loc_sic',
                    'name' => 'GT FLEET 365 PLUS (LOC + SEC)',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante'],
                ],
                [
                    'id'   => 'gold_loc_tel',
                    'name' => 'GT FLEET 365 GOLD (LOC + TEL)',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante'],
                ],
                [
                    'id'   => 'premium_loc_tel_sic',
                    'name' => 'GT FLEET 365 PREMIUM (LOC + TEL + SEC)',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante'],
                ],
                [
                    'id'   => 'plus_trattore',
                    'name' => 'GT FLEET 365 PLUS',
                    'categories' => ['opera'],
                ],
                [
                    'id'   => 'truck_crono_base',
                    'name' => 'GT FLEET 365 TRUCK CRONO BASE (LOC + CRONO)',
                    'categories' => ['pesanti', 'frigo_pesante'],
                ],
                [
                    'id'   => 'truck_crono_plus',
                    'name' => 'GT FLEET 365 TRUCK CRONO PLUS (LOC + SEC + CRONO)',
                    'categories' => ['pesanti', 'frigo_pesante'],
                ],
                [
                    'id'   => 'truck_crono_tel',
                    'name' => 'GT FLEET 365 TRUCK CRONO TEL (LOC + TEL + CRONO)',
                    'categories' => ['pesanti', 'frigo_pesante'],
                ],
                [
                    'id'   => 'truck_crono_premium',
                    'name' => 'GT FLEET 365 TRUCK CRONO PREMIUM (LOC + TEL + SEC + CRONO)',
                    'categories' => ['pesanti', 'frigo_pesante'],
                ],
                [
                    'id'   => 'rimorchi_comodato',
                    'name' => 'GT FLEET 365 TRAILERS (loaned device)',
                    'categories' => ['rimorchi', 'frigo_rimorchio'],
                ],
                [
                    'id'   => 'rimorchi_vendita',
                    'name' => 'GT FLEET 365 TRAILERS (purchased device)',
                    'categories' => ['rimorchi', 'frigo_rimorchio'],
                ],
                [
                    'id'   => 'asset',
                    'name' => 'GT FLEET 365 ASSET',
                    'categories' => ['asset'],
                ],
            ],
        ],

        [
            'title' => 'App & Additional Services',
            'icon'  => '📱',
            'type'  => 'checkbox',
            'items' => [
                [
                    'id'   => 'app_fleet_manager',
                    'name' => 'GT FLEET 365 APP - FLEET MANAGER',
                    'input' => 'check',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante', 'frigo_rimorchio', 'rimorchi', 'opera', 'asset'],
                ],
                [
                    'id'   => 'app_driver',
                    'name' => 'GT FLEET 365 APP - DRIVER',
                    'input' => 'check',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante', 'opera'],
                ],
                [
                    'id'   => 'manutenzioni_scadenze',
                    'name' => 'GT FLEET 365 MAINTENANCE SERVICE - DEADLINES',
                    'input' => 'check',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante', 'opera'],
                ],
                [
                    'id'   => 'formazione_assistenza',
                    'name' => 'TRAINING + SUPPORT - GT FLEET 365 WEB AND APP',
                    'input' => 'check',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante', 'frigo_rimorchio', 'rimorchi', 'opera'],
                ],
                [
                    'id'   => 'missioni',
                    'name' => 'GT FLEET 365 MISSIONS: TRAVEL PLANS, ACTIVITIES, VISITS',
                    'input' => 'check',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante'],
                ],
                [
                    'id'   => 'mappe_track',
                    'name' => 'TRACK MAPS SERVICE - Heavy Vehicles',
                    'input' => 'check',
                    'categories' => ['pesanti', 'frigo_pesante'],
                ],
                [
                    'id'   => 'temperatura_controllata',
                    'name' => 'CONTROLLED TEMPERATURE SERVICE',
                    'input' => 'check',
                    'categories' => ['frigo', 'frigo_pesante', 'frigo_rimorchio'],
                    'hardware' => [
                        [
                            'id'   => 'hw_sensore_temperatura',
                            'name' => 'TEMPERATURE SENSOR (Truck or Trailer)',
                            'input' => 'qty',
                            'categories' => ['frigo', 'frigo_pesante', 'frigo_rimorchio'],
                        ],
                    ],
                ],
                [
                    'id'   => 'gestione_portellone',
                    'name' => 'TAILGATE MANAGEMENT SERVICE',
                    'input' => 'check',
                    'categories' => ['frigo', 'frigo_pesante', 'frigo_rimorchio'],
                    'hardware' => [
                        [
                            'id'   => 'hw_sensore_portellone',
                            'name' => 'TAILGATE SENSOR (Van, Truck, Trailer)',
                            'input' => 'qty',
                            'categories' => ['frigo', 'frigo_pesante', 'frigo_rimorchio'],
                        ],
                    ],
                ],
                [
                    'id'   => 'riconoscimento_driver',
                    'name' => 'DRIVER RECOGNITION SERVICE',
                    'input' => 'check',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante'],
                    'hardware' => [
                        [
                            'id'   => 'hw_chiave_dallas',
                            'name' => 'DALLAS KEY',
                            'input' => 'qty',
                            'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante', 'opera'],
                        ],
                        [
                            'id'   => 'hw_kit_lettore_dallas',
                            'name' => 'DALLAS READER + KEY KIT',
                            'input' => 'qty',
                            'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante', 'opera'],
                        ],
                        [
                            'id'   => 'hw_tastierino',
                            'name' => 'KEYPAD',
                            'input' => 'qty',
                            'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante'],
                        ],
                    ],
                ],
                [
                    'id'   => 'riconoscimento_driver_app',
                    'name' => 'DRIVER RECOGNITION SERVICE WITH APP',
                    'input' => 'check',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante'],
                    'hardware' => [
                        [
                            'id'   => 'hw_beacon',
                            'name' => 'BEACON',
                            'input' => 'qty',
                            'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante', 'opera'],
                        ],
                        [
                            'id'   => 'hw_beacon_passeggero',
                            'name' => 'BEACON (Passenger Recognition)',
                            'input' => 'qty',
                            'categories' => ['pesanti'],
                        ],
                    ],
                ],
                [
                    'id'   => 'tempi_guida_realtime',
                    'name' => 'REAL TIME DRIVING TIMES - DRIVER RECOGNITION with TACHOGRAPH CARD',
                    'input' => 'check',
                    'categories' => ['pesanti', 'frigo_pesante'],
                ],
                [
                    'id'   => 'centrale_operativa_live',
                    'name' => 'LIVE OPERATIONS CENTER (24/7)',
                    'input' => 'check',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante'],
                    'hardware' => [
                        [
                            'id'   => 'hw_pedale_antirapina',
                            'name' => 'ANTI-ROBBERY / PANIC PEDAL',
                            'input' => 'qty',
                            'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante'],
                        ],
                    ],
                ],
                [
                    'id'   => 'centrale_operativa_ondemand',
                    'name' => 'ON DEMAND OPERATIONS CENTER',
                    'input' => 'check',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante'],
                ],
                [
                    'id'   => 'scorta_digitale',
                    'name' => 'GT FLEET 365 DIGITAL ESCORT',
                    'input' => 'check',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante', 'frigo_rimorchio', 'rimorchi', 'opera', 'asset'],
                    'hardware' => [
                        [
                            'id'   => 'hw_gost_shadow',
                            'name' => 'GOST - SHADOW DEVICE SUPPLY',
                            'input' => 'qty',
                            'categories' => ['aziendali', 'frigo', 'opera'],
                        ],
                    ],
                ],
                [
                    'id'   => 'app_gt5_app',
                    'name' => 'GT FLEET 365 APP - GT 5.0.APP',
                    'input' => 'check',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante', 'frigo_rimorchio', 'rimorchi', 'opera', 'asset'],
                ],

            ],
        ],

        [
            'title' => 'Chronotachograph',
            'icon'  => '⏱️',
            'type'  => 'checkbox',
            'items' => [
                [
                    'id'   => 'carta_aziendale_crono',
                    'name' => 'COMPANY CRONO CARD (every 25 Vehicles)',
                    'input' => 'qty',
                    'categories' => ['pesanti', 'frigo_pesante'],
                ],
                [
                    'id'   => 'crono_ddd_silver',
                    'name' => 'CRONO - DDD MANAGER - SILVER',
                    'input' => 'check',
                    'categories' => ['pesanti', 'frigo_pesante'],
                ],
                [
                    'id'   => 'crono_ddd_gold',
                    'name' => 'CRONO - DDD MANAGER - GOLD',
                    'input' => 'check',
                    'categories' => ['pesanti', 'frigo_pesante'],
                ],
            ],
        ],

        [
            'title' => 'Development & Customizations',
            'icon'  => '🔧',
            'type'  => 'checkbox',
            'items' => [
                [
                    'id'   => 'sviluppo_digital_transformation',
                    'name' => 'DEVELOPMENT - DIGITAL TRANSFORMATION',
                    'input' => 'check',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante', 'frigo_rimorchio', 'rimorchi', 'opera', 'asset'],
                ],
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Prezzi di listino pubblico dei pacchetti/servizi (ID => prezzo annuo)
    | Fonte unica per form servizi e validazione lato server.
    |--------------------------------------------------------------------------
    */
    // Prezzi per pagamento mensile dal listino Rel 9 (Canone Mensile con Pagamento Bimestrale Anticipato).
    // 'monthly' = prezzo al mese, 'year' = totale annuale per canoni mensili. Se un servizio non e' qui, il mensile non e' disponibile.
    'monthly_prices' => [
        'base_loc'                        => ['monthly' => 12.0, 'year' => 144.0],
        'plus_loc_sic'                    => ['monthly' => 14.666666666666666, 'year' => 176.0],
        'gold_loc_tel'                    => ['monthly' => 16.666666666666668, 'year' => 200.0],
        'premium_loc_tel_sic'             => ['monthly' => 18.0, 'year' => 216.0],
        'truck_crono_base'                => ['monthly' => 18.0, 'year' => 216.0],
        'truck_crono_plus'                => ['monthly' => 23.333333333333332, 'year' => 280.0],
        'truck_crono_tel'                 => ['monthly' => 28.666666666666668, 'year' => 344.0],
        'truck_crono_premium'             => ['monthly' => 35.333333333333336, 'year' => 424.0],
        'tempi_guida_realtime'            => ['monthly' => 6.666666666666667, 'year' => 80.0],
        'app_fleet_manager'               => ['monthly' => 2.6, 'year' => 31.2],
        'manutenzioni_scadenze'           => ['monthly' => 5.0, 'year' => 60.0],
        'formazione_assistenza'           => ['monthly' => 5.0, 'year' => 60.0],
        'app_driver'                      => ['monthly' => 5.0, 'year' => 60.0],
        'rimorchi_comodato'               => ['monthly' => 14.666666666666666, 'year' => 176.0],
        'missioni'                        => ['monthly' => 13.333333333333334, 'year' => 160.0],
        'mappe_track'                     => ['monthly' => 13.333333333333334, 'year' => 160.0],
        'gestione_portellone'             => ['monthly' => 7.399999999999999, 'year' => 88.8],
        'temperatura_controllata'         => ['monthly' => 7.333333333333333, 'year' => 88.0],
        'riconoscimento_driver'           => ['monthly' => 7.399999999999999, 'year' => 88.8],
        'centrale_operativa_live'         => ['monthly' => 13.333333333333334, 'year' => 160.0],
        'plus_trattore'                   => ['monthly' => 16.333333333333336, 'year' => 196.0],
        'centrale_operativa_ondemand'     => ['monthly' => 6.666666666666667, 'year' => 80.0],
        'scorta_digitale'                 => ['monthly' => 60.0, 'year' => 720.0],
    ],

    'package_prices' => [
        // Pacchetti base
        "base_loc"              => 180,  // GT FLEET 365 BASE (LOC)
        "plus_loc_sic"          => 220,  // GT FLEET 365 PLUS (LOC + SEC)
        "gold_loc_tel"          => 250,  // GT FLEET 365 GOLD (LOC + TEL)
        "premium_loc_tel_sic"   => 270,  // GT FLEET 365 PREMIUM (LOC + TEL + SEC)
        "plus_trattore"         => 245,  // GT FLEET 365 PLUS (Trattori)
        "truck_crono_base"      => 270,  // GT FLEET 365 TRUCK CRONO BASE
        "truck_crono_plus"      => 350,  // GT FLEET 365 TRUCK CRONO PLUS
        "truck_crono_tel"       => 430,  // GT FLEET 365 TRUCK CRONO TEL
        "truck_crono_premium"   => 530,  // GT FLEET 365 TRUCK CRONO PREMIUM
        "rimorchi_comodato"     => 220,  // GT FLEET 365 TRAILERS (dispositivo in comodato)
        "rimorchi_vendita"      => 150,  // GT FLEET 365 TRAILERS (dispositivo in vendita)
        "asset"                 => 150,  // GT FLEET 365 ASSET
        // Servizi aggiuntivi
        "app_fleet_manager"     => 39,   // APP GT FLEET 365 - FLEET MANAGER
        "app_driver"            => 75,  // APP GT FLEET 365 - DRIVER
        "manutenzioni_scadenze" => 75,  // GT FLEET 365 SERVIZIO MANUTENZIONI - SCADENZE
        "formazione_assistenza" => 75,  // FORMAZIONE + ASSISTENZA
        "missioni"              => 200,  // GT FLEET 365 MISSIONI
        "mappe_track"           => 200,  // SERVIZIO MAPPE TRUCK - MEZZI PESANTI
        "scorta_digitale"       => 900,  // GT FLEET 365 SCORTA DIGITALE
        "app_gt5_app"           => 100,  // GT FLEET 365 APP - GT 5.0
    ],

];
