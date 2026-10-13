<?php
$zip = new ZipArchive;
if ($zip->open(__DIR__ . '/resources/templates/template_offerta.docx') === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    $count = substr_count($xml, 'VALORIZZAZIONE ECONOMICA DELLA FORNITURA GT FLEET 365');
    echo "Occurrences: " . $count . "\n";
    $zip->close();
}
