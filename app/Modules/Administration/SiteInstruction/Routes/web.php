<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Administration\SiteInstruction\Controllers\SiteInstructionController;
use App\Modules\Administration\SiteInstruction\Controllers\SiteInstructionExportController;

Route::controller(SiteInstructionController::class)->group(function () {
    Route::get('/', 'main')->name('main');

    Route::get('detail/{id}', 'showDetailPage')->name('showDetailPage');
});

Route::controller(SiteInstructionExportController::class)->group(function () {
    Route::get('{encryptedId}/export/pdf', 'pdf')->name('export.pdf');
});

