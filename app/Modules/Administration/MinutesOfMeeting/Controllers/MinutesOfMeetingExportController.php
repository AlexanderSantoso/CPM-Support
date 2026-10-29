<?php

namespace App\Modules\Administration\MinutesOfMeeting\Controllers;

use App\Core\Controllers\BaseModuleController;
use App\Modules\Administration\MinutesOfMeeting\Models\MinutesOfMeeting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;

class MinutesOfMeetingExportController extends BaseModuleController
{
    public function pdf(string $encryptedId)
    {
        $minutesOfMeeting = MinutesOfMeeting::findOrFail($this->cryptoService()->decryptId($encryptedId));

        $minutesOfMeeting->load([
            'project.account',
            'attendees' => fn ($q) => $q->with('company')->orderBy('id'),
            'details' => fn ($q) => $q->orderBy('id'),
        ]);

        // Satu baris per perusahaan, urut berdasarkan attendee pertama dari perusahaan itu.
        $attendeeGroups = $minutesOfMeeting->attendees
            ->groupBy('company_id')
            ->map(fn ($rows) => [
                'company' => $rows->first()->company?->company_name ?? '-',
                'names' => $rows->pluck('attendee_name')->all(),
            ])
            ->values();

        // Baris A = notes meeting, baris B, C, ... = meeting details.
        $items = collect([[
            'subject' => 'Notes',
            'description' => $minutesOfMeeting->notes,
            'action_by' => null,
            'due_date' => null,
        ]])
            ->concat($minutesOfMeeting->details->map(fn ($d) => [
                'subject' => $d->subject,
                'description' => $d->description,
                'action_by' => $d->action_by,
                'due_date' => $d->due_date?->format('d-M-Y'),
            ]))
            ->values()
            ->map(fn ($row, $i) => ['no' => $i < 26 ? chr(65 + $i) : (string) ($i + 1)] + $row);

        $docNo = $minutesOfMeeting->minutes_of_meeting_number ?? '-';
        $fileNo = str_replace('/', '-', $docNo); // "/" tidak boleh ada di nama file

        $project = $minutesOfMeeting->project;
        $account = $project?->account;

        return Pdf::view('modules.administration.minutes-of-meeting.export.minutes-of-meeting', [
            'docNo' => $docNo,
            'projectName' => $project?->project_name,
            'issuedDate' => $minutesOfMeeting->issued_date?->format('d-M-Y'),
            'subject' => $minutesOfMeeting->meeting_subject,
            'venue' => $minutesOfMeeting->venue,
            'time' => Carbon::parse($minutesOfMeeting->meeting_time)->format('H:i'),
            'attendeeGroups' => $attendeeGroups,
            'items' => $items,
            'logo' => $this->imageDataUri($account?->logo, 'images/account/'),
            'projectLogo' => $this->imageDataUri($project?->logo, 'images/project/logo/'),
        ])->format('a4')->name('Minutes of Meeting - ' . $fileNo . '.pdf');
    }

    /**
     * Logo account disimpan di storage/app/public/images/account/.
     * Diubah menjadi data URI supaya DOMPDF tidak perlu mencari path atau URL.
     * Mengembalikan null kalau file tidak ada, jadi PDF tetap jadi tanpa logo.
     */
    private function logoDataUri(?string $file): ?string
    {
        if (blank($file)) {
            return null;
        }

        $disk = Storage::disk('public');
        $path = 'images/account/' . $file;

        if (! $disk->exists($path)) {
            return null;
        }

        $mime = $disk->mimeType($path) ?: 'image/png';

        return 'data:' . $mime . ';base64,' . base64_encode($disk->get($path));
    }

    private function imageDataUri(?string $file, string $folder): ?string
    {
        if (blank($file)) {
            return null;
        }

        $disk = Storage::disk('public');
        $path = $folder . $file;

        if (! $disk->exists($path)) {
            return null;
        }

        $mime = $disk->mimeType($path) ?: 'image/png';

        return 'data:' . $mime . ';base64,' . base64_encode($disk->get($path));
    }
}
