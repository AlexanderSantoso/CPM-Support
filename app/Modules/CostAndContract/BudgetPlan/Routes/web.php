<?php

use App\Modules\CostAndContract\BudgetPlan\Controllers\BudgetPlanController;
use App\Modules\CostAndContract\BudgetPlan\Controllers\BudgetPlanExportController;
use Illuminate\Support\Facades\Route;

Route::controller(BudgetPlanController::class)->group(function () {
    Route::get('/', 'main')->name('main');
});

Route::controller(BudgetPlanExportController::class)->group(function () {
    Route::get('export/excel', 'excel')->name('export.excel');
    Route::get('export/all-pdfs', 'allPdfs')->name('export.all-pdfs');
    Route::get('{encryptedId}/export/pdf', 'pdf')->name('export.pdf');
});
