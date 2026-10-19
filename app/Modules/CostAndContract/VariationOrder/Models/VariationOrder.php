<?php

namespace App\Modules\CostAndContract\VariationOrder\Models;

use App\Modules\CostAndContract\WorkPackage\Models\WorkPackage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VariationOrder extends Model
{
    protected $table = 'cost_contract_variation_orders';

    protected $casts = [
        'approved_variation' => 'decimal:2',
    ];

    public function workPackage(): BelongsTo
    {
        return $this->belongsTo(WorkPackage::class, 'cc_work_package_id');
    }
}