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
use App\Modules\CostAndContract\TenderReport\Models\TenderReport;
use App\Modules\CostAndContract\TenderProgress\Models\TenderProgress;
use App\Modules\CostAndContract\ProgressClaim\Models\ProgressClaim;
use App\Modules\CostAndContract\CertificateOfPayment\Models\CertificateOfPayment;
use App\Modules\CostAndContract\ChangeOrder\Models\ChangeOrder;
use App\Modules\CostAndContract\VariationOrder\Models\VariationOrder;
use App\Modules\CostAndContract\TrackingInvoice\Models\TrackingInvoice;
use App\Modules\CostAndContract\FinalAccount\Models\FinalAccount;
use App\Modules\Administration\ClosingDocumentChecklist\Models\ClosingDocumentChecklist;
use App\Modules\Administration\MinutesOfMeeting\Models\MinutesOfMeeting;
use App\Modules\Administration\SiteInstruction\Models\SiteInstruction;
use App\Modules\Administration\SiteMemo\Models\SiteMemo;

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

    public function administrationClosingDocumentChecklists(): HasMany
    {
        return $this->hasMany(ClosingDocumentChecklist::class, 'log_status_id');
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

    public function costContractTenderReports(): HasMany
    {
        return $this->hasMany(TenderReport::class, 'log_status_id');
    }

    public function costContractTenderProgress(): HasMany
    {
        return $this->hasMany(TenderProgress::class, 'log_status_id');
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
}
