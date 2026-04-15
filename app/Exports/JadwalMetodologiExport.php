<?php

namespace App\Exports;

use App\Models\JadwalMetodologi;
use Maatwebsite\Excel\Concerns\FromCollection;

class JadwalMetodologiExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return JadwalMetodologi::all();
    }
}
