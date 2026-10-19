<?php

namespace App\Modules\Dashboard\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Support\CurrentContext;
use App\Modules\CostAndContract\WorkPackage\Models\WorkPackage;
use Illuminate\Support\Facades\View;

class DashboardController extends Controller
{
    protected const MODULE_PATH = "modules.dashboard.";
    
    public function __construct(
        protected CurrentContext $currentContext
    ) {}

    public function main()
    {
        $packages = WorkPackage::query()
        ->forCurrentTenant(
            $this->currentContext->accountId(),
            $this->currentContext->projectId()
        )
        ->withFinancialSummary()
        ->with(
            'budgetPlans.logStatus',
            'budgetPlans.latestActivity.logStatus',
            'budgetPlans.latestActivity.assignee',
            'contractDocuments.vendor',
            'changeOrders.contractDocument.vendor',
            'changeOrders.phase',
            'changeOrders.type',
            'changeOrders.origin',
            'changeOrders.priority',
            'changeOrders.latestActivity.creator',
            'changeOrders.latestActivity.logStatus',
            'changeOrders.latestActivity.assignee',
        )
        ->get();
    
    $costRows = $packages->map(function ($package) {
    $budgetPlanDetails = $package->budgetPlans->map(function ($budgetPlan) use ($package) {
    $activity = $budgetPlan->latestActivity;
    $logStatus = $activity?->logStatus ?? $budgetPlan->logStatus;
    

    return [
        'issued_date' => $budgetPlan->issued_date?->format('d M Y') ?? '',
        'work_package' => $package->work_package,
        'budget_estimate' => Helper::formatRupiah($budgetPlan->budget_estimate),
        'status' => $logStatus?->status ?? '',
        'assigned_to' => trim(
            ($activity?->assignee?->first_name ?? '') . ' ' .
            ($activity?->assignee?->last_name ?? '')
        ) ?: '-',
    ];
    
})->all();
$contractDetails = $package->contractDocuments->map(function ($contract) use ($package) {
    $amountExclVat = (float) $contract->contract_amount_excl_vat;
    $vatRate = (float) $contract->vat_rate;
    $amountInclVat = $amountExclVat * (1 + ($vatRate / 100));

    return [
        'contract_no' => $contract->contract_no,
        'issued_date' => $contract->issued_date?->format('d M Y') ?? '',
        'vendor' => $contract->vendor?->company_name ?? '',
        'address' => $contract->address ?? '',
        'work_package' => $package->work_package,
        'start_date' => $contract->start_date?->format('d M Y') ?? '',
        'end_date' => $contract->end_date?->format('d M Y') ?? '',
        'contract_amount_excl_vat' => Helper::formatRupiah($amountExclVat),
        'contract_amount_incl_vat' => Helper::formatRupiah($amountInclVat),
        'down_payment' => Helper::formatRupiah($contract->down_payment) . '%',
        'retention' => Helper::formatRupiah($contract->retention) . '%',
    ];
})->all();
    
    $changeOrderDetails = $package->changeOrders
    ->filter(function ($changeOrder) {
        $statusId = $changeOrder->latestActivity?->log_status_id;

        return in_array((int) $statusId, [1, 3, 4], true);
    })
    ->map(function ($changeOrder) use ($package) {
        $activity = $changeOrder->latestActivity;
        $contract = $changeOrder->contractDocument;

        return [
            'date_raised' => $changeOrder->date_raised?->format('d M Y') ?? '',
            'input_by' => trim(
                ($activity?->creator?->first_name ?? '') . ' ' .
                ($activity?->creator?->last_name ?? '')
            ) ?: '-',
            'cr_no' => $changeOrder->change_order_number ?? '',
            'contract_no' => $contract?->contract_no ?? '',
            'vendor' => $contract?->vendor?->company_name ?? '',
            'work_package' => $package->work_package,
            'building_phase' => $changeOrder->phase?->name ?? '',
            'type_of_change' => $changeOrder->type?->name ?? '',
            'originator' => $changeOrder->origin?->name ?? '',
            'priority' => $changeOrder->priority?->name ?? '',
            'description' => $changeOrder->description ?? '',
            'estimated_variation' => Helper::formatRupiah($changeOrder->estimated_variation),
            'potential_impact' => $changeOrder->potential_impact_on_project ?? '',
            'cost_estimation' => Helper::formatRupiah($changeOrder->cost_estimation),
            'time_estimation' => $changeOrder->time_estimation ?? '',
            'pm_comment' => $changeOrder->pm_comment ?? '',
            'consultant_comment' => $changeOrder->consultant_design_specialist_comment ?? '',
            'qs_comment' => $changeOrder->qs_comment ?? '',
            'client_comment' => $changeOrder->client_comment ?? '',
            'status' => $activity?->logStatus?->status ?? '',
            'assigned_to' => trim(
                ($activity?->assignee?->first_name ?? '') . ' ' .
                ($activity?->assignee?->last_name ?? '')
            ) ?: '-',
        ];
    })
    ->values()
    ->all();

    $contractAmountExclVat = (float) ($package->contract_amount_excl_vat_total ?? 0);

    $changeOrderVariation = (float) ($package->estimated_variation_change_order_total ?? 0);
    $variationOrderVariation = (float) ($package->estimated_variation_variation_order_total ?? 0);
    $estimatedVariation = $changeOrderVariation + $variationOrderVariation;

    $approvedVariation = (float) ($package->approved_variation_variation_order_total ?? 0);

    return [
        'package' => $package->work_package,
        'budget_plan' => $package->budget_estimate_total,
        'estimated_contract' => $package->estimated_contract_total,
        'contract_amount_excl_vat' => $contractAmountExclVat,
        'estimated_variation' => $estimatedVariation,
        'approved_variation' => $approvedVariation,
        'final_account' => $contractAmountExclVat + $estimatedVariation + $approvedVariation,
        'budget_plan_details' => $budgetPlanDetails,
        'contract_details' => $contractDetails,
        'change_order_details' => $changeOrderDetails,
    ];
})->all();

        $totals = [
            'budget_plan' => array_sum(array_column($costRows, 'budget_plan')),
            'estimated_contract' => array_sum(array_column($costRows, 'estimated_contract')),
            'contract_amount_excl_vat' => array_sum(array_column($costRows, 'contract_amount_excl_vat')),
            'estimated_variation' => array_sum(array_column($costRows, 'estimated_variation')),
            'approved_variation' => array_sum(array_column($costRows, 'approved_variation')),
            'final_account' => array_sum(array_column($costRows, 'final_account')),
        ];

        return view(self::MODULE_PATH . 'main', [
            'costRows' => $costRows,
            'totals' => $totals,
        ]);
    }

}
