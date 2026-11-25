<?php

namespace App\Modules\CostAndContract\TenderSubmission\Models;

use App\Models\LogStatus;
use App\Models\Project;
use App\Modules\CostAndContract\RFP\Models\RFP;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderSubmission extends Model
{
    use SoftDeletes;

    protected $table = 'cost_contract_tender_submissions';

    protected $casts = [
        'submit_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function rfp(): BelongsTo
    {
        return $this->belongsTo(RFP::class, 'cc_rfp_id');
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
            ->whereHas('rfp', fn ($rfp) => $rfp->where('project_id', $projectId));
    }
}