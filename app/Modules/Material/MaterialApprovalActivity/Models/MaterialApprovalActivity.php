<?php

namespace App\Modules\Material\MaterialApprovalActivity\Models;

use App\Modules\Material\MaterialApproval\Models\MaterialApproval;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialApprovalActivity extends Model
{
    use SoftDeletes;

    protected $table = 'material_material_approval_activities';

    public function materialApproval(): BelongsTo
    {
        return $this->belongsTo(MaterialApproval::class, 'mtrl_material_approval_id');
    }

    public function scopeForCurrentTenant(Builder $query, int $accountId, int $projectId): Builder
    {
        return $query->whereHas(
            'materialApproval',
            fn (Builder $approval) => $approval->forCurrentTenant($accountId, $projectId),
        );
    }
}
