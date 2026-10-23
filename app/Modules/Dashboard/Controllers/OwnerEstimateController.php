<?php

namespace App\Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CostAndContract\OwnerEstimate\Models\OwnerEstimate;
use App\Support\CurrentContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Helpers\Helper;

class OwnerEstimateController extends Controller
{
    public function __construct(protected CurrentContext $currentContext) {}

    public function data(Request $request): JsonResponse
    {
        $ownerEstimates = OwnerEstimate::query()
            ->forCurrentTenant($this->currentContext->accountId(), $this->currentContext->projectId())
            ->withDashboardDetails($request->integer('work_package_id'))
            ->orderByDesc('issued_date')
            ->get()
            ->map(fn (OwnerEstimate $ownerEstimate) => $this->detailRow($ownerEstimate));

        return response()->json(['data' => $ownerEstimates]);
    }

    private function detailRow(OwnerEstimate $ownerEstimate): array
    {
        return [
            'issued_date' => $ownerEstimate->issued_date?->format('d M Y') ?? '-',
            'issued_date_sort' => $ownerEstimate->issued_date?->format('Y-m-d') ?? '',
            'work_package' => $ownerEstimate->workPackage?->work_package ?? '-',
            'remarks' => filled($ownerEstimate->remarks) ? $ownerEstimate->remarks : '-',
            'estimated_contract' => Helper::formatRupiah($ownerEstimate->estimated_contract),
            'estimated_contract_raw' => (float) $ownerEstimate->estimated_contract,
            'status' => $ownerEstimate->logStatus?->status ?? '-',
            'status_bg_color' => $ownerEstimate->logStatus?->bg_color,
            'assigned_to' => $ownerEstimate->latestActivity?->assignee?->display_name ?? '-',
            'last_updated_by' => $ownerEstimate->latestActivity?->creator?->display_name ?? $ownerEstimate->updater?->display_name ?? '-',
            'last_updated' => ($ownerEstimate->latestActivity?->updated_at ?? $ownerEstimate->updated_at)?->format('d M Y H:i') ?? '-',
        ];
    }
}
