<?php

namespace App\Modules\Administration\SiteInstruction\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Models\ContractDocument;
use App\Models\LogStatus;
use App\Models\Project;
use App\Models\User;
use App\Modules\CostAndContract\ChangeOrder\Models\ChangeOrder;
use Carbon\Carbon;
use App\Helpers\Helper;

class SiteInstruction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'cc_contract_document_id',
        'cc_change_order_id',
        'log_status_id',
        'type_of_work_id',
        'attachment_type_id',
        'potential_cost_time_id',
        'received_by_id',
        'doc_sequence',
        'doc_project_initial',
        'issued_date',
        'refer_to_dwg',
        'location',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'deleted_by',
        'deleted_at'
    ];

    protected $table = 'administration_site_instructions';

    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime'
    ];

    protected function issuedDate(): Attribute
    {
        return Attribute::make(
            set: fn($value) => $value ? Carbon::parse($value)->format('Y-m-d') : null,
            get: fn($value) => $value ? Carbon::parse($value)->format('d-M-Y') : null,
        );
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function contractDocument(): BelongsTo
    {
        return $this->belongsTo(ContractDocument::class, 'cc_contract_document_id');
    }

    public function changeOrder(): BelongsTo
    {
        return $this->belongsTo(ChangeOrder::class, 'cc_change_order_id');
    }

    public function logStatus(): BelongsTo
    {
        return $this->belongsTo(LogStatus::class, 'log_status_id');
    }

    public function typeOfWork(): BelongsTo
    {
        return $this->belongsTo(SiteInstructionTypeOfWork::class, 'type_of_work_id');
    }

    public function attachmentType(): BelongsTo
    {
        return $this->belongsTo(SiteInstructionAttachmentType::class, 'attachment_type_id');
    }

    public function potentialCostTime(): BelongsTo
    {
        return $this->belongsTo(SiteInstructionPotentialCostTime::class, 'potential_cost_time_id');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SiteInstructionItem::class, 'adm_site_instruction_id');
    }

    public function scopeForCurrentTenant(Builder $query, int $accountId, int $projectId): Builder
    {
        return $query
            ->where('project_id', $projectId)
            ->whereHas('project', function (Builder $q) use ($accountId) {
                $q->where('account_id', $accountId);
            });
    }

    protected function siteInstructionNumber(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->created_at || !$this->doc_sequence) {
                    return null;
                }

                return sprintf(
                    '%03d/SI/%s/%s/%d',
                    $this->doc_sequence,
                    $this->doc_project_initial,
                    Helper::romanMonth($this->created_at->month),
                    $this->created_at->year
                );
            }
        );
    }
}
