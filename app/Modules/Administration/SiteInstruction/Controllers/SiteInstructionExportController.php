<?php

namespace App\Modules\Administration\SiteInstruction\Controllers;

use App\Modules\Administration\SiteInstruction\Models\SiteInstruction;
use App\Modules\Administration\SiteInstruction\Models\SiteInstructionAttachmentType;
use App\Modules\Administration\SiteInstruction\Models\SiteInstructionPotentialCostTime;
use App\Modules\Administration\SiteInstruction\Models\SiteInstructionTypeOfWork;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;
use Illuminate\Support\Carbon;
use App\Core\Controllers\BaseModuleController;

class SiteInstructionExportController extends BaseModuleController
{
    public function pdf(string $encryptedId)
    {
        $siteInstruction = SiteInstruction::findOrFail(
            $this->cryptoService()->decryptId($encryptedId)
        );

        $siteInstruction->load([
            'project.account',
            'contractDocument.vendor',
            'receivedBy',
            'creator',
            'items' => fn ($q) => $q->orderBy('id'),
        ]);

        // Semua pilihan lookup ditampilkan sebagai checkbox, yang terpilih dicentang.
        $typesOfWork = SiteInstructionTypeOfWork::orderBy('id')->get()->values();
        $attachmentTypes = SiteInstructionAttachmentType::orderBy('id')->get();
        $potentialCostTimes = SiteInstructionPotentialCostTime::orderBy('id')->get();

        // Type of Works ditampilkan dua kolom: separuh pertama di kiri, sisanya di kanan.
        $half = (int) ceil($typesOfWork->count() / 2);
        $typeRows = [];
        for ($i = 0; $i < $half; $i++) {
            $typeRows[] = [$typesOfWork->get($i), $typesOfWork->get($i + $half)];
        }

        $docNo = $siteInstruction->site_instruction_number ?? '-';
        $fileNo = str_replace('/', '-', $docNo); // "/" tidak boleh ada di nama file

        $project = $siteInstruction->project;
        $account = $project?->account;

        $issued = $siteInstruction->getRawOriginal('issued_date');
        $issuedDate = $issued ? Carbon::parse($issued)->format('d/m/Y') : null;

        return Pdf::view('modules.administration.site-instruction.export.site-instruction', [
            'si' => $siteInstruction,
            'docNo' => $docNo,
            'issuedDate' => $issuedDate,
            'projectName' => $project?->project_name,
            'projectLocation' => null, // kolom lokasi proyek belum diketahui, lihat Tahap 1
            'contractor' => $siteInstruction->contractDocument?->vendor?->company_name,
            'contractNo' => $siteInstruction->contractDocument?->contract_no,
            'issuedBy' => $siteInstruction->creator,
            'receivedBy' => $siteInstruction->receivedBy,
            'typeRows' => $typeRows,
            'attachmentTypes' => $attachmentTypes,
            'potentialCostTimes' => $potentialCostTimes,
            'items' => $siteInstruction->items,
            'clientLogo' => $this->imageDataUri(filled($project?->logo) ? 'images/project/logo/' . $project->logo : null),
            'accountName' => $account?->account_name,
            'accountLogo' => $this->imageDataUri(filled($account?->logo) ? 'images/account/' . $account->logo : null),
        ])->format('a4')->name('Site Instruction - ' . $fileNo . '.pdf');
    }

    /**
     * Gambar dibaca dari storage/app/public dan diubah menjadi data URI
     * supaya DOMPDF tidak perlu mencari path atau URL.
     * Mengembalikan null kalau file tidak ada, jadi PDF tetap jadi tanpa gambar.
     */
    private function imageDataUri(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($path)) {
            return null;
        }

        $mime = $disk->mimeType($path) ?: 'image/png';

        return 'data:' . $mime . ';base64,' . base64_encode($disk->get($path));
    }
}
