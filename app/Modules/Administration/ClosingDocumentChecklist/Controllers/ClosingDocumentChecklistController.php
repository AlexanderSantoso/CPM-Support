<?php

namespace App\Modules\Administration\ClosingDocumentChecklist\Controllers;

use App\Core\Controllers\BaseModuleController;
use App\Models\LogStatus;
use App\Modules\Administration\ClosingDocumentChecklist\Models\ClosingDocumentChecklist;
use App\Support\CurrentContext;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ClosingDocumentChecklistController extends BaseModuleController
{
    protected const MODULE_PATH = 'modules.administration.closing-document-checklist.';

    public function __construct(
        protected CurrentContext $currentContext
    ) {}

    public function main(): View
    {
        $statuses = LogStatus::pluck('status', 'id');

        $items = ClosingDocumentChecklist::query()
            ->with(['workPackage', 'activities'])
            ->where('project_id', $this->currentContext->projectId())
            ->orderBy('id')
            ->get();

        $rows = $items->map(function ($i) use ($statuses) {
            // Status dan target date mengikuti activity terakhir (id terbesar), sama seperti PDF-nya.
            $latest = $i->activities->sortByDesc('id')->first();

            return [
                'work_package' => $i->workPackage?->work_package ?? '',
                'status' => $statuses->get($latest?->log_status_id, ''),
                'target_date' => $latest?->getRawOriginal('due_date') ? Carbon::parse($latest->getRawOriginal('due_date'))->format('d-M-Y') : '',
                'encrypted_id' => $this->cryptoService()->encryptId($i->id),
            ];
        });

        return $this->viewModule('main', ['rows' => $rows]);
    }
}
