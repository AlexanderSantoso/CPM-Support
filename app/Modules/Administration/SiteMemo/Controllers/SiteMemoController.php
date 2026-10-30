<?php

namespace App\Modules\Administration\SiteMemo\Controllers;

use App\Core\Controllers\BaseModuleController;
use App\Models\LogStatus;
use App\Modules\Administration\SiteMemo\Models\SiteMemo;
use App\Support\CurrentContext;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class SiteMemoController extends BaseModuleController
{
    protected const MODULE_PATH = 'modules.administration.site-memo.';

    public function __construct(
        protected CurrentContext $currentContext
    ) {}

    public function main(): View
    {
        $statuses = LogStatus::pluck('status', 'id');

        $items = SiteMemo::query()
            ->with(['category', 'toCompany'])
            ->where('project_id', $this->currentContext->projectId())
            ->orderBy('id')
            ->get();

        $rows = $items->map(function ($i) use ($statuses) {
            return [
                'number' => $i->siteMemoNumber,
                'date' => Carbon::parse($i->getRawOriginal('issued_date'))->format('d-M-Y'),
                'subject' => $i->subject,
                'to' => $i->to_all === 'Y' ? 'All' : ($i->toCompany?->company_name ?? ''),
                'category' => $i->category?->name ?? '',
                'status' => $statuses->get($i->log_status_id, ''),
                'encrypted_id' => $this->cryptoService()->encryptId($i->id),
            ];
        });

        return $this->viewModule('main', ['rows' => $rows]);
    }
}
