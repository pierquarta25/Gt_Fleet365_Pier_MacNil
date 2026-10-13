<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\ServiceFormController;
use App\Services\QuoteGeneratorService;

$reqs = \App\Models\ServiceRequest::all();
if ($reqs->isEmpty()) {
    echo "NO SERVICE REQUESTS FOUND!\n";
    exit;
}

// Prendi il primo token
$token = $reqs->first()->token;
echo "Testing token: " . $token . "\n";

$controller = new ServiceFormController();
$service = new QuoteGeneratorService();

try {
    $serviceRequests = \App\Models\ServiceRequest::where('token', $token)->orWhere('group_token', $token)->get();
    $file = $service->generateQuote($serviceRequests, $token);
    echo "Generated: " . $file . "\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
