<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Modules\CostAndContract\WorkPackage\Models\WorkPackage;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractDocument extends Model
{
    use SoftDeletes;

    protected $table = 'cost_contract_contract_documents';

    protected $casts = [
        'issued_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'vendor_id');
    }

    public function workPackage(): BelongsTo
    {
        return $this->belongsTo(WorkPackage::class, 'cc_work_package_id');
    }

    public function scopeForCurrentTenant(Builder $query, int $accountId, int $projectId): Builder
    {
        return $query->whereHas('workPackage', fn ($workPackages) => $workPackages->forCurrentTenant($accountId, $projectId));
    }

    public function termsOfPayment(): HasMany
    {
        return $this->hasMany(ContractDocumentTermOfPayment::class, 'cc_contract_document_id');
    }

    public function scopeWithDashboardDetails(Builder $query, int $workPackageId = 0): Builder
    {
        return $query
            ->when($workPackageId, fn ($contracts) => $contracts->where('cc_work_package_id', $workPackageId))
            ->with(['vendor:id,company_name', 'workPackage:id,work_package', 'termsOfPayment:id,cc_contract_document_id,payment_term']);
    }
}
