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
use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use App\Modules\CostAndContract\BudgetPlan\Models\BudgetPlan;
use App\Modules\CostAndContract\OwnerEstimate\Models\OwnerEstimate;
use App\Models\ContractDocument;
use App\Modules\CostAndContract\ChangeOrder\Models\ChangeOrder;
use App\Modules\CostAndContract\VariationOrder\Models\VariationOrder;
use App\Modules\CostAndContract\TrackingInvoice\Models\TrackingInvoice;
use App\Traits\Auditable;

class WorkPackage extends Model
{
    use HasFactory, SoftDeletes;

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

    protected $casts = [
        'updated_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
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
            ->whereHas('project', fn (Builder $project) => $project->where('account_id', $accountId));
    }

    public function scopeWithCurrentStatus(Builder $query): Builder
    {
        return $query
            ->select($this->qualifyColumn('id'))
            ->with([
                'budgetPlans.latestActivity',
                'ownerEstimates:id,cc_work_package_id,log_status_id',
                'ownerEstimates.latestActivity',
                'contractDocuments:id,cc_work_package_id',
                'changeOrders.latestActivity',
                'variationOrders:id,cc_work_package_id,log_status_id',
            ]);
    }

    public function scopeWithFinancialOverview(Builder $query): Builder
    {
        return $query
            ->select($this->qualifyColumns(['id', 'work_package']))
            ->withFinancialSummary()
            ->without(['latestContractDocument', 'latestContractDocument.vendor'])
            ->withCount([
                'contractDocuments',
                'changeOrders as estimated_change_order_count' => fn ($changeOrders) => $changeOrders->forEstimatedVariation(),
            ]);
    }

    public function scopeWithFinancialSummary(Builder $query): Builder
    {
        return $query
            ->with('latestContractDocument.vendor')
            ->withSum('budgetPlans as budget_estimate_total', 'budget_estimate')
            ->withSum('ownerEstimates as estimated_contract_total', 'estimated_contract')
            ->withSum('contractDocuments as contract_amount_excl_vat_total', 'contract_amount_excl_vat')
            ->withSum([
                'changeOrders as estimated_variation_change_order_total' => fn ($changeOrders) => $changeOrders->forEstimatedVariation(),
            ], 'estimated_variation')
            ->withSum([
                'variationOrders as estimated_variation_variation_order_total' => fn ($variationOrders) => $variationOrders->where('log_status_id', 1),
            ], 'approved_variation')
            ->withSum([
                'variationOrders as approved_variation_variation_order_total' => fn ($variationOrders) => $variationOrders->forApprovedVariation(),
            ], 'approved_variation');
    }

    public function scopeWithDashboardDetails(Builder $query): Builder
    {
        return $query
            ->select($this->qualifyColumns(['id', 'work_package', 'company_id', 'updated_by', 'updated_at']))
            ->with(['company:id,company_name', 'updater:id,first_name,last_name']);
    }
}
