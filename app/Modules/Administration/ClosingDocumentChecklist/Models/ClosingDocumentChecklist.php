<?php

namespace App\Modules\Administration\ClosingDocumentChecklist\Models;

use App\Models\LogStatus;
use App\Models\Project;
use App\Models\User;
use App\Models\WorkPackage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClosingDocumentChecklist extends Model
{
    use SoftDeletes;

    protected $table = 'administration_closing_document_checklists';

    // checklist_item berisi daftar id item yang dipilih, misalnya [1, 2, 4].
    protected $casts = [
        'checklist_item' => 'array',
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

    public function activities(): HasMany
    {
        return $this->hasMany(ClosingDocumentChecklistActivity::class, 'adm_closing_document_checklist_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
