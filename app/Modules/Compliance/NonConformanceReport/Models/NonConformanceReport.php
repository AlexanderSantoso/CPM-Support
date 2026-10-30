<?php

namespace App\Modules\Compliance\NonConformanceReport\Models;

use App\Models\Company;
use App\Models\LogStatus;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class NonConformanceReport extends Model
{
    use SoftDeletes;

    protected $table = 'compliance_non_conformance_reports';

    protected $casts = [
        'inspection_date' => 'date',
        'completion_date' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function logStatus(): BelongsTo
    {
        return $this->belongsTo(LogStatus::class, 'log_status_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(NonConformanceReportCategory::class, 'category_id');
    }

    public function firmResponsible(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'firm_responsible_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(NonConformanceReportPhoto::class, 'comp_non_conformance_report_id');
    }

    /**
     * Nomor mengikuti template: no/NCR-<Project>/MM/YYYY, bulan dan tahun dari tanggal inspeksi.
     */
    protected function ncrNumber(): Attribute
    {
        return Attribute::make(
            get: function () {
                $inspection = $this->getRawOriginal('inspection_date');

                if (! $inspection || ! $this->doc_sequence) {
                    return null;
                }

                $date = Carbon::parse($inspection);

                return sprintf(
                    '%03d/NCR-%s/%s/%s',
                    $this->doc_sequence,
                    $this->doc_project_initial,
                    $date->format('m'),
                    $date->format('Y')
                );
            }
        );
    }
}
