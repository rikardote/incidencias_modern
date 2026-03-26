<?php

use Illuminate\Support\Facades\Route;

// ─── API WEAROS (App Móvil Kotlin) ─────────────────────────
// Por defecto, todas las rutas en este archivo tendrán el prefijo "/api"
// Y no se verán afectadas por la verificación CSRF.

Route::post('wearos/checar', [\App\Http\Controllers\Api\WearOSController::class, 'storeCheckin']);
Route::get('wearos/historial/{identificador}', [\App\Http\Controllers\Api\WearOSController::class, 'getHistory']);
Route::get('wearos/checadas/{num_empleado}', [\App\Http\Controllers\Api\WearOSController::class, 'getCheckinsByEmployee']);
