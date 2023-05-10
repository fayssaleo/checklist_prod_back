<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Comment\Http\Controllers\CommentController;

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'api/comments'

], function ($router) {

    Route::get('/{id}', [CommentController::class, 'index']);
    Route::get('/comment/{id}', [CommentController::class, 'get']);
    Route::post('/create', [CommentController::class, 'create']);
    Route::post('/update', [CommentController::class, 'update']);
    Route::post('/delete', [CommentController::class, 'delete']);
    Route::post('/addPhotos', [CommentController::class, 'addPhotos']);
    Route::post('/deletePhoto', [CommentController::class, 'deletePhoto']);
    Route::post('/sendDamagePhotosStoragePath', [CommentController::class, 'sendDamagePhotosStoragePath']);



});