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
    </style>
</head>

<body>
    <div class="header">
        <div class="header-title">{{ __('Service Configuration') }}</div>
        <div class="header-subtitle">MacNil — GT Fleet 365</div>
        <div style="margin-top: 8px;">
            <a href="?lang=it" style="color: #fff; text-decoration: none; font-size: 14px; {{ app()->getLocale() === 'it' ? 'font-weight: 800; border-bottom: 2px solid #fff;' : 'opacity: 0.7;' }} padding: 2px 6px;">🇮🇹 IT</a>
            <a href="?lang=en" style="color: #fff; text-decoration: none; font-size: 14px; {{ app()->getLocale() === 'en' ? 'font-weight: 800; border-bottom: 2px solid #fff;' : 'opacity: 0.7;' }} padding: 2px 6px;">🇬🇧 EN</a>
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
                @endphp
                <div id="tab-{{ $req->id }}" class="tab-content {{ $index === 0 ? 'active' : '' }}">
                    
                    {{-- Vehicle Header --}}
                    <div class="vehicle-header">
                        @if($req->vehicle_img)
                            <img src="{{ asset($req->vehicle_img) }}" alt="{{ $req->vehicle_name }}" class="vehicle-img">
                        @else
                            <span style="font-size: 32px;">🚛</span>
                        @endif
                        <div class="vehicle-header-info">
                            <div class="vehicle-name">{{ $req->vehicle_name }}</div>
                            <div class="qty-field">
                                <label>{{ __('Quantity:') }}</label>
                                <input type="number" 
                                       name="vehicles[{{ $req->id }}][vehicle_qty]" 
                                       class="qty-input" 
                                       value="{{ $req->vehicle_qty }}" 
                                       min="1">
                            </div>
                        </div>
                    </div>

                    {{-- Sections --}}
                    @foreach($sections as $sIndex => $section)
                        <div class="section">
                            <div class="section-header" onclick="toggleSection('body-{{ $req->id }}-{{ $sIndex }}', 'toggle-{{ $req->id }}-{{ $sIndex }}')">
                                <span class="section-icon">{{ $section['icon'] }}</span>
                                <span class="section-title">{{ __($section['title']) }}</span>
                                <span class="section-toggle" id="toggle-{{ $req->id }}-{{ $sIndex }}">▼</span>
                            </div>
                            <div class="section-body" id="body-{{ $req->id }}-{{ $sIndex }}">
                                @foreach($section['items'] as $item)
                                    <label class="service-item">
                                        @if($section['type'] === 'radio')
                                            <input type="radio"
                                                   name="vehicles[{{ $req->id }}][base_package]"
                                                   value="{{ $item['id'] }}"
                                                   onchange="updateSelection(this)"
                                                   {{ $alreadyCompleted && collect($req->services)->where('id', $item['id'])->isNotEmpty() ? 'checked' : '' }}>
                                        @else
                                            <input type="checkbox"
                                                   name="vehicles[{{ $req->id }}][services][]"
                                                   value="{{ $item['id'] }}"
                                                   onchange="updateSelection(this)"
                                                   {{ $alreadyCompleted && collect($req->services)->where('id', $item['id'])->isNotEmpty() ? 'checked' : '' }}>
                                        @endif

                                        <span class="service-name">{{ __($item['name']) }}</span>

                                        @if(isset($item['input']) && $item['input'] === 'qty')
                                            @php
                                                $savedQty = 0;
                                                if ($alreadyCompleted && $req->services) {
                                                    $saved = collect($req->services)->firstWhere('id', $item['id']);
                                                    $savedQty = $saved['qty'] ?? 0;
                                                }
                                            @endphp
                                            <input type="number"
                                                   name="vehicles[{{ $req->id }}][quantities][{{ $item['id'] }}]"
                                                   class="service-qty"
                                                   value="{{ $savedQty ?: $req->vehicle_qty }}"
                                                   min="0"
                                                   disabled>
                                        @endif
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    {{-- Notes --}}
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

        // Aggiorna stile selezione e abilita/disabilita campi qty
        function updateSelection(input) {
            const item = input.closest('.service-item');

            if (input.type === 'radio') {
                const section = input.closest('.section-body');
                section.querySelectorAll('.service-item').forEach(el => el.classList.remove('selected'));
            }

            if (input.checked) {
                item.classList.add('selected');
                const qtyInput = item.querySelector('.service-qty');
                if (qtyInput) qtyInput.disabled = false;
            } else {
                item.classList.remove('selected');
                const qtyInput = item.querySelector('.service-qty');
                if (qtyInput) qtyInput.disabled = true;
            }
        }

        // Init selezioni
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input[type="checkbox"]:checked, input[type="radio"]:checked').forEach(updateSelection);
        });

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
