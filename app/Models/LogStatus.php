<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Modules\CostAndContract\BudgetPlan\Models\BudgetPlan;
use App\Modules\CostAndContract\OwnerEstimate\Models\OwnerEstimate;
use App\Modules\CostAndContract\ValueEngineering\Models\ValueEngineering;
use App\Modules\CostAndContract\RFP\Models\RFP;
use App\Modules\CostAndContract\TenderSubmission\Models\TenderSubmission;
use App\Modules\CostAndContract\TenderAddendum\Models\TenderAddendum;
use App\Modules\CostAndContract\TenderScoring\Models\TenderScoring;
use App\Modules\CostAndContract\TenderReport\Models\TenderReport;
use App\Modules\CostAndContract\TenderProgress\Models\TenderProgress;
use App\Modules\CostAndContract\BQ\Models\Bq;
use App\Modules\CostAndContract\ProgressClaim\Models\ProgressClaim;
use App\Modules\CostAndContract\CertificateOfPayment\Models\CertificateOfPayment;
use App\Modules\CostAndContract\ChangeOrder\Models\ChangeOrder;
use App\Modules\CostAndContract\VariationOrder\Models\VariationOrder;
use App\Modules\CostAndContract\TrackingInvoice\Models\TrackingInvoice;
use App\Modules\CostAndContract\FinalAccount\Models\FinalAccount;
use App\Modules\Administration\ClosingDocumentChecklist\Models\ClosingDocumentChecklist;
use App\Modules\Administration\CertificateOfCompletion\Models\CertificateOfCompletion;
use App\Modules\Administration\MinutesOfMeeting\Models\MinutesOfMeeting;
use App\Modules\Administration\SiteInstruction\Models\SiteInstruction;
use App\Modules\Administration\SiteMemo\Models\SiteMemo;
use App\Modules\Administration\Rfi\Models\Rfi;
use App\Modules\Administration\ProjectActivityTracker\Models\ProjectActivityTracker;
use App\Modules\Compliance\ProjectExecutionPlan\Models\ProjectExecutionPlan;
use App\Modules\Compliance\NonConformanceReport\Models\NonConformanceReport;
use App\Modules\Compliance\InspectionTesting\Models\InspectionTesting;
use App\Modules\Compliance\ProjectAudit\Models\ProjectAudit;
use App\Modules\Compliance\QualityInspection\Models\QualityInspection;
use App\Modules\Drawing\DrawingConceptualDesign\Models\DrawingConceptualDesign;
use App\Modules\Drawing\DrawingSchematicDesign\Models\DrawingSchematicDesign;
use App\Modules\Drawing\DrawingDesignDevelopment\Models\DrawingDesignDevelopment;
use App\Modules\Drawing\DrawingForTender\Models\DrawingForTender;
use App\Modules\Drawing\DrawingForConstruction\Models\DrawingForConstruction;
use App\Modules\Drawing\PreConstructionDrawing\Models\PreConstructionDrawing;
use App\Modules\Drawing\ShopDrawings\Models\ShopDrawings;
use App\Modules\Drawing\AsBuiltDrawings\Models\AsBuiltDrawings;
use App\Modules\Material\MaterialApproval\Models\MaterialApproval;
use App\Modules\Material\MaterialApprovalActivity\Models\MaterialApprovalActivity;


class LogStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'status',
        'bg_color',
    ];

    public function currentStatusSummary(
        string $relation,
        array $statusIds,
        int $accountId,
        int $projectId
    ): array {
        $statuses = static::query()
            ->select(['id', 'status', 'bg_color'])
            ->whereIn('id', $statusIds)
            ->withCount([
                $relation . ' as total' => fn ($query) => $query->forCurrentTenant($accountId, $projectId),
            ])
            ->get()
            ->sortBy(fn ($status) => array_search($status->id, $statusIds))
            ->values();

        $total = $this->{$relation}()->getRelated()->newQuery()
            ->forCurrentTenant($accountId, $projectId)
            ->count();

        return ['total' => $total, 'statuses' => $statuses];
    }

    public function administrationMinutesOfMeetings(): HasMany
    {
        return $this->hasMany(MinutesOfMeeting::class, 'log_status_id');
    }

    public function administrationSiteInstructions(): HasMany
    {
        return $this->hasMany(SiteInstruction::class, 'log_status_id');
    }

    public function administrationSiteMemos(): HasMany
    {
        return $this->hasMany(SiteMemo::class, 'log_status_id');    
    }

    public function administrationCertificateOfCompletions(): HasMany
    {
        return $this->hasMany(CertificateOfCompletion::class, 'log_status_id');
    }

    public function administrationClosingDocumentChecklists(): HasMany
    {
        return $this->hasMany(ClosingDocumentChecklist::class, 'log_status_id');
    }

    public function administrationRfis(): HasMany
    {
        return $this->hasMany(Rfi::class, 'log_status_id');
    }

    public function administrationProjectActivityTrackers(): HasMany
    {
        return $this->hasMany(ProjectActivityTracker::class, 'log_status_id');
    }

    public function costContractBudgetPlans(): HasMany
    {
        return $this->hasMany(BudgetPlan::class, 'log_status_id');
    }

    public function costContractOwnerEstimates(): HasMany
    {
        return $this->hasMany(OwnerEstimate::class, 'log_status_id');
    }

    public function costContractValueEngineerings(): HasMany
    {
        return $this->hasMany(ValueEngineering::class, 'log_status_id');
    }

    public function costContractRFPs(): HasMany
    {
        return $this->hasMany(RFP::class, 'log_status_id');
    }

    public function costContractTenderSubmissions(): HasMany
    {
        return $this->hasMany(TenderSubmission::class, 'log_status_id');
    }

    public function costContractTenderAddendums(): HasMany
    {
        return $this->hasMany(TenderAddendum::class, 'log_status_id');
    }

    public function costContractTenderScorings(): HasMany
    {
        return $this->hasMany(TenderScoring::class, 'log_status_id');
    }

    public function costContractTenderReports(): HasMany
    {
        return $this->hasMany(TenderReport::class, 'log_status_id');
    }

    public function costContractTenderProgress(): HasMany
    {
        return $this->hasMany(TenderProgress::class, 'log_status_id');
    }

    public function costContractBqs(): HasMany
    {
        return $this->hasMany(Bq::class, 'log_status_id');
    }

    public function costContractProgressClaims(): HasMany
    {
        return $this->hasMany(ProgressClaim::class, 'log_status_id');
    }

    public function costContractCertificateOfPayments(): HasMany
    {
        return $this->hasMany(CertificateOfPayment::class, 'log_status_id');
    }

    public function costContractChangeOrders(): HasMany
    {
        return $this->hasMany(ChangeOrder::class, 'log_status_id');
    }

    public function costContractVariationOrders(): HasMany
    {
        return $this->hasMany(VariationOrder::class, 'log_status_id');
    }

    public function costContractTrackingInvoices(): HasMany
    {
        return $this->hasMany(TrackingInvoice::class, 'log_status_id');
    }

    public function costContractFinalAccounts(): HasMany
    {
        return $this->hasMany(FinalAccount::class, 'log_status_id');
    }

    public function materialApprovals(): HasMany
    {
        return $this->hasMany(MaterialApproval::class, 'log_status_id');
    }

    public function approvedMaterialTrackings(): HasMany
    {
        return $this->hasMany(MaterialApprovalActivity::class, 'log_status_id');
    }

    public function materialApprovalTrackings(): HasMany
    {
        return $this->hasMany(MaterialApproval::class, 'material_log_status_id');
    }

    public function complianceProjectExecutionPlans(): HasMany
    {
        return $this->hasMany(ProjectExecutionPlan::class, 'log_status_id');
    }

    public function drawingConceptualDesigns(): HasMany
    {
        return $this->hasMany(DrawingConceptualDesign::class, 'log_status_id');
    }
  
    public function drawingSchematicDesigns(): HasMany
    {
        return $this->hasMany(DrawingSchematicDesign::class, 'log_status_id');
    }

    public function drawingDesignDevelopments(): HasMany
    {
        return $this->hasMany(DrawingDesignDevelopment::class, 'log_status_id');
    }

    public function drawingForTenders(): HasMany
    {
        return $this->hasMany(DrawingForTender::class, 'log_status_id');
    }

    public function drawingForConstructions(): HasMany
    {
        return $this->hasMany(DrawingForConstruction::class, 'log_status_id');
    }

    public function preConstructionDrawings(): HasMany
    {
        return $this->hasMany(PreConstructionDrawing::class, 'log_status_id');
    }

    public function shopDrawings(): HasMany
    {
        return $this->hasMany(ShopDrawings::class, 'log_status_id');
    }

    public function drawingAsBuilts(): HasMany
    {
        return $this->hasMany(AsBuiltDrawings::class, 'log_status_id');
    }

    public function nonConformanceReports(): HasMany
    {
        return $this->hasMany(NonConformanceReport::class, 'log_status_id');
    }  

    public function inspectionTestings(): HasMany
    {
        return $this->hasMany(InspectionTesting::class, 'log_status_id');
    }

    public function projectAudit(): HasMany
    {
        return $this->hasMany(ProjectAudit::class, 'log_status_id');
    }

    public function qualityInspections(): HasMany
    {
        return $this->hasMany(QualityInspection::class, 'log_status_id');
    }
}
