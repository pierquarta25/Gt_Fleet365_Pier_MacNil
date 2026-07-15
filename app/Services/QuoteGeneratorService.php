<?php

namespace App\Services;

use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\ServiceRequest;

class QuoteGeneratorService
{
    /**
     * Mappatura dei prezzi dal Listino "Bibbia"
     * Formato: ID_SERVIZIO => ['name' => ..., 'price' => ..., 'type' => 'servizio|hardware', 'period' => 'annuale|una tantum']
     */
    protected $pricing = [
        // --- BASE PACKAGES ---
        'base_loc' => ['name' => "GT FLEET 365 BASE (LOC)", 'price' => 128.00, 'type' => 'servizio', 'period' => 'annuale'],
        'plus_loc_sic' => ['name' => "GT FLEET 365 PLUS (LOC + SEC)", 'price' => 153.60, 'type' => 'servizio', 'period' => 'annuale'],
        // Valori di default temporanei per gli altri basati su listino
        'app_fleet_manager' => ['name' => "APP GT FLEET 365 - FLEET MANAGER", 'price' => 24.96, 'type' => 'servizio', 'period' => 'annuale'],
        'app_driver' => ['name' => "APP GT FLEET 365 - DRIVER", 'price' => 64.00, 'type' => 'servizio', 'period' => 'annuale'],
        'manutenzioni_scadenze' => ['name' => "GT FLEET 365 SERVIZIO MANUTENZIONI", 'price' => 64.00, 'type' => 'servizio', 'period' => 'annuale'],
        'formazione_assistenza' => ['name' => "FORMAZIONE + ASSISTENZA", 'price' => 64.00, 'type' => 'servizio', 'period' => 'annuale'],
        
        // Hardware
        'hw_dispositivo_bordo' => ['name' => "FORNITURA DISPOSITIVO DI BORDO", 'price' => 190.00, 'type' => 'hardware', 'period' => 'una tantum'],
    ];

    /**
     * Genera il file DOCX e tenta la conversione in PDF
     * Restituisce il percorso del file finale (PDF se possibile, altrimenti DOCX)
     */
    public function generateQuote($serviceRequests, $token)
    {
        $templatePath = resource_path('templates/template_offerta.docx');
        
        if (!file_exists($templatePath)) {
            Log::error("Template non trovato in {$templatePath}");
            throw new \Exception("Template Word non trovato.");
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        // Prepara i dati dinamici
        $firstReq = $serviceRequests->first();
        $companyName = $firstReq->client_data['company'] ?? 'Cliente';
        $contactName = ($firstReq->client_data['contact'] ?? '') . ' ' . ($firstReq->client_data['lastname'] ?? '');
        
        $templateProcessor->setValue('nome_azienda', htmlspecialchars($companyName));
        $templateProcessor->setValue('data_offerta', date('d/m/Y'));
        $templateProcessor->setValue('validita_offerta', date('d/m/Y', strtotime('+30 days')));

        // Costruisci le righe per la tabella "VALORIZZAZIONE ECONOMICA"
        // Poiché il file non ha una vera <w:tbl> con un id o tag, e usare cloneRow() richiede un placeholder strutturato,
        // useremo un blocco dinamico o sostituiremo un blocco testuale con le info raggruppate.
        // Un trucco semplice con TemplateProcessor è rimpiazzare una keyword (es. "VALORIZZAZIONE ECONOMICA DELLA FORNITURA GT FLEET 365")
        // con un layout formattato.
        
        // Raccogliamo i servizi
        $lines = [];
        $totaleServiziAnnuo = 0;
        $totaleHardwareUnaTantum = 0;

        foreach ($serviceRequests as $req) {
            $qty = $req->vehicle_qty ?? 1;
            $services = $req->services ?? [];
            
            $lines[] = "Mezzo: " . strtoupper($req->vehicle_name) . " (Qtà: {$qty})";
            
            foreach ($services as $srv) {
                $id = $srv['id'];
                $srvQty = $srv['qty'] ?? 1;
                $totalQty = $qty * $srvQty;
                
                $priceInfo = $this->pricing[$id] ?? ['name' => $srv['name'], 'price' => 0, 'type' => 'custom', 'period' => '-'];
                $costoUnitario = $priceInfo['price'];
                $costoTotale = $costoUnitario * $totalQty;
                
                $lines[] = "  - {$priceInfo['name']} (Qtà: {$totalQty}) = €" . number_format($costoTotale, 2, ',', '.');
                
                if ($priceInfo['period'] === 'annuale') {
                    $totaleServiziAnnuo += $costoTotale;
                } else if ($priceInfo['period'] === 'una tantum') {
                    $totaleHardwareUnaTantum += $costoTotale;
                }
            }
            $lines[] = ""; // Spazio
        }

        $lines[] = "--------------------------------------------------";
        $lines[] = "TOTALE CANONI ANNUALI: €" . number_format($totaleServiziAnnuo, 2, ',', '.');
        $lines[] = "TOTALE HARDWARE UNA TANTUM: €" . number_format($totaleHardwareUnaTantum, 2, ',', '.');
        
        // Questo è un approccio basilare per iniettare testo "plain". 
        // L'ideale sarebbe inserire una tabella formattata, ma PHPWord's TemplateProcessor supporta setValue con \n se configurato,
        // o si deve usare un trucco XML per iniettare tabelle.
        
        $testoFormattato = implode("</w:t><w:br/><w:t>", $lines);
        
        // Sostituisco "VALORIZZAZIONE ECONOMICA DELLA FORNITURA GT FLEET 365" con il titolo + i dati.
        $templateProcessor->setValue('VALORIZZAZIONE ECONOMICA DELLA FORNITURA GT FLEET 365', "VALORIZZAZIONE ECONOMICA DELLA FORNITURA GT FLEET 365</w:t><w:br/><w:t>" . $testoFormattato);

        // Salva il file DOCX
        $fileNameDocx = 'preventivo-' . \Illuminate\Support\Str::slug($companyName) . '-' . time() . '.docx';
        $pathDocx = storage_path('app/public/service-pdfs/' . $fileNameDocx);
        
        // Assicura directory
        if (!file_exists(storage_path('app/public/service-pdfs'))) {
            mkdir(storage_path('app/public/service-pdfs'), 0755, true);
        }

        $templateProcessor->saveAs($pathDocx);

        // PROVA CONVERSIONE IN PDF (usando LibreOffice headless se presente)
        $fileNamePdf = str_replace('.docx', '.pdf', $fileNameDocx);
        $pathPdf = storage_path('app/public/service-pdfs/' . $fileNamePdf);
        $outdir = storage_path('app/public/service-pdfs');

        // Execute LibreOffice command (soffice)
        // Note: this assumes soffice is available in the server's PATH.
        $cmd = "soffice --headless --convert-to pdf --outdir " . escapeshellarg($outdir) . " " . escapeshellarg($pathDocx) . " 2>&1";
        $output = [];
        $returnVar = 0;
        exec($cmd, $output, $returnVar);

        if ($returnVar === 0 && file_exists($pathPdf)) {
            // Conversione riuscita
            return $pathPdf;
        }

        Log::warning("Impossibile convertire in PDF con soffice. Output: " . implode("\n", $output));
        
        // Se la conversione fallisce, ritorno il DOCX come fallback
        return $pathDocx;
    }
}
