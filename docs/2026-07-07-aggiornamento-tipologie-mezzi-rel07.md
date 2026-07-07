# Aggiornamento Tipologie Mezzi - Rel.07

## Descrizione
Aggiornamento del catalogo veicoli in base al nuovo documento **GT_FLEET_365_Tipologia_mezzi_202503_Rel.07.pdf**.

## Piano di Sviluppo

### Step 1 - Correzione typo
- Correggere `SEMIRMORCHIO` → `SEMIRIMORCHIO` nel nome del veicolo in `vehicleTypes.js` (riga 48)

### Step 2 - Aggiunta nuovo mezzo
- Aggiungere `CARRO FUNEBRE` nella categoria "Trasporto Pesante & Semirimorchi" in `vehicleTypes.js`
- Copiare l'immagine `carro_funebre.png` come `CARRO_FUNEBRE.png` in `public/media/`

### Step 3 - Aggiornamento immagini
- Sostituire 3 immagini aggiornate:
  - `RIMORCHIO_FRIGO.png`
  - `RIMORCHIO_per_MOTRICI.png`
  - `SEMIRIMORCHIO_FRIGO.png`

### Step 4 - Verifica
- Verifica visiva del form per controllare le nuove immagini e il nuovo mezzo
