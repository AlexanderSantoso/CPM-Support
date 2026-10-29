<?php

namespace App\Modules\Administration\CertificateOfCompletion\Controllers;

use App\Core\Controllers\BaseModuleController;
use App\Models\WorkPackage;
use App\Modules\Administration\CertificateOfCompletion\Models\CertificateOfCompletion;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;

class CertificateOfCompletionExportController extends BaseModuleController
{
    public function pdf(string $encryptedId)
    {
        $certificateOfCompletion = CertificateOfCompletion::findOrFail(
            $this->cryptoService()->decryptId($encryptedId)
        );

        $certificateOfCompletion->load([
            'project.account',
            'contractDocument.vendor',
            'company',
        ]);

        $docNo = $certificateOfCompletion->certificate_number ?? '-';
        $fileNo = str_replace('/', '-', $docNo); // "/" tidak boleh ada di nama file

        // Dibaca dari nilai mentah supaya tidak terpengaruh kalau model nanti memakai accessor tanggal.
        $issued = $certificateOfCompletion->getRawOriginal('issued_date');
        $dayDate = $issued ? Carbon::parse($issued)->format('l, j F Y') : null;

        $project = $certificateOfCompletion->project;
        $account = $project?->account;
        $contract = $certificateOfCompletion->contractDocument;
        $contractor = $contract?->vendor;  // pihak yang membuat sertifikat
        $client = $certificateOfCompletion->company;

        // Label pihak kontraktor mengikuti jenis work package pada kontrak, misalnya "Interior Contractors".
        $workPackage = $contract?->cc_work_package_id ? WorkPackage::find($contract->cc_work_package_id) : null;
        $contractorLabel = filled($workPackage?->work_package) ? $workPackage->work_package . ' Contractors' : 'Contractor';

        return Pdf::view('modules.administration.certificate-of-completion.export.certificate-of-completion', [
            'coc' => $certificateOfCompletion,
            'docNo' => $docNo,
            'dayDate' => $dayDate,
            'projectName' => $project?->project_name,
            'contractorLabel' => $contractorLabel,
            'contractorName' => $contractor?->company_name,
            'contractorAddress' => $contractor?->address,
            'clientName' => $client?->company_name,
            'clientAddress' => $client?->address,
            'accountName' => $account?->account_name,
            'accountAddress' => $account?->address,
            'accountPhone' => $account?->phone,
            'accountEmail' => $account?->email,
            'accountLogo' => $this->imageDataUri(filled($account?->logo) ? 'images/account/' . $account->logo : null),
        ])->format('a4')->name('Certificate of Completion - ' . $fileNo . '.pdf');
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
