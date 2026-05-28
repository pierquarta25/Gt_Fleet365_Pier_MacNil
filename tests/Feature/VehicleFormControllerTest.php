<?php

namespace Tests\Feature;

use App\Models\User;
use App\Mail\LeadSummaryMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class VehicleFormControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Configura le email del reparto commerciale per i test
        config([
            'mail.commerciale_generica' => 'commerciali@macnil.it',
            'mail.capi_commerciali' => [
                'capo1@macnil.it',
                'capo2@macnil.it',
            ]
        ]);
    }

    /**
     * Test che verifica l'invio al commerciale di riferimento e in CC alla mail generica e ai capi commerciali.
     */
    public function test_submitting_form_with_agent_sends_email_to_agent_and_cc_to_generic_and_managers(): void
    {
        Mail::fake();

        // 1. Creiamo un agente
        $agent = User::create([
            'name' => 'Mario Rossi',
            'email' => 'mario.rossi@macnil.it',
            'password' => bcrypt('password'),
            'role' => 'agent',
            'slug' => 'mario-rossi'
        ]);

        // 2. Visita la rotta con lo slug dell'agente per metterlo in sessione
        $this->get('/c/mario-rossi');

        // 3. Esegui il post al form
        $payload = [
            'client' => [
                'company' => 'Azienda Test S.r.l.',
                'email' => 'cliente@test.com',
                'contact' => 'John Doe',
                'phone' => '123456',
                'notes' => 'Note di test',
                'italia' => true,
                'estero' => false,
            ],
            'vehicles' => [
                'auto' => 2,
                'furgone' => 1
            ]
        ];

        $response = $this->postJson('/api/vehicle-form', $payload);

        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => 'success']);

        // Verifichiamo che l'email sia stata inviata correttamente
        Mail::assertSent(LeadSummaryMail::class, function (LeadSummaryMail $mail) {
            // Destinatario principale: l'agente commerciale
            $hasTo = $mail->hasTo('mario.rossi@macnil.it');

            // Destinatari in copia: mail generica + capi commerciali
            $hasCcGeneric = $mail->hasCc('commerciali@macnil.it');
            $hasCcCapo1 = $mail->hasCc('capo1@macnil.it');
            $hasCcCapo2 = $mail->hasCc('capo2@macnil.it');

            return $hasTo && $hasCcGeneric && $hasCcCapo1 && $hasCcCapo2;
        });
    }

    /**
     * Test che verifica l'invio alla mail generica come destinatario principale (e capi in CC) se non c'è alcun agente in sessione.
     */
    public function test_submitting_form_without_agent_sends_email_to_generic_and_cc_to_managers(): void
    {
        Mail::fake();

        // 1. Esegui il post al form senza prima passare dalla rotta dell'agente (/c/{slug})
        $payload = [
            'client' => [
                'company' => 'Azienda Test S.r.l.',
                'email' => 'cliente@test.com',
                'contact' => 'John Doe',
                'phone' => '123456',
                'notes' => 'Note di test',
                'italia' => true,
                'estero' => false,
            ],
            'vehicles' => [
                'auto' => 2,
                'furgone' => 1
            ]
        ];

        $response = $this->postJson('/api/vehicle-form', $payload);

        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => 'success']);

        // Verifichiamo che l'email sia stata inviata correttamente
        Mail::assertSent(LeadSummaryMail::class, function (LeadSummaryMail $mail) {
            // Destinatario principale: la mail generica (commerciali@macnil.it) perché non c'è l'agente
            $hasTo = $mail->hasTo('commerciali@macnil.it');

            // Destinatari in copia: solo i capi commerciali (la mail generica è già il destinatario principale, quindi non va in CC)
            $hasCcCapo1 = $mail->hasCc('capo1@macnil.it');
            $hasCcCapo2 = $mail->hasCc('capo2@macnil.it');
            $hasNoCcGeneric = !$mail->hasCc('commerciali@macnil.it');

            return $hasTo && $hasCcCapo1 && $hasCcCapo2 && $hasNoCcGeneric;
        });
    }
}
