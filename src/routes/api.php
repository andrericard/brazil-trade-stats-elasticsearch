<?php

use Illuminate\Support\Facades\Route;

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

Route::post('charts/{chartName}', [\App\Http\Controllers\Api\ChartController::class, 'getChartData'])
    ->whereIn('chartName', \App\Enums\ChartEnum::toArray());

Route::get('autocomplete', [\App\Http\Controllers\Api\ChartController::class, 'getAutocompleteData']);
