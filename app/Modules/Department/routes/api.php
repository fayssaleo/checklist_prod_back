<?php

use App\Modules\Department\Http\Controllers\DepartmentController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'api/departments'

], function ($router) {
    Route::get('/', [DepartmentController::class, 'index']);
    Route::get('/{id}', [DepartmentController::class, 'get']);
    Route::post('/create', [DepartmentController::class, 'create']);
    Route::post('/update', [DepartmentController::class, 'update']);
    Route::post('/delete', [DepartmentController::class, 'delete']);

});
