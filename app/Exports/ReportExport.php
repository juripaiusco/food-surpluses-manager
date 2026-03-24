<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected $data,
        protected array $labels
    ) {}

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return $this->labels;
    }
}
