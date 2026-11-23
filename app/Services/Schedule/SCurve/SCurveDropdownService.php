<?php

namespace App\Services\Schedule\SCurve;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use App\Support\CurrentContext;
use App\Services\CryptoService;
use App\Models\Project;
use App\Models\ProjectWorkPackage;
use App\Models\Company;

class SCurveDropdownService
{
    public function __construct(
        protected CurrentContext $currentContext,
        protected CryptoService $cryptoService,
    ) {}

    protected function getCurrentProject(): Project
    {
        return Project::query()
                      ->where('account_id', $this->currentContext->accountId())
                      ->findOrFail($this->currentContext->projectId());
    }

    protected function getContractorIds(Project $project): array
    {
        return collect($project->project_contractor ?? [])
               ->map(fn ($id) => (int) $id)
               ->filter()
               ->unique()
               ->values()
               ->all();
    }

    public function validateContractor(string $contractorCode): Company
    {
        $project       = $this->getCurrentProject();
        $contractorIds = $this->getContractorIds($project);

        $company = Company::query()
                          ->where('company_code', trim($contractorCode))
                          ->where('account_id', $this->currentContext->accountId())
                          ->whereIn('id', $contractorIds)
                          ->first();

        if (!$company) {
            throw ValidationException::withMessages([
                'contractor_code' => 'The selected contractor is invalid',
            ]);
        }

        return $company;
    }

    public function validateWorkPackage(int $workPackageId, Company $contractor): ProjectWorkPackage
    {
        $projectId   = $this->currentContext->projectId();
        $workPackage = ProjectWorkPackage::query()
                                         ->where('id', $workPackageId)
                                         ->where('project_id', $projectId)
                                         ->whereHas('schedules', function ($query) use ($contractor) {
                                            $query->where('company_id', $contractor->id);
                                         })
                                         ->first();

        if (!$workPackage) {
            throw ValidationException::withMessages([
                'work_package' => 'The selected work package is invalid',
            ]);
        }

        return $workPackage;
    }

    public function contractorDropdown(string $keyword = ''): Collection
    {
        $keyword = substr(trim($keyword), 0, 50);

        $project = $this->getCurrentProject();

        $contractorIds = $this->getContractorIds($project);

        if (empty($contractorIds)) {
            return collect();
        }

        $query = Company::query()
                        ->select([
                            'companies.id',
                            'companies.company_code',
                            'companies.company_name',
                        ])
                        ->where('companies.account_id', $this->currentContext->accountId())
                        ->whereIn('companies.id', $contractorIds)
                        ->whereHas('projectSchedules', function ($q) use ($project) {
                            $q->whereHas('workPackage', function ($workPackageQuery) use ($project) {
                                $workPackageQuery->where('project_id', $project->id);
                            });
                        });

        $this->applySearch($query, 'company_name', $keyword);

        return $query
               ->orderBy('company_name')
               ->limit(20)
               ->get()
               ->map(fn ($company) => [
                    'id'   => $company->company_code,
                    'text' => $company->company_name,
               ])
               ->values();
    }

    public function projectWorkPackageDropdown(string $contractorCode, string $keyword = ''): Collection
    {
        $keyword = substr(trim($keyword), 0, 50);

        if (trim($contractorCode) === '') {
            return collect();
        }

        $project       = $this->getCurrentProject();
        $contractorIds = $this->getContractorIds($project);

        if (empty($contractorIds)) {
            return collect();
        }

        $company = Company::query()
                          ->where('company_code', trim($contractorCode))
                          ->where('account_id', $this->currentContext->accountId())
                          ->whereIn('id', $contractorIds)
                          ->first();

        if (!$company) {
            return collect();
        }

        $query = ProjectWorkPackage::query()
                                    ->select([
                                        'project_work_packages.id',
                                        'project_work_packages.work_package',
                                    ])
                                    ->where('project_id', $project->id)
                                    ->whereHas('schedules', function ($q) use ($company) {
                                        $q->where('company_id', $company->id);
                                    });

        $this->applySearch($query, 'work_package', $keyword);

        return $query
               ->orderBy('work_package')
               ->limit(20)
               ->get()
               ->map(fn ($workPackage) => [
                    'id'   => $this->cryptoService->encryptId($workPackage->id),
                    'text' => $workPackage->work_package,
               ])
               ->values();
    }

    protected function applySearch(Builder $query, string $column, string $keyword): void {
        if ($keyword === '') {
            return;
        }

        $escaped   = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], strtolower($keyword));

        $query->whereRaw("LOWER({$column}) LIKE ? ESCAPE '\\\\'", ["%{$escaped}%"]);
    }

    public function dashboardDefaults(): array
    {
        $contractor = $this->contractorDropdown()->first();

        if (!$contractor) {
            return [
                'contractor'   => null,
                'work_package' => null,
            ];
        }

        $workPackage = $this->projectWorkPackageDropdown($contractor['id'])->first();

        return [
            'contractor'   => $contractor,
            'work_package' => $workPackage,
        ];
    }
}