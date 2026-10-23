<?php

namespace App\Modules\CostAndContract\VariationOrder\Models;

use App\Models\LogStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VariationOrderActivity extends Model
{
    use SoftDeletes;

    protected $table = 'cost_contract_variation_order_activities';

    public function logStatus(): BelongsTo
    {
        return $this->belongsTo(LogStatus::class, 'log_status_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
