<?php

namespace App\Exports;

use App\Models\AttendanceSession;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SessionExport implements FromView
{
    protected $session;

    public function __construct(AttendanceSession $session)
    {
        $this->session = $session;
    }

    public function view(): View
    {
        return view('exports.session', [
            'session' => $this->session,
            'records' => $this->session->records()->with('user')->get()
        ]);
    }
}
