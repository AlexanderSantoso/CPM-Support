<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class BudgetPlansExport implements FromView, ShouldAutoSize, WithColumnFormatting
{
    public function __construct(private Collection $plans) {}

    public function view(): View
    {
        return view('modules.cost-and-contract.budget-plan.export.excel', ['plans' => $this->plans]);
    }

    public function columnFormats(): array
    {
        return ['D' => '#,##0.00']; // column D = Budget Estimate
    }
}
