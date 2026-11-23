<?php

namespace App\Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Dashboard\Services\CurrentStatusService;
use App\Modules\CostAndContract\WorkPackage\Models\WorkPackage;
use App\Support\CurrentContext;
use App\Services\Schedule\SCurve\SCurveDropdownService;
use App\Services\CryptoService;
use App\Services\Schedule\SCurve\SCurveProgressService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected CurrentContext $currentContext,
    ) {}

    public function main(CurrentStatusService $currentStatus, SCurveDropdownService $sCurveDropdown): View
    {
        $dashboardDefaults = $sCurveDropdown->dashboardDefaults();
        return view('modules.dashboard.main', [
            'activeDashboardSection' => 'dashboard',
            'initialDashboardData' => $this->payload(),
            'initialCurrentStatusData' => $currentStatus->get(),
            'contractors' => $sCurveDropdown->contractorDropdown(),
            'dashboardDefaults' => $dashboardDefaults,
        ]);
    }
    

    public function data(): JsonResponse
    {
        return response()->json($this->payload());
    }


    public function sCurveWorkPackages(
        Request $request, SCurveDropdownService $dropdownService
    ): JsonResponse {
        $data = $request->validate([
            'contractor_code' => 'required|string',
        ]);

        return response()->json($dropdownService->projectWorkPackageDropdown($data['contractor_code']));
    }

    public function sCurveProgress(
        Request $request,
        SCurveProgressService $progressService,
        SCurveDropdownService $dropdownService,
        CryptoService $cryptoService
    ): JsonResponse {
        $data = $request->validate([
            'contractor_code' => 'required|string',
            'work_package_id' => 'required|string',
        ]);

    $contractor = $dropdownService->validateContractor($data['contractor_code']);
    $workPackageId = $cryptoService->decryptId($data['work_package_id']);

    $rows = $progressService->getProgress(
        $this->currentContext->projectId(),
        $contractor->id,
        $workPackageId
    );

    $firstRow = $rows->first();
    $lastRow = $rows->last();

    $startDate = $firstRow ? \Carbon\Carbon::parse($firstRow->report_start_date) : null;
    $endDate = $lastRow ? \Carbon\Carbon::parse($lastRow->report_end_date) : null;

    return response()->json([
        'periods' => $rows->map(fn ($row) =>
        \Carbon\Carbon::parse($row->report_start_date)->format('d-M-Y')
        . ' to ' .
        \Carbon\Carbon::parse($row->report_end_date)->format('d-M-Y')
    ),
    'planned' => $rows->pluck('total_planned_progress'),
    'actual' => $rows->pluck('total_actual_progress'),
    'duration' => [
        'start' => $startDate?->format('d-M-Y') ?? '-',
        'end' => $endDate?->format('d-M-Y') ?? '-',
        'days' => ($startDate && $endDate) ? $startDate->diffInDays($endDate) + 1 : 0,
    ],
    ]);
    }

    private function payload(): array
    {
        $workPackages = WorkPackage::query()
            ->forCurrentTenant($this->currentContext->accountId(), $this->currentContext->projectId())
            ->withFinancialOverview()
            ->get();

        $rows = $workPackages->map(fn (WorkPackage $workPackage) => $this->financialRow($workPackage));

        return [
            'data' => $rows->all(),
            'totals' => $this->totals($rows),
        ];
    }

    private function financialRow(WorkPackage $workPackage): array
    {
        $contractAmount = (float) ($workPackage->contract_amount_excl_vat_total ?? 0);
        $estimatedVariation = (float) ($workPackage->estimated_variation_change_order_total ?? 0)
            + (float) ($workPackage->estimated_variation_variation_order_total ?? 0);
        $approvedVariation = (float) ($workPackage->approved_variation_variation_order_total ?? 0);

        return [
            'work_package_id' => $workPackage->id,
            'package' => $workPackage->work_package,
            'has_owner_estimate_details' => $workPackage->owner_estimates_count > 0,
            'has_contract_details' => $workPackage->contract_documents_count > 0,
            'has_change_order_details' => $workPackage->estimated_change_order_count > 0,
            'budget_plan' => $workPackage->budget_estimate_total,
            'estimated_contract' => (float) ($workPackage->estimated_contract_total ?? 0),
            'contract_amount_excl_vat' => $contractAmount,
            'estimated_variation' => $estimatedVariation,
            'approved_variation' => $approvedVariation,
            'final_account' => $contractAmount + $estimatedVariation + $approvedVariation,
        ];
    }

    private function totals(Collection $rows): array
    {
        return collect([
            'budget_plan',
            'estimated_contract',
            'contract_amount_excl_vat',
            'estimated_variation',
            'approved_variation',
            'final_account',
        ])->mapWithKeys(fn ($column) => [$column => $rows->sum($column)])->all();
    }
}
