<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClearanceExport implements FromCollection, WithHeadings
{
    private $surveyResults;

    public function __construct($surveyResults)
    {
        $this->surveyResults = $surveyResults;
    }

    public function collection()
    {
        return $this->surveyResults;
        // return DB::table('entries')->get();
    }

    public function headings(): array
    {
        return [
            'Clearance Entry ID',
            'Clearance Beach ID',
            'Clearance Responsible Party Beach ID',
            'Clearance Responsible Party Email',
            'Clearance Entry Full Name',
            'Clearance Division',
            'Clearance Department Desc',
            'Clearance Department ID',
            'Clearance Title',
            'Clearance ASM Email',
            'Clearance Last Date Worked',
            'Clearance Declaration Keys',
            'Clearance Declaration Library Obligation',
            'Clearance Declaration AV Equipment',
            'Clearance Declaration IT Equipment',
            'Clearance PCard ',
            'Clearance Exit Survey',
            'Clearance Conflict',
            'Clearance Payroll',
            'Clearance Timestamp'];
    }
}
