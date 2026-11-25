<?php

namespace App\Modules\CostAndContract\OwnerEstimate\Models;

use App\Modules\CostAndContract\WorkPackage\Models\WorkPackage;
use App\Models\LogStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OwnerEstimate extends Model
{
    protected $table = 'cost_contract_owner_estimates';

    protected $casts = [
        'issued_date' => 'date',
        'estimated_contract' => 'decimal:2',
    ];

    public function workPackage(): BelongsTo
    {
        return $this->belongsTo(WorkPackage::class, 'cc_work_package_id');
    }

    public function logStatus(): BelongsTo
    {
        return $this->belongsTo(LogStatus::class, 'log_status_id');
    }

    public function latestActivity(): HasOne
    {
        return $this->hasOne(OwnerEstimateActivity::class, 'cc_owner_estimate_id')->latestOfMany();
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeForCurrentTenant(Builder $query, int $accountId, int $projectId): Builder
    {
        return $query->whereHas('workPackage', fn ($workPackages) => $workPackages->forCurrentTenant($accountId, $projectId));
    }

    public function scopeWithDashboardDetails(Builder $query, int $workPackageId = 0): Builder
    {
        return $query
            ->select($this->qualifyColumns([
                'id', 'cc_work_package_id', 'log_status_id', 'issued_date', 'remarks',
                'estimated_contract', 'updated_by', 'updated_at',
            ]))
            ->when($workPackageId, fn ($estimates) => $estimates->where('cc_work_package_id', $workPackageId))
            ->with([
                'workPackage:id,work_package',
                'logStatus:id,status,bg_color',
                'latestActivity.assignee:id,first_name,last_name',
                'latestActivity.creator:id,first_name,last_name',
                'updater:id,first_name,last_name',
            ]);
    }
}
