<?php

namespace App\Modules\Compliance\NonConformanceReport\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NonConformanceReportPhoto extends Model
{
    use SoftDeletes;

    protected $table = 'compliance_non_conformance_report_photos';

    // Tabel ini tidak punya kolom updated_at.
    public $timestamps = false;
}
