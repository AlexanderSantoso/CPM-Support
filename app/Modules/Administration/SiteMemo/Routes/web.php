<?php

use App\Modules\Administration\SiteMemo\Controllers\SiteMemoController;
use App\Modules\Administration\SiteMemo\Controllers\SiteMemoExportController;
use Illuminate\Support\Facades\Route;

Route::controller(SiteMemoController::class)->group(function () {
    Route::get('/', 'main')->name('main');
});

Route::controller(SiteMemoExportController::class)->group(function () {
    Route::get('{encryptedId}/export/pdf', 'pdf')->name('export.pdf');
});
