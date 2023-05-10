<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\PresenceCheck\Http\Controllers\PresenceCheckController;


Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'api/presence_checks'

], function ($router) {
    Route::get('/', [PresenceCheckController::class, 'index']);
    Route::get('/{id}', [PresenceCheckController::class, 'get']);
    Route::post('/create', [PresenceCheckController::class, 'create']);
    Route::post('/update', [PresenceCheckController::class, 'update']);
    Route::post('/delete', [PresenceCheckController::class, 'delete']);

});
