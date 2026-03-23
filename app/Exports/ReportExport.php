<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportExport implements FromCollection, WithHeadings
{
    public function __construct(protected $data) {}

    public function collection()
    {
        return collect($this->data)->map(fn($row) => (array) $row);
    }

    public function headings(): array
    {
        // Prende le chiavi del primo elemento come intestazioni
        if ($this->data->isEmpty()) return [];
        return array_keys((array) $this->data->first());
    }
}
