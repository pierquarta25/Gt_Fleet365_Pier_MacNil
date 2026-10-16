<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    protected $fillable = [
        'lead_id',
        'token',
        'group_token',
        'vehicle_type',
        'vehicle_name',
        'vehicle_qty',
        'vehicle_img',
        'client_data',
        'services',
        'notes',
        'agent_email',
        'hubspot_deal_id',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'client_data' => 'array',
        'services' => 'array',
        'completed_at' => 'datetime',
    ];

    // Relazione con il lead originale
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    // Scope per trovare un record tramite token
    public function scopeByToken($query, string $token)
    {
        return $query->where('token', $token);
    }

    // Verifica se la richiesta è già stata completata
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    // Segna la richiesta come completata
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Restituisce i services come array di gruppi normalizzato.
     * Gestisce sia il vecchio formato flat che il nuovo formato con gruppi.
     * Formato output: [['group_idx'=>0, 'qty'=>N, 'services'=>[...]], ...]
     */
    public function getServiceGroups(): array
    {
        $services = $this->services ?? [];
        if (empty($services)) return [];

        $first = reset($services);
        if (isset($first['group_idx'])) {
            return $services; // nuovo formato multi-gruppo
        }

        // vecchio formato flat → wrap in un gruppo singolo
        return [['group_idx' => 0, 'qty' => $this->vehicle_qty, 'services' => $services]];
    }

    /**
     * Restituisce tutti i services appiattiti (flat), indipendente dal formato.
     * Utile per ricerche tipo firstWhere('type', 'base_package').
     */
    public function getFlatServices(): array
    {
        $groups = $this->getServiceGroups();
        $flat = [];
        foreach ($groups as $group) {
            foreach ($group['services'] ?? [] as $srv) {
                $flat[] = $srv;
            }
        }
        return $flat;
    }
}
