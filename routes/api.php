<?php

use App\Http\Controllers\HeroController;
use Illuminate\Support\Facades\Route;


//Route::namespace('App\Http\Controllers')->group(function () {
//    Route::apiResource('developers', 'DeveloperController');
//});

Route::resources([
    'heroes' => HeroController::class,
]);
