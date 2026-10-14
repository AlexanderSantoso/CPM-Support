<?php
use Illuminate\Support\Facades\Route;

use App\Modules\Administration\MinutesOfMeeting\Controllers\MinutesOfMeetingExportController;

Route::controller(MinutesOfMeetingExportController::class)->group(function () {
    Route::get('{minutesOfMeeting}/export/pdf', 'pdf')->whereNumber('minutesOfMeeting')->name('export.pdf');
});
