# Link ai Servizi - Form Servizi per Mezzo

## Obiettivo
Aggiungere nella mail di riepilogo lead un link **"Link ai Servizi"** sotto ogni card di mezzo selezionato. Il link apre un **form web dedicato** dove il commerciale può configurare i servizi aggiuntivi specifici per quel tipo di mezzo. I dati raccolti vengono salvati in **Database + PDF + HubSpot**.

## Contesto
Attualmente la mail di riepilogo (`lead-summary.blade.php`) mostra le card dei mezzi selezionati con immagine, nome e quantità. Il commerciale riceve questa mail ma non ha modo di aggiungere servizi aggiuntivi legati a ciascun mezzo.

---

## Piano di Implementazione

### FASE 1 — Database & Backend

#### 1.1 Migrazione: tabella `service_requests`
Crea una nuova tabella per salvare le richieste servizi:

```
- id
- lead_id (FK → leads.id, nullable) — collegamento al lead originale
- vehicle_type (string) — id del tipo di mezzo (es. "furgone", "auto")
- vehicle_name (string) — nome leggibile del mezzo
- vehicle_qty (integer) — quantità dal lead originale
- services (json) — array dei servizi selezionati
- notes (text, nullable) — note aggiuntive del commerciale
- agent_email (string, nullable) — email del commerciale che compila
- token (string, unique) — token univoco per accesso sicuro al form
- hubspot_deal_id (string, nullable) — ID del deal aggiornato su HubSpot
- status (enum: pending, completed) — stato della richiesta
- completed_at (timestamp, nullable)
- timestamps
```

#### 1.2 Model `ServiceRequest`
- Relazione `belongsTo` con `Lead`
- Cast `services` → `array`
- Scope per token

#### 1.3 Controller `ServiceFormController`
Due rotte principali:
- `GET /servizi/{token}` → mostra il form pre-compilato con i dati del mezzo
- `POST /api/servizi/{token}` → salva i servizi selezionati

Logica del `store`:
1. Valida i dati in input
2. Salva nel database (`service_requests`)
3. Genera un PDF di riepilogo servizi
4. Aggiorna/crea una nota sul Deal HubSpot collegato
5. Restituisce conferma

#### 1.4 Generazione Token
Nel `VehicleFormController::store()`, dopo l'invio della mail, generare un token univoco per ogni mezzo selezionato e salvarlo nella tabella `service_requests` con status `pending`.

---

### FASE 2 — Template Email

#### 2.1 Modifica `lead-summary.blade.php`
Sotto ogni `vehicle-card`, aggiungere un link stilizzato:

```html
<a href="{{ url('/servizi/' . $item['service_token']) }}" 
   style="...stile inline per email...">
   🔗 Link ai Servizi
</a>
```

#### 2.2 Modifica `LeadSummaryMail.php`
Passare i token dei servizi insieme ai dati dei veicoli nella vista.

#### 2.3 Modifica `VehicleFormController::store()`
Prima dell'invio email:
1. Per ogni mezzo selezionato, creare un record `ServiceRequest` con token univoco
2. Aggiungere il `service_token` ai dati del veicolo passati alla mail

---

### FASE 3 — Form Web Servizi (Frontend)

#### 3.1 Nuova vista Blade `resources/views/services/form.blade.php`
Pagina standalone (non React) con:
- Header con logo MacNil e titolo "Configurazione Servizi"
- Info del mezzo (immagine, nome, quantità) pre-compilate
- Elenco servizi selezionabili tramite **checkbox** (specifici per tipo di mezzo)
- Campo note aggiuntive
- Pulsante "Invia Configurazione"
- Stile coerente con il design system del progetto (`#0052BD`, Mobile First)

#### 3.2 Lista Servizi per Tipo Mezzo
Creare un file di configurazione `config/vehicle_services.php` con la mappatura:

