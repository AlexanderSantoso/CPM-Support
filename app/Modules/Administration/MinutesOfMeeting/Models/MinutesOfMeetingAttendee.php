<?php

namespace App\Modules\Administration\MinutesOfMeeting\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MinutesOfMeetingAttendee extends Model
{
    use SoftDeletes;

    protected $table = 'administration_minutes_of_meeting_attendees';

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
