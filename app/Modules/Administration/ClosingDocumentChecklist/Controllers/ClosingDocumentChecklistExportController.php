<?php

namespace App\Modules\Administration\ClosingDocumentChecklist\Controllers;

use App\Core\Controllers\BaseModuleController;
use App\Models\Company;
use App\Modules\Administration\ClosingDocumentChecklist\Models\ClosingDocumentChecklist;
use App\Modules\Administration\ClosingDocumentChecklist\Models\ClosingDocumentChecklistItem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;

class ClosingDocumentChecklistExportController extends BaseModuleController
{
    public function pdf(string $encryptedId)
    {
        $checklist = ClosingDocumentChecklist::findOrFail(
            $this->cryptoService()->decryptId($encryptedId)
        );

        $checklist->load(['project.account']);

        $project = $checklist->project;
        $account = $project?->account;

        // Client = company pada projects.project_owner (kolom JSON, misalnya "[1]").
        $ownerIds = $this->idList($project?->project_owner);
        $clientName = $ownerIds
            ? Company::whereIn('id', $ownerIds)->orderBy('id')->pluck('company_name')->implode(', ')
            : null;

        // Status dan target date diambil dari aktivitas terakhir (id terbesar) milik checklist ini,
        // sesuai arahan mentor. Nilai yang sama berlaku untuk semua item pada checklist tersebut.
        $latest = $checklist->activities()->with('logStatus')->orderByDesc('id')->first();
        $statusName = $latest?->logStatus?->status;
        $dueRaw = $latest?->getRawOriginal('due_date');
        $targetDate = $dueRaw ? Carbon::parse($dueRaw)->format('j-M-y') : null;

        // Item yang dipilih pada checklist ini (kolom checklist_item, daftar id) digabung menjadi satu baris,
        // dipisah titik koma, misalnya "Contract; As Built Drawing; BAST".
        // Kolom Scope dihilangkan atas konfirmasi mentor (datanya memang tidak ada).
        $itemNames = ClosingDocumentChecklistItem::whereIn('id', $this->idList($checklist->checklist_item))
            ->orderBy('id')
            ->pluck('name')
            ->implode('; ');

        $items = [[
            'name' => $itemNames,
            'status' => $statusName,
            'status_class' => $this->statusClass($statusName),
            'target_date' => $targetDate,
        ]];

        return Pdf::view('modules.administration.closing-document-checklist.export.closing-document-checklist', [
            'projectName' => $project?->project_name,
            'clientName' => $clientName,
            'items' => $items,
            'accountName' => $account?->account_name,
            'accountLogo' => $this->imageDataUri(filled($account?->logo) ? 'images/account/' . $account->logo : null),
        ])->format('a4')->name('Closing Document Checklist - ' . $checklist->id . '.pdf');
    }

    /**
     * Warna sel status ditentukan otomatis dari teks status:
     * selesai = hijau, gagal atau ditolak = merah, selain itu (menunggu, berjalan) = kuning.
     */
    private function statusClass(?string $status): ?string
    {
        if (blank($status)) {
            return null;
        }

        $s = strtolower($status);

        return match (true) {
            str_starts_with($s, 'completed'), str_starts_with($s, 'approved') => 'done',
            str_starts_with($s, 'rejected'), str_starts_with($s, 'not accepted'), str_starts_with($s, 'revision') => 'fail',
            default => 'wait',
        };
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
