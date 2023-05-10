<?php

use Illuminate\Support\Facades\Route;
use App\Modules\DamageType\Http\Controllers\DamageTypeController;

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'api/damage_types'

], function ($router) {
    Route::get('/', [DamageTypeController::class, 'index']);
    Route::get('/{id}', [DamageTypeController::class, 'get']);
    Route::post('/create', [DamageTypeController::class, 'create']);
    Route::post('/update', [DamageTypeController::class, 'update']);
    Route::post('/delete', [DamageTypeController::class, 'delete']);

});
