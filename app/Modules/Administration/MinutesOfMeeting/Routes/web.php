<?php
use Illuminate\Support\Facades\Route;

use App\Modules\Administration\MinutesOfMeeting\Controllers\MinutesOfMeetingExportController;

Route::controller(MinutesOfMeetingExportController::class)->group(function () {
    Route::get('{encryptedId}/export/pdf', 'pdf')->name('export.pdf');
});
