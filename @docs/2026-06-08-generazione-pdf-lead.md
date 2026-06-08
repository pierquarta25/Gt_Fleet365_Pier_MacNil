# Progetto: Generazione Automatica PDF Riepilogo Flotta

**Data:** 2026-06-08  
**Stato:** In attesa di approvazione  

## Descrizione della Funzionalità
Si desidera che, al momento della ricezione del lead configurato, il sistema generi automaticamente un file PDF riepilogativo contenente i dettagli della flotta (identico allo Step 3 del configuratore) e lo alleghi all'email inviata al commerciale e agli amministratori. 

Questo permetterà ai commerciali di disporre subito di un documento formale e stampabile da poter inviare al cliente o archiviare nei sistemi aziendali.

---

## Architettura e Flusso di Lavoro

### 1. Installazione della Libreria
- Verrà installato il pacchetto standard per Laravel: `barryvdh/laravel-dompdf`.
- Questa libreria converte il codice HTML in file PDF.

### 2. Differenziazione Mail / PDF (Gestione Immagini)
- Le immagini dei veicoli hanno requisiti di caricamento diversi:
  - **Email**: Hanno bisogno di un URL HTTP assoluto (es. `http://localhost:8000/media/AUTO.png`) per poter essere scaricate dai client email.
  - **PDF (dompdf)**: Funziona meglio caricando le immagini direttamente dal disco locale usando il path assoluto del server (es. `/path/to/public/media/AUTO.png`) tramite la funzione `public_path()`, per evitare problemi di timeout di rete locali o configurazioni del DNS.
- Risolveremo questo problema introducendo la variabile `$isPdf` nella vista Blade `emails.lead-summary`:
  - Se `$isPdf` è `true`, usiamo il percorso locale `public_path($item['img'])`.
  - Se `$isPdf` è `false` (invio email), usiamo l'URL pubblico `config('app.url') . $item['img']`.

### 3. Generazione e Allegato Automatico
- Modificheremo la classe Mailable `LeadSummaryMail`:
  - Nel metodo `content()`, carichiamo la vista impostando `isPdf = false`.
  - Nel metodo `attachments()`, utilizziamo il generatore `Pdf::loadView` per compilare la stessa vista ma impostando `isPdf = true`, e alleghiamo il PDF risultante come `riepilogo-flotta.pdf`.
- Non è necessaria alcuna modifica al controller `VehicleFormController.php`, in quanto l'allegato viene gestito ed assemblato in autonomia direttamente dalla classe Mail di Laravel.

---

## Modifiche Proposte

### Backend
- **[MODIFY] [composer.json](file:///Users/pierfilippoquartarella/GT_FLEET365/gt_fleet_form/gtfleet365/composer.json)**:
  - Aggiungere `"barryvdh/laravel-dompdf": "^3.0"` (o versione compatibile con Laravel 11/13).
- **[MODIFY] [LeadSummaryMail.php](file:///Users/pierfilippoquartarella/GT_FLEET365/gt_fleet_form/gtfleet365/app/Mail/LeadSummaryMail.php)**:
  - Importare `Barryvdh\DomPDF\Facade\Pdf`.
  - Passare `'isPdf' => false` a livello di `Content` view data.
  - Implementare la generazione del PDF e l'allegato `Attachment::fromData` all'interno del metodo `attachments()`.
- **[MODIFY] [lead-summary.blade.php](file:///Users/pierfilippoquartarella/GT_FLEET365/gt_fleet_form/gtfleet365/resources/views/emails/lead-summary.blade.php)**:
  - Modificare il tag `<img>` dei veicoli per selezionare dinamicamente il percorso locale (`public_path`) o quello HTTP in base al valore della variabile `$isPdf`.

---

## Piano di Verifica

1. **Installazione dipendenze**: Eseguire l'installazione e confermare il corretto caricamento delle classi.
2. **Esecuzione dei test automatizzati**: Verificare che i test continuino a passare (verrà simulato l'invio mail e la generazione dell'allegato).
3. **Verifica su Mailtrap**: Effettuare un invio dal browser e verificare che su Mailtrap arrivi l'email con in allegato il file `riepilogo-flotta.pdf`. Scaricare il PDF e verificare che si veda perfettamente, comprensivo di immagini e testi.
