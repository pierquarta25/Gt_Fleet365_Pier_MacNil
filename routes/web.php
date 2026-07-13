<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehicleFormController;
use App\Http\Controllers\ServiceFormController;

// Rotta per i link personalizzati dei commerciali (/c/mario-rossi)
Route::get('/c/{slug}', [VehicleFormController::class, 'handleSlug']);

// Rotta per visualizzare il form (ora pulita)
Route::get('/', [VehicleFormController::class, 'index']);


// Rotta API (chiamata da Axios in React) per salvare i dati
// Usiamo /api/ nel path per convenzione, anche se sta in web.php
Route::post('/api/vehicle-form', [VehicleFormController::class, 'store'])->middleware('throttle:vehicle-form');

// Rotte per il form di configurazione servizi (accessibile via link nella mail)
Route::get('/servizi/{token}', [ServiceFormController::class, 'show']);
Route::get('/servizi/{token}/successo', [ServiceFormController::class, 'success']);
Route::get('/servizi/{token}/pdf', [ServiceFormController::class, 'downloadPdf']);
Route::post('/api/servizi/{token}', [ServiceFormController::class, 'store']);
