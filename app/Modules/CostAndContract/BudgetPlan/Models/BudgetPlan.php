<?php

namespace App\Modules\CostAndContract\BudgetPlan\Models;

use App\Models\LogStatus;
use App\Models\Project;
use App\Models\User;
use App\Models\WorkPackage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetPlan extends Model
{
    use SoftDeletes;

    protected $table = 'cost_contract_budget_plans';

    protected $casts = [
        'issued_date' => 'date',
        'budget_estimate' => 'decimal:2',
    ];

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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(BudgetPlanActivity::class, 'cc_budget_plan_id');
    }

    public function latestActivity(): HasOne
    {
        return $this->hasOne(BudgetPlanActivity::class, 'cc_budget_plan_id')->latestOfMany();
    }

    public function scopeForCurrentTenant(Builder $query, int $accountId, int $projectId): Builder
    {
        return $query
            ->where($this->qualifyColumn('project_id'), $projectId)
            ->whereHas('project', fn ($project) => $project->where('account_id', $accountId))
            ->whereHas('workPackage', fn ($workPackage) => $workPackage->where('project_id', $projectId));
    }

    public function scopeWithDashboardDetails(Builder $query, int $workPackageId = 0): Builder
    {
        return $query
            ->select($this->qualifyColumns([
                'id', 'cc_work_package_id', 'log_status_id', 'issued_date', 'budget_estimate', 'updated_at',
            ]))
            ->when($workPackageId, fn ($budgetPlans) => $budgetPlans->where('cc_work_package_id', $workPackageId))
            ->with([
                'workPackage:id,work_package',
                'logStatus:id,status,bg_color',
                'latestActivity.assignee:id,first_name,last_name',
            ]);
    }
}
