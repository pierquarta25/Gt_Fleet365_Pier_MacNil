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
     /**
     * Test che verifica che la rotta /c/{slug} reindirizzi correttamente a /?agent={slug}.
     */
    public function test_handle_slug_redirects_to_home_with_agent_query_param(): void
    {
        // Creiamo un agente
        User::create([
            'name' => 'Mario Rossi',
            'email' => 'mario.rossi@macnil.it',
            'password' => bcrypt('password'),
            'role' => 'agent',
            'slug' => 'mario-rossi'
        ]);

        $response = $this->get('/c/mario-rossi');
        $response->assertRedirect('/?agent=mario-rossi');
        $this->assertEquals('mario.rossi@macnil.it', session('agent_email'));
    }

    /**
     * Test che verifica l'invio al commerciale di riferimento (in sessione) e in CC alla mail generica e ai capi commerciali.
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

        // 2. Visita la rotta con lo slug dell'agente per metterlo in sessione e fare il redirect
        $this->get('/c/mario-rossi')->assertRedirect('/?agent=mario-rossi');

        // 3. Esegui il post al form
        $payload = [
            'client' => [
                'company' => 'Azienda Test S.r.l.',
                'email' => 'cliente@test.com',
                'contact' => 'John',
                'lastname' => 'Doe',
                'phone' => '123456',
                'notes' => 'Note di test',
                'italia' => true,
                'estero' => false,
                'drivers' => 3,
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
     * Test che verifica che l'invio del form con agent_slug nel payload client invii la mail all'agente.
     */
    public function test_submitting_form_with_agent_slug_in_payload_sends_email_to_agent(): void
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

        // 2. Esegui il post al form passando agent_slug direttamente nel payload client
        $payload = [
            'client' => [
                'company' => 'Azienda Test S.r.l.',
                'email' => 'cliente@test.com',
                'contact' => 'John',
                'lastname' => 'Doe',
                'phone' => '123456',
                'notes' => 'Note di test',
                'italia' => true,
                'estero' => false,
                'agent_slug' => 'mario-rossi',
                'drivers' => 3,
            ],
            'vehicles' => [
                'auto' => 2,
                'furgone' => 1
            ]
        ];

        $response = $this->postJson('/api/vehicle-form', $payload);

        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => 'success']);

        // Verifichiamo che l'email sia stata inviata all'agente mario.rossi@macnil.it
        Mail::assertSent(LeadSummaryMail::class, function (LeadSummaryMail $mail) {
            return $mail->hasTo('mario.rossi@macnil.it') && $mail->hasCc('commerciali@macnil.it');
        });
    }

    /**
     * Test che verifica che l'invio del form con agent_email nel payload client invii la mail all'agente.
     */
    public function test_submitting_form_with_agent_email_in_payload_sends_email_to_agent(): void
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

        // 2. Esegui il post al form passando agent_email direttamente nel payload client
        $payload = [
            'client' => [
                'company' => 'Azienda Test S.r.l.',
                'email' => 'cliente@test.com',
                'contact' => 'John',
                'lastname' => 'Doe',
                'phone' => '123456',
                'notes' => 'Note di test',
                'italia' => true,
                'estero' => false,
                'agent_email' => 'mario.rossi@macnil.it',
                'drivers' => 3,
            ],
            'vehicles' => [
                'auto' => 2,
                'furgone' => 1
            ]
        ];

        $response = $this->postJson('/api/vehicle-form', $payload);

        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => 'success']);

        // Verifichiamo che l'email sia stata inviata all'agente mario.rossi@macnil.it
        Mail::assertSent(LeadSummaryMail::class, function (LeadSummaryMail $mail) {
            return $mail->hasTo('mario.rossi@macnil.it') && $mail->hasCc('commerciali@macnil.it');
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
                'contact' => 'John',
                'lastname' => 'Doe',
                'phone' => '123456',
                'notes' => 'Note di test',
                'italia' => true,
                'estero' => false,
                'drivers' => 3,
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
