<?php

use App\Modules\Administration\ClosingDocumentChecklist\Controllers\ClosingDocumentChecklistExportController;
use Illuminate\Support\Facades\Route;

Route::controller(ClosingDocumentChecklistExportController::class)->group(function () {
    Route::get('{encryptedId}/export/pdf', 'pdf')->name('export.pdf');
});
