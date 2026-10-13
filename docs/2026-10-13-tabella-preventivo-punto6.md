# Tabella Preventivo Punto 6 – Valorizzazione Economica

**Data:** 2026-10-13  
**Feature:** Inserimento tabella dinamica Excel-like nella sezione 6 del preventivo DOCX/PDF

---

## Problema

Il punto 6 del preventivo completo ("VALORIZZAZIONE ECONOMICA DELLA FORNITURA GT FLEET 365") attualmente non mostra nessuna tabella. Il `QuoteGeneratorService.php` costruisce una tabella tramite PhpWord e tenta di iniettarla con `setComplexBlock('quote_table', $table)`, ma il **placeholder `${quote_table}` non esiste nel template Word** (`template_offerta.docx`). Il documento ha 35 paragrafi vuoti al suo posto.

Attualmente MacNil risolve manualmente con screenshot del file Excel del listino mezzi.

---

## Obiettivo

Generare automaticamente, al punto 6, una **tabella identica allo stile del listino Excel MacNil** con:

- Le righe dei servizi/hardware scelti dal commerciale durante la configurazione
- I prezzi inseriti nel configuratore (non hardcoded)
- Quantità corrette per ogni servizio
- Totali aggregati (canoni annuali + hardware una tantum)

---

## Soluzione Tecnica

### Approccio: Iniezione XML diretta nel DOCX

Invece di usare `setComplexBlock` (che richiede un placeholder nel template), generiamo l'XML della tabella Word (OOXML) e lo **iniettiamo direttamente** nel `document.xml` del DOCX, sostituendo i 35 paragrafi vuoti che si trovano tra la sezione "VALORIZZAZIONE ECONOMICA" e "CONDIZIONI DI FORNITURA".

**Motivo:** Il template Word è un file prodotto da LibreOffice/Word e il PhpWord TemplateProcessor funziona solo se il placeholder non viene spezzato dal word processor in più `<w:r>` separati. L'iniezione diretta via ZipArchive è l'approccio più robusto.

### Struttura della tabella (12 colonne – uguale al listino Excel MacNil)

| # | Colonna | Contenuto |
|---|---------|-----------|
| 1 | Codice Servizio | Nome del servizio (es. GT FLEET 365 BASE) |
| 2 | Hw o Servizio | `servizio` o `hardware` |
| 3 | Descrizione | Bullet list delle feature |
| 4 | Prezzo di Listino al Pubblico | `list_price` dal configuratore |
| 5 | Prezzo Scontato | `discounted_price` |
| 6 | Prezzo Offerta LAST MINUTE *(giallo)* | `offer_price` / `final_price` |
| 7 | Periodicità Pagamento | annuale / una tantum |
| 8 | Durata Contrattuale (mesi) | 24 o 1 |
| 9 | Quantità | `qty × vehicle_qty` |
| 10 | Modalità Pagamento | "Canone Mensile Bimestrale Anticipato" |
| 11 | Prezzo Mensile Offerta *(giallo)* | `offer_price / 12` |
| 12 | Totale Annuale | `prezzo_mensile × 12 × qty` |

**Righe totali:** TOTALE CANONI ANNUALI e TOTALE HARDWARE UNA TANTUM

---

## Piano di Implementazione

### Step 1 – Aggiornare `template_offerta.docx`

Aggiungere il placeholder `${quote_table}` in un paragrafo all'interno dei 35 paragrafi vuoti tra VALORIZZAZIONE ed il prossimo titolo. Questo si fa **via script Python** che modifica il `document.xml` dentro lo ZIP del DOCX.

> In alternativa (più robusto): eliminare i paragrafi vuoti e iniettare l'XML della tabella direttamente senza placeholder, trovando il punto di inserimento tramite un testo ancora nel documento (come il testo `VALORIZZAZIONE ECONOMICA`).

### Step 2 – Aggiornare `QuoteGeneratorService.php`

Modificare il metodo `generateQuote()`:
1. Costruire la tabella come **XML OOXML puro** (stringa) invece di oggetto PhpWord.
2. Nella fase di manipolazione ZipArchive (già esistente nel codice), iniettare l'XML della tabella nel punto corretto del `document.xml`.
3. Rimuovere/sostituire il blocco dei 35 paragrafi vuoti tra `VALORIZZAZIONE ECONOMICA` e `CONDIZIONI DI FORNITURA`.
4. Rimuovere la chiamata `setComplexBlock` (non più necessaria).

### Step 3 – Test

- Generare un preventivo di test con 2-3 servizi misti (annuali + hardware)
- Verificare che la tabella appaia al punto 6 nel DOCX e nel PDF convertito
- Verificare che i totali siano corretti

---

## File Coinvolti

- `app/Services/QuoteGeneratorService.php` ← logica di generazione
- `resources/templates/template_offerta.docx` ← template Word

---

## Note

- I prezzi nella tabella usano i dati inseriti dal commerciale nel configuratore (`$srv['price']`, `$srv['discount']`, `$srv['final_price']`), non i prezzi hardcoded nel `$pricing` array (che vengono usati solo come fallback).
- I colori gialli (celle highlight) vengono mantenuti nell'XML OOXML con `<w:shd w:val="clear" w:fill="FFFF00"/>`.
- **Layout Verticale Nativo (Portrait):** la struttura delle sezioni del documento originale è preservata al 100% (nessun cambio di orientamento o `sectPr` aggiunto). La tabella a 12 colonne è dimensionata a 9.750 twip con `w:tblLayout w:type="fixed"`, colonne dei costi ristrette al minimo necessario per le cifre numeriche, e font compatto (5.5pt / 5pt) per garantire leggibilità e perfetto inserimento nei margini della pagina A4 verticale.
