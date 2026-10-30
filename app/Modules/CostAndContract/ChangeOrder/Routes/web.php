<?php

use App\Modules\CostAndContract\ChangeOrder\Controllers\ChangeOrderController;
use App\Modules\CostAndContract\ChangeOrder\Controllers\ChangeOrderExportController;
use Illuminate\Support\Facades\Route;

Route::controller(ChangeOrderController::class)->group(function () {
    Route::get('/', 'main')->name('main');
});

Route::controller(ChangeOrderExportController::class)->group(function () {
    Route::get('{encryptedId}/export/pdf', 'pdf')->name('export.pdf');
});
