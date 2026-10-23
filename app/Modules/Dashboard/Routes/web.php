<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Dashboard\Controllers\DashboardController;
use App\Modules\Dashboard\Controllers\CurrentStatusController;
use App\Modules\Dashboard\Controllers\BudgetPlanController;
use App\Modules\Dashboard\Controllers\ContractController;
use App\Modules\Dashboard\Controllers\ChangeOrderController;
use App\Modules\Dashboard\Controllers\OwnerEstimateController;
use App\Modules\Dashboard\Controllers\VariationOrderController;

Route::group([], function () {
    Route::get('/', [DashboardController::class, 'main'])
        ->name('dashboard');

    Route::get('data', [DashboardController::class, 'data'])
        ->name('data');

    Route::get('current-status', [CurrentStatusController::class, 'main'])
        ->name('current-status');

    Route::get('current-status/data', [CurrentStatusController::class, 'data'])
        ->name('current-status.data');

    Route::get('current-status/budget-plans', [BudgetPlanController::class, 'data'])
        ->name('current-status.budget-plans');

    Route::get('current-status/contracts', [ContractController::class, 'data'])
        ->name('current-status.contracts');

    Route::get('owner-estimates', [OwnerEstimateController::class, 'data'])
        ->name('owner-estimates');

    Route::get('current-status/change-orders', [ChangeOrderController::class, 'data'])
        ->name('current-status.change-orders');
    Route::get('approved-variations', [VariationOrderController::class, 'data'])
        ->name('approved-variations');
});
