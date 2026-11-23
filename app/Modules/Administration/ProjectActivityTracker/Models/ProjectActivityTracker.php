<?php

namespace App\Modules\Administration\ProjectActivityTracker\Models;

use App\Models\LogStatus;
use App\Models\Project;
use App\Models\WorkPackage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectActivityTracker extends Model
{
    use SoftDeletes;

    protected $table = 'administration_project_activity_trackers';

    protected $casts = [
        'issued_date' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function workPackage(): BelongsTo
    {
        return $this->belongsTo(WorkPackage::class, 'cc_work_package_id');
    }

    public function logStatus(): BelongsTo
    {
        return $this->belongsTo(LogStatus::class, 'log_status_id');
    }

    public function scopeForCurrentTenant(Builder $query, int $accountId, int $projectId): Builder
    {
        return $query
            ->where('project_id', $projectId)
            ->whereHas('project', fn ($project) => $project->where('account_id', $accountId))
            ->whereHas('workPackage', fn ($workPackage) => $workPackage->where('project_id', $projectId));
    }
}