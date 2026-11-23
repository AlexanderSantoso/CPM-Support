<?php

namespace App\Modules\Compliance\InspectionTesting\Models;

use App\Models\LogStatus;
use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionTesting extends Model
{
    protected $table = 'compliance_inspection_testing_plans';

    public function logStatus(): BelongsTo
    {
        return $this->belongsTo(LogStatus::class, 'log_status_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function scopeForCurrentTenant(Builder $query, int $accountId, int $projectId): Builder
    {
        return $query
            ->where('project_id', $projectId)
            ->whereHas('project', fn ($project) => $project->where('account_id', $accountId));
    }
}