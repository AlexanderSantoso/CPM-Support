<?php

namespace App\Modules\Administration\MinutesOfMeeting\Models;

use App\Helpers\Helper;
use App\Models\Project;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MinutesOfMeeting extends Model
{
    use SoftDeletes;

    protected $table = 'administration_minutes_of_meetings';

    protected $casts = [
        'issued_date' => 'date',
        'visible_to_role_id' => 'array',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(MinutesOfMeetingAttendee::class, 'adm_minutes_of_meeting_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(MinutesOfMeetingDetail::class, 'adm_minutes_of_meeting_id');
    }

    // Nomor MoM, contoh: 001/MoM/ULUBELU/IX/2026
    // Asumsi: bulan dan tahun dari created_at, mengikuti pola nomor Change Order.
    protected function minutesOfMeetingNumber(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->created_at || ! $this->doc_sequence) {
                    return null;
                }

                return sprintf(
                    '%03d/MoM/%s/%s/%d',
                    $this->doc_sequence,
                    $this->doc_project_initial,
                    Helper::romanMonth($this->created_at->month),
                    $this->created_at->year
                );
            }
        );
    }
}
