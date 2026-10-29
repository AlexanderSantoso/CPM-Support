<?php

use App\Modules\Compliance\NonConformanceReport\Controllers\NonConformanceReportExportController;
use Illuminate\Support\Facades\Route;

Route::controller(NonConformanceReportExportController::class)->group(function () {
    Route::get('{encryptedId}/export/pdf', 'pdf')->name('export.pdf');
});
