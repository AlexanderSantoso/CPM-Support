<?php

use App\Modules\Administration\CertificateOfCompletion\Controllers\CertificateOfCompletionExportController;
use Illuminate\Support\Facades\Route;

Route::controller(CertificateOfCompletionExportController::class)->group(function () {
    Route::get('{encryptedId}/export/pdf', 'pdf')->name('export.pdf');
});
