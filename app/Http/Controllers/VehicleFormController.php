<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\HubSpotService;

class VehicleFormController extends Controller
{
    protected $hubspot;

    public function __construct(HubSpotService $hubspot)
    {
        $this->hubspot = $hubspot;
    }

    /**
     * Mostra la pagina principale dove gira React.
     */
    public function index()
    {
        return view('app');
    }

    /**
     * Riceve i dati dal form React e li gira a HubSpot.
     */
    public function store(Request $request)
    {
        // Validazione robusta lato server: mai fidarsi del solo frontend!
        $request->validate([
            'client.company' => 'required|string|max:255',
            'client.email'   => 'required|email|max:255',
            'client.contact' => 'required|string|max:255',
            'client.phone'   => 'nullable|string|max:30',
            'vehicles'       => 'required|array'
        ], [
            'client.company.required' => 'La ragione sociale è obbligatoria anche sul server.',
            'client.email.email'      => 'Il formato email non è valido.',
        ]);
$client = $request->input('client');
$vehicles = $request->input('vehicles');

// Chiamiamo il servizio HubSpot per creare il lead
        $dealId = $this->hubspot->createLead($client, $vehicles);

        if ($dealId) {
            return response()->json([
                'status' => 'success',
                'message' => 'Deal creato con successo!',
                'deal_id' => $dealId
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Impossibile creare il lead su HubSpot.'
        ], 500);
    }
}