```php
return [
    'default' => [
        'Localizzazione GPS',
        'Tachigrafo digitale',
        'Sensore temperatura',
        'Telecamera bordo',
        'Immobilizer',
        'Sensore carburante',
        'Stile di guida (Driving Behaviour)',
        'Manutenzione predittiva',
    ],
    // Override per tipi specifici (se necessario in futuro)
    'furgone_frigo' => [
        'Localizzazione GPS',
        'Sensore temperatura',
        'Allarme apertura porte',
        'Monitoraggio catena del freddo',
        'Telecamera bordo',
        'Immobilizer',
    ],
    // ... altri tipi
];
```

> **NOTA**: La lista servizi sopra è un esempio. **L'utente deve confermare quali servizi inserire** prima dell'implementazione.

#### 3.3 Gestione stato form
- Token scaduto/già usato → messaggio di errore
- Form compilato con successo → pagina di conferma
- Loading spinner durante l'invio

---

### FASE 4 — HubSpot Integration

#### 4.1 Aggiornamento `HubSpotService`
Aggiungere metodo `addServiceNote($dealId, $vehicleName, $services)`:
- Crea una **Nota** sul Deal esistente con i servizi selezionati per quel mezzo
- Se il Deal non esiste, logga un warning e prosegue

---

### FASE 5 — PDF Servizi

#### 5.1 Template PDF `resources/views/pdf/service-summary.blade.php`
PDF con:
- Intestazione MacNil
- Dettagli mezzo (nome, quantità)
- Elenco servizi selezionati
- Note aggiuntive
- Data e ora compilazione

Il PDF viene salvato nello storage e opzionalmente inviato via email come conferma.

---

## File da Creare/Modificare

### Nuovi File
| File | Descrizione |
|------|-------------|
| `database/migrations/xxxx_create_service_requests_table.php` | Migrazione tabella servizi |
| `app/Models/ServiceRequest.php` | Model Eloquent |
| `app/Http/Controllers/ServiceFormController.php` | Controller form servizi |
| `resources/views/services/form.blade.php` | Form web standalone |
| `resources/views/services/success.blade.php` | Pagina conferma |
| `resources/views/services/expired.blade.php` | Pagina token scaduto |
| `resources/views/pdf/service-summary.blade.php` | Template PDF servizi |
| `config/vehicle_services.php` | Mappatura servizi per mezzo |

### File da Modificare
| File | Modifica |
|------|----------|
| `resources/views/emails/lead-summary.blade.php` | Aggiungere link "Link ai Servizi" sotto ogni card |
| `app/Mail/LeadSummaryMail.php` | Passare i token servizi alla vista |
| `app/Http/Controllers/VehicleFormController.php` | Creare i record ServiceRequest con token |
| `app/Services/HubSpotService.php` | Aggiungere metodo per note servizi |
| `routes/web.php` | Aggiungere rotte per il form servizi |

---

## Domande Aperte

> [!IMPORTANT]
> **Quali servizi inserire?** La lista servizi sopra è un esempio generico. Hai una lista precisa dei servizi che devono comparire nel form? Devono variare per tipo di mezzo o sono uguali per tutti?

> [!IMPORTANT]
> **Token con scadenza?** Il link nella mail deve avere una scadenza (es. 30 giorni) oppure deve funzionare sempre?

> [!NOTE]
> **Email di conferma?** Dopo che il commerciale compila il form servizi, deve ricevere un'email di conferma con il PDF allegato?

---

## Piano di Verifica

### Test Automatici
```bash
composer run test
```
- Test del controller `ServiceFormController` (form display, submission, token validation)
- Test della generazione token nel `VehicleFormController`

### Verifica Manuale
1. Completare un lead nel configuratore → verificare che la mail contenga i link "Link ai Servizi"
2. Cliccare il link → verificare che il form si apra con i dati del mezzo
3. Compilare e inviare il form → verificare salvataggio DB, generazione PDF, aggiornamento HubSpot
4. Provare un token già usato → verificare messaggio di errore/scaduto
