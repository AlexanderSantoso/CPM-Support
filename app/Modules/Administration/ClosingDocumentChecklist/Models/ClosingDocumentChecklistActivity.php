<?php

namespace App\Modules\Administration\ClosingDocumentChecklist\Models;

use App\Models\LogStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClosingDocumentChecklistActivity extends Model
{
    use SoftDeletes;

    protected $table = 'administration_closing_document_checklist_activities';

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(ClosingDocumentChecklist::class, 'adm_closing_document_checklist_id');
    }

    public function logStatus(): BelongsTo
    {
        return $this->belongsTo(LogStatus::class, 'log_status_id');
    }
}
