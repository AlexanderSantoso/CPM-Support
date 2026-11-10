<?php

namespace App\Modules\Gantt\Models;

use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;

class GanttTask extends Model
{
    protected $table = 'schedule_master_schedule_tasks';
    protected $fillable = [
        'account_id',
        'project_id',
        'parent_id',
        'text',
        'start_date',
        'end_date',
        'type',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    protected function startDate(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value ? Carbon::parse($value)->format('Y-m-d') : null,
            get: fn ($value) => $value ? Carbon::parse($value)->format('d-M-Y') : null,
        );
    }

    protected function endDate(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value ? Carbon::parse($value)->format('Y-m-d') : null,
            get: fn ($value) => $value ? Carbon::parse($value)->format('d-M-Y') : null,
        );
    }

    public function calculateDurationInDays(): ?int
    {
        if (!$this->start_date || !$this->end_date) {
            return null;
        }
        $start = Carbon::parse($this->start_date)->startOfDay();
        $end = Carbon::parse($this->end_date)->startOfDay();

        if ($end->lt($start)) {
            return null;
        }

        return (int) $start->diffInDays($end);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function scopeForCurrentTenant(Builder $query, int $accountId, int $projectId): Builder
    {
        return $query
            ->where('project_id', $projectId)
            ->whereHas('project', function (Builder $q) use ($accountId) {
                $q->where('account_id', $accountId);
            });
    }

}
