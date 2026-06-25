<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Riepilogo Servizi Globale - GT Fleet 365</title>
    <style>
        body {
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            background-color: #ffffff;
            color: #1A1F36;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
        }

        .header {
            background-color: #0052BD;
            color: #ffffff;
            padding: 16px 24px;
            font-family: 'Exo 2', Arial, sans-serif;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }

        .body {
            padding: 24px;
            border: 1px solid #DDE2EF;
            border-top: none;
            border-radius: 0 0 8px 8px;
        }

        .client-info {
            background-color: #F8FAFF;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            border: 1px solid #E3E8F4;
        }

        .client-row {
            display: flex;
            margin-bottom: 6px;
        }

        .client-label {
            color: #8A93B0;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            width: 80px;
        }

        .client-value {
            font-weight: 600;
            color: #1A1F36;
        }

        .vehicle-section {
            margin-bottom: 32px;
            padding-bottom: 16px;
            border-bottom: 2px dashed #E3E8F4;
        }

        .vehicle-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .vehicle-box {
            background-color: #E8F0FF;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 16px;
            border-left: 4px solid #FF6B00;
        }

        .vehicle-name {
            font-family: 'Exo 2', Arial, sans-serif;
            font-size: 16px;
            font-weight: 800;
            color: #1A1F36;
        }

        .vehicle-qty {
            font-size: 12px;
            color: #4A5578;
            margin-top: 4px;
        }

        .section-title {
            font-family: 'Exo 2', Arial, sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: #0052BD;
            text-transform: uppercase;
            margin: 16px 0 8px;
            padding-bottom: 6px;
            border-bottom: 2px solid #E3E8F4;
        }

        .service-list {
            list-style: none;
            padding: 0;
            margin: 0 0 16px 0;
        }

        .service-list li {
            padding: 6px 0;
            border-bottom: 1px solid #F4F6FA;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .service-list li:last-child {
            border-bottom: none;
        }

        .service-qty-badge {
            background: #FF6B00;
            color: #ffffff;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 700;
        }

        .notes-box {
            background-color: #F8FAFF;
            border: 1px solid #E3E8F4;
            border-radius: 8px;
            padding: 12px 16px;
            margin-top: 12px;
        }

        .notes-title {
            font-size: 10px;
            color: #8A93B0;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .notes-text {
            font-size: 12px;
            color: #1A1F36;
            line-height: 1.5;
        }

        .footer {
            margin-top: 24px;
            text-align: center;
            font-size: 10px;
            color: #8A93B0;
            border-top: 1px solid #DDE2EF;
            padding-top: 12px;
        }

        .timestamp {
            font-size: 10px;
            color: #8A93B0;
            text-align: right;
            margin-top: 16px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            Riepilogo Servizi Preventivo
        </div>
        <div class="body">
            
            {{-- Info Cliente Globale --}}
            @php
                $firstReq = $serviceRequests->first();
            @endphp
            @if($firstReq && $firstReq->client_data)
                <div class="client-info">
                    @if(!empty($firstReq->client_data['company']))
                        <div class="client-row">
                            <span class="client-label">Azienda</span>
                            <span class="client-value">{{ $firstReq->client_data['company'] }}</span>
                        </div>
                    @endif
                    @if(!empty($firstReq->client_data['contact']))
                        <div class="client-row">
                            <span class="client-label">Contatto</span>
                            <span class="client-value">{{ $firstReq->client_data['contact'] }} {{ $firstReq->client_data['lastname'] ?? '' }}</span>
                        </div>
                    @endif
                    @if(!empty($firstReq->client_data['email']))
                        <div class="client-row">
                            <span class="client-label">Email</span>
                            <span class="client-value">{{ $firstReq->client_data['email'] }}</span>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Iterazione sui Mezzi --}}
            @foreach($serviceRequests as $req)
                <div class="vehicle-section">
                    <div class="vehicle-box">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                @if($req->vehicle_img)
                                    <td style="width: 80px; vertical-align: middle;">
                                        <img src="{{ public_path($req->vehicle_img) }}" alt="{{ $req->vehicle_name }}" style="max-width: 70px; max-height: 50px;">
                                    </td>
                                @endif
                                <td style="vertical-align: middle;">
                                    <div class="vehicle-name">{{ $req->vehicle_name }}</div>
                                    <div class="vehicle-qty">Quantità: {{ $req->vehicle_qty }}</div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    @if($req->services && count($req->services) > 0)
                        @php
                            $basePackage = collect($req->services)->firstWhere('type', 'base_package');
                            $addons = collect($req->services)->where('type', 'addon')->values();
                        @endphp

                        @if($basePackage)
                            <div class="section-title">PACCHETTO BASE</div>
                            <ul class="service-list">
                                <li>
                                    <span>{{ $basePackage['name'] }}</span>
                                </li>
                            </ul>
                        @endif

                        @if($addons->count() > 0)
                            <div class="section-title">SERVIZI & HARDWARE SELEZIONATI</div>
                            <ul class="service-list">
                                @foreach($addons as $addon)
                                    <li>
                                        <span>{{ $addon['name'] }}</span>
                                        @if(!empty($addon['qty']))
                                            <span class="service-qty-badge">× {{ $addon['qty'] }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    @else
                        <p style="color: #8A93B0; font-style: italic;">Nessun servizio selezionato per questo mezzo.</p>
                    @endif

                    @if($req->notes)
                        <div class="notes-box">
                            <div class="notes-title">Note Aggiuntive ({{ $req->vehicle_name }})</div>
                            <div class="notes-text">{{ $req->notes }}</div>
                        </div>
                    @endif
                </div>
            @endforeach

            <div class="timestamp">
                Compilato il {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>

        <div class="footer">
            © {{ date('Y') }} MacNil | GT Fleet 365. Tutti i diritti riservati.
        </div>
    </div>
</body>

</html>
