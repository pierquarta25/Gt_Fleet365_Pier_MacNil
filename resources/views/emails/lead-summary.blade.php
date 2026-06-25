<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Nuovo Lead Configurato</title>
    <style>
        body {
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            background-color: #F4F6FA;
            color: #1A1F36;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #DDE2EF;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
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
        }

        .body {
            padding: 24px;
        }

        .client-box {
            background-color: #E8F0FF;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 24px;
        }

        .client-table {
            width: 100%;
            border-collapse: collapse;
        }

        .client-table td {
            padding: 6px 8px;
            vertical-align: top;
            width: 33.33%;
        }

        .label {
            display: block;
            color: #8A93B0;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .value {
            font-size: 13px;
            font-weight: 700;
            color: #1A1F36;
        }

        .section-title {
            font-size: 13px;
            color: #4A5578;
            margin-top: 0;
            margin-bottom: 16px;
            font-weight: 600;
        }

        .vehicle-card {
            background-color: #ffffff;
            border: 1px solid #DDE2EF;
            border-left: 4px solid #FF6B00;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 12px;
        }

        .vehicle-table {
            width: 100%;
            border-collapse: collapse;
        }

        .vehicle-table td {
            vertical-align: middle;
        }

        .img-cell {
            width: 80px;
            text-align: center;
        }

        .img-cell img {
            max-height: 40px;
            max-width: 70px;
            display: block;
            margin: 0 auto;
        }

        .name-cell {
            font-size: 13px;
            font-weight: 700;
            color: #4A5578;
            padding-left: 12px;
        }

        .qty-cell {
            text-align: right;
            font-family: 'Exo 2', Arial, sans-serif;
            font-size: 22px;
            font-weight: 800;
            color: #FF6B00;
            width: 40px;
        }

        .total-container {
            margin-top: 24px;
            text-align: right;
        }

        .total-badge {
            background-color: #FF6B00;
            color: #ffffff;
            display: inline-block;
            padding: 8px 24px;
            border-radius: 50px;
            font-family: 'Exo 2', Arial, sans-serif;
            font-size: 16px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #8A93B0;
            border-top: 1px solid #DDE2EF;
            padding-top: 16px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            Riepilogo Finale
        </div>
        <div class="body">
            <div class="client-box">
                <table class="client-table">
                    <tr>
                        <td>
                            <span class="label">Azienda</span>
                            <span class="value">{{ $client['company'] }}</span>
                        </td>
                        <td>
                            <span class="label">Contatto</span>
                            <span class="value">{{ $client['contact'] }} {{ $client['lastname'] ?? '' }}</span>
                        </td>
                        <td>
                            <span class="label">Email</span>
                            <span class="value">{{ $client['email'] }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 15px;">
                            <span class="label">Telefono</span>
                            <span class="value">{{ $client['phone'] ?? '—' }}</span>
                        </td>
                        <td style="padding-top: 15px;">
                            <span class="label">Autisti</span>
                            <span class="value">{{ $client['drivers'] ?? '0' }}</span>
                        </td>
                        <td style="padding-top: 15px;">
                            <span class="label">Traffico 4G</span>
                            <span class="value">
                                @php
                                    $traffico = [];
                                    if ($client['italia'] ?? false)
                                        $traffico[] = 'Italia';
                                    if ($client['estero'] ?? false)
                                        $traffico[] = 'Estero';
                                    echo implode(', ', $traffico) ?: '—';
                                @endphp
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="padding-top: 15px;">
                            <span class="label">Note</span>
                            <span class="value">{{ $client['notes'] ?? '—' }}</span>
                        </td>
                    </tr>
                </table>
            </div>

            <p class="section-title">Mezzi selezionati:</p>

            @foreach($vehicles as $item)
                <div class="vehicle-card">
                    <table class="vehicle-table">
                        <tr>
                            <td class="img-cell">
                                @if(!empty($item['img']))
                                    <img src="{{ ($isPdf ?? false) ? public_path($item['img']) : asset($item['img']) }}"
                                        alt="{{ $item['name'] }}">
                                @else
                                    <span style="font-size: 20px;">📦</span>
                                @endif
                            </td>
                            <td class="name-cell">
                                {{ strtoupper($item['name']) }}
                            </td>
                            <td class="qty-cell">
                                {{ $item['qty'] }}
                            </td>
                        </tr>
                    </table>
                </div>
                @if(!empty($item['service_token']) && !($isPdf ?? false))
                    <div style="text-align: center; margin-top: -6px; margin-bottom: 16px;">
                        <a href="{{ url('/servizi/' . $item['service_token']) }}"
                           style="display: inline-block; padding: 8px 24px; background-color: #0052BD; color: #ffffff; text-decoration: none; border-radius: 50px; font-family: 'Exo 2', Arial, sans-serif; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                            🔗 Link ai Servizi
                        </a>
                    </div>
                @endif
            @endforeach

            <div class="total-container">
                <div class="total-badge">
                    @php
                        $totaleMezzi = array_sum(array_column($vehicles, 'qty'));
                    @endphp
                    Totale: {{ $totaleMezzi }} Mezzi
                </div>
            </div>
        </div>
    </div>
    <div class="footer">
        © {{ date('Y') }} MacNil | GT Fleet 365. Tutti i diritti riservati.
    </div>
</body>

</html>