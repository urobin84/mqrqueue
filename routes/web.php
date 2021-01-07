<?php

use App\Http\Controllers\MutasiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/upload', [MutasiController::class, 'index']);
Route::post('/upload', [MutasiController::class, 'upload']);
Route::get('/batch', [MutasiController::class, 'batch']);
