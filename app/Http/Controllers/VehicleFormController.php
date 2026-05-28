<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\HubSpotService;
use App\Mail\LeadSummaryMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

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
        }

        return redirect('/');
    }

    // Riceve i dati del form e li invia a HubSpot ed email
    public function store(Request $request)
    {
        $request->validate([
            'client.company' => 'required|string|max:255',
            'client.email'   => 'required|email|max:255',
            'client.contact' => 'required|string|max:255',
            'client.phone'   => 'nullable|string|max:30',
            'client.agent_email' => 'nullable|email|max:255',
            'vehicles'       => 'required|array'
        ], [
            'client.company.required' => 'La ragione sociale è obbligatoria.',
            'client.email.email'      => 'Il formato email non è valido.',
        ]);

        $client = $request->input('client');
        $vehiclesInput = $request->input('vehicles');

        $agentEmail = session('agent_email');
        
        $dealId = null;
        if (config('services.hubspot.token')) {
            $dealId = $this->hubspot->createLead($client, $vehiclesInput);
        }

        $agent = User::where('email', $agentEmail)->first();
        
        $toEmails = [];
        if ($agent) {
            $toEmails[] = $agent->email;
        } elseif ($genericEmail = config('mail.commerciale_generica')) {
            $toEmails[] = $genericEmail;
        }

        if (!empty($toEmails)) {
            $formattedVehicles = [];
            foreach ($vehiclesInput as $id => $qty) {
                if ($qty > 0) {
                    $formattedVehicles[] = [
                        'name' => ucfirst(str_replace('_', ' ', $id)),
                        'qty' => $qty
                    ];
                }
            }

            $mail = Mail::to($toEmails);
            $ccEmails = [];

            if ($agent && ($genericEmail = config('mail.commerciale_generica'))) {
                $ccEmails[] = $genericEmail;
            }

            $capi = config('mail.capi_commerciali') ?? [];
            foreach ($capi as $capoEmail) {
                if ($capoEmail && !in_array($capoEmail, $toEmails)) {
                    $ccEmails[] = $capoEmail;
                }
            }

            if (!empty($ccEmails)) {
                $mail->cc($ccEmails);
            }

            $mail->send(new LeadSummaryMail($client, $formattedVehicles));
        }

        return response()->json([
            'status' => 'success',
            'message' => $dealId ? 'Configurazione salvata con successo.' : 'Configurazione inviata in modalità test.',
            'deal_id' => $dealId
        ]);
    }
}

