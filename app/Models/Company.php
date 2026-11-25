<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';

    public function projectSchedules(): HasMany
    {
        return $this->hasMany(ProjectSchedule::class, 'company_id');
    }
}
