<?php

namespace App\Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CostAndContract\VariationOrder\Models\VariationOrder;
use App\Support\CurrentContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Helpers\Helper;

class VariationOrderController extends Controller
{
    public function __construct(protected CurrentContext $currentContext) {}

    public function data(Request $request): JsonResponse
    {
        $request->validate(['work_package_id' => 'required|integer|min:1']);

        $variationOrders = VariationOrder::query()
            ->forCurrentTenant($this->currentContext->accountId(), $this->currentContext->projectId())
            ->forApprovedVariation()
            ->withDashboardDetails($request->integer('work_package_id'))
            ->orderByDesc('doc_sequence')
            ->get()
            ->map(fn (VariationOrder $variationOrder) => $this->detailRow($variationOrder));

        return response()->json(['data' => $variationOrders]);
    }

    private function detailRow(VariationOrder $variationOrder): array
    {
        $activity = $variationOrder->latestActivity;

        return [
            'variation_order_no' => $variationOrder->variation_order_number ?? '-',
            'doc_sequence' => $variationOrder->doc_sequence,
            'vendor' => $variationOrder->siteInstruction?->contractDocument?->vendor?->company_name ?? '-',
            'work_package' => $variationOrder->workPackage?->work_package ?? '-',
            'si_no' => $variationOrder->siteInstruction?->site_instruction_number ?? '-',
            'change_order_no' => $variationOrder->siteInstruction?->changeOrder?->change_order_number ?? '-',
            'approved_variation' => Helper::formatRupiah($variationOrder->approved_variation),
            'approved_variation_raw' => (float) $variationOrder->approved_variation,
            'status' => $activity?->logStatus?->status ?? '-',
            'status_bg_color' => $activity?->logStatus?->bg_color,
            'assigned_to' => $activity?->assignee?->display_name ?? '-',
        ];
    }
}
