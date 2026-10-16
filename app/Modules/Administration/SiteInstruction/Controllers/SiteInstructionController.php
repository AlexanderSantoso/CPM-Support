<?php

namespace App\Modules\Administration\SiteInstruction\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use App\Modules\Administration\SiteInstruction\Models\SiteInstruction;

class SiteInstructionController extends Controller
{
    protected const MODULE_PATH = "modules.administration.site-instruction.";

    public function main()
    {
        $siteInstructions = SiteInstruction::all();

        $data = [
            'siteInstructions' => $siteInstructions,
        ];

        return view(self::MODULE_PATH . 'main', $data);
    }

}
