<?php

namespace App\Modules\CostAndContract\CertificateOfPayment\Models;

use App\Models\ContractDocument;
use App\Models\LogStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificateOfPayment extends Model
{
    use SoftDeletes;

    protected $table = 'cost_contract_certificate_of_payments';

    public function logStatus(): BelongsTo
    {
        return $this->belongsTo(LogStatus::class, 'log_status_id');
    }

    public function contractDocument(): BelongsTo
    {
        return $this->belongsTo(
            ContractDocument::class,
            'cc_contract_document_id'
        );
    }

    public function scopeForCurrentTenant(
        Builder $query,
        int $accountId,
        int $projectId
    ): Builder {
        return $query
            ->where($this->qualifyColumn('project_id'), $projectId)
            ->whereHas(
                'contractDocument.workPackage',
                fn ($workPackages) =>
                    $workPackages->forCurrentTenant($accountId, $projectId)
            );
    }
}