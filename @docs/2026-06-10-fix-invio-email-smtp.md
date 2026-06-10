# Progetto: Risoluzione Errore Invio Email e Gestione Eccezioni SMTP

**Data:** 2026-06-10  
**Stato:** In attesa di approvazione  

## 1. Diagnosi del Problema
Dai log dell'applicazione (`storage/logs/laravel.log`), emerge che ad ogni tentativo di invio dell'ordine viene sollevata la seguente eccezione:
`Connection could not be established with host "sandbox.smtp.mailtrap.io:2525": stream_socket_client(): Unable to connect to sandbox.smtp.mailtrap.io:2525 (Operation timed out)`

Abbiamo effettuato un test di connettività di rete verso `sandbox.smtp.mailtrap.io` su tutte le porte supportate (2525, 587, 465, 25, 80) e tutte hanno restituito un timeout. Questo indica che la rete o il provider Internet locale bloccano il traffico SMTP in uscita verso Mailtrap (situazione molto comune su reti residenziali o aziendali).

Dato che l'invio dell'email è attualmente sincrono all'interno del metodo `store()` del controller, qualsiasi fallimento di connessione SMTP fa fallire l'intera richiesta HTTP, restituendo un errore 500 al client e mostrando a schermo il popup rosso:
*"Ops! Qualcosa è andato storto. Non è stato possibile inviare i dati..."*

---

## 2. Soluzione Proposta

### A. Gestione Robusta dell'Eccezione (try-catch)
Per evitare che un problema di rete o del server di posta blocchi l'invio dell'ordine (e l'eventuale salvataggio su HubSpot), avvolgeremo la chiamata di invio email in un blocco `try-catch`. In questo modo:
1. L'utente vedrà la schermata di successo dell'ordine anche se l'email non può essere recapitata immediatamente.
2. L'errore verrà registrato nei log di Laravel per consentire il debug, senza compromettere la UX.

### B. Uso del Driver 'log' in Locale per il Test delle Mail
Dato il blocco di rete del provider, per poter comunque verificare e testare l'aspetto e i dati delle email inviate, modificheremo la configurazione nel file `.env` per utilizzare il driver `log` di Laravel in ambiente locale. Con questa impostazione, Laravel scriverà l'intero contenuto delle email direttamente nel file `storage/logs/laravel.log` invece di tentare la connessione a Mailtrap.

---

## 3. Modifiche Dettagliate

### Backend
* **[MODIFY] [VehicleFormController.php](file:///Users/pierfilippoquartarella/GT_FLEET365/gt_fleet_form/gtfleet365/app/Http/Controllers/VehicleFormController.php)**:
  - Integrare un blocco `try-catch` attorno a `$mail->send(...)`.
  - In caso di eccezione, loggare l'errore con `\Illuminate\Support\Facades\Log::error(...)` in modo da non bloccare la risposta JSON di successo al frontend.

### Configurazione
* **[MODIFY] [.env](file:///Users/pierfilippoquartarella/GT_FLEET365/gt_fleet_form/gtfleet365/.env)**:
  - Cambiare `MAIL_MAILER=smtp` in `MAIL_MAILER=log` per l'ambiente locale.

---

## 4. Piano di Verifica

1. **Test del Flusso di Invio**:
   - Compilare il modulo dal frontend e cliccare su Invia.
   - Verificare che il form venga inviato con successo (schermata di successo verde) senza generare più l'errore del popup rosso.
2. **Verifica della Mail nei Log**:
   - Aprire il file `storage/logs/laravel.log` e verificare che in coda ci sia l'email registrata dal driver `log` con tutti i dettagli del lead e l'allegato.
