<?php

namespace App\Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Dashboard\Services\CurrentStatusService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class CurrentStatusController extends Controller
{
    public function __construct(
        protected CurrentStatusService $currentStatus,
    ) {}

    public function main(): RedirectResponse
    {
        return redirect()->route('dashboard.dashboard');
    }

    public function data(): JsonResponse
    {
        return response()->json($this->currentStatus->get());
    }
}
