# Progetto: Notifiche Email per Agenti Marketing

**Data:** 2026-05-28  
**Stato:** In attesa di approvazione  
**Obiettivo:** Inviare un'email di riepilogo automatica all'agente marketing quando un'azienda completa la configurazione della flotta.

---

## 🎯 Requisiti
1. Identificare l'agente marketing tramite un percorso pulito nell'URL (es. `/agente/nome.cognome@macnil.it`).
2. Catturare questo dato nel frontend React e includerlo nella richiesta finale.
3. Al termine dell'invio dei dati a HubSpot, il sistema deve inviare una mail all'agente con il riepilogo completo:
   - Dati dell'azienda.
   - Tipologia e quantità di mezzi selezionati.
   - Note aggiuntive.

---

## 🛠️ Piano di Implementazione

### 1. Frontend (React)
- Modificare `VehicleForm.jsx` per leggere il parametro `agent` dall'URL all'avvio dell'app.
- Includere l'email dell'agente nell'oggetto JSON inviato al controller Laravel.

### 2. Backend (Laravel)
- Creare una classe di notifica `App\Mail\LeadSummaryMail`.
- Creare un template HTML/Blade per l'email professionale, coerente con il brand GT Fleet 365.
- Aggiornare `VehicleFormController@store` per:
    - Ricevere l'email dell'agente.
    - Inviare la mail subito dopo la creazione del lead su HubSpot.

### 3. Configurazione
- Configurare il driver email in `.env` (inizialmente impostato su `log` per i test, poi SMTP per la produzione).

---

## 🧪 Piano di Test
1. Aprire l'app con `?agent=test@example.com`.
2. Compilare il form e inviare.
3. Verificare nei log di Laravel (`storage/logs/laravel.log`) che l'email sia stata "inviata" correttamente con tutto il riepilogo.

---

## 📅 Passaggi Successivi
1. Approvazione del presente documento.
2. Generazione della classe Mail in Laravel.
3. Aggiornamento logica di invio nel controller.
4. Aggiornamento stato del form in React.
