<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\FileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/file/edit/{id}', [FileController::class, 'edit']);

Route::post('/file/edit/{id}', [FileController::class, 'update']);

Route::delete('/file/delete/{id}', [FileController::class, 'delete']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/home/get-data', [HomeController::class, 'getDataHome']);

Route::post('/file/upload/{id}', [FileController::class, 'upload']);
