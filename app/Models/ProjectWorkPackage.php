<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectWorkPackage extends Model
{
    protected $table = 'project_work_packages';

    public function schedules(): HasMany
    {
        return $this->hasMany(ProjectSchedule::class, 'project_work_package_id');
    }
}
