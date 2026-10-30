<?php

namespace App\Modules\CostAndContract\CertificateOfPayment\Controllers;

use App\Core\Controllers\BaseModuleController;
use App\Models\LogStatus;
use App\Modules\CostAndContract\CertificateOfPayment\Models\CertificateOfPayment;
use App\Support\CurrentContext;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class CertificateOfPaymentController extends BaseModuleController
{
    protected const MODULE_PATH = 'modules.cost-and-contract.certificate-of-payment.';

    public function __construct(
        protected CurrentContext $currentContext
    ) {}

    public function main(): View
    {
        $statuses = LogStatus::pluck('status', 'id');

        $items = CertificateOfPayment::query()
            ->with(['contractDocument.vendor'])
            ->where('project_id', $this->currentContext->projectId())
            ->orderBy('id')
            ->get();

        $rows = $items->map(function ($i) use ($statuses) {
            return [
                'number' => $i->copNumber,
                'date' => Carbon::parse($i->getRawOriginal('issued_date'))->format('d-M-Y'),
                'contract' => $i->contractDocument?->contract_no ?? '',
                'contractor' => $i->contractDocument?->vendor?->company_name ?? '',
                'amount' => number_format((float) $i->amount_to_be_paid_excl_vat, 0, ',', '.'),
                'status' => $statuses->get($i->log_status_id, ''),
                'encrypted_id' => $this->cryptoService()->encryptId($i->id),
            ];
        });

        return $this->viewModule('main', ['rows' => $rows]);
    }
}
