<?php
use Illuminate\Support\Facades\Route;

use App\Modules\Dashboard\Controllers\DashboardController;

Route::group([], function () {
    Route::get('/', [DashboardController::class, 'main'])->name('dashboard');
});