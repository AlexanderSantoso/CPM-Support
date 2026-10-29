<?php

namespace App\Modules\CostAndContract\ChangeOrder\Controllers;

use App\Core\Controllers\BaseModuleController;
use App\Models\User;
use App\Modules\CostAndContract\ChangeOrder\Models\ChangeOrder;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;

class ChangeOrderExportController extends BaseModuleController
{
    public function pdf(string $encryptedId)
    {
        $changeOrder = ChangeOrder::findOrFail($this->cryptoService()->decryptId($encryptedId));

        $changeOrder->load([
            'project.account',
            'phase',
            'type',
            'origin',
            'logStatus',
            'activities' => fn ($q) => $q
                ->with(['creator', 'assignee', 'logStatus'])
                ->orderBy('created_at')
                ->orderBy('id'),
        ]);

        $activities = $changeOrder->activities; // urut dari yang paling lama

        // Asumsi: PM = pembuat activity step 2 yang paling baru.
        $pmActivity = $activities->where('step_order', 2)->last();

        // Asumsi: "Decision Required By" = due_date activity terbaru yang punya due_date.
        $decisionBy = $activities->whereNotNull('due_date')->last()?->due_date;

        // Distribution = semua user yang pernah di-CC pada activity CO ini.
        $ccIds = $activities->pluck('cc')->filter()->flatten()->unique()->values();
        $distribution = User::whereIn('id', $ccIds)->orderBy('id')->get();

        $docNo = $changeOrder->change_order_number ?? '-';
        $fileNo = str_replace('/', '-', $docNo);

        $account = $changeOrder->project?->account;

        return Pdf::view('modules.cost-and-contract.change-order.export.change-control-form', [
            'co' => $changeOrder,
            'docNo' => $docNo,
            'pmActivity' => $pmActivity,
            'decisionBy' => $decisionBy,
            'distribution' => $distribution,
            'accountName' => $account?->account_name,
            'projectCategory' => null, // "Office Fit-Out Project": tabel project_categories tidak ada di dump
            'logo' => $this->logoDataUri($account?->logo),
        ])->format('a4')->name('Change Control Form - ' . $fileNo . '.pdf');
    }

    private function logoDataUri(?string $file): ?string
    {
        if (blank($file)) {
            return null;
        }

        $disk = Storage::disk('public');
        $path = 'images/account/' . $file;

        if (! $disk->exists($path)) {
            return null;
        }

        $mime = $disk->mimeType($path) ?: 'image/png';

        return 'data:' . $mime . ';base64,' . base64_encode($disk->get($path));
    }
}
