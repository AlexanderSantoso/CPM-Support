<?php

namespace App\Modules\Compliance\ProjectExecutionPlan\Controllers;

use App\Core\Controllers\BaseModuleController;
use App\Modules\Compliance\ProjectExecutionPlan\Models\ProjectExecutionPlan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;

class ProjectExecutionPlanExportController extends BaseModuleController
{
    public function pdf(string $encryptedId)
    {
        $plan = ProjectExecutionPlan::findOrFail(
            $this->cryptoService()->decryptId($encryptedId)
        );

        $plan->load(['project.account']);

        $project = $plan->project;
        $account = $project?->account;

        // Dibaca dari nilai mentah supaya tidak terpengaruh kalau model nanti memakai accessor tanggal.
        $issued = $plan->getRawOriginal('issued_date');
        $issuedDate = $issued ? Carbon::parse($issued)->format('j F Y') : null;

        return Pdf::view('modules.compliance.project-execution-plan.export.project-execution-plan', [
            // Teks ringkasan eksekutif diambil dari kolom remarks pada rencana ini.
            'executiveSummary' => $plan->remarks,
            'issuedDate' => $issuedDate,
            'projectName' => $project?->project_name,
            'projectArea' => $this->listText($project?->area),
            'accountName' => $account?->account_name,
            'accountAddress' => $account?->address,
            'accountPhone' => $account?->phone,
            'accountEmail' => $account?->email,
            'accountLogo' => $this->imageDataUri(filled($account?->logo) ? 'images/account/' . $account->logo : null),
        ])->format('a4')->name('Project Execution Plan - ' . $plan->id . '.pdf');
    }

    /**
     * Kolom JSON seperti projects.area (misalnya ["1 HA"]) bisa datang sebagai string atau array.
     * Hasilnya digabung dengan koma, atau null kalau kosong.
     */
    private function listText(mixed $value): ?string
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        $items = is_array($value) ? array_filter($value, fn ($v) => filled($v)) : [];

        return $items ? implode(', ', $items) : null;
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
