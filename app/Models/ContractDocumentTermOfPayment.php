<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractDocumentTermOfPayment extends Model
{
    use SoftDeletes;

    protected $table = 'cost_contract_contract_document_term_of_payments';
}