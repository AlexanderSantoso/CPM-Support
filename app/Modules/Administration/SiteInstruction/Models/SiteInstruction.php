<?php

namespace App\Modules\Administration\SiteInstruction\Models;

use App\Helpers\Helper;
use App\Models\ContractDocument;
use App\Models\LogStatus;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteInstruction extends Model
{
    use SoftDeletes;

    protected $table = 'administration_site_instructions';

    protected $casts = [
        'issued_date' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function contractDocument(): BelongsTo
    {
        return $this->belongsTo(ContractDocument::class, 'cc_contract_document_id');
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

    protected function siteInstructionNumber(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->created_at || ! $this->doc_sequence) {
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
