<?php

namespace App\Modules\CostAndContract\BudgetPlan\Controllers;

use App\Core\Controllers\BaseModuleController;
use App\Modules\CostAndContract\BudgetPlan\Exports\BudgetPlansExport;
use App\Modules\CostAndContract\BudgetPlan\Models\BudgetPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\LaravelPdf\Facades\Pdf;
use ZipArchive;

class BudgetPlanExportController extends BaseModuleController
{
    /** Excel: satu baris per plan. */
    public function excel(Request $request)
    {
        $plans = $this->plans($request, withActivities: false);

        return Excel::download(
            new BudgetPlansExport($plans),
            'Budget Plan - ' . now()->format('d M Y') . '.xlsx'
        );
    }

    /** PDF untuk satu plan. */
    public function pdf(string $encryptedId)
    {
        $budgetPlan = BudgetPlan::findOrFail($this->cryptoService()->decryptId($encryptedId));

        $budgetPlan->load($this->pdfRelations());

        return $this->buildPdf($budgetPlan)
            ->name('Budget Plan - ' . $budgetPlan->id . '.pdf');
    }

    /** ZIP berisi satu PDF per plan. */
    public function allPdfs(Request $request)
    {
        $plans = $this->plans($request, withActivities: true);

        if ($plans->isEmpty()) {
            abort(404, 'Tidak ada budget plan untuk diexport.');
        }

        // Folder kerja sementara, dihapus setelah ZIP selesai dibuat.
        $workDir = storage_path('app/tmp/budget-plans-' . Str::uuid());
        File::ensureDirectoryExists($workDir);

        $zipPath = $workDir . '.zip';
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            File::deleteDirectory($workDir);
            abort(500, 'File ZIP tidak bisa dibuat.');
        }

        foreach ($plans as $plan) {
            $fileName = 'Budget Plan - ' . $plan->id . '.pdf';
            $filePath = $workDir . DIRECTORY_SEPARATOR . $fileName;

            $this->buildPdf($plan)->save($filePath);

            $zip->addFile($filePath, $fileName);
        }

        // addFile membaca isi file saat close(), jadi PDF baru boleh dihapus sesudahnya.
        $zip->close();
        File::deleteDirectory($workDir);

        return response()
            ->download($zipPath, 'Budget Plans - ' . now()->format('d M Y') . '.zip')
            ->deleteFileAfterSend(true);
    }

    /**
     * Satu-satunya query: dipakai Excel dan ZIP.
     * Filter opsional: ?project_id=2 dan ?ids[]=111&ids[]=113
     */
    private function plans(Request $request, bool $withActivities)
    {
        $validated = $request->validate([
            'project_id' => ['nullable', 'integer'],
            'ids' => ['nullable', 'array'],
            'ids.*' => ['integer'],
        ]);

        return BudgetPlan::query()
            ->with($withActivities ? $this->pdfRelations() : [
                'workPackage',
                'logStatus',
                'creator',
                'latestActivity.assignee',
                'latestActivity.creator',
            ])
            ->when($validated['project_id'] ?? null, fn ($q, $id) => $q->where('project_id', $id))
            ->when($validated['ids'] ?? null, fn ($q, $ids) => $q->whereIn('id', $ids))
            ->orderByDesc('issued_date')
            ->orderByDesc('id')
            ->get();
    }

    private function pdfRelations(): array
    {
        return [
            'project',
            'workPackage',
            'logStatus',
            'creator',
            'latestActivity.assignee',
            'latestActivity.creator',
            'activities' => fn ($q) => $q
                ->with(['creator', 'assignee', 'logStatus'])
                ->orderByDesc('created_at')
                ->orderByDesc('id'),
        ];
    }

    private function buildPdf(BudgetPlan $plan)
    {
        return Pdf::view('modules.cost-and-contract.budget-plan.export.pdf', [
            'plan' => $plan,
            'activities' => $plan->activities,
            'generatedAt' => now(),
            'generatedBy' => 'Manpro',   // dari footer contoh PDF
            'projectCategory' => null,   // contoh PDF menampilkan "FIT OUT PROJECTS"; sumbernya belum jelas
            'qr' => null,                // contoh PDF menampilkan QR; belum ada paket dan keputusan isinya
        ])->format('a4');
    }
}
