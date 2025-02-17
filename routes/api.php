<?php

use App\Http\Controllers\Api\NasaController;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

Route::resource('instruments', NasaController::class,['except' => ['create','edit']]);
Route::get('activityIds', [NasaController::class,'activityIds']);
Route::get('instrument_use', [NasaController::class,'instrumentsPercentage']);
Route::post('instrument_activity', [NasaController::class, 'instrumentActivity']);
