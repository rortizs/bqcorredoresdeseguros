<?php

declare(strict_types=1);

use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::post('/contacto', [LandingController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contacto.store');
