<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TokenController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/refresh-token', [TokenController::class, 'refreshToken']);

// */5 * * * * curl -X POST https://ujian.test/api/refresh-token -H "X-Api-Key: aja_kepo_ya"
