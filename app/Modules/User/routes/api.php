<?php

use App\Modules\User\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'api/users'

], function ($router) {
    Route::post('/logout', [UserController::class, 'logout']);
});


Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'api/users'

], function ($router) {
    Route::get('/', [UserController::class, 'index']);
    Route::get('/{id}', [UserController::class, 'get']);
    Route::post('/create', [UserController::class, 'create']);
    Route::post('/addArrayUsers', [UserController::class, 'addArrayUsers']);
    Route::post('/update', [UserController::class, 'update']);
    Route::post('/delete', [UserController::class, 'delete']);
    Route::post('/changePassword', [UserController::class, 'changePassword']);
    Route::post('/resetPassword', [UserController::class, 'resetPassword']);

});


Route::group([
    'middleware' => 'api',
    'prefix' => 'api/users'

], function ($router) {
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/register', [UserController::class, 'create']);


});
