<?php

namespace App\Modules\CostAndContract\ChangeOrder\Models;

use App\Helpers\Helper;
use App\Models\ChangeOrderOrigin;
use App\Models\ChangeOrderType;
use App\Models\LogStatus;
use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\ContractDocument;
use App\Models\ChangeOrderPriority;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class ChangeOrder extends Model
{
    use SoftDeletes;

    protected $table = 'cost_contract_change_orders';

    protected $casts = [
        'date_raised' => 'date',
        'estimated_variation' => 'decimal:2',
        'cost_estimation' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function phase(): BelongsTo
    {
        return $this->belongsTo(ProjectPhase::class, 'project_phase_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ChangeOrderType::class, 'change_type_id');
    }

    public function origin(): BelongsTo
    {
        return $this->belongsTo(ChangeOrderOrigin::class, 'change_origin_id');
    }

    public function priority(): BelongsTo
    {
    return $this->belongsTo(ChangeOrderPriority::class, 'change_priority_id');
    }

    public function logStatus(): BelongsTo
    {
        return $this->belongsTo(LogStatus::class, 'log_status_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ChangeOrderActivity::class, 'cc_change_order_id');
    }

    public function latestActivity(): HasOne
    {
        return $this->hasOne(ChangeOrderActivity::class, 'cc_change_order_id')->latestOfMany();
    }

    public function contractDocument(): BelongsTo
    {
    return $this->belongsTo(ContractDocument::class, 'cc_contract_document_id');
    }

    protected function changeOrderNumber(): Attribute
    {
        return Attribute::make(
            get: function () {

                if (!$this->created_at || !$this->doc_sequence) {
                    return null;
                }

                return sprintf(
                    '%03d/CCO/%s/%s/%d',
                    $this->doc_sequence,
                    $this->doc_project_initial,
                    Helper::romanMonth($this->created_at->month),
                    Carbon::parse($this->created_at)->year
                );
            }
        );
    }
}
