<?php

namespace App\Modules\CostAndContract\PreQualification\Models;

use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreQualification extends Model
{
    protected $table = 'cost_contract_pre_qualifications';

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
