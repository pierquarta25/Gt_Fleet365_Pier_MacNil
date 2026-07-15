<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\HubSpotService;
use App\Mail\LeadSummaryMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\ServiceRequest;
use Illuminate\Support\Str;

class VehicleFormController extends Controller
{
    protected $hubspot;

    public function __construct(HubSpotService $hubspot)
    {
        $this->hubspot = $hubspot;
    }

    // Mostra la pagina principale del configuratore
    public function index()
    {
        return view('app');
    }

    // Associa la sessione al commerciale tramite slug e reindirizza al form
    public function handleSlug($slug)
    {
        $agent = User::where('slug', $slug)->first();
        
        if ($agent) {
            session(['agent_email' => $agent->email]);
            return redirect('/?agent=' . $slug);
        }

        return redirect('/');
    }

    // Riceve i dati del form e li invia a HubSpot ed email
    public function store(Request $request)
    {
        $request->validate([
            'client.company' => 'required|string|max:255',
            'client.vatNumber' => 'required|string|max:50',
            'client.email'   => 'required|email|max:255',
            'client.contact' => 'required|string|max:255',
            'client.lastname'=> 'required|string|max:255',
            'client.phone'   => 'nullable|string|max:30',
            'client.drivers' => 'nullable|integer|min:0',
            'client.agent_email' => 'nullable|email|max:255',
            'client.agent_slug'  => 'nullable|string|max:255',
            'vehicles'       => 'required|array'
        ], [
            'client.company.required' => __('Company name is required.'),
            'client.contact.required' => __('Contact first name is required.'),
            'client.lastname.required'=> __('Contact last name is required.'),
            'client.email.email'      => __('Invalid email format.'),
        ]);

        $client = $request->input('client');

        // Set locale from frontend language
        $locale = $request->input('language', 'it');
        if (in_array($locale, ['it', 'en'])) {
            app()->setLocale($locale);
        }

        $vehiclesInput = $request->input('vehicles');

        $agent = null;
        $agentSlug = $client['agent_slug'] ?? null;
        $agentEmailFromClient = $client['agent_email'] ?? null;
        $sessionAgentEmail = session('agent_email');

        if ($agentSlug) {
            $agent = User::where('slug', $agentSlug)->first();
        }
        if (!$agent && $agentEmailFromClient) {
            $agent = User::where('email', $agentEmailFromClient)->first();
        }
        if (!$agent && $sessionAgentEmail) {
            $agent = User::where('email', $sessionAgentEmail)->first();
        }

        $dealId = null;
        if (config('services.hubspot.token')) {
            $dealId = $this->hubspot->createLead($client, $vehiclesInput);
        }
        
        $toEmails = [];
        if ($agent) {
            $toEmails[] = $agent->email;
        } elseif ($genericEmail = config('mail.commerciale_generica')) {
            $toEmails[] = $genericEmail;
        }

        $capi = config('mail.capi_commerciali') ?? [];

        // Se non c'è un destinatario principale, usiamo i capi commerciali come destinatari
        if (empty($toEmails) && !empty($capi)) {
            $toEmails = $capi;
            $capi = [];
        }

        \Illuminate\Support\Facades\Log::info("Debug invio email", [
            'default_mailer' => config('mail.default'),
            'toEmails' => $toEmails,
            'capi' => $capi,
            'agent' => $agent ? $agent->email : null
        ]);

        if (!empty($toEmails)) {
            $formattedVehicles = [];
            $selectedVehicles = $request->input('selectedVehicles', []);

            if (!empty($selectedVehicles)) {
                foreach ($selectedVehicles as $item) {
                    $formattedVehicles[] = [
                        'name' => $item['name'] ?? ucfirst(str_replace('_', ' ', $item['id'] ?? '')),
                        'qty' => $item['qty'],
                        'img' => $item['img'] ?? '',
                        'id'  => $item['id'] ?? '',
                    ];
                }
            } else {
                foreach ($vehiclesInput as $id => $qty) {
                    if ($qty > 0) {
                        $formattedVehicles[] = [
                            'name' => ucfirst(str_replace('_', ' ', $id)),
                            'qty' => $qty,
                            'img' => '/media/' . strtoupper($id) . '.png',
                            'id'  => $id,
                        ];
                    }
                }
            }

            // Genera un token univoco per il gruppo (tutto il preventivo)
            $groupToken = Str::random(48);

            // Genera un token univoco per ogni mezzo e crea i record ServiceRequest
            foreach ($formattedVehicles as &$vehicle) {
                $token = Str::random(48);

                ServiceRequest::create([
                    'token'        => $token,
                    'group_token'  => $groupToken,
                    'vehicle_type' => $vehicle['id'] ?? Str::slug($vehicle['name'], '_'),
                    'vehicle_name' => $vehicle['name'],
                    'vehicle_qty'  => $vehicle['qty'],
                    'vehicle_img'  => $vehicle['img'] ?? null,
                    'client_data'  => $client,
                    'agent_email'  => $agent ? $agent->email : null,
                    'hubspot_deal_id' => $dealId,
                    'status'       => 'pending',
                ]);

                // Manteniamo service_token se serve in futuro, ma usiamo group_token per la mail globale
                $vehicle['service_token'] = $token;
            }
            unset($vehicle); // Rilascia il riferimento

            $mail = Mail::to($toEmails);
            $ccEmails = [];

            if ($agent && ($genericEmail = config('mail.commerciale_generica'))) {
                $ccEmails[] = $genericEmail;
            }

            foreach ($capi as $capoEmail) {
                if ($capoEmail && !in_array($capoEmail, $toEmails)) {
                    $ccEmails[] = $capoEmail;
                }
            }

            if (!empty($ccEmails)) {
                $mail->cc($ccEmails);
            }

            \Illuminate\Support\Facades\Log::info("Tentativo invio con mailer: " . config('mail.default'), [
                'to' => $toEmails,
                'cc' => $ccEmails
            ]);

            $mail->send(new LeadSummaryMail($client, $formattedVehicles, $groupToken));
            \Illuminate\Support\Facades\Log::info("Email inviata con successo.");
        } else {
            \Illuminate\Support\Facades\Log::warning("Invio email saltato: nessun destinatario configurato.");
        }

        return response()->json([
            'status' => 'success',
            'message' => $dealId ? __('Configuration saved successfully.') : __('Configuration sent in test mode.'),
            'deal_id' => $dealId
        ]);
    }
}

