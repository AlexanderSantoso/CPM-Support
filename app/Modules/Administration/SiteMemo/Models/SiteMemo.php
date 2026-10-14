<?php

namespace App\Modules\Administration\SiteMemo\Models;

use App\Helpers\Helper;
use App\Models\Company;
use App\Models\LogStatus;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteMemo extends Model
{
    use SoftDeletes;

    protected $table = 'administration_site_memos';

    protected $casts = [
        'issued_date' => 'date',
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
        return $this->belongsTo(SiteMemoCategory::class, 'site_memo_category_id');
    }

    public function toCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'to_company_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected function siteMemoNumber(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->created_at || ! $this->doc_sequence) {
                    return null;
                }

                return sprintf(
                    '%03d/SM/%s/%s/%d',
                    $this->doc_sequence,
                    $this->doc_project_initial,
                    Helper::romanMonth($this->created_at->month),
                    $this->created_at->year
                );
            }
        );
    }
}
