<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\QuoteGeneratorService;

$reqs = [];
for($i=0; $i<19; $i++) {
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
$file = $service->generateQuote($serviceRequests, 'test_token');
echo "Generated file: " . $file . PHP_EOL;
