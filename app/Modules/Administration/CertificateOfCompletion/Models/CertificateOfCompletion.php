<?php

namespace App\Modules\Administration\CertificateOfCompletion\Models;

use App\Helpers\Helper;
use App\Models\Company;
use App\Models\ContractDocument;
use App\Models\LogStatus;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificateOfCompletion extends Model
{
    use SoftDeletes;

    protected $table = 'administration_certificate_of_completions';

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

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected function certificateNumber(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->created_at || ! $this->doc_sequence) {
                    return null;
                }

                return sprintf(
                    '%03d/CTF/%s/%s/%d',
                    $this->doc_sequence,
                    $this->doc_project_initial,
                    Helper::romanMonth($this->created_at->month),
                    $this->created_at->year
                );
            }
        );
    }
}
