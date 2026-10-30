<?php

namespace App\Modules\CostAndContract\BudgetPlan\Controllers;

use App\Core\Controllers\BaseModuleController;
use App\Models\LogStatus;
use App\Modules\CostAndContract\BudgetPlan\Models\BudgetPlan;
use App\Support\CurrentContext;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class BudgetPlanController extends BaseModuleController
{
    protected const MODULE_PATH = 'modules.cost-and-contract.budget-plan.';

    public function __construct(
        protected CurrentContext $currentContext
    ) {}

    public function main(): View
    {
        $statuses = LogStatus::pluck('status', 'id');

        $items = BudgetPlan::query()
            ->with(['workPackage'])
            ->where('project_id', $this->currentContext->projectId())
            ->orderBy('id')
            ->get();

        $rows = $items->map(function ($i) use ($statuses) {
            return [
                'work_package' => $i->workPackage?->work_package ?? '',
                'date' => Carbon::parse($i->getRawOriginal('issued_date'))->format('d-M-Y'),
                'budget' => number_format((float) $i->budget_estimate, 0, ',', '.'),
                'status' => $statuses->get($i->log_status_id, ''),
                'encrypted_id' => $this->cryptoService()->encryptId($i->id),
            ];
        });

        return $this->viewModule('main', ['rows' => $rows]);
    }
}
