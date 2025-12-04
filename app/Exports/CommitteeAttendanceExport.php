<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CommitteeAttendanceExport implements FromView
{
    protected $committees;

    public function __construct($committees)
    {
        $this->committees = $committees;
    }

    public function view(): View
    {
        return view('exports.committees', [
            'committees' => $this->committees
        ]);
    }
}
