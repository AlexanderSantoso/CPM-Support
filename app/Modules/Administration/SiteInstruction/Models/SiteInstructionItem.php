<?php

namespace App\Modules\Administration\SiteInstruction\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteInstructionItem extends Model
{
    use SoftDeletes;

    protected $table = 'administration_site_instruction_items';

    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    public function siteInstruction(): BelongsTo
    {
        return $this->belongsTo(SiteInstruction::class, 'adm_site_instruction_id');
    }
}
