<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SCurve extends Model
{
    use SoftDeletes;

    protected $table = 'schedule_s_curves';
}