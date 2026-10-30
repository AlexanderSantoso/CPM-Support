<?php

namespace App\Modules\CostAndContract\ChangeOrder\Controllers;

use App\Core\Controllers\BaseModuleController;
use App\Models\ContractDocument;
use App\Models\LogStatus;
use App\Modules\CostAndContract\ChangeOrder\Models\ChangeOrder;
use App\Support\CurrentContext;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ChangeOrderController extends BaseModuleController
{
    protected const MODULE_PATH = 'modules.cost-and-contract.change-order.';

    public function __construct(
        protected CurrentContext $currentContext
    ) {}

    public function main(): View
    {
        $statuses = LogStatus::pluck('status', 'id');
        $contracts = ContractDocument::pluck('contract_no', 'id');

        $items = ChangeOrder::query()
            ->with(['type'])
            ->where('project_id', $this->currentContext->projectId())
            ->orderBy('id')
            ->get();

        $rows = $items->map(function ($i) use ($statuses, $contracts) {
            return [
                'number' => $i->changeOrderNumber,
                'date' => Carbon::parse($i->getRawOriginal('date_raised'))->format('d-M-Y'),
                'contract' => $contracts->get($i->cc_contract_document_id, ''),
                'type' => $i->type?->name ?? '',
                'description' => $i->description,
                'status' => $statuses->get($i->log_status_id, ''),
                'encrypted_id' => $this->cryptoService()->encryptId($i->id),
            ];
        });

        return $this->viewModule('main', ['rows' => $rows]);
    }
}
