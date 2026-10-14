<?php

namespace App\Modules\CostAndContract\BudgetPlan\Models;

use App\Models\LogStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetPlanActivity extends Model
{
    use SoftDeletes;

    protected $table = 'cost_contract_budget_plan_activities';

    protected $casts = [
        'cc' => 'array',
        'due_date' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function logStatus(): BelongsTo
    {
        return $this->belongsTo(LogStatus::class, 'log_status_id');
    }
}
