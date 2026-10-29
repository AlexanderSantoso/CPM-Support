<?php

namespace App\Modules\Compliance\NonConformanceReport\Models;

use App\Models\Company;
use App\Models\LogStatus;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
