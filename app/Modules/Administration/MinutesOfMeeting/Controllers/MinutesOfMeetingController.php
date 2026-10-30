<?php

namespace App\Modules\Administration\MinutesOfMeeting\Controllers;

use App\Core\Controllers\BaseModuleController;
use App\Models\LogStatus;
use App\Modules\Administration\MinutesOfMeeting\Models\MinutesOfMeeting;
use App\Support\CurrentContext;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class MinutesOfMeetingController extends BaseModuleController
{
    protected const MODULE_PATH = 'modules.administration.minutes-of-meeting.';

    public function __construct(
        protected CurrentContext $currentContext
    ) {}

    public function main(): View
    {
        $statuses = LogStatus::pluck('status', 'id');

        $items = MinutesOfMeeting::query()
            ->where('project_id', $this->currentContext->projectId())
            ->orderBy('id')
            ->get();

        $rows = $items->map(function ($i) use ($statuses) {
            return [
                'number' => $i->minutesOfMeetingNumber,
                'date' => Carbon::parse($i->getRawOriginal('issued_date'))->format('d-M-Y'),
                'subject' => $i->meeting_subject,
                'venue' => $i->venue,
                'status' => $statuses->get($i->log_status_id, ''),
                'encrypted_id' => $this->cryptoService()->encryptId($i->id),
            ];
        });

        return $this->viewModule('main', ['rows' => $rows]);
    }
}
