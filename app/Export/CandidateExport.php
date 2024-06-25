<?php

namespace App\Export;

use App\Http\Resources\CandidateExportResource;
use App\Models\Candidate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;

class CandidateExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        $candidates = Candidate::with('user')->get();

        if ($candidates->isEmpty()) {
            return collect(); // Return an empty collection
        }

        return CandidateExportResource::collection($candidates);
    }

    public function headings(): array
    {
        return ['Name', 'Email', 'Role', 'Profession', 'Gender', 'Website', 'Number', 'Address'];
    }

    public function styles($sheet)
    {
        $headerRange = 'A1:'.$sheet->getHighestColumn().'1';

        // Set background color for the header row
        $sheet
            ->getStyle($headerRange)
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor(); // Yellow background color for the header row

        // Set text alignment to center for the header row
        $sheet
            ->getStyle($headerRange)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Set the range for the entire table (adjust the range as needed)
        $tableData = 'A2:'.$sheet->getHighestColumn().$sheet->getHighestRow();

        // Set text alignment to center for the table cells
        $sheet
            ->getStyle($tableData)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    }
}
