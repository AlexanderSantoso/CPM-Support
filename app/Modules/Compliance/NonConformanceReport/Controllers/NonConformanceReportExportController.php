<?php

namespace App\Modules\Compliance\NonConformanceReport\Controllers;

use App\Core\Controllers\BaseModuleController;
use App\Modules\Compliance\NonConformanceReport\Models\NonConformanceReport;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;

class NonConformanceReportExportController extends BaseModuleController
{
    public function pdf(string $encryptedId)
    {
        $ncr = NonConformanceReport::findOrFail(
            $this->cryptoService()->decryptId($encryptedId)
        );

        $ncr->load([
            'project.account',
            'category',
            'firmResponsible',
            'logStatus',
            'creator',
            'photos' => fn ($q) => $q->orderBy('id'),
        ]);

        $project = $ncr->project;
        $account = $project?->account;

        $inspection = $ncr->getRawOriginal('inspection_date');
        $completion = $ncr->getRawOriginal('completion_date');
        $inspectionDate = $inspection ? Carbon::parse($inspection)->format('d-m-Y') : null;
        $closedDate = $completion ? Carbon::parse($completion)->format('d-m-Y') : null;

        // Format nomor mengikuti template: no/NCR-<Project>/MM/YYYY, bulan dan tahun dari tanggal inspeksi.
        $month = $inspection ? Carbon::parse($inspection)->format('m') : '--';
        $year = $inspection ? Carbon::parse($inspection)->format('Y') : '----';
        $ncrNo = str_pad((string) $ncr->doc_sequence, 3, '0', STR_PAD_LEFT)
            . '/NCR-' . $ncr->doc_project_initial . '/' . $month . '/' . $year;

        // Foto dikelompokkan menurut kolom type. KEY_PLAN tidak ada di template, jadi tidak dipakai.
        $photoGroup = fn (string $type) => $ncr->photos
            ->where('type', $type)
            ->map(fn ($p) => $this->imageDataUri($p->photo_path, $p->disk))
            ->filter()
            ->values();

        // Result hanya diisi kalau laporan sudah Closed. Selain itu tetap mengikuti teks template.
        $result = strcasecmp(trim((string) $ncr->logStatus?->status), 'Closed') === 0
            ? 'PASS'
            : 'PASS / NOT PASS';

        return Pdf::view('modules.compliance.non-conformance-report.export.non-conformance-report', [
            'ncrNo' => $ncrNo,
            'projectName' => $project?->project_name,
            'projectLocation' => $ncr->area,
            'accountName' => $account?->account_name,
            'accountLogo' => $this->imageDataUri(
                filled($account?->logo) ? 'images/account/' . $account->logo : null,
                'public'
            ),
            'category' => $ncr->category?->name,
            'contractor' => $ncr->firmResponsible?->company_name,
            'process' => $ncr->key_plan_description,
            'descriptionItems' => $this->lines($ncr->ncr_description),
            'correctiveItems' => $this->lines($ncr->corrective_action),
            'ncrPhotos' => $photoGroup('NCR'),
            'rectificationPhotos' => $photoGroup('RECTIFICATION'),
            'inspectionDate' => $inspectionDate,
            'closedDate' => $closedDate,
            'endorsedBy' => $ncr->creator ? trim($ncr->creator->first_name . ' ' . $ncr->creator->last_name) : null,
            'result' => $result,
        ])->format('a4')->name('Non-Conformance Report - ' . str_replace('/', '-', $ncrNo) . '.pdf');
    }

    /**
     * Teks bebas dipecah per baris supaya bisa ditampilkan sebagai daftar bernomor seperti template.
     */
    private function lines(?string $text): array
    {
        if (blank($text)) {
            return [];
        }

        return array_values(array_filter(
            array_map('trim', preg_split('/\r\n|\r|\n/', $text)),
            fn ($line) => $line !== ''
        ));
    }

    /**
     * Gambar dibaca dari disk yang tersimpan di datanya (misalnya local_private) dan diubah menjadi data URI
     * supaya DOMPDF tidak perlu mencari path atau URL. Disk kosong memakai disk default.
     * Mengembalikan null kalau file tidak ada, jadi PDF tetap jadi tanpa gambar.
     */
    private function imageDataUri(?string $path, ?string $diskName = null): ?string
    {
        if (blank($path)) {
            return null;
        }

        $disk = Storage::disk(filled($diskName) ? $diskName : config('filesystems.default'));

        if (! $disk->exists($path)) {
            return null;
        }

        $mime = $disk->mimeType($path) ?: 'image/jpeg';

        return 'data:' . $mime . ';base64,' . base64_encode($disk->get($path));
    }
}
