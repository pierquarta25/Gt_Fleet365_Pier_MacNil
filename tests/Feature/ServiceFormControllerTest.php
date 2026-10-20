<?php

namespace Tests\Feature;

use App\Models\ServiceRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceFormControllerTest extends TestCase
{
    use RefreshDatabase;

    private function makeRequest(): ServiceRequest
    {
        return ServiceRequest::create([
            'token'        => 'tok-single',
            'group_token'  => 'tok-group',
            'vehicle_type' => 'auto',
            'vehicle_name' => 'AUTO',
            'vehicle_qty'  => 1,
            'client_data'  => ['company' => 'Test Srl'],
            'status'       => 'pending',
        ]);
    }

    public function test_client_cannot_override_package_prices(): void
    {
        $req = $this->makeRequest();

        $this->postJson('/api/servizi/tok-group', [
            'vehicles' => [$req->id => ['groups' => [[
                'qty' => 1,
                'base_package' => 'base_loc',
                'package_price' => 1,
                'package_discount' => 10,
                'package_final_price' => 0.01,
            ]]]],
        ])->assertOk();

        $pkg = $req->fresh()->services[0]['services'][0];
        $this->assertSame(200.0, (float) $pkg['price']);
        $this->assertSame(180.0, (float) $pkg['final_price']);
    }

    public function test_hardware_sub_items_are_accepted(): void
    {
        $req = $this->makeRequest();

        $this->postJson('/api/servizi/tok-group', [
            'vehicles' => [$req->id => ['groups' => [[
                'qty' => 1,
                'base_package' => 'base_loc',
                'services' => ['temperatura_controllata', 'hw_sensore_temperatura'],
                'quantities' => ['hw_sensore_temperatura' => 2],
            ]]]],
        ])->assertOk();
    }

    public function test_unknown_service_ids_are_rejected(): void
    {
        $req = $this->makeRequest();

        $this->postJson('/api/servizi/tok-group', [
            'vehicles' => [$req->id => ['groups' => [['base_package' => 'inventato']]]],
        ])->assertStatus(422);

        $this->postJson('/api/servizi/tok-group', [
            'vehicles' => [$req->id => ['groups' => [['qty' => 1, 'package_discount' => 500]]]],
        ])->assertStatus(422);
    }

    public function test_pdf_route_is_rate_limited(): void
    {
        $this->makeRequest();

        for ($i = 0; $i < 10; $i++) {
            $this->get('/servizi/tok-group/pdf')->assertNotFound(); // non completato
        }

        $this->get('/servizi/tok-group/pdf')->assertStatus(429);
    }
}
