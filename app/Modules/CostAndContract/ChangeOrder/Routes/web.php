<?php
use Illuminate\Support\Facades\Route;

use App\Modules\CostAndContract\ChangeOrder\Controllers\ChangeOrderExportController;

Route::controller(ChangeOrderExportController::class)->group(function () {
    Route::get('{changeOrder}/export/pdf', 'pdf')->whereNumber('changeOrder')->name('export.pdf');
});
