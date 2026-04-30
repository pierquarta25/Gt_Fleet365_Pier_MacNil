<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehicleFormController;

// Rotta per visualizzare il form (React)
Route::get('/', [VehicleFormController::class, 'index']);

// Rotta API (chiamata da Axios in React) per salvare i dati
// Usiamo /api/ nel path per convenzione, anche se sta in web.php
Route::post('/api/vehicle-form', [VehicleFormController::class, 'store']);
