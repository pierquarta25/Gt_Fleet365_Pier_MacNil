# Progetto: Configurazione del Deploy su Render come Web Service

**Data:** 2026-06-24  
**Stato:** In attesa di approvazione  

---

## 1. Descrizione delle Modifiche Richieste
L'utente desidera distribuire l'applicazione monolitica (Laravel 13 + React 19) su **Render** in modo completamente **gratuito**. 
Per fare ciò senza separare l'app in due progetti distinti (Frontend e Backend), configureremo un **Web Service gratuito** su Render che utilizzi un container **Docker**.

Questo documento descrive la configurazione di:
1. Un **Dockerfile** multi-stage per compilare gli asset React e configurare l'ambiente PHP 8.4 con Apache.
2. Una configurazione custom per il server web Apache per servire la cartella `public` di Laravel.
3. Uno script di avvio (`render-start.sh`) per gestire cache e migrazioni automatiche su Render.

---

## 2. Dettaglio delle Modifiche

### Configurazione Docker

* **[NEW] [Dockerfile](file:///Users/pierfilippoquartarella/GT_FLEET365/gt_fleet_form/gtfleet365/Dockerfile)**:
  - Definizione di uno Stage 1 per Node.js per installare i pacchetti npm e lanciare `npm run build` compilando gli asset tramite Vite.
  - Definizione di uno Stage 2 basato su `php:8.4-apache`.
  - Installazione delle estensioni PHP necessarie: `pdo`, `pdo_pgsql`, `pgsql`, `zip`, `gd`.
  - Installazione di Composer copiato dall'immagine ufficiale.
  - Abilitazione del modulo Apache `mod_rewrite`.
  - Configurazione delle cartelle scrivibili (`storage` e `bootstrap/cache`) con i permessi corretti per `www-data`.

* **[NEW] [apache.conf](file:///Users/pierfilippoquartarella/GT_FLEET365/gt_fleet_form/gtfleet365/docker/apache.conf)**:
  - File di configurazione del virtual host di Apache per far puntare il server a `/var/www/html/public` e abilitare le direttive `.htaccess` di Laravel.

* **[NEW] [render-start.sh](file:///Users/pierfilippoquartarella/GT_FLEET365/gt_fleet_form/gtfleet365/render-start.sh)**:
  - Script bash che ottimizza l'avvio su Render eseguendo:
    ```bash
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan migrate --force
    apache2-foreground
    ```

---

## 3. Piano di Verifica

1. **Test di Regressione Locali**:
   - Prima del push, verificheremo che tutti i test dell'app passino localmente:
     ```bash
     composer run test
     ```

2. **Verifica Deploy su Render**:
   - Una volta pushate le modifiche su GitHub, l'utente collegherà il repository a Render.
   - Si controllerà il log della build per verificare che:
     - Vite compili correttamente gli asset React.
     - Composer installi le dipendenze di produzione.
     - All'avvio, vengano eseguite con successo le migrazioni del database e che il server Apache rimanga in esecuzione.
