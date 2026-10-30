<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgressClaim extends Model
{
    use SoftDeletes;

    protected $table = 'cost_contract_progress_claims';

    protected $casts = [
        'physical_progress' => 'decimal:2',
    ];

    public function termOfPayment(): BelongsTo
    {
        return $this->belongsTo(ContractDocumentTermOfPayment::class, 'cc_contract_document_term_of_payment_id');
    }
}
