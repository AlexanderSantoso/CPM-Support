<?php
use Illuminate\Support\Facades\Route;

use App\Modules\CostAndContract\BudgetPlan\Controllers\BudgetPlanExportController;

Route::controller(BudgetPlanExportController::class)->group(function () {
    Route::get('export/excel', 'excel')->name('export.excel');
    Route::get('export/all-pdfs', 'allPdfs')->name('export.all-pdfs');
    Route::get('{encryptedId}/export/pdf', 'pdf')->name('export.pdf');
});
