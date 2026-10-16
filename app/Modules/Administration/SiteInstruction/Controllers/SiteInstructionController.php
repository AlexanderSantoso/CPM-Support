<?php

namespace App\Modules\Administration\SiteInstruction\Controllers;

use App\Core\Controllers\BaseModuleController;
use Illuminate\View\View;
use App\Support\CurrentContext;
use App\Modules\Administration\SiteInstruction\Models\SiteInstruction;

class SiteInstructionController extends BaseModuleController
{
    protected const MODULE_PATH = "modules.administration.site-instruction.";

    public function __construct(
        protected CurrentContext $currentContext
    ) {}

    public function main(): View
    {
        $siteInstructions = SiteInstruction::query()
                                            ->with([
                                                'contractDocument',
                                                'contractDocument.vendor',
                                                'changeOrder',
                                                'typeOfWork',
                                            ])
                                            ->forCurrentTenant($this->currentContext->accountId(), $this->currentContext->projectId())
                                            ->where(function ($query) {
                                                $query->whereNull('deleted_by')
                                                    ->whereNull('deleted_at');
                                            })
                                            ->get();

        $siteInstructions->each(function ($siteInstruction) {
            $siteInstruction->encrypted_id = $this->cryptoService()->encryptId($siteInstruction->id);
        });

        $data = [
            'siteInstructions' => $siteInstructions,
        ];

        return $this->viewModule('main', $data);
    }

    public function showDetailPage(string $encryptedId): View
    {
        $data = [
            'encryptedId' => $encryptedId,
            'id'          => $this->cryptoService()->decryptId($encryptedId),
        ];

        return $this->viewModule('detail', $data);
    }
}
