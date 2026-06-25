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
            'title' => 'Pacchetto Base',
            'icon'  => '📡',
            'type'  => 'radio', // Solo uno selezionabile
            'items' => [
                [
                    'id'   => 'base_loc',
                    'name' => 'GT FLEET 365 BASE (LOC) - Mezzi Aziendali, Pesanti, D\'Opera',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante', 'opera'],
                ],
                [
                    'id'   => 'plus_loc_sic',
                    'name' => 'GT FLEET 365 PLUS (LOC + SIC) - Mezzi Aziendali, Pesanti',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante'],
                ],
                [
                    'id'   => 'gold_loc_tel',
                    'name' => 'GT FLEET 365 GOLD (LOC + TEL) - Mezzi Aziendali, Pesanti',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante'],
                ],
                [
                    'id'   => 'premium_loc_tel_sic',
                    'name' => 'GT FLEET 365 PREMIUM (LOC + TEL + SIC) - Mezzi Aziendali, Pesanti',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante'],
                ],
                [
                    'id'   => 'plus_trattore',
                    'name' => 'GT FLEET 365 PLUS - Trattore Agricolo, Mezzi D\'Opera',
                    'categories' => ['opera'],
                ],
                [
                    'id'   => 'truck_crono_base',
                    'name' => 'GT FLEET 365 TRUCK CRONO BASE (LOC + CRONO)',
                    'categories' => ['pesanti', 'frigo_pesante'],
                ],
                [
                    'id'   => 'truck_crono_plus',
                    'name' => 'GT FLEET 365 TRUCK CRONO PLUS (LOC + SIC + CRONO)',
                    'categories' => ['pesanti', 'frigo_pesante'],
                ],
                [
                    'id'   => 'truck_crono_tel',
                    'name' => 'GT FLEET 365 TRUCK CRONO TEL (LOC + TEL + CRONO)',
                    'categories' => ['pesanti', 'frigo_pesante'],
                ],
                [
                    'id'   => 'truck_crono_premium',
                    'name' => 'GT FLEET 365 TRUCK CRONO PREMIUM (LOC + TEL + SIC + CRONO)',
                    'categories' => ['pesanti', 'frigo_pesante'],
                ],
                [
                    'id'   => 'rimorchi_comodato',
                    'name' => 'GT FLEET 365 RIMORCHI (dispositivo in comodato d\'uso)',
                    'categories' => ['rimorchi', 'frigo_rimorchio'],
                ],
                [
                    'id'   => 'rimorchi_vendita',
                    'name' => 'GT FLEET 365 RIMORCHI (dispositivo in vendita)',
                    'categories' => ['rimorchi', 'frigo_rimorchio'],
                ],
                [
                    'id'   => 'asset',
                    'name' => 'GT FLEET 365 ASSET - Pacco Civetta, Gruppi Elettrogeni',
                    'categories' => ['asset'],
                ],
            ],
        ],

        [
            'title' => 'App & Servizi Aggiuntivi',
            'icon'  => '📱',
            'type'  => 'checkbox',
            'items' => [
                [
                    'id'   => 'app_fleet_manager',
                    'name' => 'APP GT FLEET 365 - FLEET MANAGER',
                    'input' => 'check',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante', 'frigo_rimorchio', 'rimorchi', 'opera', 'asset'],
                ],
                [
                    'id'   => 'app_driver',
                    'name' => 'APP GT FLEET 365 - DRIVER',
                    'input' => 'check',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante', 'opera'],
                ],
                [
                    'id'   => 'manutenzioni_scadenze',
                    'name' => 'GT FLEET 365 SERVIZIO MANUTENZIONI - SCADENZE',
                    'input' => 'check',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante', 'opera'],
                ],
                [
                    'id'   => 'formazione_assistenza',
                    'name' => 'FORMAZIONE + ASSISTENZA - GT FLEET 365 WEB E APP',
                    'input' => 'check',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante', 'frigo_rimorchio', 'rimorchi', 'opera'],
                ],
                [
                    'id'   => 'missioni',
                    'name' => 'GT FLEET 365 MISSIONI: PIANI DI VIAGGIO, ATTIVITÀ, VISITE',
                    'input' => 'check',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante'],
                ],
                [
                    'id'   => 'mappe_track',
                    'name' => 'SERVIZIO MAPPE TRACK - Mezzi Pesanti',
                    'input' => 'check',
                    'categories' => ['pesanti', 'frigo_pesante'],
                ],
                [
                    'id'   => 'temperatura_controllata',
                    'name' => 'SERVIZIO TEMPERATURA CONTROLLATA',
                    'input' => 'check',
                    'categories' => ['frigo', 'frigo_pesante', 'frigo_rimorchio'],
                ],
                [
                    'id'   => 'gestione_portellone',
                    'name' => 'SERVIZIO GESTIONE PORTELLONE',
                    'input' => 'check',
                    'categories' => ['frigo', 'frigo_pesante', 'frigo_rimorchio'],
                ],
                [
                    'id'   => 'riconoscimento_driver',
                    'name' => 'SERVIZIO RICONOSCIMENTO DRIVER',
                    'input' => 'check',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante'],
                ],
                [
                    'id'   => 'riconoscimento_driver_app',
                    'name' => 'SERVIZIO RICONOSCIMENTO DRIVER CON APP',
                    'input' => 'check',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante'],
                ],
                [
                    'id'   => 'tempi_guida_realtime',
                    'name' => 'TEMPI DI GUIDA REAL TIME - RICONOSCIMENTO DRIVER con CARTA TACHIGRAFICA',
                    'input' => 'check',
                    'categories' => ['pesanti', 'frigo_pesante'],
                ],
                [
                    'id'   => 'centrale_operativa_live',
                    'name' => 'CENTRALE OPERATIVA LIVE (H24)',
                    'input' => 'check',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante'],
                ],
                [
                    'id'   => 'centrale_operativa_ondemand',
                    'name' => 'CENTRALE OPERATIVA ON DEMAND',
                    'input' => 'check',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante'],
                ],
                [
                    'id'   => 'scorta_digitale',
                    'name' => 'GT FLEET 365 SCORTA DIGITALE',
                    'input' => 'check',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante', 'frigo_rimorchio', 'rimorchi', 'opera', 'asset'],
                ],
                [
                    'id'   => 'app_gt5_app',
                    'name' => 'APP GT FLEET 365 - GT 5.0.APP',
                    'input' => 'check',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante', 'frigo_rimorchio', 'rimorchi', 'opera', 'asset'],
                ],
            ],
        ],

        [
            'title' => 'Crono & Tachigrafo',
            'icon'  => '⏱️',
            'type'  => 'checkbox',
            'items' => [
                [
                    'id'   => 'carta_aziendale_crono',
                    'name' => 'CARTA AZIENDALE CRONO (ogni 25 Mezzi)',
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
                    'name' => 'FORNITURA DISPOSITIVO GOST - SHADOW',
                    'input' => 'qty',
                    'categories' => ['aziendali', 'frigo', 'opera'],
                ],
                [
                    'id'   => 'hw_dispositivo_bordo',
                    'name' => 'FORNITURA DISPOSITIVO DI BORDO (per servizio Base)',
                    'input' => 'qty',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante'],
                ],
                [
                    'id'   => 'hw_dispositivo_rimorchio',
                    'name' => 'FORNITURA DISPOSITIVO RIMORCHIO',
                    'input' => 'qty',
                    'categories' => ['rimorchi', 'frigo_rimorchio'],
                ],
                [
                    'id'   => 'hw_dispositivo_trattore',
                    'name' => 'FORNITURA DISPOSITIVO TRATTORE AGRICOLO, MEZZI D\'OPERA',
                    'input' => 'qty',
                    'categories' => ['opera'],
                ],
                [
                    'id'   => 'hw_dispositivo_asset',
                    'name' => 'FORNITURA DISPOSITIVO ASSET',
                    'input' => 'qty',
                    'categories' => ['asset'],
                ],
                [
                    'id'   => 'hw_sensore_portellone',
                    'name' => 'SENSORE PORTELLONE (Furgone, Motrice, Rimorchio)',
                    'input' => 'qty',
                    'categories' => ['frigo', 'frigo_pesante', 'frigo_rimorchio'],
                ],
                [
                    'id'   => 'hw_sensore_temperatura',
                    'name' => 'SENSORE TEMPERATURA (Motrice o Rimorchio)',
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
                    'name' => 'CHIAVE DALLAS',
                    'input' => 'qty',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante', 'opera'],
                ],
                [
                    'id'   => 'hw_kit_lettore_dallas',
                    'name' => 'KIT LETTORE + CHIAVE DALLAS',
                    'input' => 'qty',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante', 'opera'],
                ],
                [
                    'id'   => 'hw_tastierino',
                    'name' => 'TASTIERINO',
                    'input' => 'qty',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante'],
                ],
                [
                    'id'   => 'hw_pedale_antirapina',
                    'name' => 'PEDALE ANTIRAPINA / ANTIPANICO',
                    'input' => 'qty',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante'],
                ],
                [
                    'id'   => 'hw_beacon_passeggero',
                    'name' => 'BEACON (Riconoscimento Passeggero)',
                    'input' => 'qty',
                    'categories' => ['pesanti'],
                ],
            ],
        ],

        [
            'title' => 'Sviluppo & Personalizzazioni',
            'icon'  => '🔧',
            'type'  => 'checkbox',
            'items' => [
                [
                    'id'   => 'sviluppo_digital_transformation',
                    'name' => 'ATTIVITÀ DI SVILUPPO - DIGITAL TRANSFORMATION',
                    'input' => 'check',
                    'categories' => ['aziendali', 'pesanti', 'frigo', 'frigo_pesante', 'frigo_rimorchio', 'rimorchi', 'opera', 'asset'],
                ],
            ],
        ],

    ],

];
