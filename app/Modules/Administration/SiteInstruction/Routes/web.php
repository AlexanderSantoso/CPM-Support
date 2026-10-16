<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Administration\SiteInstruction\Controllers\SiteInstructionController;
use App\Modules\Administration\SiteInstruction\Controllers\SiteInstructionExportController;

Route::controller(SiteInstructionController::class)->group(function () {
    Route::get('/', 'main')->name('main');
});

Route::controller(SiteInstructionExportController::class)->group(function () {
    Route::get('{siteInstruction}/export/pdf', 'pdf')->whereNumber('siteInstruction')->name('export.pdf');
});

