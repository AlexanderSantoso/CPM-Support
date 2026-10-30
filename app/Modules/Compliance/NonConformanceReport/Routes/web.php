<?php

use App\Modules\Compliance\NonConformanceReport\Controllers\NonConformanceReportController;
use App\Modules\Compliance\NonConformanceReport\Controllers\NonConformanceReportExportController;
use Illuminate\Support\Facades\Route;

Route::controller(NonConformanceReportController::class)->group(function () {
    Route::get('/', 'main')->name('main');
});

Route::controller(NonConformanceReportExportController::class)->group(function () {
    Route::get('{encryptedId}/export/pdf', 'pdf')->name('export.pdf');
});
