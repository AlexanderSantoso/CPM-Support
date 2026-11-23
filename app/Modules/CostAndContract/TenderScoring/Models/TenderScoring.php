<?php

namespace App\Modules\CostAndContract\TenderScoring\Models;

use App\Models\LogStatus;
use App\Models\Project;
use App\Models\WorkPackage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderScoring extends Model
{
    use SoftDeletes;

    protected $table = 'cost_contract_tender_scorings';

    protected $casts = [
        'issued_date' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function workPackage(): BelongsTo
    {
        return $this->belongsTo(WorkPackage::class, 'cc_rfp_id');
    }

    public function scopeForCurrentTenant(Builder $query, int $accountId, int $projectId): Builder
    {
        return $query
            ->where('project_id', $projectId)
            ->whereHas('project', fn ($project) => $project->where('account_id', $accountId))
            ->whereHas('workPackage', fn ($workPackage) => $workPackage->where('project_id', $projectId));
    }
}
