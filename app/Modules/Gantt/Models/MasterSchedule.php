<?php

namespace App\Modules\Gantt\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Project;
use App\Modules\Gantt\Models\GanttTask;
use Carbon\Carbon;

class MasterSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $table = 'schedule_master_schedules';

    public $timestamps = false;

    protected $casts = [
        'authorization' => 'array',
        'start_date'    => 'date',
        'end_date'      => 'date',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    protected function startDate(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value ? Carbon::parse($value)->format('Y-m-d') : null,
        );
    }


    protected function startDateFormatted(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => !empty($attributes['start_date']) ? Carbon::parse($attributes['start_date'])->format('d-M-Y') : null,
        );
    }

    protected function endDate(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value ? Carbon::parse($value)->format('Y-m-d') : null,
        );
    }

    protected function endDateFormatted(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => !empty($attributes['end_date']) ? Carbon::parse($attributes['end_date'])->format('d-M-Y') : null,
        );
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(GanttTask::class, 'sch_master_schedule_id');
    }

    public function baselines(): HasMany
    {
        return $this->hasMany(GanttTaskLink::class, 'sch_master_schedule_id');
    }

    public function scopeForCurrentTenant(Builder $query, int $accountId, int $projectId): Builder
    {
        return $query
                ->where('project_id', $projectId)
                ->whereHas('project', function (Builder $q) use ($accountId) {
                    $q->where('account_id', $accountId);
                });
    }

    protected static function booted(): void
    {
        static::deleting(function (MasterSchedule $masterSchedule) {
            $masterSchedule->tasks()
                           ->whereNull('parent_id')
                           ->get()
                           ->each(function ($task) {
                                $task->delete();
                           });

            $masterSchedule->baselines()
                           ->get()
                           ->each(function ($baseline) {
                                $baseline->delete();
                           });

            $masterSchedule->weeklyDaysOff()->delete();

            $masterSchedule->publicHolidays()->delete();
        });
    }
}
