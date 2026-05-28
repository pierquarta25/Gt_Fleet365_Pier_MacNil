<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehicleFormController;

// Rotta per i link personalizzati dei commerciali (/c/mario-rossi)
Route::get('/c/{slug}', [VehicleFormController::class, 'handleSlug']);

// Rotta per visualizzare il form (ora pulita)
Route::get('/', [VehicleFormController::class, 'index']);


// Rotta API (chiamata da Axios in React) per salvare i dati
// Usiamo /api/ nel path per convenzione, anche se sta in web.php
Route::post('/api/vehicle-form', [VehicleFormController::class, 'store']);
