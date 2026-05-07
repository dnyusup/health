<?php

namespace App\Services;

use App\Models\AssyWorkOrder;
use App\Models\User;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class AssyWorkOrderExcelService
{
    public function export(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Work Orders');

        $headers = [
            // Pembongkaran
            'Tanggal Bongkar', 'Order Number', 'Order Type',
            'Mach Number', 'Mach Type', 'Pos',
            'Part ID', 'Part Name', 'Category', 'Part Detail',
            'Kerusakan', 'PIC Bongkar', 'Remark', 'Status',
            'Created By', 'Created On',
            // Repair / Assembling
            'Tgl Assembling', 'Action Assembling', 'PIC Assembling',
            'Remark Assembling', 'Repaired By', 'Repaired At',
            // Pemasangan / Install
            'Tgl Pasang', 'Mesin Install', 'Type Install', 'Pos Install',
            'PIC Pasang', 'Remark Pemasangan', 'Installed By', 'Installed At',
        ];

        foreach ($headers as $col => $header) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col + 1) . '1', $header);
        }

        // Section header colors
        $blueStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];
        $amberStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D97706']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];
        $emeraldStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];

        $sheet->getStyle('A1:P1')->applyFromArray($blueStyle);
        $sheet->getStyle('Q1:V1')->applyFromArray($amberStyle);
        $sheet->getStyle('W1:AD1')->applyFromArray($emeraldStyle);

        $colWidths = [
            18, 18, 12, 15, 12, 8, 12, 30, 25, 30, 25, 20, 25, 12, 20, 22,
            18, 40, 35, 30, 20, 22,
            18, 15, 12, 10, 35, 30, 20, 22,
        ];
        foreach ($colWidths as $i => $w) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i + 1))->setWidth($w);
        }

        // Pre-load all users into a map id -> name for pic_assembling / pic_pasang lookups
        $userMap = User::pluck('name', 'id')->toArray();

        $resolvePicNames = function (?array $ids) use ($userMap): string {
            if (empty($ids)) return '';
            return implode(', ', array_map(fn($id) => $userMap[$id] ?? "#{$id}", $ids));
        };

        $row = 2;
        foreach (AssyWorkOrder::with(['pic', 'creator', 'repairedBy', 'installedBy'])->cursor() as $wo) {
            // Pembongkaran
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
            // Repair / Assembling
            $sheet->setCellValue('Q' . $row, $wo->tanggal_assembling?->format('m/d/Y'));
            $sheet->setCellValue('R' . $row, $wo->action_assembling);
            $sheet->setCellValue('S' . $row, $resolvePicNames($wo->pic_assembling));
            $sheet->setCellValue('T' . $row, $wo->remark_assembling);
            $sheet->setCellValue('U' . $row, $wo->repairedBy->name ?? '');
            $sheet->setCellValue('V' . $row, $wo->repaired_at?->format('m/d/Y H:i:s'));
            // Pemasangan / Install
            $sheet->setCellValue('W' . $row, $wo->tanggal_pasang?->format('m/d/Y'));
            $sheet->setCellValue('X' . $row, $wo->install_mach_number);
            $sheet->setCellValue('Y' . $row, $wo->install_mach_type);
            $sheet->setCellValue('Z' . $row, $wo->install_pos);
            $sheet->setCellValue('AA' . $row, $resolvePicNames($wo->pic_pasang));
            $sheet->setCellValue('AB' . $row, $wo->remark_pemasangan);
            $sheet->setCellValue('AC' . $row, $wo->installedBy->name ?? '');
            $sheet->setCellValue('AD' . $row, $wo->installed_at?->format('m/d/Y H:i:s'));
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
