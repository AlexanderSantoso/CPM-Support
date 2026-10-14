<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class UsersExport implements FromView, ShouldAutoSize
{
    public function __construct(private Collection $users) {}

    public function view(): View
    {
        return view('exports.users-excel', ['users' => $this->users]);
    }
}
