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
            'title' => 'Base Package',
            'icon'  => '📡',
            'type'  => 'radio', // Solo uno selezionabile
            'items' => [
                [
                    'id'   => 'base_loc',
                    'name' => 'GT FLEET 365 BASE (LOC) - Corporate, Heavy, Construction Vehicles',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante', 'opera'],
                ],
                [
                    'id'   => 'plus_loc_sic',
                    'name' => 'GT FLEET 365 PLUS (LOC + SEC) - Corporate, Heavy Vehicles',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante'],
                ],
                [
                    'id'   => 'gold_loc_tel',
                    'name' => 'GT FLEET 365 GOLD (LOC + TEL) - Corporate, Heavy Vehicles',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante'],
                ],
                [
                    'id'   => 'premium_loc_tel_sic',
                    'name' => 'GT FLEET 365 PREMIUM (LOC + TEL + SEC) - Corporate, Heavy Vehicles',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante'],
                ],
                [
                    'id'   => 'plus_trattore',
                    'name' => 'GT FLEET 365 PLUS - Agricultural Tractor, Construction Vehicles',
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
                    'name' => 'GT FLEET 365 ASSET - Dummy Packs, Power Generators',
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
                ],
                [
                    'id'   => 'gestione_portellone',
                    'name' => 'TAILGATE MANAGEMENT SERVICE',
                    'input' => 'check',
                    'categories' => ['frigo', 'frigo_pesante', 'frigo_rimorchio'],
                ],
                [
                    'id'   => 'riconoscimento_driver',
                    'name' => 'DRIVER RECOGNITION SERVICE',
                    'input' => 'check',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante'],
                ],
                [
                    'id'   => 'riconoscimento_driver_app',
                    'name' => 'DRIVER RECOGNITION SERVICE WITH APP',
                    'input' => 'check',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante'],
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
            'title' => 'Crono & Tachograph',
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
            'title' => 'Hardware',
            'icon'  => '⚙️',
            'type'  => 'checkbox',
            'items' => [
                [
                    'id'   => 'hw_gost_shadow',
                    'name' => 'GOST - SHADOW DEVICE SUPPLY',
                    'input' => 'qty',
                    'categories' => ['aziendali', 'frigo', 'opera'],
                ],
                [
                    'id'   => 'hw_dispositivo_bordo',
                    'name' => 'ON-BOARD DEVICE SUPPLY (for Base service)',
                    'input' => 'qty',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante'],
                ],
                [
                    'id'   => 'hw_dispositivo_rimorchio',
                    'name' => 'TRAILER DEVICE SUPPLY',
                    'input' => 'qty',
                    'categories' => ['rimorchi', 'frigo_rimorchio'],
                ],
                [
                    'id'   => 'hw_dispositivo_trattore',
                    'name' => 'AGRICULTURAL TRACTOR, CONSTRUCTION VEHICLE DEVICE SUPPLY',
                    'input' => 'qty',
                    'categories' => ['opera'],
                ],
                [
                    'id'   => 'hw_dispositivo_asset',
                    'name' => 'ASSET DEVICE SUPPLY',
                    'input' => 'qty',
                    'categories' => ['asset'],
                ],
                [
                    'id'   => 'hw_sensore_portellone',
                    'name' => 'TAILGATE SENSOR (Van, Truck, Trailer)',
                    'input' => 'qty',
                    'categories' => ['frigo', 'frigo_pesante', 'frigo_rimorchio'],
                ],
                [
                    'id'   => 'hw_sensore_temperatura',
                    'name' => 'TEMPERATURE SENSOR (Truck or Trailer)',
                    'input' => 'qty',
                    'categories' => ['frigo', 'frigo_pesante', 'frigo_rimorchio'],
                ],
                [
                    'id'   => 'hw_beacon',
                    'name' => 'BEACON',
                    'input' => 'qty',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante', 'opera'],
                ],
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
                [
                    'id'   => 'hw_pedale_antirapina',
                    'name' => 'ANTI-ROBBERY / PANIC PEDAL',
                    'input' => 'qty',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante'],
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

];
