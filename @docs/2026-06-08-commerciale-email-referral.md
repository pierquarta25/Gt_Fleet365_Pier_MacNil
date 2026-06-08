# Progetto: Invio Email al Commerciale Referente

**Data:** 2026-06-08  
**Stato:** In attesa di approvazione  

## Descrizione del Problema
Attualmente, quando un cliente compila il configuratore tramite un link personalizzato inviato da un commerciale (ad esempio `/c/mario-rossi`), la sessione di Laravel memorizza l'email del commerciale. Tuttavia, se la sessione scade, se si verificano problemi di trasmissione dei cookie o se l'utente compila il form dopo molto tempo, il backend non riesce a risalire al commerciale e invia l'email esclusivamente all'indirizzo generico del reparto commerciale.

L'obiettivo è rendere persistente e robusto il tracciamento del commerciale di riferimento (referente), salvandolo nel browser del client (tramite query parameter e `localStorage`) e trasmettendolo esplicitamente nell'API di invio dati. Una volta inviata la configurazione, il sistema deve recapitare l'email riepilogativa direttamente al commerciale di riferimento (oltre a caricarla su HubSpot).

---

## Architettura e Flusso di Lavoro

### 1. Reindirizzamento e Tracciamento
- Quando il cliente visita il link `/c/{slug}`:
  1. Il backend associa la sessione all'email dell'agente.
  2. Effettua un redirect a `/?agent={slug}` per esporre lo slug al client.
- Il frontend React intercetta il parametro `agent` nell'URL, lo estrae e lo memorizza nello stato `clientData`, che a sua volta viene persistito in `localStorage`.
- In questo modo, lo slug dell'agente rimane salvato nel browser del cliente anche in caso di ricaricamento pagina o sessione Laravel scaduta.

### 2. Invio del Form
- Al momento del `handleSubmit`, il client invia via POST sia i dati inseriti che il campo `agent_slug` all'interno del payload `client`.
- Il controller del backend riceve il payload ed effettua una ricerca a cascata per trovare l'agente commerciale:
  1. Ricerca tramite `agent_slug` fornito nel payload JSON.
  2. Ricerca tramite `agent_email` (se presente) fornito nel payload JSON.
  3. Ricerca tramite l'email salvata nella sessione di Laravel (`session('agent_email')`).
- Una volta identificato l'agente commerciale, l'email di notifica viene inviata direttamente alla sua casella postale (`$agent->email`).
- Se l'agente è presente, l'email commerciale generica (`config('mail.commerciale_generica')`) viene messa in copia carbone (`CC`), insieme agli indirizzi dei capi commerciali (`config('mail.capi_commerciali')`).
- Se nessun agente viene identificato, l'email viene inviata alla mail commerciale generica come destinatario principale.

---

## Modifiche Proposte

### Backend
- **[MODIFY] [VehicleFormController.php](file:///Users/pierfilippoquartarella/GT_FLEET365/gt_fleet_form/gtfleet365/app/Http/Controllers/VehicleFormController.php)**:
  - Modificare `handleSlug` per effettuare il redirect a `/?agent={slug}`.
  - Modificare `store` per accettare e convalidare `client.agent_slug` nel request validation.
  - Implementare la risoluzione robusta dell'agente commerciale a partire dallo slug del payload, dall'email del payload o dalla sessione.

### Frontend
- **[MODIFY] [VehicleForm.jsx](file:///Users/pierfilippoquartarella/GT_FLEET365/gt_fleet_form/gtfleet365/resources/js/components/VehicleForm.jsx)**:
  - Aggiornare l'inizializzazione di `clientData` per estrarre il parametro query `agent` dall'URL usando `URLSearchParams`.
  - Assicurarsi che `agent_slug` sia mantenuto nel `localStorage` insieme agli altri dati del cliente.

---

## Piano di Verifica

1. **Test di Reindirizzamento**: 
   Visitare l'indirizzo `/c/pier-quartarella` e verificare che reindirizzi a `/?agent=pier-quartarella`.
2. **Test di Persistenza in LocalStorage**:
   Verificare tramite gli strumenti per sviluppatori del browser (Application -> Local Storage) che la chiave `gtfleet_clientData` contenga il campo `"agent_slug": "pier-quartarella"`.
3. **Test di Invio Dati ed Email**:
   Inviare un modulo di test e verificare nel file `storage/logs/laravel.log` (poiché il driver mail locale è impostato su `log`) che l'email venga inviata a `info@getpierfilippo.com` con in copia la mail commerciale generica e i capi commerciali.
