<?php

namespace App\Modules\CostAndContract\CertificateOfPayment\Controllers;

use App\Core\Controllers\BaseModuleController;
use App\Modules\CostAndContract\CertificateOfPayment\Models\CertificateOfPayment;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;

class CertificateOfPaymentExportController extends BaseModuleController
{
    public function pdf(string $encryptedId)
    {
        $cop = CertificateOfPayment::findOrFail(
            $this->cryptoService()->decryptId($encryptedId)
        );

        $cop->load(['project.account', 'contractDocument.vendor', 'progressClaim.termOfPayment']);

        $project = $cop->project;
        $account = $project?->account;
        $contract = $cop->contractDocument;
        $vendor = $contract?->vendor;

        $contractValue = (float) $contract?->contract_amount_excl_vat;
        $progressValue = $cop->progress_excl_vat_per_term_payment;

        // Persentase progress mengikuti progress claim (cost_contract_certificate_of_payments.cc_progress_claim_id):
        // tipe 'physical' memakai cost_contract_progress_claims.physical_progress,
        // tipe 'top' memakai cost_contract_contract_document_term_of_payments.payment_progress.
        $claim = $cop->progressClaim;
        $claimPercent = $claim?->progress_type === 'top'
            ? $claim->termOfPayment?->payment_progress
            : $claim?->physical_progress;

        $progressPercent = $claimPercent !== null
            ? number_format((float) $claimPercent, 3, '.', '') . '%'
            : '';

        $docNo = $cop->copNumber ?? '-';
        $fileNo = str_replace('/', '-', $docNo); // "/" tidak boleh ada di nama file

        return Pdf::view('modules.cost-and-contract.certificate-of-payment.export.certificate-of-payment', [
            'docNo' => $docNo,
            // Name dan Position dikosongkan dulu karena sampel aplikasi acuan juga kosong.
            'signerName' => '',
            'signerPosition' => '',
            'accountName' => $account?->account_name,
            'accountAddress' => $account?->address,
            'accountLogo' => $this->imageDataUri(filled($account?->logo) ? 'images/account/' . $account->logo : null),
            'projectText' => collect([$project?->project_name, $project?->project_description])->filter()->implode(', '),
            'vendorName' => $vendor?->company_name,
            'vendorAddress' => $vendor?->address,

            'contractValue' => $this->money($contractValue),
            'downPercent' => $this->percent($contract?->down_payment),
            'downValue' => $this->money($cop->down_payment_value),
            'progressPercent' => $progressPercent,
            'progressValue' => $this->money($progressValue),
            'additional' => $this->money($cop->additional_deductional_works, blankZero: true),
            'valueOfProgress' => $this->money($cop->value_of_progress),
            'rounded' => $this->money($cop->rounded_value),
            'retentionPercent' => $this->percent($contract?->retention, blankZero: true),
            'retentionValue' => $this->money($cop->retention_value, blankZero: true),
            'previouslyPaid' => $this->money($cop->deduction_previously_paid, blankZero: true),
            'totalDeduction' => $this->money($cop->total_deduction, blankZero: true),
            'totalToReceive' => $this->money($cop->total_to_receive_payment),
            'others' => $this->money($cop->deduction_others, blankZero: true),
            'amountToBePaid' => $this->money($cop->amount_to_be_paid_excl_vat),
            'remarks' => $cop->deduction_remarks,
        ])->format('a4')->name('Certificate of Payment - ' . $fileNo . '.pdf');
    }

    /**
     * Format rupiah seperti aplikasi acuan: Rp426.620.000,00. Nilai kosong menjadi teks kosong,
     * dan nilai 0 ikut kosong bila blankZero aktif (baris deduction pada sampel).
     */
    private function money(mixed $value, bool $blankZero = false): string
    {
        if ($value === null || $value === '' || ($blankZero && (float) $value == 0.0)) {
            return '';
        }

        return 'Rp' . number_format((float) $value, 2, ',', '.');
    }

    /**
     * Persentase dari kontrak tanpa nol di belakang: 20.00 menjadi "20%".
     */
    private function percent(mixed $value, bool $blankZero = false): string
    {
        if ($value === null || $value === '' || ($blankZero && (float) $value == 0.0)) {
            return '';
        }

        return rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.') . '%';
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
