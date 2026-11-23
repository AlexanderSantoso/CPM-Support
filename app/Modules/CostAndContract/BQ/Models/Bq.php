<?php

namespace App\Modules\CostAndContract\BQ\Models;

use App\Models\ContractDocument;
use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bq extends Model
{
    use SoftDeletes;

    protected $table = 'cost_contract_bqs';

    protected $casts = [
        'issued_date' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function contractDocument(): BelongsTo
    {
        return $this->belongsTo(ContractDocument::class, 'cc_contract_document_id');
    }

    public function scopeForCurrentTenant(Builder $query, int $accountId, int $projectId): Builder
    {
        return $query
            ->where('project_id', $projectId)
            ->whereHas('project', fn ($project) => $project->where('account_id', $accountId))
            ->whereHas(
                'contractDocument.workPackage',
                fn ($workPackages) => $workPackages->forCurrentTenant($accountId, $projectId)
            );
    }
}
