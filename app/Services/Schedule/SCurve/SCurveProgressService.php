<?php

namespace App\Services\Schedule\SCurve;

use App\Models\SCurve;

class SCurveProgressService
{
    public function getProgress($projectId, $contractorId, $workPackageId)
    {
        return SCurve::query()
            ->where('project_id', $projectId)
            ->where('contractor_id', $contractorId)
            ->where('project_work_package_id', $workPackageId)
            ->select('id', 'report_start_date', 'report_end_date')
            ->selectRaw("
                  SUM(planned_progress) OVER (
                  PARTITION BY
                  project_id,
                  contractor_id,
                  project_work_package_id
                  ORDER BY report_start_date, id
                  ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
            ) AS total_planned_progress")
            ->selectRaw("SUM(actual_progress) OVER (
                  PARTITION BY
                  project_id,
                  contractor_id,
                  project_work_package_id
                  ORDER BY report_start_date, id
                  ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
                  ) AS total_actual_progress")
            ->orderBy('report_start_date')
            ->orderBy('id')
            ->get();
    }
}