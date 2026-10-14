<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkPackage extends Model
{
    use SoftDeletes;

    protected $table = 'cost_contract_work_packages';
}
