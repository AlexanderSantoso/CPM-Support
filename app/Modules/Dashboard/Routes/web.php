<?php

use App\Modules\Dashboard\Controllers\BudgetPlanController;
use App\Modules\Dashboard\Controllers\ChangeOrderController;
use App\Modules\Dashboard\Controllers\ContractController;
use App\Modules\Dashboard\Controllers\CurrentStatusController;
use App\Modules\Dashboard\Controllers\DashboardController;
use App\Modules\Dashboard\Controllers\OwnerEstimateController;
use App\Modules\Dashboard\Controllers\VariationOrderController;
use Illuminate\Support\Facades\Route;

Route::controller(DashboardController::class)->group(function () {
    Route::get('/', 'main')->name('dashboard');
    Route::get('data', 'data')->name('data');
    Route::get('s-curve/progress', 'sCurveProgress')->name('s-curve.progress');
    Route::get('s-curve/work-packages', 'sCurveWorkPackages')->name('s-curve.work-packages');
});

Route::controller(CurrentStatusController::class)->group(function () {
    Route::get('current-status', 'main')->name('current-status');
    Route::get('current-status/data', 'data')->name('current-status.data');
});

Route::controller(BudgetPlanController::class)->group(function () {
    Route::get('current-status/budget-plans', 'data')->name('current-status.budget-plans');
});

Route::controller(ContractController::class)->group(function () {
    Route::get('current-status/contracts', 'data')->name('current-status.contracts');
});

Route::controller(OwnerEstimateController::class)->group(function () {
    Route::get('owner-estimates', 'data')->name('owner-estimates');
});

Route::controller(ChangeOrderController::class)->group(function () {
    Route::get('current-status/change-orders', 'data')->name('current-status.change-orders');
});

Route::controller(VariationOrderController::class)->group(function () {
    Route::get('approved-variations', 'data')->name('approved-variations');
});
