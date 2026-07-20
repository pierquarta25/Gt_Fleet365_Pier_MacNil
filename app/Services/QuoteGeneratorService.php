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

        $firstReq = $serviceRequests->first();
        $companyName = $firstReq->client_data['company'] ?? 'Cliente';
        $contactName = ($firstReq->client_data['contact'] ?? '') . ' ' . ($firstReq->client_data['lastname'] ?? '');
        
        // --- CREAZIONE TABELLA PREVENTIVO ---
        $table = new \PhpOffice\PhpWord\Element\Table([
            'borderSize' => 6, 
            'borderColor' => '0052BD', 
            'width' => 5000, // 100% width
            'unit' => \PhpOffice\PhpWord\SimpleType\TblWidth::PERCENT,
            'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER
        ]);
        
        $table->addRow();
        $table->addCell(5000, ['bgColor' => '0052BD'])->addText('Servizio / Prodotto', ['color' => 'FFFFFF', 'bold' => true, 'name' => 'Arial']);
        $table->addCell(2000, ['bgColor' => '0052BD'])->addText('Quantità', ['color' => 'FFFFFF', 'bold' => true, 'name' => 'Arial']);
        $table->addCell(3000, ['bgColor' => '0052BD'])->addText('Totale', ['color' => 'FFFFFF', 'bold' => true, 'name' => 'Arial']);

        $totaleServiziAnnuo = 0;
        $totaleHardwareUnaTantum = 0;

        foreach ($serviceRequests as $req) {
            $qty = $req->vehicle_qty ?? 1;
            $services = $req->services ?? [];
            
            $vehicleNameSafe = htmlspecialchars(strtoupper($req->vehicle_name));
            
            // Riga Intestazione Veicolo
            $table->addRow();
            $table->addCell(10000, ['gridSpan' => 3, 'bgColor' => 'F0F0F0'])->addText("Mezzo: {$vehicleNameSafe} (Qtà: {$qty})", ['bold' => true, 'name' => 'Arial']);
            
            foreach ($services as $srv) {
                $id = $srv['id'];
                $srvQty = $srv['qty'] ?? 1;
                $totalQty = $qty * $srvQty;
                
                $priceInfo = $this->pricing[$id] ?? ['name' => $srv['name'], 'price' => 0, 'type' => 'custom', 'period' => '-'];
                $costoUnitario = $priceInfo['price'];
                $costoTotale = $costoUnitario * $totalQty;
                
                $serviceNameSafe = htmlspecialchars($priceInfo['name']);
                
                $table->addRow();
                $table->addCell(5000)->addText($serviceNameSafe, ['name' => 'Arial']);
                $table->addCell(2000)->addText((string)$totalQty, ['name' => 'Arial']);
                $table->addCell(3000)->addText('€ ' . number_format($costoTotale, 2, ',', '.'), ['name' => 'Arial']);
                
                if ($priceInfo['period'] === 'annuale') {
                    $totaleServiziAnnuo += $costoTotale;
                } else if ($priceInfo['period'] === 'una tantum') {
                    $totaleHardwareUnaTantum += $costoTotale;
                }
            }
        }
        
        $table->addRow();
        $table->addCell(10000, ['gridSpan' => 3])->addText('', ['name' => 'Arial']); // Spazio vuoto

        $table->addRow();
        $table->addCell(7000, ['gridSpan' => 2])->addText('TOTALE CANONI ANNUALI', ['bold' => true, 'name' => 'Arial']);
        $table->addCell(3000)->addText('€ ' . number_format($totaleServiziAnnuo, 2, ',', '.'), ['bold' => true, 'name' => 'Arial']);

        $table->addRow();
        $table->addCell(7000, ['gridSpan' => 2])->addText('TOTALE HARDWARE UNA TANTUM', ['bold' => true, 'name' => 'Arial']);
        $table->addCell(3000)->addText('€ ' . number_format($totaleHardwareUnaTantum, 2, ',', '.'), ['bold' => true, 'name' => 'Arial']);

        // --- PRE-ELABORAZIONE ZIP ARCHIVE ---
        $tempDocx = storage_path('app/public/temp-' . time() . '.docx');
        if (!file_exists(storage_path('app/public'))) {
            mkdir(storage_path('app/public'), 0755, true);
        }
        copy($templatePath, $tempDocx);

        $zip = new \ZipArchive();
        if ($zip->open($tempDocx) === TRUE) {
            // Sostituisci in document.xml
            $docXml = $zip->getFromName('word/document.xml');
            if ($docXml !== false) {
                // Sostituisce il nome azienda
                $docXml = str_replace('Nome Azienda', htmlspecialchars($companyName), $docXml);
                
                // Rimuove eventuali evidenziazioni gialle
                $docXml = str_replace('<w:highlight w:val="yellow"/>', '', $docXml);
                
                // Inserisce il macro-placeholder per la tabella sotto la seconda occorrenza (quella nel corpo)
                $search = 'VALORIZZAZIONE ECONOMICA DELLA FORNITURA GT FLEET 365';
                $replace = 'VALORIZZAZIONE ECONOMICA DELLA FORNITURA GT FLEET 365</w:t></w:r></w:p><w:p><w:r><w:t>${quote_table}</w:t></w:r></w:p><w:p><w:r><w:t>';
                
                $pos = strpos($docXml, $search); // Prima occorrenza (nell'Indice)
                if ($pos !== false) {
                    $pos2 = strpos($docXml, $search, $pos + strlen($search)); // Seconda occorrenza (nel Corpo)
                    if ($pos2 !== false) {
                        $docXml = substr_replace($docXml, $replace, $pos2, strlen($search));
                    }
                }
                
                $zip->addFromString('word/document.xml', $docXml);
            }
            
            // Sostituisci negli header (per RAGIONE SOCIALE, emesso da, data)
            for ($i = 1; $i <= 5; $i++) {
                $headerName = 'word/header' . $i . '.xml';
                $headerXml = $zip->getFromName($headerName);
                if ($headerXml !== false) {
                    $headerXml = str_replace('RAGIONE SOCIALE', htmlspecialchars($companyName), $headerXml);
                    $headerXml = str_replace('Emesso da: TEAM Sales', htmlspecialchars('Emesso da: GT Fleet 365'), $headerXml);
                    $headerXml = str_replace('xx mese 2026', date('d/m/Y'), $headerXml);
                    
                    // Rimuove eventuali evidenziazioni gialle
                    $headerXml = str_replace('<w:highlight w:val="yellow"/>', '', $headerXml);
                    
                    $zip->addFromString($headerName, $headerXml);
                }
            }
            $zip->close();
        }

        // --- INSERIMENTO TABELLA CON TEMPLATE PROCESSOR ---
        $templateProcessor = new TemplateProcessor($tempDocx);
        
        // Questo setComplexBlock andrà a sostituire la riga ${quote_table} che abbiamo iniettato
        $templateProcessor->setComplexBlock('quote_table', $table);

        // Salva il file DOCX finale
        $fileNameDocx = 'preventivo-' . \Illuminate\Support\Str::slug($companyName) . '-' . time() . '.docx';
        $pathDocx = storage_path('app/public/service-pdfs/' . $fileNameDocx);
        
        if (!file_exists(storage_path('app/public/service-pdfs'))) {
            mkdir(storage_path('app/public/service-pdfs'), 0755, true);
        }

        $templateProcessor->saveAs($pathDocx);
        
        // Pulisce il temp file
        @unlink($tempDocx);

        // PROVA CONVERSIONE IN PDF
        $fileNamePdf = str_replace('.docx', '.pdf', $fileNameDocx);
        $pathPdf = storage_path('app/public/service-pdfs/' . $fileNamePdf);
        $outdir = storage_path('app/public/service-pdfs');

        $cmd = "soffice --headless --convert-to pdf --outdir " . escapeshellarg($outdir) . " " . escapeshellarg($pathDocx) . " 2>&1";
        $output = [];
        $returnVar = 0;
        exec($cmd, $output, $returnVar);

        if ($returnVar === 0 && file_exists($pathPdf)) {
            return $pathPdf;
        }

        Log::warning("Impossibile convertire in PDF con soffice. Output: " . implode("\n", $output));
        
        return $pathDocx;
    }
}
