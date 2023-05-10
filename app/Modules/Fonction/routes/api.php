<?php

use App\Modules\Fonction\Http\Controllers\FonctionController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'api/fonctions'

], function ($router) {
    Route::get('/', [FonctionController::class, 'index']);
    Route::get('/{id}', [FonctionController::class, 'get']);
    Route::post('/create', [FonctionController::class, 'create']);
    Route::post('/update', [FonctionController::class, 'update']);
    Route::post('/delete', [FonctionController::class, 'delete']);

});
