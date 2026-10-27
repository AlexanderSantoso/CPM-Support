<?php

namespace App\Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CostAndContract\BudgetPlan\Models\BudgetPlan;
use App\Support\CurrentContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Helpers\Helper;

class BudgetPlanController extends Controller
{
    public function __construct(protected CurrentContext $currentContext) {}

    public function data(Request $request): JsonResponse
    {
        $budgetPlans = BudgetPlan::query()
            ->forCurrentTenant($this->currentContext->accountId(), $this->currentContext->projectId())
            ->withDashboardDetails($request->integer('work_package_id'))
            ->orderByDesc('issued_date')
            ->get()
            ->map(fn (BudgetPlan $budgetPlan) => $this->detailRow($budgetPlan));

        return response()->json(['data' => $budgetPlans]);
    }

    private function detailRow(BudgetPlan $budgetPlan): array
    {
        return [
            'issued_date' => $budgetPlan->issued_date?->format('d M Y') ?? '-',
            'issued_date_sort' => $budgetPlan->issued_date?->format('Y-m-d') ?? '',
            'work_package' => $budgetPlan->workPackage?->work_package ?? '-',
            'budget_estimate' => Helper::formatRupiah($budgetPlan->budget_estimate),
            'budget_estimate_raw' => (float) $budgetPlan->budget_estimate,
            'status' => $budgetPlan->logStatus?->status ?? '-',
            'status_bg_color' => $budgetPlan->logStatus?->bg_color,
            'assigned_to' => $budgetPlan->latestActivity?->assignee?->display_name ?? '-',
        ];
    }
}
