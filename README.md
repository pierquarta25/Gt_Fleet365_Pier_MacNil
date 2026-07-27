<![CDATA[# 🚀 GT Fleet 365 — Configuratore Flotta Dinamico

<p align="center">
  <img src="public/media/logo.png" alt="GT Fleet 365 Logo" width="280">
</p>

<p align="center">
  <strong>Applicazione web per la configurazione digitale delle flotte aziendali con integrazione CRM HubSpot.</strong><br>
  Sviluppata per <a href="#">MacNil</a> — Gravina in Puglia.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/React-19-61DAFB?logo=react&logoColor=white" alt="React 19">
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white" alt="Laravel 13">
  <img src="https://img.shields.io/badge/Vite-8-646CFF?logo=vite&logoColor=white" alt="Vite 8">
  <img src="https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?logo=tailwindcss&logoColor=white" alt="Tailwind CSS 4">
  <img src="https://img.shields.io/badge/HubSpot-CRM-FF7A59?logo=hubspot&logoColor=white" alt="HubSpot CRM">
  <img src="https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white" alt="PHP 8.3">
</p>

---

## 📋 Indice

- [Panoramica](#-panoramica)
- [Flusso Operativo](#-flusso-operativo)
- [Architettura Tecnica](#️-architettura-tecnica)
- [Catalogo Veicoli](#-catalogo-veicoli)
- [Integrazione HubSpot CRM](#-integrazione-hubspot-crm)
- [Sistema Email e PDF](#-sistema-email-e-pdf)
- [Tracciamento Commerciali](#-tracciamento-commerciali)
- [Internazionalizzazione (i18n)](#-internazionalizzazione-i18n)
- [Design System](#-design-system)
- [Struttura del Progetto](#-struttura-del-progetto)
- [Rotte dell'Applicazione](#-rotte-dellapplicazione)
- [Sicurezza](#-sicurezza)
- [Setup e Installazione](#-setup-e-installazione)
- [Comandi Principali](#-comandi-principali)
- [Deploy su Render](#-deploy-su-render)
- [Variabili d'Ambiente](#️-variabili-dambiente)

---

## 🎯 Panoramica

**GT Fleet 365** è una Single Page Application (SPA) che digitalizza il processo di configurazione flotta per i clienti aziendali di MacNil. L'obiettivo è sostituire il flusso cartaceo/manuale con un sistema completamente digitale, automatizzato e integrato con il CRM aziendale.

### Cosa fa in breve

1. Un **commerciale MacNil** invia un link personalizzato al cliente (es. `tuosito.com/c/mario-rossi`)
2. Il **cliente** compila un configuratore a 3 step: dati aziendali → selezione mezzi → riepilogo
3. Il sistema crea automaticamente il **lead su HubSpot** (Contatto + Deal)
4. Il commerciale riceve un'**email con PDF** riepilogativo della flotta configurata
5. Dalla mail, il commerciale accede a un **form di dettaglio** per configurare servizi e hardware per ogni mezzo
6. Viene generato un **preventivo commerciale ufficiale** in PDF, pronto per il cliente

---

## 🔄 Flusso Operativo

L'applicazione si articola in **due fasi** sequenziali:

### Fase 1 — Configurazione Flotta (React SPA)

| Step | Contenuto | Dettagli |
|------|-----------|----------|
| **Step 1** | Dati Cliente | Ragione Sociale, P.IVA, Nome/Cognome Contatto, Email, Telefono, N° Autisti (counter +/-), Ambito Traffico 4G (Italia/Estero), Note |
| **Step 2** | Selezione Mezzi | Catalogo visivo con 55+ tipologie in 3 categorie. Input quantità per ogni mezzo, totale in tempo reale |
| **Step 3** | Riepilogo & Invio | Riquadro sinottico dati + griglia mezzi selezionati. Conferma e invio al backend |

**Al momento dell'invio:**
- ✅ Creazione Contatto e Deal su HubSpot
- ✅ Generazione token univoci per ogni veicolo (per la Fase 2)
- ✅ Invio email con PDF allegato al commerciale
- ✅ Pulizia `localStorage` e modale di successo

### Fase 2 — Configurazione Servizi (Form Blade)

Il commerciale riceve via email un **link univoco** (`/servizi/{token}`) che apre un form multi-tab dove:

1. Per ogni veicolo selezionato, viene visualizzata una **scheda dedicata**
2. Ogni scheda permette di scegliere:
   - **Pacchetto Base** (selezione esclusiva)
   - **App & Servizi Aggiuntivi** (selezione multipla con quantità)
   - **Crono & Tachigrafo**
   - **Hardware** (sensori, telecamere, ecc.)
   - **Sviluppo & Personalizzazioni**
3. Al submit: salvataggio DB → nota sul Deal HubSpot → generazione preventivo Word/PDF

---

## 🛠️ Architettura Tecnica

Il progetto segue un pattern **Decoupled Frontend/Backend** all'interno di un monolite Laravel:

### Frontend

| Tecnologia | Versione | Ruolo |
|-----------|---------|-------|
| React | 19 | Componente SPA principale (`VehicleForm.jsx`) |
| Vite | 8 | Build tool con HMR |
| Tailwind CSS | 4 | Utility classes per lo styling |
| Bootstrap | 5 | Grid system e componenti base |
| FontAwesome | 7 | Icone |
| Axios | 1.15 | Chiamate HTTP al backend |

**Caratteristiche principali:**
- **State Management granulare** per form multi-step fluido
- **Persistenza automatica in `localStorage`**: i dati non si perdono al refresh
- **Validazione client-side in tempo reale**: Regex email, campi obbligatori, feedback visivo immediato
- **Internazionalizzazione (IT/EN)** con React Context e file JSON

### Backend

| Tecnologia | Versione | Ruolo |
|-----------|---------|-------|
| Laravel | 13 | Framework PHP principale |
| PHP | 8.3 | Runtime |
| DomPDF | 3.1 | Generazione PDF riepilogo flotta |
| PhpWord | 1.4 | Compilazione template preventivo Word |
| Mailtrap SDK | — | Driver email (sviluppo/staging) |

**Servizi principali:**
- `HubSpotService` — Integrazione CRM (Contatti, Deal, Note)
- `QuoteGeneratorService` — Generazione preventivi Word → PDF (via LibreOffice headless)
- `LeadSummaryMail` — Email riepilogativa con PDF allegato

---

## 🚗 Catalogo Veicoli

Il configuratore include **55+ tipologie di mezzi** organizzate in 3 macro-categorie:

### 🚙 Veicoli Stradali (19 tipologie)
Auto, Auto Fringe Benefit, Auto Elettrica, Auto Ibrida, Furgone, Furgone Cassonato, Furgone Frigo, Furgone Multitemperatura, Moto, Scuola Bus, Bisarca, Bus Trasporto Privato, Bus Trasporto Pubblico, Motrice Cisterna, Motrice con Gru, Motrice Frigo, Motrice Isotermica, e altre.

### 🚛 Trasporto Pesante & Semirimorchi (20 tipologie)
Motrice per Container, Motrice per Rimorchio, Motrice Telonata, Golf Cart, Carro Funebre, Spazzatrice, Trattore Stradale, Veicolo Raccolta Rifiuti, Semirimorchio, Semirimorchio Frigo, Semirimorchio Isotermico, Rimorchio, Container, Cassa Mobile, Cassone Scarrabile, Attrezzatura da Cantiere, Bagno Chimico, e altre.

### 🔧 Asset, Cantieri & Speciali (16 tipologie)
Cucina Mobile, Gommone, Gruppo Elettrogeno, Betoniera, Macchinari per Cantieri Edili, Mezzi da Magazzino, Mezzi Movimento Terra, Mezzi per Cave, Mezzo Portuale, Minidumper, Motocarriola, Muletto, Piattaforma Aerea su Furgone, Piattaforme Aeree su Gomma/Cingoli, Trattore Agricolo, Trattore Agricolo Compatto.

Ogni mezzo è accompagnato dalla propria immagine in `public/media/`.

---

## 🔗 Integrazione HubSpot CRM

Il `HubSpotService` gestisce l'intero ciclo di vita del lead su HubSpot tramite le API REST v3:

| Operazione | Metodo | Descrizione |
|-----------|--------|-------------|
| **Upsert Contatto** | `upsertContact()` | Crea il contatto. Se esiste già (HTTP 409), recupera l'ID esistente via ricerca per email |
| **Creazione Deal** | `createDeal()` | Crea l'opportunità `"GT Fleet 365: {azienda}"` nella pipeline predefinita e la associa al contatto |
| **Nota Servizi** | `addServiceNote()` | Aggiunge una nota dettagliata al Deal con il riepilogo completo dei servizi, pacchetti e hardware scelti |

La descrizione del Deal include: elenco veicoli con quantità, P.IVA e numero autisti.

---

## 📧 Sistema Email e PDF

### Email di Riepilogo (Fase 1)

La classe `LeadSummaryMail` invia una mail HTML professionale contenente:
- Tabella con tutti i dati del cliente
- Card visive dei mezzi selezionati (con immagini e quantità)
- Link **"🔗 Link ai Servizi"** sotto ogni veicolo per accedere alla Fase 2
- **PDF allegato** generato al volo con DomPDF

Il nome del PDF è dinamico: `riepilogo-flotta-{azienda-slugificata}-{YYYY-MM-DD-HH-mm-ss}.pdf`

**Logica di destinazione:**
- Se il commerciale è identificato → email **a lui** con CC ai capi commerciali
- Se il commerciale non è identificato → email alla **mail commerciale generica**

### Preventivo Commerciale (Fase 2)

Il `QuoteGeneratorService` compila un preventivo ufficiale:
1. Parte da un template Word (`resources/templates/template_offerta.docx`)
2. Sostituisce i placeholder (ragione sociale, date, ecc.)
3. Compila una tabella con prezzi di listino, canoni annuali e costi hardware
4. Converte in PDF via LibreOffice headless (`soffice --headless --convert-to pdf`)
5. Fallback: se LibreOffice non è disponibile, eroga il `.docx`

---

## 👤 Tracciamento Commerciali

Il sistema di tracciamento del commerciale referente è **multi-livello e resiliente**:

1. **Link personalizzato:** il commerciale condivide `/c/mario-rossi`
2. **Backend:** trova l'utente dal DB via `slug`, salva l'email in sessione, redirige a `/?agent=mario-rossi`
3. **Frontend:** estrae `?agent=...` dall'URL e lo salva in `localStorage`
4. **Risoluzione a cascata** all'invio:
   - `agent_slug` dal payload JSON → `agent_email` dal payload → `session('agent_email')`

Questo approccio garantisce il corretto tracciamento anche se la sessione Laravel scade o i cookie vengono cancellati.

---

## 🌐 Internazionalizzazione (i18n)

L'app supporta **Italiano** (default) e **Inglese**:

| Livello | Meccanismo | File |
|---------|-----------|------|
| **Frontend** | React Context + hook `useLanguage()` + funzione `t()` | `resources/js/i18n/it.json`, `en.json` |
| **Backend** | Middleware `SetLocale` + funzione `__()` di Laravel | `lang/it.json`, `lang/en.json` |

- ~125 stringhe tradotte in totale
- Fallback automatico EN → IT per chiavi mancanti
- Lingua persistita in `localStorage`
- I nomi di veicoli e servizi sono nomi commerciali e **non vengono tradotti**

---

## 🎨 Design System

| Proprietà | Valore |
|-----------|--------|
| **Colore primario (brand)** | `#0052BD` |
| **Colore secondario** | `#003F94` (blu scuro) |
| **Accent arancione** | `#FF6B00` |
| **Accent ciano** | `#29ABE2` |
| **Successo** | `#10B981` |
| **Font titoli** | Exo 2 (Google Fonts) |
| **Font testo** | Inter (Google Fonts) |
| **Approccio** | Mobile First |
| **Breakpoints** | 992px, 768px, 480px |

**Componenti UI principali:**
- **Stepper** — Badge numerici circolari con stati `active` (arancio), `done` (verde ✓), `pending`
- **Modali** — Sfondo sfocato (`backdrop-filter: blur(8px)`), animazione di scala, icone SVG
- **Counter autisti** — Pulsanti `+`/`-` stilizzati in azzurro
- **Toggle 4G** — Selezione Italia/Estero con stato attivo arancione
- **Tabelle mobile** — Si trasformano in liste verticali sotto i 768px

---

## 📁 Struttura del Progetto

```
gtfleet365/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── VehicleFormController.php    # SPA + API invio lead
│   │   │   └── ServiceFormController.php    # Form servizi (Fase 2)
│   │   └── Middleware/
│   │       ├── SecurityHeaders.php          # HTTP Security Headers
│   │       └── SetLocale.php                # Gestione lingua (IT/EN)
│   ├── Mail/
│   │   └── LeadSummaryMail.php              # Email riepilogativa + PDF
│   ├── Models/
│   │   ├── Lead.php                         # Dati lead
│   │   ├── ServiceRequest.php               # Configurazione servizi per mezzo
│   │   └── User.php                         # Utenti/Commerciali (con slug)
│   └── Services/
│       ├── HubSpotService.php               # Integrazione HubSpot CRM
│       └── QuoteGeneratorService.php        # Generazione preventivo Word/PDF
├── config/
│   ├── services.php                         # Config HubSpot token
│   └── vehicle_services.php                 # Catalogo servizi per tipo mezzo
├── resources/
│   ├── css/
│   │   ├── app.css                          # Entry point CSS
│   │   └── style.css                        # Design system completo
│   ├── js/
│   │   ├── app.jsx                          # Entry point React
│   │   ├── bootstrap.js                     # Config Axios
│   │   ├── components/
│   │   │   └── VehicleForm.jsx              # Componente SPA a 3 step
│   │   ├── data/
│   │   │   └── vehicleTypes.js              # Catalogo 55+ veicoli
│   │   └── i18n/
│   │       ├── LanguageContext.jsx           # Provider multilingua
│   │       ├── it.json                      # Traduzioni IT
│   │       └── en.json                      # Traduzioni EN
│   ├── templates/
│   │   └── template_offerta.docx            # Template preventivo Word
│   └── views/
│       ├── app.blade.php                    # Root template SPA
│       ├── emails/
│       │   └── lead-summary.blade.php       # Template email lead
│       ├── pdf/
│       │   └── service-summary.blade.php    # Template PDF servizi
│       └── services/
│           ├── form.blade.php               # Form servizi (multi-tab)
│           └── success.blade.php            # Conferma + download PDF
├── public/media/                            # 56 immagini veicoli + logo
├── routes/
│   └── web.php                              # Tutte le rotte (SPA + API)
├── Dockerfile                               # Build multi-stage per Render
├── docker/
│   └── apache.conf                          # Configurazione Apache
└── render-start.sh                          # Script avvio su Render
```

---

## 🛤️ Rotte dell'Applicazione

| Metodo | Percorso | Controller | Descrizione |
|--------|----------|-----------|-------------|
| `GET` | `/` | `VehicleFormController@index` | Caricamento SPA React |
| `GET` | `/c/{slug}` | `VehicleFormController@handleSlug` | Assegnazione commerciale e redirect |
| `POST` | `/api/vehicle-form` | `VehicleFormController@store` | Invio dati flotta (rate limited) |
| `GET` | `/servizi/{token}` | `ServiceFormController@show` | Form configurazione servizi |
| `POST` | `/api/servizi/{token}` | `ServiceFormController@store` | Salvataggio servizi scelti |
| `GET` | `/servizi/{token}/successo` | `ServiceFormController@success` | Pagina conferma |
| `GET` | `/servizi/{token}/pdf` | `ServiceFormController@downloadPdf` | Download preventivo PDF/DOCX |

---

## 🔒 Sicurezza

| Misura | Dettaglio |
|--------|----------|
| **Rate Limiting** | Endpoint `/api/vehicle-form` protetto da `throttle:vehicle-form` |
| **HTTP Security Headers** | `X-Content-Type-Options: nosniff`, `X-Frame-Options: DENY`, `X-XSS-Protection`, `Referrer-Policy`, `Permissions-Policy` |
| **HSTS** | `Strict-Transport-Security` abilitato in produzione |
| **Doppia Validazione** | Client-side (React) + Server-side (Laravel) |
| **Token Univoci** | Accesso ai form servizi protetto da token generati al momento dell'invio |
| **CSRF Protection** | Gestito nativamente da Laravel |

---

## 🚀 Setup e Installazione

### Prerequisiti

- PHP 8.3+
- Composer
- Node.js 18+ e npm
- MySQL / PostgreSQL
- (Opzionale) LibreOffice per la conversione DOCX → PDF

### Installazione rapida

```bash
# Clona il repository
git clone <url-repository>
cd gtfleet365

# Setup completo (dipendenze, .env, chiave, migrazioni, build assets)
composer run setup
```

### Installazione manuale

```bash
# 1. Installa dipendenze PHP
composer install

# 2. Installa dipendenze Node.js
npm install

# 3. Configura l'ambiente
cp .env.example .env
php artisan key:generate

# 4. Configura il database e le variabili d'ambiente nel file .env

# 5. Esegui le migrazioni
php artisan migrate

# 6. Compila gli asset
npm run build
```

---

## ⚡ Comandi Principali

### Sviluppo

```bash
# Avvia tutto in parallelo: Laravel server, Vite HMR, Queue listener, Log viewer
composer run dev
```

### Produzione

```bash
# Compila gli asset per la produzione
npm run build
```

### Test

```bash
# Esegui la suite di test PHPUnit
composer run test
```

---

## 🐳 Deploy su Render

L'applicazione è configurata per il deploy su **Render** come Web Service Docker gratuito:

1. **Stage 1 (Node.js):** Installa pacchetti npm e compila gli asset React con Vite
2. **Stage 2 (PHP 8.4 + Apache):** Installa estensioni PHP (`pdo_pgsql`, `gd`, `zip`), Composer, abilita `mod_rewrite`
3. **Script di avvio** (`render-start.sh`):
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan migrate --force
   apache2-foreground
   ```

Per il deploy, è sufficiente collegare il repository GitHub a Render e configurare le variabili d'ambiente.

---

## ⚙️ Variabili d'Ambiente

| Variabile | Descrizione | Esempio |
|-----------|-------------|---------|
| `APP_URL` | URL base dell'applicazione | `https://smartfleet-configurator.onrender.com` |
| `DB_CONNECTION` | Driver database | `mysql` / `pgsql` |
| `DB_DATABASE` | Nome database | `gtfleet365` |
| `HUBSPOT_ACCESS_TOKEN` | Token API privato HubSpot | `pat-xx-xxxxxxxx` |
| `MAIL_MAILER` | Driver email | `mailtrap-sdk` / `log` / `smtp` |
| `MAIL_CAPI_COMMERCIALI` | Email CC per i responsabili (separate da virgola) | `capo1@azienda.it,capo2@azienda.it` |

Per l'elenco completo, consultare il file `.env.example`.

---

<p align="center">
  <sub>Sviluppato da <strong>Pier</strong> @ MacNil, Gravina in Puglia.</sub>
</p>
]]>
