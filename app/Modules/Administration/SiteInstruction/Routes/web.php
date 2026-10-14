<?php

use App\Modules\Administration\SiteInstruction\Controllers\SiteInstructionExportController;
use Illuminate\Support\Facades\Route;

Route::controller(SiteInstructionExportController::class)->group(function () {
    Route::get('{siteInstruction}/export/pdf', 'pdf')->whereNumber('siteInstruction')->name('export.pdf');
});
