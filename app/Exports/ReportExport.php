<?php

namespace App\Exports;


use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements FromArray, WithStyles, ShouldAutoSize
{
    use Exportable;

	protected $invoices;

    public function __construct(array $invoices)
    {
        $this->invoices = $invoices[0];
        $this->colors = $invoices[1];
    }

    public function styles(Worksheet $sheet)
    {
        $color = [];
        $color[1] = ['font' => ['bold' => true], 'background' => ['grey'=>true]];
        foreach ($this->colors as $key => $value) {
            $color[$value] = ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'rotation' => 0, 'color' => ['rgb' => 'D9D9D9']]];
        }

        return $color;
    }
    // public function headings(): array
    // {
    //     $headings = [];
    //     foreach ($this->invoices as $key => $value) {
    //         foreach ($value as $k => $v) {
    //             $headings[$k] = $k;
    //         }
    //     }
    //     return $headings;
          
    // }
    public function array(): array
    {
        return $this->invoices;
    }
}
