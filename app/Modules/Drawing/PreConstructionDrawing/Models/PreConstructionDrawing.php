<?php

namespace App\Modules\Drawing\PreConstructionDrawing\Models;

use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreConstructionDrawing extends Model
{
    use SoftDeletes;

    protected $table = 'drawing_pre_construction_drawings';

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
