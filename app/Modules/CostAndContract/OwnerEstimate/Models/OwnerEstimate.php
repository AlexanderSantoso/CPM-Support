<?php

namespace App\Modules\CostAndContract\OwnerEstimate\Models;

use App\Modules\CostAndContract\WorkPackage\Models\WorkPackage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OwnerEstimate extends Model
{
    protected $table = 'cost_contract_owner_estimates';

    protected $casts = [
        'estimated_contract' => 'decimal:2',
    ];

    public function workPackage(): BelongsTo
    {
        return $this->belongsTo(WorkPackage::class, 'cc_work_package_id');
    }
}
