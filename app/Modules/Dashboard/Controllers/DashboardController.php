<?php

namespace App\Modules\Dashboard\Controllers;

use Illuminate\Http\Request;
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
        ->get();
    
        $costRows = $packages->map(fn ($package) => [
            'package' => $package->work_package,
            'budget_plan' => $package->budget_estimate_total,
            'estimated_contract' => $package->estimated_contract_total,
            'approved_contract' => $package->contract_amount_excl_vat_total,
            'estimated_variation' => null,
            'approved_variation' => $package->approved_variation_total,
            'final_account' => null,
        ])->all();

        $totals = [
            'budget_plan' => array_sum(array_column($costRows, 'budget_plan')),
            'estimated_contract' => array_sum(array_column($costRows, 'estimated_contract')),
            'approved_contract' => array_sum(array_column($costRows, 'approved_contract')),
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
