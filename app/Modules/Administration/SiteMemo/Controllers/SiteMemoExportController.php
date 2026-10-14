<?php

namespace App\Modules\Administration\SiteMemo\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Modules\Administration\SiteMemo\Models\SiteMemo;
use App\Modules\Administration\SiteMemo\Models\SiteMemoCategory;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;

class SiteMemoExportController extends Controller
{
    public function pdf(SiteMemo $siteMemo)
    {
        $siteMemo->load(['project.account', 'toCompany', 'creator']);

        // Semua kategori ditampilkan sebagai checkbox, yang terpilih dicentang.
        $categories = SiteMemoCategory::orderBy('id')->get();

        $project = $siteMemo->project;
        $account = $project?->account;

        // Client = company pada projects.project_owner (kolom JSON, misalnya "[1]").
        $ownerIds = $this->idList($project?->project_owner);
        $clientName = $ownerIds
            ? Company::whereIn('id', $ownerIds)->orderBy('id')->pluck('company_name')->implode(', ')
            : null;

        // Penerima memo: semua kontraktor ("All") atau satu company.
        $toName = $siteMemo->to_all === 'Y' ? 'All' : $siteMemo->toCompany?->company_name;

        $docNo = $siteMemo->site_memo_number ?? '-';
        $fileNo = str_replace('/', '-', $docNo); // "/" tidak boleh ada di nama file

        return Pdf::view('modules.administration.site-memo.export.site-memo', [
            'memo' => $siteMemo,
            'docNo' => $docNo,
            'projectName' => $project?->project_name,
            'projectLocation' => null, // kolom lokasi proyek belum diketahui
            'clientName' => $clientName,
            'contractNo' => null, // Site Memo tidak punya relasi ke dokumen kontrak
            'fromName' => $account?->account_name,
            'toName' => $toName,
            'salutation' => filled($toName) ? $toName : 'Sir / Madam',
            'signerName' => trim(($siteMemo->creator?->first_name ?? '') . ' ' . ($siteMemo->creator?->last_name ?? '')),
            'categories' => $categories,
            // Kiri: accounts.logo (images/account). Kanan (client): projects.logo (images/project/logo).
            'accountName' => $account?->account_name,
            'accountLogo' => $this->imageDataUri(filled($account?->logo) ? 'images/account/' . $account->logo : null),
            'clientLogo' => $this->imageDataUri(filled($project?->logo) ? 'images/project/logo/' . $project->logo : null),
        ])->format('a4')->name('Site Memo - ' . $fileNo . '.pdf');
    }

    /**
     * project_owner bisa berupa array (kalau model memakai cast) atau string JSON.
     */
    private function idList(mixed $value): array
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        return is_array($value) ? array_values(array_filter($value)) : [];
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
