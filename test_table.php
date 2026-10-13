<?php
require __DIR__ . '/vendor/autoload.php';

$templatePath = __DIR__ . '/resources/templates/template_offerta.docx';
$tempDocxPath = __DIR__ . '/temp_test_offerta.docx';
copy($templatePath, $tempDocxPath);

$zip = new \ZipArchive();
if ($zip->open($tempDocxPath) === TRUE) {
    $docXml = $zip->getFromName('word/document.xml');
    
    $search = 'VALORIZZAZIONE ECONOMICA DELLA FORNITURA GT FLEET 365';
    // We add ${quote_table} as a new paragraph.
    $replace = 'VALORIZZAZIONE ECONOMICA DELLA FORNITURA GT FLEET 365</w:t></w:r></w:p><w:p><w:r><w:t>${quote_table}</w:t></w:r></w:p><w:p><w:r><w:t>';
    
    $pos = strpos($docXml, $search);
    if ($pos !== false) {
        $pos2 = strpos($docXml, $search, $pos + strlen($search));
        if ($pos2 !== false) {
            $docXml = substr_replace($docXml, $replace, $pos2, strlen($search));
        }
    }
    
    $zip->addFromString('word/document.xml', $docXml);
    $zip->close();
}

// Now use TemplateProcessor
$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($tempDocxPath);

$table = new \PhpOffice\PhpWord\Element\Table([
    'borderSize' => 6, 
    'borderColor' => '0052BD', 
    'width' => 100 * 50, 
    'unit' => \PhpOffice\PhpWord\Style\TablePosition::DIR_PCT,
    'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER
]);
$table->addRow();
$table->addCell(5000, ['bgColor' => '0052BD'])->addText('Servizio / Prodotto', ['color' => 'FFFFFF', 'bold' => true]);
$table->addCell(2000, ['bgColor' => '0052BD'])->addText('Quantità', ['color' => 'FFFFFF', 'bold' => true]);
$table->addCell(3000, ['bgColor' => '0052BD'])->addText('Totale', ['color' => 'FFFFFF', 'bold' => true]);

$table->addRow();
$table->addCell(5000)->addText('GT FLEET 365 BASE (LOC)');
$table->addCell(2000)->addText('2');
$table->addCell(3000)->addText('€ 256,00');

$templateProcessor->setComplexBlock('quote_table', $table);
$templateProcessor->saveAs(__DIR__ . '/final_test_offerta.docx');

echo "Test successful, final_test_offerta.docx generated.\n";
