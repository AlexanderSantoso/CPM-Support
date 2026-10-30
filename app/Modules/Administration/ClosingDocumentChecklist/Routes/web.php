<?php

use App\Modules\Administration\ClosingDocumentChecklist\Controllers\ClosingDocumentChecklistController;
use App\Modules\Administration\ClosingDocumentChecklist\Controllers\ClosingDocumentChecklistExportController;
use Illuminate\Support\Facades\Route;

Route::controller(ClosingDocumentChecklistController::class)->group(function () {
    Route::get('/', 'main')->name('main');
});

Route::controller(ClosingDocumentChecklistExportController::class)->group(function () {
    Route::get('{encryptedId}/export/pdf', 'pdf')->name('export.pdf');
});
