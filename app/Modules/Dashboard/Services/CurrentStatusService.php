<?php

namespace App\Modules\Dashboard\Services;

use App\Models\LogStatus;
use App\Modules\CostAndContract\WorkPackage\Models\WorkPackage;
use App\Support\CurrentContext;
use Illuminate\Database\Eloquent\Collection;

class CurrentStatusService
{
    private Collection $logStatuses;

    public function __construct(
        protected CurrentContext $currentContext,
    ) {}

    public function get(): array
    {
        $this->logStatuses = LogStatus::query()->get(['id', 'status', 'bg_color'])->keyBy('id');

        return [
            'data' => $this->costAndContractCards(),
            'administration' => $this->administrationCards(),
            'compliance' => $this->complianceCards(),
            'drawing' => $this->drawingCards(),
        ];
    }

    private function costAndContractCards(): array
    {
        $workPackages = WorkPackage::query()
            ->forCurrentTenant($this->currentContext->accountId(), $this->currentContext->projectId())
            ->select('cost_contract_work_packages.id')
            ->withCount('contractDocuments')
            ->get();

        return [
            $this->card('Work Package', $workPackages->count()),
            $this->statusCard('Budget Plan', 'costContractBudgetPlans', [1, 2, 3, 4, 5]),
            $this->statusCard('Owner Estimate', 'costContractOwnerEstimates', [1, 2, 3, 4, 5]),
            $this->unavailableCard('Value Engineering', true, [1, 2, 3, 4, 5]),
            $this->unavailableCard('PreQualification', false, []),
            $this->unavailableCard('RFP', true, [6, 7, 8, 9]),
            $this->unavailableCard('Tender Submission', true, [10, 11, 12, 13, 14]),
            $this->unavailableCard('Tender Addendum', false, []),
            $this->unavailableCard('Tender Scoring', false, []),
            $this->unavailableCard('Tender Report', true, [1, 2, 3, 4, 5]),
            $this->card('Contract Document', $workPackages->sum('contract_documents_count')),
            $this->unavailableCard('BQ', false, []),
            $this->unavailableCard('Progress Claim', true, [1, 2, 3, 4, 5]),
            $this->statusCard('Certificate of Payment (COP)', 'costContractCertificateOfPayments', [1, 2, 3, 4, 5, 32]),
            $this->statusCard('Change Order', 'costContractChangeOrders', [1, 2, 3, 4, 5]),
            $this->statusCard('Variation Order', 'costContractVariationOrders', [26, 2, 3, 4, 5]),
            $this->statusCard('Tracking Invoice', 'costContractTrackingInvoices', [24, 25]),
            $this->unavailableCard('Final Account', true, [1, 2, 3, 4, 5]),
            $this->unavailableCard('Financial Report', false, []),
            $this->unavailableCard('Tender Progress', true, [15, 16, 17, 19, 10, 20]),
        ];
    }

    private function administrationCards(): array
    {
        return [
            $this->statusCard('Minutes of Meeting', 'administrationMinutesOfMeetings', [26, 2, 27, 3, 4, 5]),
            $this->unavailableCard('Minutes of Meeting Consultant', true, [26, 2, 27, 3, 4, 5]),
            $this->statusCard('Site Instruction', 'administrationSiteInstructions', [26, 2, 3, 4, 5]),
            $this->statusCard('Site Memo', 'administrationSiteMemos', [26, 2, 3, 4, 5]),
            $this->unavailableCard('Certificate of Completion (BA)', true, [26, 2, 3, 4, 5]),
            $this->statusCard('Closing Document Checklist', 'administrationClosingDocumentChecklists', [26, 2, 3, 4, 5]),
            $this->unavailableCard('RFI', true, [28, 29]),
            $this->unavailableCard('Project Activity Tracker', true, [28, 29]),
        ];
    }

    private function complianceCards(): array
    {
        return [
            $this->unavailableCard('Project Execution Plan (PEP)', true, [1, 3, 4, 5]),
            $this->unavailableCard('ITP', true, [26, 2, 3, 4, 5]),
            $this->unavailableCard('Non-Conformance Report (NCR)', true, [28, 29]),
            $this->unavailableCard('Project Audit', false, []),
            $this->unavailableCard('Quality Inspection', true, [1, 2, 3, 4, 5, 32]),
        ];
    }

    private function drawingCards(): array
    {
        return [
            $this->unavailableCard('Drawing Schedules', false, []),
            $this->unavailableCard('Conceptual Design', true, [1, 2, 3, 4, 5]),
            $this->unavailableCard('Schematic Design', true, [1, 2, 3, 4, 5]),
            $this->unavailableCard('Design Development', true, [1, 2, 3, 4, 5]),
            $this->unavailableCard('For Tender', true, [1, 3, 4, 5, 2]),
            $this->unavailableCard('For Construction', true, [1, 2, 3, 4, 5]),
            $this->unavailableCard('Pre Construction Drawing', true, [1, 2, 3, 4, 5]),
            $this->unavailableCard('Shop Drawing', true, [1, 2, 3, 4, 5]),
            $this->unavailableCard('As Built Drawing', true, [1, 2, 3, 4, 5]),
        ];
    }

    private function statusCard(
        string $title,
        string $relation,
        array $statusIds
    ): array {
        $summary = (new LogStatus)->currentStatusSummary(
            $relation,
            $statusIds,
            $this->currentContext->accountId(),
            $this->currentContext->projectId(),
        );

        return $this->card($title, $summary['total'], $summary['statuses']);
    }

    private function card(string $title, int $total, ?Collection $statuses = null): array
    {
        return [
            'title' => $title,
            'count' => $total,
            'statuses' => $statuses ?? [],
            'show_chart' => $statuses !== null,
        ];
    }

    private function unavailableCard(
        string $title,
        bool $showChart,
        array $statusIds
    ): array {
        return [
            'title' => $title,
            'count' => null,
            'has_data_source' => false,
            'statuses' => $this->logStatuses->whereIn('id', $statusIds)
                ->sortBy(fn ($status) => array_search($status->id, $statusIds))->values(),
            'show_chart' => $showChart,
        ];
    }
}
