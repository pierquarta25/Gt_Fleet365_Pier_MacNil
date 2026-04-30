<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'company', 'contact_name', 'email', 'phone', 
        'data_4g_italy', 'data_4g_abroad', 'notes', 
        'vehicles_data', 'hubspot_deal_id'
    ];

    protected $casts = [
        'vehicles_data' => 'array',
        'data_4g_italy' => 'boolean',
        'data_4g_abroad' => 'boolean',
    ];
}
