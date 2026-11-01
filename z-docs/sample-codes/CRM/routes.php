<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\Http\Controllers\LeadController;

Route::middleware(['auth', 'verified'])
    ->prefix('crm')
    ->name('crm.')
    ->group(function () {
        Route::resource('leads', LeadController::class);
    });