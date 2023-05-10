<?php

use Illuminate\Support\Facades\Route;
use \App\Modules\Equipment\Http\Controllers\EquipmentController;

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'api/equipments'

], function ($router) {
    Route::get('/', [EquipmentController::class, 'index']);
    Route::get('/getEquipmentsByCounter/{id}', [EquipmentController::class, 'getEquipmentsByCounter']);
    Route::get('/getEquipmentsByCounterIT/{id}', [EquipmentController::class, 'getEquipmentsByCounterIT']);
    Route::get('/getEquipmentsByCounterTEC/{id}', [EquipmentController::class, 'getEquipmentsByCounterTEC']);

    Route::get('/getEquipmentsByCounters/{id}', [EquipmentController::class, 'getEquipmentsByCounters']);
    Route::get('/getEquipmentsByCountersIT/{id}', [EquipmentController::class, 'getEquipmentsByCountersIT']);
    Route::get('/getEquipmentsByCountersTEC/{id}', [EquipmentController::class, 'getEquipmentsByCountersTEC']);

    Route::get('/{id}', [EquipmentController::class, 'get']);
    Route::post('/create', [EquipmentController::class, 'create']);
    Route::post('/update', [EquipmentController::class, 'update']);
    Route::post('/delete', [EquipmentController::class, 'delete']);


    Route::post('/all', [EquipmentController::class, 'EquipmentsCheckedByEquipment']);

});
