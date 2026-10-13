<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\QuoteGeneratorService;

$reqs = [];
for($i=0; $i<2; $i++) {
    $reqs[] = (object)[
        'vehicle_qty' => 1,
        'client_data' => ['company' => 'Test Company'],
        'services' => [
            ['id' => 'base_loc', 'name' => 'GT FLEET 365 BASE (LOC)', 'qty' => 1, 'type' => 'base_package', 'price' => 200, 'discount' => 40, 'final_price' => 112]
        ]
    ];
}
$serviceRequests = collect($reqs);

$service = new QuoteGeneratorService();
try {
    $file = $service->generateQuote($serviceRequests, 'test_token');
    echo "Success: " . $file . PHP_EOL;
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString();
}
