<?php

use App\Modules\CostAndContract\CertificateOfPayment\Controllers\CertificateOfPaymentController;
use App\Modules\CostAndContract\CertificateOfPayment\Controllers\CertificateOfPaymentExportController;
use Illuminate\Support\Facades\Route;

Route::controller(CertificateOfPaymentController::class)->group(function () {
    Route::get('/', 'main')->name('main');
});

Route::controller(CertificateOfPaymentExportController::class)->group(function () {
    Route::get('{encryptedId}/export/pdf', 'pdf')->name('export.pdf');
});
