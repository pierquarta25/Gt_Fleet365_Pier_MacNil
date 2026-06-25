<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurazione Servizi - GT Fleet 365</title>
    <meta name="description" content="Configura i servizi GT Fleet 365 per il tuo mezzo aziendale.">
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
            padding: 20px 16px 100px;
        }

        /* Vehicle Info Card */
        .vehicle-info {
            background: #ffffff;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            border-left: 5px solid #FF6B00;
        }

        .vehicle-info-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
        }

        .vehicle-img {
            width: 90px;
            height: 60px;
            object-fit: contain;
        }

        .vehicle-name {
            font-family: 'Exo 2', Arial, sans-serif;
            font-size: 18px;
            font-weight: 800;
            color: #1A1F36;
        }

        .vehicle-qty-label {
            font-size: 12px;
            color: #8A93B0;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 4px;
        }

        .client-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            padding-top: 16px;
            border-top: 1px solid #EDF0F7;
        }

        .client-info-item {
            display: flex;
            flex-direction: column;
        }

        .client-info-label {
            font-size: 10px;
            color: #8A93B0;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .client-info-value {
            font-size: 13px;
            font-weight: 600;
            color: #1A1F36;
        }

        /* Quantity Field */
        .qty-field {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #EDF0F7;
        }

        .qty-field label {
            font-size: 13px;
            font-weight: 600;
            color: #4A5578;
        }

        .qty-input {
            width: 70px;
            padding: 8px 12px;
            border: 2px solid #DDE2EF;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            font-family: 'Exo 2', Arial, sans-serif;
            color: #FF6B00;
            text-align: center;
            transition: border-color 0.2s;
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

        .section-header:hover {
            background: linear-gradient(135deg, #EDF2FF 0%, #E3EBFF 100%);
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
            transition: border-color 0.2s, opacity 0.2s;
        }

        .service-qty:focus {
            outline: none;
            border-color: #0052BD;
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
            letter-spacing: 0.5px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .notes-textarea {
            width: 100%;
            min-height: 100px;
            padding: 14px;
            border: 2px solid #DDE2EF;
            border-radius: 10px;
            font-family: 'Inter', Arial, sans-serif;
            font-size: 14px;
            color: #1A1F36;
            resize: vertical;
            transition: border-color 0.2s;
        }

        .notes-textarea:focus {
            outline: none;
            border-color: #0052BD;
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
            transition: transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 4px 16px rgba(255, 107, 0, 0.35);
        }

        .submit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(255, 107, 0, 0.45);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Spinner */
        .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top: 3px solid #ffffff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Already completed banner */
        .completed-banner {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            color: #ffffff;
            padding: 16px 24px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 14px;
        }

        .completed-banner-icon {
            font-size: 24px;
            display: block;
            margin-bottom: 6px;
        }

        /* Toast */
        .toast {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(-100px);
            padding: 14px 28px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            z-index: 1000;
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        .toast.show {
            transform: translateX(-50%) translateY(0);
        }

        .toast.success {
            background: #10B981;
            color: #ffffff;
        }

        .toast.error {
            background: #EF4444;
            color: #ffffff;
        }

        /* Footer */
        .footer {
            text-align: center;
            font-size: 11px;
            color: #8A93B0;
            padding: 20px 16px 30px;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .vehicle-info-header {
                flex-direction: column;
                text-align: center;
            }

            .client-info {
                grid-template-columns: 1fr;
            }

            .service-name {
                font-size: 12px;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="header-title">Configurazione Servizi</div>
        <div class="header-subtitle">MacNil — GT Fleet 365</div>
    </div>

    <div class="container">

        @if($alreadyCompleted)
            <div class="completed-banner">
                <span class="completed-banner-icon">✅</span>
                Questa configurazione è già stata completata il {{ $serviceRequest->completed_at->format('d/m/Y H:i') }}.
                Puoi modificarla e inviarla nuovamente.
            </div>
        @endif

        {{-- Info mezzo e cliente --}}
        <div class="vehicle-info">
            <div class="vehicle-info-header">
                @if($serviceRequest->vehicle_img)
                    <img src="{{ asset($serviceRequest->vehicle_img) }}"
                         alt="{{ $serviceRequest->vehicle_name }}"
                         class="vehicle-img">
                @else
                    <span style="font-size: 40px;">🚛</span>
                @endif
                <div>
                    <div class="vehicle-name">{{ $serviceRequest->vehicle_name }}</div>
                    <div class="vehicle-qty-label">Quantità dal preventivo: {{ $serviceRequest->vehicle_qty }}</div>
                </div>
            </div>

            @if($serviceRequest->client_data)
                <div class="client-info">
                    @if(!empty($serviceRequest->client_data['company']))
                        <div class="client-info-item">
                            <span class="client-info-label">Azienda</span>
                            <span class="client-info-value">{{ $serviceRequest->client_data['company'] }}</span>
                        </div>
                    @endif
                    @if(!empty($serviceRequest->client_data['contact']))
                        <div class="client-info-item">
                            <span class="client-info-label">Contatto</span>
                            <span class="client-info-value">{{ $serviceRequest->client_data['contact'] }} {{ $serviceRequest->client_data['lastname'] ?? '' }}</span>
                        </div>
                    @endif
                    @if(!empty($serviceRequest->client_data['email']))
                        <div class="client-info-item">
                            <span class="client-info-label">Email</span>
                            <span class="client-info-value">{{ $serviceRequest->client_data['email'] }}</span>
                        </div>
                    @endif
                    @if(!empty($serviceRequest->client_data['phone']))
                        <div class="client-info-item">
                            <span class="client-info-label">Telefono</span>
                            <span class="client-info-value">{{ $serviceRequest->client_data['phone'] }}</span>
                        </div>
                    @endif
                </div>
            @endif

            <div class="qty-field">
                <label for="vehicle_qty">Quantità mezzi:</label>
                <input type="number"
                       id="vehicle_qty"
                       name="vehicle_qty"
                       class="qty-input"
                       value="{{ $serviceRequest->vehicle_qty }}"
                       min="1">
            </div>
        </div>

        {{-- Form servizi --}}
        <form id="serviceForm">
            @csrf

            @foreach($sections as $sIndex => $section)
                <div class="section">
                    <div class="section-header" onclick="toggleSection({{ $sIndex }})">
                        <span class="section-icon">{{ $section['icon'] }}</span>
                        <span class="section-title">{{ $section['title'] }}</span>
                        <span class="section-toggle" id="toggle-{{ $sIndex }}">▼</span>
                    </div>
                    <div class="section-body" id="body-{{ $sIndex }}">
                        @foreach($section['items'] as $item)
                            <label class="service-item" id="item-{{ $item['id'] }}">
                                @if($section['type'] === 'radio')
                                    <input type="radio"
                                           name="base_package"
                                           value="{{ $item['id'] }}"
                                           onchange="updateSelection(this)"
                                           {{ $alreadyCompleted && $serviceRequest->services && collect($serviceRequest->services)->where('id', $item['id'])->isNotEmpty() ? 'checked' : '' }}>
                                @else
                                    <input type="checkbox"
                                           name="services[]"
                                           value="{{ $item['id'] }}"
                                           onchange="updateSelection(this)"
                                           {{ $alreadyCompleted && $serviceRequest->services && collect($serviceRequest->services)->where('id', $item['id'])->isNotEmpty() ? 'checked' : '' }}>
                                @endif

                                <span class="service-name">{{ $item['name'] }}</span>

                                @if(isset($item['input']) && $item['input'] === 'qty')
                                    @php
                                        $savedQty = 0;
                                        if ($alreadyCompleted && $serviceRequest->services) {
                                            $saved = collect($serviceRequest->services)->firstWhere('id', $item['id']);
                                            $savedQty = $saved['qty'] ?? 0;
                                        }
                                    @endphp
                                    <input type="number"
                                           name="quantities[{{ $item['id'] }}]"
                                           class="service-qty"
                                           value="{{ $savedQty ?: $serviceRequest->vehicle_qty }}"
                                           min="0"
                                           disabled>
                                @endif
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            {{-- Note --}}
            <div class="notes-section">
                <div class="notes-label">
                    <span>📝</span> Note aggiuntive
                </div>
                <textarea class="notes-textarea"
                          name="notes"
                          placeholder="Inserisci eventuali note o richieste particolari...">{{ $alreadyCompleted ? $serviceRequest->notes : '' }}</textarea>
            </div>
        </form>
    </div>

    {{-- Submit --}}
    <div class="submit-container">
        <button class="submit-btn" id="submitBtn" onclick="submitForm()">
            <span id="btnText">Invia Configurazione</span>
            <div class="spinner" id="btnSpinner"></div>
        </button>
    </div>

    {{-- Toast --}}
    <div class="toast" id="toast"></div>

    <script>
        // Toggle sezioni
        function toggleSection(index) {
            const body = document.getElementById('body-' + index);
            const toggle = document.getElementById('toggle-' + index);

            body.classList.toggle('collapsed');
            toggle.classList.toggle('collapsed');
        }

        // Aggiorna stile selezione e abilita/disabilita campi qty
        function updateSelection(input) {
            const item = input.closest('.service-item');

            if (input.type === 'radio') {
                // Deseleziona tutti i radio della stessa sezione
                const section = input.closest('.section-body');
                section.querySelectorAll('.service-item').forEach(el => el.classList.remove('selected'));
            }

            if (input.checked) {
                item.classList.add('selected');
                // Abilita il campo qty se presente
                const qtyInput = item.querySelector('.service-qty');
                if (qtyInput) qtyInput.disabled = false;
            } else {
                item.classList.remove('selected');
                const qtyInput = item.querySelector('.service-qty');
                if (qtyInput) qtyInput.disabled = true;
            }
        }

        // Abilita campi qty per servizi pre-selezionati (modifica)
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input[type="checkbox"]:checked, input[type="radio"]:checked').forEach(function(input) {
                updateSelection(input);
            });
        });

        // Toast
        function showToast(message, type) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = 'toast ' + type + ' show';

            setTimeout(() => {
                toast.classList.remove('show');
            }, 4000);
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

            // Aggiungi quantità mezzo
            formData.append('vehicle_qty', document.getElementById('vehicle_qty').value);

            try {
                const response = await fetch('/api/servizi/{{ $serviceRequest->token }}', {
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
                        window.location.href = '/servizi/{{ $serviceRequest->token }}/successo';
                    }, 1500);
                } else {
                    showToast('❌ ' + (data.message || 'Errore durante il salvataggio.'), 'error');
                }
            } catch (error) {
                showToast('❌ Errore di connessione. Riprova.', 'error');
            } finally {
                btn.disabled = false;
                btnText.style.display = 'inline';
                spinner.style.display = 'none';
            }
        }
    </script>
</body>

</html>
