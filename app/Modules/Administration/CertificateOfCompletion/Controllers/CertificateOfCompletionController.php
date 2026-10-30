<?php

namespace App\Modules\Administration\CertificateOfCompletion\Controllers;

use App\Core\Controllers\BaseModuleController;
use App\Models\LogStatus;
use App\Modules\Administration\CertificateOfCompletion\Models\CertificateOfCompletion;
use App\Support\CurrentContext;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class CertificateOfCompletionController extends BaseModuleController
{
    protected const MODULE_PATH = 'modules.administration.certificate-of-completion.';

    public function __construct(
        protected CurrentContext $currentContext
    ) {}

    public function main(): View
    {
        $statuses = LogStatus::pluck('status', 'id');

        $items = CertificateOfCompletion::query()
            ->with(['contractDocument', 'company'])
            ->where('project_id', $this->currentContext->projectId())
            ->orderBy('id')
            ->get();

        $rows = $items->map(function ($i) use ($statuses) {
            return [
                'number' => $i->certificateNumber,
                'date' => Carbon::parse($i->getRawOriginal('issued_date'))->format('d-M-Y'),
                'contract' => $i->contractDocument?->contract_no ?? '',
                'company' => $i->company?->company_name ?? '',
                'certificate_of' => $i->certificate_of,
                'status' => $statuses->get($i->log_status_id, ''),
                'encrypted_id' => $this->cryptoService()->encryptId($i->id),
            ];
        });

        return $this->viewModule('main', ['rows' => $rows]);
    }
}
