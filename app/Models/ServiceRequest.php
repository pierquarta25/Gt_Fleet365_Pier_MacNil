<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    protected $fillable = [
        'lead_id',
        'token',
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
}
