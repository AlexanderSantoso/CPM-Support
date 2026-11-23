<?php

namespace App\Modules\Dashboard\Services;

use App\Models\LogStatus;
use App\Modules\Compliance\ProjectAudit\Models\ProjectAudit;
use App\Modules\CostAndContract\PreQualification\Models\PreQualification;
use App\Modules\CostAndContract\WorkPackage\Models\WorkPackage;
use App\Modules\CostAndContract\TenderAddendum\Models\TenderAddendum;
use App\Modules\CostAndContract\TenderScoring\Models\TenderScoring;
use App\Modules\CostAndContract\BQ\Models\Bq;
use App\Modules\Drawing\DrawingSchedule\Models\DrawingSchedule;
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
            'material' => $this->materialCards(),
        ];
    }

    private function costAndContractCards(): array
    {
        $workPackages = WorkPackage::query()
            ->forCurrentTenant($this->currentContext->accountId(), $this->currentContext->projectId())
            ->select('cost_contract_work_packages.id')
            ->withCount('contractDocuments')
            ->get();
        
        $preQualificationCount = PreQualification::query()
            ->forCurrentTenant($this->currentContext->accountId(), $this->currentContext->projectId())
            ->count();

        $tenderAddendumCount = TenderAddendum::query()
            ->forCurrentTenant($this->currentContext->accountId(), $this->currentContext->projectId())
            ->count();

        $tenderScoringCount = TenderScoring::query()
            ->forCurrentTenant($this->currentContext->accountId(), $this->currentContext->projectId())
            ->count();
        
        $BQCount = Bq::query()
            ->forCurrentTenant($this->currentContext->accountId(), $this->currentContext->projectId())
            ->count();

        return [
            $this->card('Work Package', $workPackages->count(), icon: 'ti ti-folder'),
            $this->statusCard('Budget Plan', 'costContractBudgetPlans', [1, 2, 3, 4, 5]),
            $this->statusCard('Owner Estimate', 'costContractOwnerEstimates', [1, 2, 3, 4, 5]),
            $this->statusCard('Value Engineering', 'costContractValueEngineerings', [1, 2, 3, 4, 5]),
            $this->card('PreQualification', $preQualificationCount, icon: 'ti ti-certificate'),
            $this->statusCard('RFP', 'costContractRFPs', [6, 7, 8, 9]),
            $this->statusCard('Tender Submission', 'costContractTenderSubmissions', [10, 11, 12, 13, 14]),
            $this->Card('Tender Addendum', $tenderAddendumCount, icon: 'ti ti-file-plus'),
            $this->Card('Tender Scoring', $tenderScoringCount, icon: 'ti ti-chart-bar'),
            $this->statusCard('Tender Report', 'costContractTenderReports', [1, 2, 3, 4, 5]),
            $this->card('Contract Document', $workPackages->sum('contract_documents_count'), icon: 'ti ti-file-text'),
            $this->Card('BQ', $BQCount, icon: 'ti ti-notes'),
            $this->statusCard('Progress Claim', 'costContractProgressClaims', [1, 2, 3, 4, 5]),
            $this->statusCard('Certificate of Payment (CoP)', 'costContractCertificateOfPayments', [1, 2, 3, 4, 5]),
            $this->statusCard('Change Order', 'costContractChangeOrders', [1, 2, 3, 4, 5]),
            $this->statusCard('Variation Order', 'costContractVariationOrders', [1, 2, 3, 4, 5]),
            $this->statusCard('Tracking Invoice', 'costContractTrackingInvoices', [24, 25]),
            $this->statusCard('Final Account', 'costContractFinalAccounts', [1, 2, 3, 4, 5]),
            $this->statusCard('Tender Progress', 'costContractTenderProgress', [15, 16, 17, 18, 19, 10, 20, 11, 21, 22, 23]),
        ];
    }

    private function administrationCards(): array
    {
        return [
            $this->statusCard('Minutes of Meeting', 'administrationMinutesOfMeetings', [5, 26, 27]),
            $this->statusCard('Site Instruction', 'administrationSiteInstructions', [2, 3, 4, 5, 26]),
            $this->statusCard('Site Memo', 'administrationSiteMemos', [2, 3, 4, 5, 26]),
            $this->statusCard('Certificate of Completion (BA)', 'administrationCertificateOfCompletions', [1, 2, 3, 4, 5]),
            $this->statusCard('Closing Document Checklist', 'administrationClosingDocumentChecklists', [1, 2, 3, 4, 5]),
            $this->statusCard('RFI', 'administrationRfis', [26, 29, 30]),
            $this->statusCard('Project Activity Tracker', 'administrationProjectActivityTrackers', [29, 28]),
        ];
    }

    private function complianceCards(): array
    {
         $ProjectAuditQCount = ProjectAudit::query()
        ->forCurrentTenant($this->currentContext->accountId(), $this->currentContext->projectId())
        ->count();

        return [
            $this->statusCard('Project Execution Plan (PEP)', 'complianceProjectExecutionPlans', [1, 3, 4, 5]),
            $this->statusCard('ITP', 'inspectionTestings', [1, 2, 3, 4, 5]),
            $this->statusCard('Non-Conformance Report (NCR)', 'nonConformanceReports', [26, 29, 30]),
            $this->Card('Project Audit', $ProjectAuditQCount, icon: 'ti ti-clipboard-check'),
            $this->statusCard('Quality Inspection', 'qualityInspections', [1, 2, 3, 4, 31, 32]),
        ];
    }

    private function drawingCards(): array
    {
        $drawingSchedules = DrawingSchedule::query()
        ->forCurrentTenant($this->currentContext->accountId(), $this->currentContext->projectId())
        ->count();
        return [
            $this->card('Drawing Schedules', $drawingSchedules, icon: 'ti ti-calendar-time'),
            $this->statusCard('Conceptual Design', 'drawingConceptualDesigns', [1, 2, 3, 4, 5]),
            $this->statusCard('Schematic Design', 'drawingSchematicDesigns', [1, 2, 3, 4, 5]),
            $this->statusCard('Design Development', 'drawingDesignDevelopments', [1, 2, 3, 4, 5]),
            $this->statusCard('For Tender', 'drawingForTenders', [1, 2, 3, 4, 5,]),
            $this->statusCard('For Construction', 'drawingForConstructions', [1, 2, 3, 4, 5]),
            $this->statusCard('Pre Construction Drawing', 'preConstructionDrawings', [1, 2, 3, 4, 5]),
            $this->statusCard('Shop Drawing', 'shopDrawings', [1, 2, 3, 4, 5]),
            $this->statusCard('As Built Drawing', 'drawingAsBuilts', [1, 2, 3, 4, 5]),
        ];
    }

    private function materialCards(): array
    {
        return [    
            $this->statusCard('Material Approval', 'materialApprovals', [1, 2, 3, 4, 5]),
            $this->statusCard('Material Approval Tracking', 'materialApprovalTrackings', [15, 33, 34, 35, 36, 37, 38]),
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

    private function card(string $title, int $total, ?Collection $statuses = null, ?string $icon = null): array
    {
        return [
            'title' => $title,
            'count' => $total,
            'statuses' => $statuses ?? [],
            'show_chart' => $statuses !== null,
            'icon' => $icon,
        ];
    }

    private function unavailableCard(
        string $title,
        bool $showChart,
        array $statusIds,
        ?string $icon = null
    ): array {
        return [
            'title' => $title,
            'count' => null,
            'has_data_source' => false,
            'statuses' => $this->logStatuses->whereIn('id', $statusIds)
                ->sortBy(fn ($status) => array_search($status->id, $statusIds))->values(),
            'show_chart' => $showChart,
            'icon' => $icon,
        ];
    }
}
