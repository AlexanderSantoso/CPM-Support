<?php

namespace App\Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ContractDocument;
use App\Support\CurrentContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Helpers\Helper;

class ContractController extends Controller
{
    public function __construct(protected CurrentContext $currentContext) {}

    public function data(Request $request): JsonResponse
    {
        $contracts = ContractDocument::query()
            ->forCurrentTenant($this->currentContext->accountId(), $this->currentContext->projectId())
            ->withDashboardDetails($request->integer('work_package_id'))
            ->orderByDesc('issued_date')
            ->get()
            ->map(fn (ContractDocument $contract) => $this->detailRow($contract));

        return response()->json(['data' => $contracts]);
    }

    private function detailRow(ContractDocument $contract): array
    {
        $contractAmount = (float) $contract->contract_amount_excl_vat;

        return [
            'contract_no' => $contract->contract_no ?? '-',
            'issued_date' => $contract->issued_date?->format('d M Y') ?? '-',
            'issued_date_sort' => $contract->issued_date?->format('Y-m-d') ?? '',
            'vendor' => $contract->vendor?->company_name ?? '-',
            'address' => $contract->address ?? '-',
            'work_package' => $contract->workPackage?->work_package ?? '-',
            'start_date' => $contract->start_date?->format('d M Y') ?? '-',
            'start_date_sort' => $contract->start_date?->format('Y-m-d') ?? '',
            'end_date' => $contract->end_date?->format('d M Y') ?? '-',
            'end_date_sort' => $contract->end_date?->format('Y-m-d') ?? '',
            'contract_amount_excl_vat' => Helper::formatRupiah($contractAmount),
            'contract_amount_excl_vat_raw' => $contractAmount,
            'contract_amount_incl_vat' => Helper::formatRupiah($contractAmount * (1 + ((float) $contract->vat_rate / 100))),
            'down_payment' => Helper::formatRupiah($contract->down_payment) . '%',
            'down_payment_raw' => (float) $contract->down_payment,
            'retention' => Helper::formatRupiah($contract->retention) . '%',
            'retention_raw' => (float) $contract->retention,
            'term_of_payment' => '',
        ];
    }
}
