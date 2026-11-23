<?php

namespace App\Modules\Compliance\ProjectExecutionPlan\Models;

use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectExecutionPlan extends Model
{
    protected $table = 'compliance_project_execution_plans';

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function scopeForCurrentTenant(
        Builder $query,
        int $accountId,
        int $projectId
    ): Builder {
        return $query
            ->where('project_id', $projectId)
            ->whereHas('project', fn ($project) => $project->where('account_id', $accountId));
    }
}
