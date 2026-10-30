<?php

use App\Modules\Administration\MinutesOfMeeting\Controllers\MinutesOfMeetingController;
use App\Modules\Administration\MinutesOfMeeting\Controllers\MinutesOfMeetingExportController;
use Illuminate\Support\Facades\Route;

Route::controller(MinutesOfMeetingController::class)->group(function () {
    Route::get('/', 'main')->name('main');
});

Route::controller(MinutesOfMeetingExportController::class)->group(function () {
    Route::get('{encryptedId}/export/pdf', 'pdf')->name('export.pdf');
});
