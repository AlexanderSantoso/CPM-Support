<?php

namespace App\Modules\CostAndContract\CertificateOfPayment\Controllers;

use App\Core\Controllers\BaseModuleController;
use App\Models\LogStatus;
use App\Modules\CostAndContract\CertificateOfPayment\Models\CertificateOfPayment;
use App\Support\CurrentContext;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class CertificateOfPaymentController extends BaseModuleController
{
    protected const MODULE_PATH = 'modules.cost-and-contract.certificate-of-payment.';

    public function __construct(
        protected CurrentContext $currentContext,
    ) {}

    public function main(): View
    {
        $statuses = LogStatus::pluck('status', 'id');

        $items = CertificateOfPayment::query()
            ->with(['contractDocument.vendor'])
            ->where('project_id', $this->currentContext->projectId())
            ->orderBy('id')
            ->get();

        $rows = $items->map(function ($item) use ($statuses) {
            return [
                'number' => $item->copNumber,
                'date' => Carbon::parse($item->getRawOriginal('issued_date'))->format('d-M-Y'),
                'contract' => $item->contractDocument?->contract_no ?? '',
                'contractor' => $item->contractDocument?->vendor?->company_name ?? '',
                'amount' => number_format((float) $item->amount_to_be_paid_excl_vat, 0, ',', '.'),
                'status' => $statuses->get($item->log_status_id, ''),
                'encrypted_id' => $this->cryptoService()->encryptId($item->id),
            ];
        });

        return $this->viewModule('main', ['rows' => $rows]);
    }

    public function data(): JsonResponse
    {
        $total = CertificateOfPayment::query()
            ->forCurrentTenant(
                $this->currentContext->accountId(),
                $this->currentContext->projectId()
            )
            ->count();

        return response()->json([
            'title' => 'Certificate of Payment (COP)',
            'count' => $total,
            'statuses' => $this->statuses(),
        ]);
    }

    private function statuses(): Collection
    {
        return LogStatus::query()
            ->select(['id', 'status', 'bg_color'])
            ->whereIn('id', [1, 2, 3, 4, 5])
            ->withCount([
                'costContractCertificateOfPayments as total' =>
                    fn ($query) => $query->forCurrentTenant(
                        $this->currentContext->accountId(),
                        $this->currentContext->projectId()
                    ),
            ])
            ->orderBy('id')
            ->get();
    }
}
