# 🚀 GT Fleet 365 — Configuratore Flotta Dinamico

Applicazione web sviluppata per **MacNil** (Gravina in Puglia) che digitalizza il processo di configurazione delle flotte aziendali, con integrazione automatica nel CRM **HubSpot**.

**Stack:** React 19 · Laravel 13 · Vite 8 · Tailwind CSS 4 · Bootstrap 5 · PHP 8.3

---

## 🎯 A cosa serve

Un **commerciale MacNil** invia al cliente un link personalizzato (es. `sito.com/c/mario-rossi`).
Il cliente apre il link e compila un configuratore online in 3 passaggi.
Il sistema fa tutto il resto in automatico:

- Crea il **lead su HubSpot** (Contatto + Deal)
- Invia un'**email al commerciale** con il PDF riepilogativo della flotta
- Genera i **link per configurare i servizi** di ogni veicolo
- Produce il **preventivo commerciale ufficiale** in PDF

---

## 🔄 Come funziona

L'app è divisa in **due fasi**.

### Fase 1 — Il cliente configura la flotta

Un form React a 3 step:

**Step 1 · Dati Cliente**
> Ragione Sociale, P.IVA, Nome, Cognome, Email, Telefono, Numero Autisti, Traffico Dati 4G (Italia/Estero), Note.

**Step 2 · Selezione Mezzi**
> Catalogo visivo con **55+ tipologie** di veicoli divise in 3 categorie. Per ogni mezzo il cliente indica la quantità. Un contatore in basso mostra il totale in tempo reale.

**Step 3 · Riepilogo e Invio**
> Panoramica completa di tutti i dati inseriti e dei mezzi selezionati. Il cliente conferma e invia.

Cosa succede dopo l'invio:
1. Il backend crea Contatto e Deal su **HubSpot**
2. Genera un **token univoco** per ogni veicolo selezionato
3. Invia una **mail con PDF allegato** al commerciale di riferimento
4. Mostra una schermata di conferma al cliente

### Fase 2 — Il commerciale configura i servizi

Il commerciale riceve via email un link univoco (`/servizi/{token}`). Cliccandolo si apre un **form a schede** dove, per ogni veicolo:

- Sceglie il **Pacchetto Base**
- Aggiunge **Servizi Aggiuntivi** (con quantità)
- Seleziona **Hardware** (sensori, telecamere, ecc.)
- Configura **Crono & Tachigrafo**
- Specifica eventuali **Personalizzazioni**

Dopo l'invio:
1. I dati vengono salvati nel **database**
2. Viene aggiunta una **nota dettagliata** al Deal su HubSpot
3. Viene generato un **preventivo ufficiale** in Word/PDF, scaricabile dalla pagina di conferma

---

## 🚗 Catalogo Veicoli

**55+ tipologie** organizzate in 3 categorie:

**🚙 Veicoli Stradali** — Auto, Auto Fringe Benefit, Elettriche, Ibride, Furgoni (vari), Moto, Bus, Bisarche, Motrici specializzate (Cisterna, Gru, Frigo, Isotermica).

**🚛 Trasporto Pesante & Semirimorchi** — Motrici per Container/Rimorchio, Carri Funebri, Spazzatrici, Trattori Stradali, Semirimorchi (Frigo, Isotermico, Telonato), Container, Casse Mobili, Cassoni Scarrabili.

**🔧 Asset, Cantieri & Speciali** — Gommoni, Gruppi Elettrogeni, Betoniere, Muletti, Piattaforme Aeree, Trattori Agricoli, Mezzi da Magazzino, Movimento Terra, Minidumper.

Ogni veicolo ha la propria immagine in `public/media/`.

---

## 🔗 Integrazione HubSpot

Il servizio `HubSpotService` comunica con le API REST v3 di HubSpot:

- **Upsert Contatto** — Crea il contatto. Se esiste già, recupera l'ID esistente.
- **Creazione Deal** — Crea l'opportunità commerciale `"GT Fleet 365: {azienda}"` e la collega al contatto.
- **Nota Servizi** — Aggiunge al Deal una nota con il riepilogo di tutti i servizi e l'hardware scelti nella Fase 2.

---

## 📧 Email e PDF

**Email di riepilogo (Fase 1):**
- Tabella con i dati del cliente
- Card visive dei mezzi selezionati
- Link per accedere alla Fase 2
- PDF allegato: `riepilogo-flotta-{azienda}-{data}.pdf`

**Preventivo commerciale (Fase 2):**
- Compilato da un template Word (`template_offerta.docx`)
- Tabella con prezzi di listino, canoni annuali, costi hardware
- Convertito in PDF tramite LibreOffice

**Destinatario della mail:**
- Se il commerciale è identificato → mail a lui, con CC ai capi commerciali
- Se non è identificato → mail alla casella commerciale generica

---

## 👤 Tracciamento Commerciali

Il sistema riconosce il commerciale in modo **resiliente** su 3 livelli:

1. Il commerciale condivide il link `/c/mario-rossi`
2. Il backend trova l'utente nel DB, salva l'email in sessione, redirige a `/?agent=mario-rossi`
3. Il frontend salva lo slug in `localStorage`

All'invio, il backend cerca il commerciale in quest'ordine:
`agent_slug` (payload) → `agent_email` (payload) → `session('agent_email')`

Funziona anche se la sessione scade o i cookie vengono cancellati.

---

## 🌐 Lingue supportate

Italiano (default) e Inglese.

