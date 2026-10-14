<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\LaravelPdf\Facades\Pdf;

class UserExportController extends Controller
{
    private function users()
    {
        return User::orderBy('name')
            ->get();
    }

    public function excel()
    {
        return Excel::download(new UsersExport($this->users()), 'users.xlsx');
    }

    public function pdf()
    {
        return Pdf::view('exports.users-pdf', ['users' => $this->users(), 'logo' => $this->logoDataUri()])
            ->name('users.pdf');
    }

    private function logoDataUri(): ?string
    {
    $path = public_path('images/logo.png');

    if (! is_file($path)) {
        return null;
    }

    return 'data:image/png;base64,' . base64_encode(file_get_contents($path));
    }
}
