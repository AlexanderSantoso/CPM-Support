<?php

namespace App\Modules\CostAndContract\VariationOrder\Models;

use App\Helpers\Helper;
use App\Models\LogStatus;
use App\Models\User;
use App\Modules\Administration\SiteInstruction\Models\SiteInstruction;
use App\Modules\CostAndContract\WorkPackage\Models\WorkPackage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class VariationOrder extends Model
{
    use SoftDeletes;

    protected $table = 'cost_contract_variation_orders';

    protected $casts = [
        'approved_variation' => 'decimal:2',
    ];

    public function workPackage(): BelongsTo
    {
        return $this->belongsTo(WorkPackage::class, 'cc_work_package_id');
    }

    public function logStatus(): BelongsTo
    {
        return $this->belongsTo(LogStatus::class, 'log_status_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function siteInstruction(): BelongsTo
    {
        return $this->belongsTo(SiteInstruction::class, 'adm_site_instruction_id');
    }

    public function latestActivity(): HasOne
    {
        return $this->hasOne(VariationOrderActivity::class, 'cc_variation_order_id')->latestOfMany();
    }

    public function scopeForCurrentTenant(Builder $query, int $accountId, int $projectId): Builder
    {
        return $query->whereHas('workPackage', fn ($workPackages) => $workPackages->forCurrentTenant($accountId, $projectId));
    }

    public function scopeForApprovedVariation(Builder $query): Builder
    {
        return $query->whereHas('latestActivity', fn ($activity) => $activity->whereIn('log_status_id', [3, 4]));
    }

    public function scopeWithDashboardDetails(Builder $query, int $workPackageId): Builder
    {
        return $query
            ->where($this->qualifyColumn('cc_work_package_id'), $workPackageId)
            ->with([
                'workPackage:id,work_package',
                'siteInstruction.contractDocument.vendor:id,company_name',
                'siteInstruction.changeOrder',
                'latestActivity.logStatus:id,status,bg_color',
                'latestActivity.assignee:id,first_name,last_name',
            ]);
    }

    protected function variationOrderNumber(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->doc_sequence && $this->doc_project_initial && $this->created_at
                ? sprintf(
                    '%03d-VO/%s/%s/%d',
                    $this->doc_sequence,
                    $this->doc_project_initial,
                    Helper::romanMonth($this->created_at->month),
                    $this->created_at->year,
                )
                : null,
        );
    }
}
