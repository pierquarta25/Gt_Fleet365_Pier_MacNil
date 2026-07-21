<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Configuration Completed - GT Fleet 365') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            background-color: #F4F6FA;
            color: #1A1F36;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .success-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 48px 36px;
            text-align: center;
            max-width: 480px;
            width: 100%;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 36px;
            animation: scaleIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes scaleIn {
            0% { transform: scale(0); }
            100% { transform: scale(1); }
        }

        .success-title {
            font-family: 'Exo 2', Arial, sans-serif;
            font-size: 22px;
            font-weight: 800;
            color: #1A1F36;
            margin-bottom: 12px;
        }

        .success-message {
            font-size: 14px;
            color: #4A5578;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .vehicle-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #EBF3FF;
            border: 1px solid #C4DBFF;
            border-radius: 50px;
            padding: 8px 20px;
            margin-bottom: 32px;
        }

        .vehicle-badge-name {
            font-family: 'Exo 2', Arial, sans-serif;
            font-weight: 700;
            font-size: 13px;
            color: #0052BD;
        }

        .back-link {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #0052BD 0%, #003A8C 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 50px;
            font-family: 'Exo 2', Arial, sans-serif;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 4px 16px rgba(0, 82, 189, 0.3);
        }

        .back-link:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(0, 82, 189, 0.4);
        }

        .footer {
            margin-top: 40px;
            font-size: 11px;
            color: #8A93B0;
        }
    </style>
</head>

<body>
    <div class="success-card">
        <div class="success-icon">✅</div>
        <h1 class="success-title">{{ __('Configuration Saved!') }}</h1>
        <p class="success-message">
            {{ __('The service configuration has been saved successfully.') }}
            {{ __('Data has been recorded and PDF has been generated.') }}
        </p>

        @if(isset($serviceRequests) && $serviceRequests->isNotEmpty())
            <div style="display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; margin-bottom: 32px;">
                @foreach($serviceRequests as $req)
                    <div class="vehicle-badge" style="margin-bottom: 0;">
                        <span>🚛</span>
                        <span class="vehicle-badge-name">{{ $req->vehicle_name }} × {{ $req->vehicle_qty }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <br>

        @if(isset($groupToken))
            <a href="/servizi/{{ $groupToken }}/pdf" class="back-link" style="background: linear-gradient(135deg, #FF6B00 0%, #E55D00 100%); margin-bottom: 12px;">
                📄 {{ __('Download Complete Offer') }}
            </a>
            <br><br>
            <a href="#" class="back-link" style="background: linear-gradient(135deg, #FF6B00 0%, #E55D00 100%); margin-bottom: 12px;">
                📄 {{ __('Download Short Offer') }}
            </a>
            <br><br>
            <a href="/servizi/{{ $groupToken }}" class="back-link">
                {{ __('Edit Configuration') }}
            </a>
        @endif
    </div>

    <div class="footer">
        © {{ date('Y') }} MacNil | GT Fleet 365. {{ __('All rights reserved.') }}
    </div>
</body>

</html>
