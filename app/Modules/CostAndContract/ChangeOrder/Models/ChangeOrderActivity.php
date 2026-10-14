<?php

namespace App\Modules\CostAndContract\ChangeOrder\Models;

use App\Models\LogStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChangeOrderActivity extends Model
{
    use SoftDeletes;

    protected $table = 'cost_contract_change_order_activities';

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
