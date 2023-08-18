<?php
 namespace App\Exports;
   
 use App\Models\Loan;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportSavings implements FromCollection
{
    public function collection()
    {
        return Loan::all();
    }
}