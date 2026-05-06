<?php

namespace App\Services;

use App\Models\AssyWorkOrder;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AssyWorkOrderExcelService
{
    public function export(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Work Orders');

        $headers = [
            'Tanggal Bongkar', 'Order Number', 'Order Type',
            'Mach Number', 'Mach Type', 'Pos',
            'Part ID', 'Part Name', 'Category', 'Part Detail',
            'Kerusakan', 'PIC Bongkar', 'Remark', 'Status', 'Created By', 'Created On',
        ];

        foreach ($headers as $col => $header) {
            $sheet->setCellValue(chr(65 + $col) . '1', $header);
        }

        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];
        $lastCol = chr(65 + count($headers) - 1);
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray($headerStyle);

        $colWidths = [18, 18, 12, 15, 12, 8, 12, 30, 25, 30, 25, 15, 25, 10, 15, 22];
        foreach ($colWidths as $i => $w) {
            $sheet->getColumnDimension(chr(65 + $i))->setWidth($w);
        }

        $row = 2;
        foreach (AssyWorkOrder::with(['pic', 'creator'])->cursor() as $wo) {
            $sheet->setCellValue('A' . $row, $wo->tanggal_bongkar?->format('m/d/Y'));
            $sheet->setCellValue('B' . $row, $wo->order_number);
            $sheet->setCellValue('C' . $row, $wo->order_type);
            $sheet->setCellValue('D' . $row, $wo->mach_number);
            $sheet->setCellValue('E' . $row, $wo->mach_type);
            $sheet->setCellValue('F' . $row, $wo->pos);
            $sheet->setCellValue('G' . $row, $wo->part_id);
            $sheet->setCellValue('H' . $row, $wo->part_name);
            $sheet->setCellValue('I' . $row, $wo->category);
            $sheet->setCellValue('J' . $row, $wo->part_detail);
            $sheet->setCellValue('K' . $row, $wo->kerusakan);
            $sheet->setCellValue('L' . $row, $wo->pic->name ?? '');
            $sheet->setCellValue('M' . $row, $wo->remark);
            $sheet->setCellValue('N' . $row, $wo->status);
            $sheet->setCellValue('O' . $row, $wo->creator->name ?? '');
            $sheet->setCellValue('P' . $row, $wo->created_at?->format('m/d/Y H:i:s'));
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);

        $filename = 'assy_work_orders_' . now()->format('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    }
}
