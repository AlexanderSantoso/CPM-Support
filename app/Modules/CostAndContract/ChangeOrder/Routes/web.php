<?php
use Illuminate\Support\Facades\Route;

use App\Modules\CostAndContract\ChangeOrder\Controllers\ChangeOrderExportController;

Route::controller(ChangeOrderExportController::class)->group(function () {
    Route::get('{encryptedId}/export/pdf', 'pdf')->name('export.pdf');
});
