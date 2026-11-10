<?php

namespace App\Modules\Gantt\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GanttTaskLink extends Model
{
    protected $table = 'schedule_master_schedule_task_links';

    protected $fillable = [
        'source_task_id',
        'target_task_id',
        'type',
    ];

    protected $casts = [
        'type' => 'integer',
    ];

    public function sourceTask(): BelongsTo
    {
        return $this->belongsTo(GanttTask::class, 'source_task_id');
    }

    public function targetTask(): BelongsTo
    {
        return $this->belongsTo(GanttTask::class, 'target_task_id');
    }
}