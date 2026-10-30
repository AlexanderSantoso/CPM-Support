<?php

namespace App\Modules\Compliance\NonConformanceReport\Controllers;

use App\Core\Controllers\BaseModuleController;
use App\Models\LogStatus;
use App\Modules\Compliance\NonConformanceReport\Models\NonConformanceReport;
use App\Support\CurrentContext;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class NonConformanceReportController extends BaseModuleController
{
    protected const MODULE_PATH = 'modules.compliance.non-conformance-report.';

    public function __construct(
        protected CurrentContext $currentContext
    ) {}

    public function main(): View
    {
        $statuses = LogStatus::pluck('status', 'id');

        $items = NonConformanceReport::query()
            ->with(['firmResponsible'])
            ->where('project_id', $this->currentContext->projectId())
            ->orderBy('id')
            ->get();

        $rows = $items->map(function ($i) use ($statuses) {
            return [
                'number' => $i->ncrNumber,
                'date' => Carbon::parse($i->getRawOriginal('inspection_date'))->format('d-M-Y'),
                'contractor' => $i->firmResponsible?->company_name ?? '',
                'area' => $i->area,
                'status' => $statuses->get($i->log_status_id, ''),
                'encrypted_id' => $this->cryptoService()->encryptId($i->id),
            ];
        });

        return $this->viewModule('main', ['rows' => $rows]);
    }
}
