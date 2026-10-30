<?php

use App\Modules\Compliance\ProjectExecutionPlan\Controllers\ProjectExecutionPlanController;
use App\Modules\Compliance\ProjectExecutionPlan\Controllers\ProjectExecutionPlanExportController;
use Illuminate\Support\Facades\Route;

Route::controller(ProjectExecutionPlanController::class)->group(function () {
    Route::get('/', 'main')->name('main');
});

Route::controller(ProjectExecutionPlanExportController::class)->group(function () {
    Route::get('{encryptedId}/export/pdf', 'pdf')->name('export.pdf');
});
