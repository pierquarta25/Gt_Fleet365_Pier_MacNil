# Tabella Preventivo Dettagliata - Nuovo Layout 12 Colonne

## Obiettivo

Sostituire la tabella attuale del preventivo DOCX (3 colonne: Servizio, Quantità, Totale) con una tabella dettagliata a 12 colonne che rispecchia il layout del Listino "Bibbia" di MacNil.

## Le 12 Colonne della Nuova Tabella

| # | Intestazione | Contenuto |
|---|---|---|
| 1 | **Codice Servizio** | Nome del servizio/pacchetto |
| 2 | **Hardware o Servizio** | `servizio` o `hardware` |
| 3 | **Descrizione Prodotto e Servizi Forniti** | Elenco puntato funzionalità |
| 4 | **Prezzo di Listino al Pubblico** | Prezzo pieno |
| 5 | **Prezzo Servizio e Fornitura Scontato** | Prezzo scontato |
| 6 | **Prezzo Offerta LAST MINUTE** *(giallo)* | Prezzo offerta |
| 7 | **Periodicità Pagamento** | annuale / una tantum |
| 8 | **Durata Contrattuale in mesi** | Default: 24 |
| 9 | **Quantità servizi** | Quantità mezzi |
| 10 | **Periodicità Pagamento** | Canone Mensile Bimestrale |
| 11 | **Prezzo Offerta LM mensile** *(giallo)* | Prezzo mensile |
| 12 | **Totale Annuale per Canoni Mensili** | Totale annuale |

## Modifiche

### File: `app/Services/QuoteGeneratorService.php`

1. Espandere `$pricing` con: list_price, discounted_price, offer_price, description, contract_months
2. Ricostruire tabella da 3 a 12 colonne
3. Header fisso, dati dinamici, colonne 6 e 11 con sfondo giallo
4. Logica: se prezzo = 0 mostra "OMAGGIO"
