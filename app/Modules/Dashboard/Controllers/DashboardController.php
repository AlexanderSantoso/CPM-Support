<?php

namespace App\Modules\Dashboard\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class DashboardController extends Controller
{
    protected const MODULE_PATH = "modules.dashboard.";

    public function main()
    {
        return view(self::MODULE_PATH . 'main');
    }

}
