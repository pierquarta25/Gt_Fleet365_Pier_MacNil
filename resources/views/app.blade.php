<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GT Fleet 365 – Tipologia Mezzi</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Configuratore flotta interattivo GT Fleet 365 per la gestione e il monitoraggio dei veicoli aziendali con integrazione HubSpot CRM.">
    <meta name="robots" content="index, follow">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="GT Fleet 365 – Tipologia Mezzi">
    <meta property="og:description" content="Configura online la tua flotta aziendale e ricevi un preventivo personalizzato per il monitoraggio dei tuoi veicoli.">
    <meta property="og:image" content="{{ asset('media/logo.png') }}">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Vite (React e CSS) -->
    @viteReactRefresh
    @vite(['resources/js/app.jsx', 'resources/css/app.css'])
</head>
<body>
    
    <!-- React monterà l'applicazione qui dentro -->
    <div id="app"></div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
