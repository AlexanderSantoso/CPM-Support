<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectSchedule extends Model
{
    protected $table = 'project_schedules';

    public function workPackage(): BelongsTo
    {
        return $this->belongsTo(ProjectWorkPackage::class, 'project_work_package_id');
    }
}
