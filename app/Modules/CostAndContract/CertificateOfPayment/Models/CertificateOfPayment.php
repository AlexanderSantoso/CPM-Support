<?php

namespace App\Modules\CostAndContract\CertificateOfPayment\Models;

use App\Helpers\Helper;
use App\Models\ContractDocument;
use App\Models\LogStatus;
use App\Models\ProgressClaim;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class CertificateOfPayment extends Model
{
    use SoftDeletes;

    protected $table = 'cost_contract_certificate_of_payments';

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function contractDocument(): BelongsTo
    {
        return $this->belongsTo(ContractDocument::class, 'cc_contract_document_id');
    }

    public function progressClaim(): BelongsTo
    {
        return $this->belongsTo(ProgressClaim::class, 'cc_progress_claim_id');
    }

    public function logStatus(): BelongsTo
    {
        return $this->belongsTo(LogStatus::class, 'log_status_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Nomor COP dari generator mentor: 002/COP/KAJIMA/ULUBELU/VIII/2026,
     * yaitu urutan / COP / inisial perusahaan / inisial proyek / bulan (Romawi) / tahun dari created_at.
     */
    protected function copNumber(): Attribute
    {
        return Attribute::make(
            get: function () {

                if (!$this->created_at || !$this->doc_sequence) {
                    return null;
                }

                return sprintf(
                    '%03d/COP/%s/%s/%s/%d',
                    $this->doc_sequence,
                    $this->doc_company_initial,
                    $this->doc_project_initial,
                    Helper::romanMonth($this->created_at->month),
                    Carbon::parse($this->created_at)->year
                );
            }
        );
    }
}
