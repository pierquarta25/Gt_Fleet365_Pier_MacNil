# Supporto Multilingua (i18n) - Italiano + Inglese

## Obiettivo

Aggiungere il supporto alla lingua inglese in tutto il progetto GT Fleet 365, mantenendo l'italiano come lingua predefinita. L'utente potrà cambiare lingua tramite un selettore visuale nell'interfaccia.

## Ambito di intervento

Il progetto ha testi hardcoded in italiano distribuiti in **10 file** principali:

### Frontend (React)
1. `resources/js/components/VehicleForm.jsx` — ~540 righe, tutte le label, messaggi, placeholder, errori
2. `resources/js/data/vehicleTypes.js` — titoli categorie veicoli

### Backend (Blade + PHP)
3. `resources/views/app.blade.php` — titoli SEO, meta description
4. `resources/views/emails/lead-summary.blade.php` — email di riepilogo lead
5. `resources/views/services/form.blade.php` — form configurazione servizi
6. `resources/views/services/success.blade.php` — pagina successo
7. `resources/views/pdf/service-summary.blade.php` — template PDF riepilogo
8. `config/vehicle_services.php` — nomi servizi (~50+ stringhe)
9. `app/Http/Controllers/VehicleFormController.php` — messaggi di errore validazione
10. `app/Http/Controllers/ServiceFormController.php` — messaggi di risposta

## Approccio tecnico proposto

### Frontend: React Context + file JSON di traduzione

1. **File di traduzione JSON** (`resources/js/i18n/it.json`, `resources/js/i18n/en.json`)
   - Struttura chiave-valore gerarchica per tutte le stringhe UI
   - Es: `{ "step1": { "title": "Configurazione Flotta", ... }, "step2": { ... } }`

2. **LanguageContext** (`resources/js/i18n/LanguageContext.jsx`)
   - React Context con `useLanguage()` hook
   - Funzione helper `t('step1.title')` per accedere alle traduzioni
   - Persistenza lingua nel `localStorage`

3. **Selettore lingua** nel header del form
   - Toggle bandiera 🇮🇹/🇬🇧 compatto e integrato nel design

### Backend: File di traduzione Laravel

1. **File di traduzione Laravel** (`lang/it.json`, `lang/en.json`)
   - Per le viste Blade (email, PDF, form servizi, pagina successo)

2. **Middleware per la lingua**
   - Rileva la lingua dalla query string (`?lang=en`), dalla sessione, o default `it`
   - Setta `app()->setLocale()` automaticamente

3. **Config servizi multilingua** (`config/vehicle_services.php`)
   - I nomi dei servizi restano invariati (sono nomi commerciali in italiano, NON vanno tradotti)

## Stima delle stringhe da tradurre

| Area | Stringhe (circa) |
|------|-------------------|
| VehicleForm.jsx (label, placeholder, errori, bottoni) | ~60 |
| vehicleTypes.js (titoli categorie) | 3 |
| app.blade.php (SEO) | 4 |
| lead-summary.blade.php (email) | ~15 |
| services/form.blade.php | ~15 |
| services/success.blade.php | ~8 |
| pdf/service-summary.blade.php | ~12 |
| VehicleFormController.php (validazione) | ~5 |
| ServiceFormController.php | ~3 |
| **Totale** | **~125** |

## Nota sui nomi dei servizi/veicoli

I nomi dei veicoli (es. "AUTO", "FURGONE", "SEMIRIMORCHIO") e dei servizi (es. "GT FLEET 365 PREMIUM") sono **nomi commerciali** e NON verranno tradotti. Solo le label dell'interfaccia e i messaggi di sistema verranno tradotti.