- **Frontend:** React Context con funzione `t('chiave')` e file JSON (`it.json`, `en.json`)
- **Backend:** Middleware `SetLocale` con funzione `__()` di Laravel
- I nomi di veicoli e servizi sono commerciali e non vengono tradotti

---

## 🎨 Design

- **Colore brand:** `#0052BD` (blu MacNil)
- **Accent:** `#FF6B00` (arancione), `#29ABE2` (ciano)
- **Font:** Exo 2 (titoli) + Inter (testo)
- **Approccio:** Mobile First
- **Breakpoints:** 992px, 768px, 480px

Componenti personalizzati: stepper con stati colorati, modali con sfondo sfocato, counter autisti (+/-), toggle Italia/Estero, tabelle che diventano liste verticali su mobile.

---

## 📁 Struttura del Progetto

```
gtfleet365/
│
├── app/
│   ├── Http/Controllers/
│   │   ├── VehicleFormController.php     ← SPA + API invio lead
│   │   └── ServiceFormController.php     ← Form servizi (Fase 2)
│   ├── Http/Middleware/
│   │   ├── SecurityHeaders.php           ← HTTP Security Headers
│   │   └── SetLocale.php                 ← Gestione lingua IT/EN
│   ├── Mail/
│   │   └── LeadSummaryMail.php           ← Email riepilogo + PDF allegato
│   ├── Models/
│   │   ├── Lead.php                      ← Dati lead
│   │   ├── ServiceRequest.php            ← Servizi per veicolo
│   │   └── User.php                      ← Commerciali (con slug)
│   └── Services/
│       ├── HubSpotService.php            ← Integrazione HubSpot CRM
│       └── QuoteGeneratorService.php     ← Preventivo Word → PDF
│
├── config/
│   ├── services.php                      ← Token HubSpot
│   └── vehicle_services.php              ← Catalogo servizi per mezzo
│
├── resources/
│   ├── css/
│   │   └── style.css                     ← Design system completo
│   ├── js/
│   │   ├── app.jsx                       ← Entry point React
│   │   ├── components/
│   │   │   └── VehicleForm.jsx           ← Form a 3 step (cuore dell'app)
│   │   ├── data/
│   │   │   └── vehicleTypes.js           ← Catalogo 55+ veicoli
│   │   └── i18n/
│   │       ├── LanguageContext.jsx        ← Provider multilingua
│   │       ├── it.json                   ← Traduzioni italiano
│   │       └── en.json                   ← Traduzioni inglese
│   ├── templates/
│   │   └── template_offerta.docx         ← Template preventivo
│   └── views/
│       ├── app.blade.php                 ← Template base SPA
│       ├── emails/
│       │   └── lead-summary.blade.php    ← Template email
│       └── services/
│           ├── form.blade.php            ← Form servizi
│           └── success.blade.php         ← Conferma + download PDF
│
├── public/media/                         ← 56 immagini veicoli + logo
├── routes/web.php                        ← Tutte le rotte
├── Dockerfile                            ← Build multi-stage per Render
└── render-start.sh                       ← Script avvio produzione
```

---

## 🛤️ Rotte

| Metodo | URL | Cosa fa |
|--------|-----|---------|
| `GET` | `/` | Apre il configuratore (SPA React) |
| `GET` | `/c/{slug}` | Link commerciale → redirect con tracciamento |
| `POST` | `/api/vehicle-form` | Invia i dati della flotta (Fase 1) |
| `GET` | `/servizi/{token}` | Apre il form servizi (Fase 2) |
| `POST` | `/api/servizi/{token}` | Salva i servizi scelti |
| `GET` | `/servizi/{token}/successo` | Pagina di conferma |
| `GET` | `/servizi/{token}/pdf` | Scarica il preventivo PDF |

---

## 🔒 Sicurezza

- **Rate Limiting** sull'endpoint di invio form
- **HTTP Security Headers:** `X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`, `HSTS` in produzione
- **Doppia validazione:** client-side (React) + server-side (Laravel)
- **Token univoci** per l'accesso ai form servizi
- **CSRF** gestito nativamente da Laravel

---

## 🚀 Setup e Installazione

**Prerequisiti:** PHP 8.3+, Composer, Node.js 18+, MySQL/PostgreSQL

### Setup rapido

```bash
git clone <url-repository>
cd gtfleet365
composer run setup
```

### Setup manuale

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
# Configura database e variabili nel file .env
php artisan migrate
npm run build
```

---

## ⚡ Comandi

```bash
# Sviluppo (Laravel + Vite + Queue + Log in parallelo)
composer run dev

# Build produzione
npm run build

# Test
composer run test
```

---

## 🐳 Deploy (Render)

L'app è deployata su **Render** come Web Service Docker:

1. **Stage 1** — Node.js compila gli asset React con Vite
2. **Stage 2** — PHP 8.4 + Apache con estensioni necessarie
3. **Avvio** — Cache, migrazioni automatiche, Apache

---

## ⚙️ Variabili d'Ambiente principali

| Variabile | Descrizione |
|-----------|-------------|
| `APP_URL` | URL base dell'app |
| `DB_CONNECTION` | Driver database (`mysql` / `pgsql`) |
| `HUBSPOT_ACCESS_TOKEN` | Token API privato HubSpot |
| `MAIL_MAILER` | Driver email (`mailtrap-sdk` / `log` / `smtp`) |
| `MAIL_CAPI_COMMERCIALI` | Email CC responsabili (separate da virgola) |

Vedi `.env.example` per l'elenco completo.

---

*Sviluppato da Pier @ MacNil, Gravina in Puglia.*
