<?php

namespace App\Modules\Compliance\ProjectExecutionPlan\Controllers;

use App\Core\Controllers\BaseModuleController;
use App\Models\LogStatus;
use App\Modules\Compliance\ProjectExecutionPlan\Models\ProjectExecutionPlan;
use App\Support\CurrentContext;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ProjectExecutionPlanController extends BaseModuleController
{
    protected const MODULE_PATH = 'modules.compliance.project-execution-plan.';

    public function __construct(
        protected CurrentContext $currentContext
    ) {}

    public function main(): View
    {
        $statuses = LogStatus::pluck('status', 'id');

        $items = ProjectExecutionPlan::query()
            ->where('project_id', $this->currentContext->projectId())
            ->orderBy('id')
            ->get();

        $rows = $items->map(function ($i) use ($statuses) {
            return [
                'date' => Carbon::parse($i->getRawOriginal('issued_date'))->format('d-M-Y'),
                'remarks' => $i->remarks,
                'status' => $statuses->get($i->log_status_id, ''),
                'encrypted_id' => $this->cryptoService()->encryptId($i->id),
            ];
        });

        return $this->viewModule('main', ['rows' => $rows]);
    }
}
