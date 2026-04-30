# 🚀 GT Fleet 365 - Configuratore Flotta Dinamico

Questo progetto è un'applicazione web "Single Page" sviluppata durante il tirocinio presso **MacNil** a Gravina in Puglia. L'obiettivo è digitalizzare il processo di configurazione flotta per i nuovi lead e i clienti esistenti, integrando i dati direttamente su **HubSpot**.

---

## 🛠️ Architettura Tecnica

Il progetto segue il pattern **Decoupled Frontend/Backend** per garantire velocità e manutenibilità:

### 🔹 Frontend (React + Vite)
- **Stack:** React 18 con Vite per un build time quasi istantaneo.
- **State Management:** Gestione granulare dello stato per un form multi-step fluido.
- **Persistence (LocalStorage):** Implementata logica di salvataggio automatico: se l'utente ricarica la pagina o perde la connessione, i dati inseriti non vanno perduti.
- **Validazione Avanzata:** 
    - Controllo in tempo reale dei campi obbligatori.
    - Validazione dell'email tramite espressioni regolari (Regex).
    - Feedback visivo immediato (bordi rossi e messaggi di errore) che sostituisce i vecchi alert, migliorando la fluidità.

### 🔹 Backend (Laravel 11)
- **HubSpot Integration:** Sviluppo di un `HubSpotService` dedicato per la creazione automatica di Lead e Deal tramite API REST.
- **Server-Side Validation:** Validazione robusta dei dati in ingresso per garantire la sicurezza del database e delle API esterne.
- **RESTful API:** Endpoint strutturati per la comunicazione asincrona con il frontend tramite Axios.

---

## 🎨 UI/UX & Design System

Il design è stato curato per riflettere l'identità di marca **GT Fleet 365**:

- **Layout Dinamico:** Header con logo posizionato a sinistra e stepper di avanzamento a destra per sfruttare tutto lo spazio orizzontale.
- **Tabella Mezzi Ottimizzata:**
    - Design pulito "Total White" con bordi definiti del colore istituzionale (`#0052BD`).
    - Immagini centrate e colonne a larghezza fissa per una lettura rapida.
    - Feedback di caricamento: Spinner animato sul pulsante di invio per evitare click multipli durante le chiamate API.
- **Responsività Totale (Mobile First):**
    - **Smartphone:** I campi del form si impilano verticalmente e le tabelle diventano scorrevoli orizzontalmente per evitare la rottura del layout.
    - **Tablet:** Griglie adattive che passano da 3 a 2 colonne per mantenere la leggibilità.

---

## 💡 Pillole per la Presentazione (Tips per Pier)
1.  **"Affidabilità del Dato":** "Ho implementato una doppia validazione (frontend e backend). Il frontend guida l'utente, il backend protegge il sistema."
2.  **"Resilienza dell'Interfaccia":** "Grazie al LocalStorage, mettiamo l'utente al centro: il suo lavoro è protetto anche in caso di refresh accidentale della pagina."
3.  **"Professionalità Visiva":** "Ho trasformato una tabella zebra standard in una griglia dal design moderno che richiama la navbar, curando ogni stato (hover, focus, caricamento)."

---

## 🚀 Setup e Installazione

1. **Clona il repo:** `git clone ...`
2. **Installazione dipendenze:** `composer install` e `npm install`
3. **Compilazione assets:** `npm run build`
4. **Avvio server:** `php artisan serve`

---
*Sviluppato da Pier @ Macnil, Gravina in Puglia.*
