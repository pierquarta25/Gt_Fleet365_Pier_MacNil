<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Global Service Configuration - GT Fleet 365') }}</title>
    <meta name="description" content="{{ __('Configure GT Fleet 365 services for your entire quote.') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            background-color: #F4F6FA;
            color: #1A1F36;
            min-height: 100vh;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #0052BD 0%, #003A8C 100%);
            color: #ffffff;
            padding: 20px 24px;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 12px rgba(0, 82, 189, 0.3);
        }

        .header-title {
            font-family: 'Exo 2', Arial, sans-serif;
            font-size: 18px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .header-subtitle {
            font-size: 12px;
            opacity: 0.85;
            margin-top: 4px;
            font-weight: 500;
        }

        /* Container */
        .container {
            max-width: 680px;
            margin: 0 auto;
            padding: 20px 16px 120px;
        }

        /* Client Info */
        .client-info-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 24px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            border-left: 5px solid #0052BD;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .client-info-icon {
            font-size: 32px;
        }

        .client-info-details {
            flex: 1;
        }

        .client-info-company {
            font-family: 'Exo 2', Arial, sans-serif;
            font-size: 16px;
            font-weight: 800;
            color: #1A1F36;
            margin-bottom: 4px;
        }

        .client-info-contact {
            font-size: 13px;
            color: #4A5578;
        }

        /* Tabs Navigation */
        .tabs-nav {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 8px;
            margin-bottom: 16px;
            scrollbar-width: none; /* Firefox */
        }

        .tabs-nav::-webkit-scrollbar {
            display: none; /* Chrome/Safari */
        }

        .tab-btn {
            background: #ffffff;
            border: 2px solid #E3E8F4;
            border-radius: 50px;
            padding: 10px 20px;
            font-family: 'Exo 2', Arial, sans-serif;
            font-size: 14px;
            font-weight: 700;
            color: #4A5578;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tab-btn:hover {
            border-color: #C4DBFF;
            background: #F8FAFF;
        }

        .tab-btn.active {
            background: #0052BD;
            border-color: #0052BD;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 82, 189, 0.25);
        }

        .tab-btn-qty {
            background: #F4F6FA;
            color: #4A5578;
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 10px;
        }

        .tab-btn.active .tab-btn-qty {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }

        /* Tab Content */
        .tab-content {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Vehicle Header in Tab */
        .vehicle-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
            padding: 16px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        }

        .vehicle-img {
            width: 80px;
            height: 50px;
            object-fit: contain;
        }

        .vehicle-header-info {
            flex: 1;
        }

        .vehicle-name {
            font-family: 'Exo 2', Arial, sans-serif;
            font-size: 18px;
            font-weight: 800;
            color: #1A1F36;
        }

        .qty-field {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 8px;
        }

        .qty-field label {
            font-size: 12px;
            font-weight: 600;
            color: #4A5578;
            text-transform: uppercase;
        }

        .qty-input {
            width: 60px;
            padding: 6px 8px;
            border: 2px solid #DDE2EF;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Exo 2', Arial, sans-serif;
            color: #FF6B00;
            text-align: center;
        }

        .qty-input:focus {
            outline: none;
            border-color: #0052BD;
        }

        /* Section */
        .section {
            background: #ffffff;
            border-radius: 16px;
            margin-bottom: 16px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .section-header {
            padding: 16px 20px;
            background: linear-gradient(135deg, #F8FAFF 0%, #EDF2FF 100%);
            border-bottom: 1px solid #E3E8F4;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            user-select: none;
        }

        .section-icon {
            font-size: 20px;
        }

        .section-title {
            font-family: 'Exo 2', Arial, sans-serif;
            font-size: 14px;
            font-weight: 700;
            color: #0052BD;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            flex: 1;
        }

        .section-toggle {
            font-size: 12px;
            color: #8A93B0;
            transition: transform 0.3s;
        }

        .section-toggle.collapsed {
            transform: rotate(-90deg);
        }

        .section-body {
            padding: 8px 0;
            transition: max-height 0.3s ease;
        }

        .section-body.collapsed {
            max-height: 0;
            overflow: hidden;
            padding: 0;
        }

        /* Service Item */
        .service-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            gap: 12px;
            transition: background-color 0.15s;
            cursor: pointer;
        }

        .service-item:hover {
            background-color: #F8FAFF;
        }

        .service-item.selected {
            background-color: #EBF3FF;
        }

        .service-item input[type="checkbox"],
        .service-item input[type="radio"] {
            width: 20px;
            height: 20px;
            accent-color: #0052BD;
            cursor: pointer;
            flex-shrink: 0;
        }

        .service-name {
            font-size: 13px;
            font-weight: 500;
            color: #1A1F36;
            flex: 1;
            line-height: 1.4;
        }

        .service-qty {
            width: 60px;
            padding: 6px 8px;
            border: 2px solid #DDE2EF;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Exo 2', Arial, sans-serif;
            color: #FF6B00;
            text-align: center;
            flex-shrink: 0;
        }

        .service-qty:disabled {
            opacity: 0.3;
            background: #F4F6FA;
        }

        /* Hardware Sub-items */
        .hardware-sub-items {
            padding-left: 40px;
            background-color: #FAFCFF;
            border-left: 3px solid #C4DBFF;
            display: none; /* hidden by default, shown via JS */
        }

        .hardware-item {
            padding: 10px 20px 10px 12px;
            border-top: 1px dashed #E3E8F4;
            background-color: transparent !important;
        }

        .hardware-item:hover {
            background-color: #F0F6FF !important;
        }

        /* Notes */
        .notes-section {
            background: #ffffff;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }

        .notes-label {
            font-family: 'Exo 2', Arial, sans-serif;
            font-size: 14px;
            font-weight: 700;
            color: #0052BD;
            text-transform: uppercase;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .notes-textarea {
            width: 100%;
            min-height: 80px;
            padding: 14px;
            border: 2px solid #DDE2EF;
            border-radius: 10px;
            font-family: 'Inter', Arial, sans-serif;
            font-size: 14px;
            color: #1A1F36;
            resize: vertical;
        }

        /* Submit Button */
        .submit-container {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 16px;
            background: linear-gradient(to top, #ffffff 70%, transparent);
            z-index: 100;
        }

        .submit-btn {
            display: block;
            width: 100%;
            max-width: 680px;
            margin: 0 auto;
            padding: 16px 32px;
            background: linear-gradient(135deg, #FF6B00 0%, #E55D00 100%);
            color: #ffffff;
            border: none;
            border-radius: 50px;
            font-family: 'Exo 2', Arial, sans-serif;
            font-size: 16px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(255, 107, 0, 0.35);
        }

        /* Spinner & Toast omitted for brevity but they are standard */
        .spinner {
            display: none; width: 20px; height: 20px; border: 3px solid rgba(255,255,255,0.3); border-top: 3px solid #fff; border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        .toast {
            position: fixed; top: 20px; left: 50%; transform: translateX(-50%) translateY(-100px); padding: 14px 28px; border-radius: 12px; font-weight: 600; font-size: 14px; z-index: 1000; transition: transform 0.4s; box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        }
        .toast.show { transform: translateX(-50%) translateY(0); }
        .toast.success { background: #10B981; color: #fff; }
        .toast.error { background: #EF4444; color: #fff; }

        .completed-banner {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: #ffffff; padding: 16px 24px; border-radius: 12px; text-align: center; margin-bottom: 20px; font-weight: 600; font-size: 14px;
        }

        /* Language Switcher */
        .lang-switcher {
            margin-top: 12px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
        }

        .lang-link {
            text-decoration: none;
            color: rgba(255, 255, 255, 0.5);
            transition: color 0.2s ease;
        }

        .lang-link--active {
            color: #ffffff;
            font-weight: 800;
        }

        .lang-separator {
            color: rgba(255, 255, 255, 0.2);
            font-weight: 400;
        }

        .vehicle-emoji-fallback {
            font-size: 32px;
        }

        /* Group Cards */
        .group-card {
            border: 2px solid #E3E8F4;
            border-radius: 16px;
            margin-bottom: 16px;
            overflow: hidden;
        }

        .group-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            background: linear-gradient(135deg, #EDF2FF 0%, #E8F0FF 100%);
            border-bottom: 1px solid #E3E8F4;
        }

        .group-label {
            font-family: 'Exo 2', sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: #0052BD;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .group-controls {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .group-controls label {
            font-size: 12px;
            font-weight: 600;
            color: #4A5578;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .remove-group-btn {
            background: #fff0f0;
            border: 1.5px solid #ffcccc;
            color: #cc0000;
            border-radius: 8px;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .add-group-btn {
            width: 100%;
            padding: 12px;
            margin-bottom: 16px;
            border: 2px dashed #0052BD;
            border-radius: 12px;
            background: transparent;
            color: #0052BD;
            font-family: 'Exo 2', sans-serif;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        .add-group-btn:hover {
            background: #EDF2FF;
        }
    </style>
</head>

<body>
@php
    // Prezzi di listino pubblico — IDs allineati a config/vehicle_services.php
    $packagePrices = [
        // Pacchetti base
        "base_loc"              => 200,  // GT FLEET 365 BASE (LOC)
        "plus_loc_sic"          => 250,  // GT FLEET 365 PLUS (LOC + SEC)
        "gold_loc_tel"          => 300,  // GT FLEET 365 GOLD (LOC + TEL)
        "premium_loc_tel_sic"   => 350,  // GT FLEET 365 PREMIUM (LOC + TEL + SEC)
        "plus_trattore"         => 200,  // GT FLEET 365 PLUS (Trattori)
        "truck_crono_base"      => 250,  // GT FLEET 365 TRUCK CRONO BASE
        "truck_crono_plus"      => 300,  // GT FLEET 365 TRUCK CRONO PLUS
        "truck_crono_tel"       => 350,  // GT FLEET 365 TRUCK CRONO TEL
        "truck_crono_premium"   => 400,  // GT FLEET 365 TRUCK CRONO PREMIUM
        "rimorchi_comodato"     => 200,  // GT FLEET 365 TRAILERS (dispositivo in comodato)
        "rimorchi_vendita"      => 150,  // GT FLEET 365 TRAILERS (dispositivo in vendita)
        "asset"                 => 150,  // GT FLEET 365 ASSET
        // Servizi aggiuntivi
        "app_fleet_manager"     => 39,   // APP GT FLEET 365 - FLEET MANAGER
        "app_driver"            => 100,  // APP GT FLEET 365 - DRIVER
        "manutenzioni_scadenze" => 100,  // GT FLEET 365 SERVIZIO MANUTENZIONI - SCADENZE
        "formazione_assistenza" => 100,  // FORMAZIONE + ASSISTENZA
        "missioni"              => 200,  // GT FLEET 365 MISSIONI
        "mappe_track"           => 200,  // SERVIZIO MAPPE TRUCK - MEZZI PESANTI
        "scorta_digitale"       => 150,  // GT FLEET 365 SCORTA DIGITALE
        "app_gt5_app"           => 100,  // GT FLEET 365 APP - GT 5.0
    ];
@endphp

    <div class="header">
        <div class="header-title">{{ __('Service Configuration') }}</div>
        <div class="header-subtitle">MacNil — GT Fleet 365</div>
        <div class="lang-switcher">
            <a href="?lang=it" class="lang-link {{ app()->getLocale() === 'it' ? 'lang-link--active' : '' }}">IT</a>
            <span class="lang-separator">|</span>
            <a href="?lang=en" class="lang-link {{ app()->getLocale() === 'en' ? 'lang-link--active' : '' }}">EN</a>
        </div>
    </div>

    <div class="container">

        @if($alreadyCompleted)
            <div class="completed-banner">
                ✅ {{ __('This global configuration has already been completed.') }}
                {{ __('You can edit and submit it again.') }}
            </div>
        @endif

        {{-- Client Info (Only shown once globally) --}}
        @php
            $firstReq = collect($vehiclesData)->first()['model'] ?? null;
        @endphp
        @if($firstReq && $firstReq->client_data)
            <div class="client-info-card">
                <div class="client-info-icon">🏢</div>
                <div class="client-info-details">
                    <div class="client-info-company">{{ $firstReq->client_data['company'] ?? 'Cliente' }}</div>
                    <div class="client-info-contact">
                        {{ $firstReq->client_data['contact'] ?? '' }} {{ $firstReq->client_data['lastname'] ?? '' }}
                        @if(!empty($firstReq->client_data['email'])) | {{ $firstReq->client_data['email'] }} @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Tabs Navigation --}}
        <div class="tabs-nav">
            @foreach($vehiclesData as $index => $data)
                <button class="tab-btn {{ $index === 0 ? 'active' : '' }}" onclick="switchTab(this, 'tab-{{ $data['model']->id }}')">
                    {{ strtoupper($data['model']->vehicle_name) }}
                    <span class="tab-btn-qty">{{ $data['model']->vehicle_qty }}</span>
                </button>
            @endforeach
        </div>

        {{-- Form --}}
        <form id="serviceForm">
            @csrf

            @foreach($vehiclesData as $index => $data)
                @php
                    $req = $data['model'];
                    $sections = $data['sections'];

                    // Prepare saved groups — support both new (group_idx) and legacy flat formats
                    $savedGroups = [];
                    if ($alreadyCompleted && $req->services) {
                        $firstService = collect($req->services)->first();
                        if ($firstService && isset($firstService['group_idx'])) {
                            // New format: array of groups
                            $savedGroups = $req->services;
                        } else {
                            // Legacy flat format: wrap in a single group 0
                            $savedGroups = [['group_idx' => 0, 'qty' => $req->vehicle_qty, 'services' => $req->services ?? []]];
                        }
                    }
                    if (empty($savedGroups)) {
                        $savedGroups = [['group_idx' => 0, 'qty' => $req->vehicle_qty, 'services' => []]];
                    }
                    $groupCount = count($savedGroups);
                @endphp
                <div id="tab-{{ $req->id }}" class="tab-content {{ $index === 0 ? 'active' : '' }}">

                    {{-- Vehicle Header (no qty here — qty is per group now) --}}
                    <div class="vehicle-header">
                        @if($req->vehicle_img)
                            <img src="{{ asset($req->vehicle_img) }}" alt="{{ $req->vehicle_name }}" class="vehicle-img">
                        @else
                            <span class="vehicle-emoji-fallback">🚛</span>
                        @endif
                        <div class="vehicle-header-info">
                            <div class="vehicle-name">{{ $req->vehicle_name }}</div>
                        </div>
                    </div>

                    {{-- Groups Container --}}
                    <div class="groups-container" id="groups-{{ $req->id }}" data-req="{{ $req->id }}" data-count="{{ $groupCount }}">

                        @foreach($savedGroups as $gIdx => $savedGroup)
                            @php
                                $gQty = $savedGroup['qty'] ?? $req->vehicle_qty ?? 1;
                                $gServices = $savedGroup['services'] ?? [];
                                $gServiceIds = collect($gServices)->pluck('id')->toArray();
                            @endphp
                            <div class="group-card" data-group="{{ $gIdx }}">

                                {{-- Group Header --}}
                                <div class="group-card-header">
                                    <span class="group-label">{{ __('Configurazione') }} {{ $gIdx + 1 }}</span>
                                    <div class="group-controls">
                                        <label>{{ __('Quantità:') }}
                                            <input type="number"
                                                   name="vehicles[{{ $req->id }}][groups][{{ $gIdx }}][qty]"
                                                   class="qty-input"
                                                   value="{{ $gQty }}"
                                                   min="1">
                                        </label>
                                        <button type="button"
                                                class="remove-group-btn"
                                                onclick="removeGroup(this)"
                                                style="{{ ($gIdx === 0 && $groupCount === 1) ? 'display:none' : 'display:inline-flex' }}">✕</button>
                                    </div>
                                </div>

                                {{-- Sections inside this group --}}
                                @foreach($sections as $sIndex => $section)
                                    <div class="section">
                                        <div class="section-header" onclick="toggleSection('body-{{ $req->id }}-grp{{ $gIdx }}-{{ $sIndex }}', 'toggle-{{ $req->id }}-grp{{ $gIdx }}-{{ $sIndex }}')">
                                            <span class="section-icon">{{ $section['icon'] }}</span>
                                            <span class="section-title">{{ __($section['title']) }}</span>
                                            <span class="section-toggle" id="toggle-{{ $req->id }}-grp{{ $gIdx }}-{{ $sIndex }}">▼</span>
                                        </div>
                                        <div class="section-body" id="body-{{ $req->id }}-grp{{ $gIdx }}-{{ $sIndex }}">
                                            @foreach($section['items'] as $item)
                                                <div class="service-item-wrapper">
                                                    <label class="service-item">
                                                        @if($section['type'] === 'radio')
                                                            <input type="radio" data-price="{{ $packagePrices[$item['id']] ?? 200 }}"
                                                                   name="vehicles[{{ $req->id }}][groups][{{ $gIdx }}][base_package]"
                                                                   value="{{ $item['id'] }}"
                                                                   onchange="updateSelection(this)"
                                                                   {{ $alreadyCompleted && in_array($item['id'], $gServiceIds) ? 'checked' : '' }}>
                                                        @else
                                                            <input type="checkbox"
                                                                   name="vehicles[{{ $req->id }}][groups][{{ $gIdx }}][services][]"
                                                                   value="{{ $item['id'] }}"
                                                                   onchange="updateSelection(this)"
                                                                   {{ $alreadyCompleted && in_array($item['id'], $gServiceIds) ? 'checked' : '' }}>
                                                        @endif

                                                        <span class="service-name">{{ __($item['name']) }}</span>

                                                        @if(isset($item['input']) && $item['input'] === 'qty')
                                                            @php
                                                                $savedQty = 0;
                                                                if ($alreadyCompleted && count($gServices) > 0) {
                                                                    $savedSvc = collect($gServices)->firstWhere('id', $item['id']);
                                                                    $savedQty = $savedSvc['qty'] ?? 0;
                                                                }
                                                            @endphp
                                                            <input type="number"
                                                                   name="vehicles[{{ $req->id }}][groups][{{ $gIdx }}][quantities][{{ $item['id'] }}]"
                                                                   class="service-qty"
                                                                   value="{{ $savedQty ?: $gQty }}"
                                                                   min="0"
                                                                   disabled>
                                                        @endif
                                                    </label>

                                                    @if($section['type'] === 'radio')
                                                        @php
                                                            $savedPkg = collect($gServices)->firstWhere('type', 'base_package');
                                                            $savedPkgPrice = $savedPkg['price'] ?? 0;
                                                            $savedPkgDiscount = $savedPkg['discount'] ?? 0;
                                                            $savedPkgFinal = $savedPkg['final_price'] ?? 0;
                                                        @endphp
                                                        <div class="package-discount-box" style="display: none; background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 20px 10px 45px; border: 1px solid #e3e8f4;">
                                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                                                <div>
                                                                    <small style="color: #666;">Prezzo Listino</small>
                                                                    <div style="font-size: 1.1rem; font-weight: bold;">€ <span class="base-price-display">0</span></div>
                                                                </div>
                                                                <div style="background: #e3f2fd; color: #0052BD; padding: 5px 15px; border-radius: 50px; font-weight: bold; border: 1px solid #bbdefb; font-size: 0.9rem;">
                                                                    Scontistica: <span class="discount-display">0</span>%
                                                                </div>
                                                            </div>
                                                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                                                <div style="display: flex; justify-content: space-between;">
                                                                    <label style="font-size: 0.9rem; font-weight: 600;">Sconto (%)</label>
                                                                    <label style="font-size: 0.8rem; color: #666; cursor: pointer;">
                                                                        <input type="checkbox" class="unlock-discount-chk" onchange="updateDiscountLimits(this)"> 🔓 Sblocca limite 50%
                                                                    </label>
                                                                </div>
                                                                <div style="display: flex; align-items: center; gap: 10px;">
                                                                    <input type="range" class="discount-slider" min="0" max="50" step="1" value="0" style="flex: 1;" oninput="updateDiscountFromSlider(this)">
                                                                    <input type="number" class="discount-input" min="0" max="50" value="0" style="width: 60px; padding: 5px; text-align: center; border-radius: 5px; border: 1px solid #ccc;" oninput="updateDiscountFromInput(this)">
                                                                </div>
                                                            </div>
                                                            <div style="border-top: 1px solid #ddd; padding-top: 10px; margin-top: 15px; display: flex; justify-content: space-between; align-items: center;">
                                                                <strong style="font-size: 1rem;">Prezzo Finale:</strong>
                                                                <strong style="font-size: 1.2rem; color: #0052BD;">€ <span class="final-price-display">0</span></strong>
                                                            </div>
                                                            <!-- Hidden inputs for form submission -->
                                                            <input type="hidden" name="vehicles[{{ $req->id }}][groups][{{ $gIdx }}][package_price]" class="hidden-price-input" value="{{ $savedPkgPrice }}">
                                                            <input type="hidden" name="vehicles[{{ $req->id }}][groups][{{ $gIdx }}][package_discount]" class="hidden-discount-input" value="{{ $savedPkgDiscount }}">
                                                            <input type="hidden" name="vehicles[{{ $req->id }}][groups][{{ $gIdx }}][package_final_price]" class="hidden-final-price-input" value="{{ $savedPkgFinal }}">
                                                        </div>
                                                    @endif

                                                    {{-- Hardware Sub-items --}}
                                                    @if(isset($item['hardware']) && count($item['hardware']) > 0)
                                                        <div class="hardware-sub-items" id="hw-{{ $req->id }}-grp{{ $gIdx }}-{{ $item['id'] }}">
                                                            @foreach($item['hardware'] as $hw)
                                                                <label class="service-item hardware-item">
                                                                    <input type="checkbox"
                                                                           name="vehicles[{{ $req->id }}][groups][{{ $gIdx }}][services][]"
                                                                           value="{{ $hw['id'] }}"
                                                                           onchange="updateSelection(this)"
                                                                           {{ $alreadyCompleted && in_array($hw['id'], $gServiceIds) ? 'checked' : '' }}>
                                                                    <span class="service-name">{{ __($hw['name']) }}</span>
                                                                    @if(isset($hw['input']) && $hw['input'] === 'qty')
                                                                        @php
                                                                            $savedHwQty = 0;
                                                                            if ($alreadyCompleted && count($gServices) > 0) {
                                                                                $savedHwSvc = collect($gServices)->firstWhere('id', $hw['id']);
                                                                                $savedHwQty = $savedHwSvc['qty'] ?? 0;
                                                                            }
                                                                        @endphp
                                                                        <input type="number"
                                                                               name="vehicles[{{ $req->id }}][groups][{{ $gIdx }}][quantities][{{ $hw['id'] }}]"
                                                                               class="service-qty"
                                                                               value="{{ $savedHwQty ?: $gQty }}"
                                                                               min="0"
                                                                               disabled>
                                                                    @endif
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach

                            </div>{{-- end .group-card --}}
                        @endforeach

                    </div>{{-- end .groups-container --}}

                    {{-- Add Group Button --}}
                    <button type="button" class="add-group-btn" onclick="addGroup('{{ $req->id }}')">+ {{ __('Aggiungi Configurazione') }}</button>

                    {{-- Notes (one per vehicle type, outside groups) --}}
                    <div class="notes-section">
                        <div class="notes-label"><span>📝</span> {{ __('Notes for') }} {{ $req->vehicle_name }}</div>
                        <textarea class="notes-textarea"
                                  name="vehicles[{{ $req->id }}][notes]"
                                  placeholder="{{ __('Enter any notes or special requests for this vehicle...') }}">{{ $req->notes }}</textarea>
                    </div>

                </div>
            @endforeach
        </form>
    </div>

    {{-- Submit --}}
    <div class="submit-container">
        <button class="submit-btn" id="submitBtn" onclick="submitForm()">
            <span id="btnText">{{ __('Save All Vehicles') }}</span>
            <div class="spinner" id="btnSpinner"></div>
        </button>
    </div>

    {{-- Toast --}}
    <div class="toast" id="toast"></div>

    <script>
        // Switch Tabs
        function switchTab(btn, tabId) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

            btn.classList.add('active');
            document.getElementById(tabId).classList.add('active');

            // Scroll tab nav to show button if needed
            btn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }

        // Toggle sezioni
        function toggleSection(bodyId, toggleId) {
            const body = document.getElementById(bodyId);
            const toggle = document.getElementById(toggleId);
            body.classList.toggle('collapsed');
            toggle.classList.toggle('collapsed');
        }

        // Aggiorna prezzi e scontistica
        function initPricing(radioInput) {
            const wrapper = radioInput.closest('.service-item-wrapper');
            const box = wrapper.querySelector('.package-discount-box');
            if(!box) return;

            const section = radioInput.closest('.section-body');
            section.querySelectorAll('.package-discount-box').forEach(b => b.style.display = 'none');

            if(radioInput.checked) {
                box.style.display = 'block';
                const basePrice = parseFloat(radioInput.getAttribute('data-price') || 0);
                box.querySelector('.base-price-display').textContent = basePrice.toFixed(2);
                box.querySelector('.hidden-price-input').value = basePrice;
                recalcFinalPrice(box, basePrice);
            }
        }

        function updateDiscountLimits(chk) {
            const box = chk.closest('.package-discount-box');
            const max = chk.checked ? 100 : 50;
            const slider = box.querySelector('.discount-slider');
            const input = box.querySelector('.discount-input');
            slider.max = max;
            input.max = max;
            if(parseInt(slider.value) > max) {
                slider.value = max;
                input.value = max;
                recalcFinalPrice(box);
            }
        }

        function updateDiscountFromSlider(el) {
            const box = el.closest('.package-discount-box');
            box.querySelector('.discount-input').value = el.value;
            recalcFinalPrice(box);
        }

        function updateDiscountFromInput(el) {
            const box = el.closest('.package-discount-box');
            let val = parseInt(el.value) || 0;
            const max = parseInt(el.max);
            if(val > max) { val = max; el.value = max; }
            if(val < 0) { val = 0; el.value = 0; }
            box.querySelector('.discount-slider').value = val;
            recalcFinalPrice(box);
        }

        function recalcFinalPrice(box, forceBasePrice = null) {
            let basePrice = forceBasePrice;
            if (basePrice === null) {
                basePrice = parseFloat(box.querySelector('.hidden-price-input').value);
            }
            const discount = parseInt(box.querySelector('.discount-slider').value) || 0;
            const finalPrice = basePrice * (1 - discount/100);

            box.querySelector('.discount-display').textContent = discount;
            box.querySelector('.final-price-display').textContent = finalPrice.toFixed(2);

            box.querySelector('.hidden-discount-input').value = discount;
            box.querySelector('.hidden-final-price-input').value = finalPrice.toFixed(2);
        }

        function updateSelection(input) {
            const item = input.closest('.service-item');

            if (input.type === 'radio') {
                const section = input.closest('.section-body');
                section.querySelectorAll('.service-item').forEach(el => el.classList.remove('selected'));
            }

            if (input.checked) {
                if(input.type === 'radio') initPricing(input);
                item.classList.add('selected');
                const qtyInput = item.querySelector('.service-qty');
                if (qtyInput) qtyInput.disabled = false;
            } else {
                item.classList.remove('selected');
                const qtyInput = item.querySelector('.service-qty');
                if (qtyInput) qtyInput.disabled = true;
            }

            // Handle hardware sub-items visibility
            const wrapper = input.closest('.service-item-wrapper');
            if (wrapper && !item.classList.contains('hardware-item')) {
                const subItems = wrapper.querySelector('.hardware-sub-items');
                if (subItems) {
                    if (input.checked) {
                        if(input.type === 'radio') initPricing(input);
                        subItems.style.display = 'block';
                    } else {
                        subItems.style.display = 'none';
                        // Deselect nested hardware when parent is deselected
                        subItems.querySelectorAll('input[type="checkbox"]').forEach(hwInput => {
                            if (hwInput.checked) {
                                hwInput.checked = false;
                                updateSelection(hwInput);
                            }
                        });
                    }
                }
            }
        }

        // Init selezioni
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input[type="checkbox"]:checked, input[type="radio"]:checked').forEach(updateSelection);
            document.querySelectorAll('input[type="radio"]:checked').forEach(initPricing);
        });

        // ─── Group management ────────────────────────────────────────────────

        function addGroup(reqId) {
            const container = document.getElementById('groups-' + reqId);
            const nextIdx = parseInt(container.dataset.count);

            // Clone the first group card as template
            const firstCard = container.querySelector('.group-card');
            const clone = firstCard.cloneNode(true);

            // Replace all occurrences of group index 0 with the new index in
            // name/id/onclick attributes — do a full innerHTML replace
            clone.innerHTML = clone.innerHTML
                .replaceAll('[groups][0]', '[groups][' + nextIdx + ']')
                .replaceAll('-grp0-', '-grp' + nextIdx + '-')
                .replaceAll('-grp0"', '-grp' + nextIdx + '"')
                .replaceAll("'body-" + reqId + "-grp0-", "'body-" + reqId + "-grp" + nextIdx + "-")
                .replaceAll("'toggle-" + reqId + "-grp0-", "'toggle-" + reqId + "-grp" + nextIdx + "-");

            // Reset all inputs
            clone.querySelectorAll('input[type="radio"], input[type="checkbox"]').forEach(i => { i.checked = false; });
            clone.querySelectorAll('.service-item').forEach(el => el.classList.remove('selected'));
            clone.querySelectorAll('.package-discount-box').forEach(b => b.style.display = 'none');
            clone.querySelectorAll('.hardware-sub-items').forEach(el => el.style.display = 'none');
            clone.querySelectorAll('.service-qty').forEach(i => { i.disabled = true; });
            clone.querySelectorAll('.hidden-price-input, .hidden-discount-input, .hidden-final-price-input').forEach(i => i.value = 0);
            clone.querySelectorAll('.discount-slider, .discount-input').forEach(i => i.value = 0);
            clone.querySelectorAll('.base-price-display, .final-price-display, .discount-display').forEach(el => el.textContent = '0');

            // Update group label and data attribute
            clone.dataset.group = nextIdx;
            clone.querySelector('.group-label').textContent = 'Configurazione ' + (nextIdx + 1);

            // Always show remove button on new cards
            clone.querySelector('.remove-group-btn').style.display = 'inline-flex';

            container.appendChild(clone);
            container.dataset.count = nextIdx + 1;

            // Ensure all cards show the remove button (now there are at least 2)
            container.querySelectorAll('.remove-group-btn').forEach(btn => btn.style.display = 'inline-flex');

            // Smooth scroll to the new card
            clone.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function removeGroup(btn) {
            const card = btn.closest('.group-card');
            const container = card.closest('.groups-container');
            container.removeChild(card);

            const remaining = container.querySelectorAll('.group-card');
            if (remaining.length === 1) {
                remaining[0].querySelector('.remove-group-btn').style.display = 'none';
            }
        }

        // ─────────────────────────────────────────────────────────────────────

        // Toast
        function showToast(message, type) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = 'toast ' + type + ' show';
            setTimeout(() => toast.classList.remove('show'), 4000);
        }

        // Submit
        async function submitForm() {
            const btn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const spinner = document.getElementById('btnSpinner');

            btn.disabled = true;
            btnText.style.display = 'none';
            spinner.style.display = 'block';

            const form = document.getElementById('serviceForm');
            const formData = new FormData(form);

            try {
                const response = await fetch('/api/servizi/{{ $groupToken }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await response.json();

                if (response.ok) {
                    showToast('✅ ' + data.message, 'success');
                    setTimeout(() => {
                        window.location.href = '/servizi/{{ $groupToken }}/successo';
                    }, 1500);
                } else {
                    showToast('❌ ' + (data.message || '{{ __('Error during submission.') }}'), 'error');
                }
            } catch (error) {
                showToast('❌ {{ __("Connection error. Please try again.") }}', 'error');
            } finally {
                btn.disabled = false;
                btnText.style.display = 'inline';
                spinner.style.display = 'none';
            }
        }
    </script>
</body>

</html>
