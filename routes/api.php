<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('throttle:600,1')->group(function () {
    Route::get('/items/all', [ItemController::class, 'getAll']);
    Route::get('/item/{id}', [ItemController::class, 'get']);
    Route::post('/item/create', [ItemController::class, 'create']);
    Route::put('/item/update/{id}', [ItemController::class, 'update']);
    Route::delete('/item/delete/{id}', [ItemController::class, 'delete']);
    Route::patch('/item/book/{id}', [ItemController::class, 'book']);
});
