<?php

namespace App\Modules\CostAndContract\CertificateOfPayment\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LogStatus;
use App\Modules\CostAndContract\CertificateOfPayment\Models\CertificateOfPayment;
use App\Support\CurrentContext;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class CertificateOfPaymentController extends Controller
{
    public function __construct(
        protected CurrentContext $currentContext,
    ) {}

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
