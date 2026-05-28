# Piano di Implementazione: Ottimizzazione SEO e Accessibilità

Questo documento descrive il piano di implementazione per ottimizzare la SEO, la gerarchia dei titoli (heading hierarchy), l'uso di elementi semantici HTML5 e l'accessibilità (a11y) dell'applicazione GT Fleet 365, concentrandosi sui file `resources/views/app.blade.php` e `resources/js/components/VehicleForm.jsx`.

## 1. Modifiche a `resources/views/app.blade.php`

### Obiettivi SEO e Social Share
*   **Meta Description:** Aggiungere una descrizione per i motori di ricerca che descriva accuratamente l'applicazione.
*   **Open Graph (OG):** Aggiungere tag Open Graph per migliorare la visualizzazione quando il link viene condiviso sui social o app di messaggistica.
*   **Robots:** Specificare le istruzioni esplicite per i crawler (`index, follow`).

### Dettaglio modifiche proposte:
```html
<!-- Meta descrittivi e SEO -->
<meta name="description" content="Configuratore flotta interattivo GT Fleet 365 per la gestione e il monitoraggio dei veicoli aziendali con integrazione HubSpot CRM.">
<meta name="robots" content="index, follow">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:title" content="GT Fleet 365 – Tipologia Mezzi">
<meta property="og:description" content="Configura online la tua flotta aziendale e ricevi un preventivo personalizzato per il monitoraggio dei tuoi veicoli.">
<meta property="og:image" content="{{ asset('media/logo.png') }}">
```

---

## 2. Modifiche a `resources/js/components/VehicleForm.jsx`

### Struttura Semantica HTML5 e Gerarchia Titoli (Heading Hierarchy)
*   **Elemento `<main>`:** Sostituire il generico `<div className="container">` contenente i vari passaggi con un tag semantico `<main className="container">`.
*   **Singolo `<h1>` globale:** Aggiungere un tag `<h1>` con classe `visually-hidden` (per i lettori di schermo, garantendo un `<h1>` unico per tutta l'applicazione a prescindere dal passo attivo).
*   **Correzione dei livelli di titolo:**
    *   Nel Passo 1, trasformare il titolo dell'Hero Banner da `<h1>Configurazione Flotta</h1>` a `<h2>Configurazione Flotta</h2>`.
    *   Gli altri titoli di sezione rimangono `<h2>` (es. `<h2>Dati Cliente</h2>`, `<h2>{cat.title}</h2>`, `<h2>Riepilogo finale</h2>`), mantenendo una gerarchia coerente: `<h1>` (globale nascosto) -> `<h2>` (sezioni).

### Accessibilità (a11y) e ID unici per Browser Testing
*   **Accoppiamento Label-Input:** Utilizzare `htmlFor` sui tag `<label>` per associarli correttamente agli input tramite `id`.
*   **Gestione Errori:** Aggiungere `aria-invalid` e `aria-describedby` sugli input con validazione attiva nel Passo 1 per notificare i lettori di schermo in caso di errori.
*   **Tastiere Mobili:** Cambiare l'input del telefono (`#phone`) da `type="text"` a `type="tel"`.
*   **Elementi Interattivi (Pulsanti):**
    *   I pulsanti di scelta area ("Italia", "Estero") riceveranno `type="button"`, `aria-pressed={...}` ed il loro contenitore avrà `role="group"` e `aria-label`.
    *   Tutti i bottoni di navigazione riceveranno un attributo esplicito `type="button"` per evitare comportamenti di submit indesiderati.
*   **Input Quantità (Passo 2):**
    *   Assegnare un `id` e un `name` univoci a ciascun input di quantità: `qty-{vehicle.id}`.
    *   Aggiungere l'attributo `aria-label={`Quantità per ${v.name}`}` e `min="0"`.
*   **Testi Alternativi Immagini (`alt`):**
    *   Sostituire gli attributi `alt=""` vuoti con testi descrittivi significativi per i loghi e le immagini dei veicoli (es. `alt={v.name}`).

---

## 3. Fasi di Sviluppo ed Esecuzione
1.  **Presentazione del piano** e attesa dell'approvazione del cliente.
2.  **Apporto delle modifiche** su `resources/views/app.blade.php`.
3.  **Apporto delle modifiche** su `resources/js/components/VehicleForm.jsx`.
4.  **Verifica e test** (compilazione tramite `npm run build` per confermare che non ci siano errori sintattici).
