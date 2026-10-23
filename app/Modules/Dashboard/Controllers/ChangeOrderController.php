<?php

namespace App\Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CostAndContract\ChangeOrder\Models\ChangeOrder;
use App\Support\CurrentContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Helpers\Helper;

class ChangeOrderController extends Controller
{
    public function __construct(protected CurrentContext $currentContext) {}

    public function data(Request $request): JsonResponse
    {
        $changeOrders = ChangeOrder::query()
            ->forCurrentTenant($this->currentContext->accountId(), $this->currentContext->projectId())
            ->forEstimatedVariation()
            ->withDashboardDetails($request->integer('work_package_id'))
            ->orderByDesc('date_raised')
            ->get()
            ->map(fn (ChangeOrder $changeOrder) => $this->detailRow($changeOrder));

        return response()->json(['data' => $changeOrders]);
    }

    private function detailRow(ChangeOrder $changeOrder): array
    {
        return [
            'date_raised' => $changeOrder->date_raised?->format('d M Y') ?? '-',
            'date_raised_sort' => $changeOrder->date_raised?->format('Y-m-d') ?? '',
            'input_by' => $changeOrder->latestActivity?->creator?->display_name ?? '-',
            'cr_no' => $changeOrder->change_order_number ?? '-',
            'doc_sequence' => $changeOrder->doc_sequence,
            'contract_no' => $changeOrder->contractDocument?->contract_no ?? '-',
            'vendor' => $changeOrder->contractDocument?->vendor?->company_name ?? '-',
            'work_package' => $changeOrder->contractDocument?->workPackage?->work_package ?? '-',
            'building_phase' => $changeOrder->phase?->name ?? '-',
            'type_of_change' => $changeOrder->type?->name ?? '-',
            'originator' => $changeOrder->origin?->name ?? '-',
            'priority' => $changeOrder->priority?->name ?? '-',
            'description' => $changeOrder->description ?? '-',
            'estimated_variation' => Helper::formatRupiah($changeOrder->estimated_variation),
            'potential_impact_on_project' => $changeOrder->potential_impact_on_project ?? '-',
            'cost_estimation' => Helper::formatRupiah($changeOrder->cost_estimation),
            'time_estimation' => $changeOrder->time_estimation,
            'pm_comment' => $changeOrder->pm_comment ?? '-',
            'consultant_design_specialist_comment' => $changeOrder->consultant_design_specialist_comment ?? '-',
            'qs_comment' => $changeOrder->qs_comment ?? '-',
            'client_comment' => $changeOrder->client_comment ?? '-',
            'status' => $changeOrder->logStatus?->status ?? '-',
            'status_bg_color' => $changeOrder->logStatus?->bg_color,
            'assigned_to' => $changeOrder->latestActivity?->assignee?->display_name ?? '-',
        ];
    }
}
