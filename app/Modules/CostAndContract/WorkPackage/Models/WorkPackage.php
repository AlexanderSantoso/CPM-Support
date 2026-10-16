<?php

namespace App\Modules\CostAndContract\WorkPackage\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Builder;
use App\Modules\Admin\Company\Models\Company;
use App\Modules\Admin\Project\Models\Project;
use App\Modules\CostAndContract\BudgetPlan\Models\BudgetPlan;
use App\Modules\CostAndContract\OwnerEstimate\Models\OwnerEstimate;
use App\Modules\CostAndContract\ContractDocument\Models\ContractDocument;
use App\Modules\CostAndContract\ChangeOrder\Models\ChangeOrder;
use App\Modules\CostAndContract\VariationOrder\Models\VariationOrder;
use App\Modules\CostAndContract\TrackingInvoice\Models\TrackingInvoice;
use App\Traits\Auditable;

class WorkPackage extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'company_id',
        'project_id',
        'identifier',
        'work_package',
        'active_work',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'deleted_by',
        'deleted_at'
    ];

    protected $table = 'cost_contract_work_packages';

    public $timestamps = false;

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function budgetPlans(): HasMany
    {
        return $this->hasMany(BudgetPlan::class, 'cc_work_package_id');
    }

    public function ownerEstimates(): HasMany
    {
        return $this->hasMany(OwnerEstimate::class, 'cc_work_package_id');
    }

    public function contractDocuments(): HasMany
    {
        return $this->hasMany(ContractDocument::class, 'cc_work_package_id');
    }

    public function latestContractDocument(): HasOne
    {
        return $this->hasOne(ContractDocument::class, 'cc_work_package_id')->latestOfMany('id');
    }

    public function changeOrders(): HasManyThrough
    {
        return $this->hasManyThrough(
            ChangeOrder::class,
            ContractDocument::class,
            'cc_work_package_id',        // FK ContractDocument -> WorkPackage
            'cc_contract_document_id',   // FK ChangeOrder -> ContractDocument
            'id',                        // PK WorkPackage
            'id'                         // PK ContractDocument
        );
    }

    public function variationOrders(): HasMany
    {
        return $this->hasMany(VariationOrder::class, 'cc_work_package_id');
    }

    public function trackingInvoices(): HasManyThrough
    {
        return $this->hasManyThrough(
            TrackingInvoice::class,
            ContractDocument::class,
            'cc_work_package_id',        // FK ContractDocument -> WorkPackage
            'cc_contract_document_id',   // FK TrackingInvoice -> ContractDocument
            'id',                        // PK WorkPackage
            'id'                         // PK ContractDocument
        );
    }

    public function scopeForCurrentTenant(Builder $query, int $accountId, int $projectId): Builder
    {
        return $query
            ->where('project_id', $projectId)
            ->whereHas('project', function (Builder $q) use ($accountId) {
                $q->where('account_id', $accountId);
            });
    }

    public function scopeWithFinancialSummary(Builder $query): Builder
    {
        return $query
                ->with('latestContractDocument.vendor')
                ->withSum('budgetPlans as budget_estimate_total', 'budget_estimate')
                ->withSum('ownerEstimates as estimated_contract_total', 'estimated_contract')
                ->withSum('contractDocuments as contract_amount_excl_vat_total', 'contract_amount_excl_vat')
                ->withSum([
                    'changeOrders as estimated_variation_change_order_total' => function ($query) {
                        $query->whereHas('latestActivity', function ($q) {
                            $q->whereIn('log_status_id', [1, 3, 4]);
                        });
                    }
                ], 'estimated_variation')
                ->withSum([
                    'variationOrders as estimated_variation_variation_order_total' => function ($query) {
                        $query->whereHas('latestActivity', function ($q) {
                            $q->where('log_status_id', 1);
                        });
                    }
                ], 'approved_variation')
                ->withSum([
                    'variationOrders as approved_variation_variation_order_total' => function ($query) {
                        $query->whereHas('latestActivity', function ($q) {
                            $q->whereIn('log_status_id', [3, 4]);
                        });
                    }
                ], 'approved_variation')
                ->withSum('trackingInvoices as invoice_submitted_total', 'invoice_submitted')
                ->withSum('trackingInvoices as invoice_paid_total', 'invoice_paid');
    }
}
