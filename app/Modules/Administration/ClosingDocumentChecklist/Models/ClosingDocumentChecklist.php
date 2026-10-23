<?php

namespace App\Modules\Administration\ClosingDocumentChecklist\Models;

use App\Models\LogStatus;
use App\Models\Project;
use App\Modules\CostAndContract\WorkPackage\Models\WorkPackage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClosingDocumentChecklist extends Model
{
    use SoftDeletes;

    protected $table = 'administration_closing_document_checklists';

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
            ->where($this->qualifyColumn('project_id'), $projectId)
            ->whereHas('project', fn ($project) => $project->where('account_id', $accountId));
    }
}
