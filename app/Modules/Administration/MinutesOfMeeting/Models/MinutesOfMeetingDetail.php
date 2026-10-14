<?php

namespace App\Modules\Administration\MinutesOfMeeting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MinutesOfMeetingDetail extends Model
{
    use SoftDeletes;

    protected $table = 'administration_minutes_of_meeting_details';

    protected $casts = [
        'due_date' => 'date',
    ];
}
