<?php

use App\Modules\Administration\SiteMemo\Controllers\SiteMemoExportController;
use Illuminate\Support\Facades\Route;

Route::controller(SiteMemoExportController::class)->group(function () {
    Route::get('{siteMemo}/export/pdf', 'pdf')->whereNumber('siteMemo')->name('export.pdf');
});
