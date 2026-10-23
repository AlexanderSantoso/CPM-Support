<?php

use Illuminate\Support\Facades\Route;
use App\Modules\CostAndContract\CertificateOfPayment\Controllers\CertificateOfPaymentController;

Route::get('data', [CertificateOfPaymentController::class, 'data'])
    ->name('data');