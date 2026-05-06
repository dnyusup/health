<?php

namespace App\Services;

use App\Models\AssyMachine;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AssyMachineExcelService
{
    /**
     * Export all machines to Excel using cursor() for memory efficiency.
     * Streams directly to browser without loading all data into memory.
     */
    public function export(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Machines');

        // Header row
        $headers = ['Mach Number', 'Mach Type', 'Mach Area', 'Created By', 'Created On'];
        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . '1';
            $sheet->setCellValue($cell, $header);
        }

        // Style header
        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(20);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(22);

        // Write data using cursor (memory efficient for large datasets)
        $row = 2;
        foreach (AssyMachine::with('creator')->cursor() as $machine) {
            $sheet->setCellValue('A' . $row, $machine->mach_number);
            $sheet->setCellValue('B' . $row, $machine->mach_type);
            $sheet->setCellValue('C' . $row, $machine->mach_area);
            $sheet->setCellValue('D' . $row, $machine->creator->name ?? '');
            $sheet->setCellValue('E' . $row, $machine->created_at?->format('m/d/Y H:i:s') ?? '');
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);

        $filename = 'assy_machines_' . now()->format('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    }

    /**
     * Import machines from Excel.
     * Reads file once with row iterator, batches DB writes every 500 rows.
     *
     * @return array{inserted: int, updated: int, skipped: int, errors: array}
     */
    public function import(UploadedFile $file, int $userId): array
    {
        $stats = ['inserted' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];

        $reader = IOFactory::createReaderForFile($file->getPathname());
        $reader->setReadDataOnly(true);

        $spreadsheet = $reader->load($file->getPathname());
        $worksheet   = $spreadsheet->getActiveSheet();

        if ($worksheet->getHighestDataRow() < 2) {
            $spreadsheet->disconnectWorksheets();
            $stats['errors'][] = 'File kosong atau tidak ada data.';
            return $stats;
        }

        // Pre-load existing mach_numbers for insert/update detection
        $existingNums = DB::table('assy_machines')->pluck('id', 'mach_number')->toArray();

        $batchInsert = [];
        $batchUpdate = [];
        $now         = now()->toDateTimeString();
        $processed   = 0;

        foreach ($worksheet->getRowIterator(2) as $excelRow) {
            $cellIterator = $excelRow->getCellIterator('A', 'C');
            $cellIterator->setIterateOnlyExistingCells(false);

            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }

            $machNumber = strtoupper(trim($cells[0] ?? ''));

            if (empty($machNumber)) {
                $stats['skipped']++;
                continue;
            }

            $machType = trim($cells[1] ?? '');
            $machArea = trim($cells[2] ?? '');

            if (empty($machType) || empty($machArea)) {
                $rowNum = $excelRow->getRowIndex();
                $stats['errors'][] = "Baris {$rowNum}: '{$machNumber}' - Mach Type dan Mach Area wajib diisi.";
                $stats['skipped']++;
                continue;
            }

            $data = [
                'mach_number' => $machNumber,
                'mach_type'   => $machType,
                'mach_area'   => $machArea,
                'updated_at'  => $now,
            ];

            if (isset($existingNums[$machNumber])) {
                $batchUpdate[] = $data;
            } else {
                $data['created_by'] = $userId;
                $data['created_at'] = $now;
                $batchInsert[]      = $data;
                $existingNums[$machNumber] = true;
            }

            $processed++;

            if (count($batchInsert) >= 500) {
                DB::table('assy_machines')->insert($batchInsert);
                $stats['inserted'] += count($batchInsert);
                $batchInsert = [];
            }

            if (count($batchUpdate) >= 500) {
                DB::table('assy_machines')->upsert(
                    $batchUpdate,
                    ['mach_number'],
                    ['mach_type', 'mach_area', 'updated_at']
                );
                $stats['updated'] += count($batchUpdate);
                $batchUpdate = [];
            }

            if ($processed % 5000 === 0) {
                gc_collect_cycles();
            }
        }

        // Flush remaining
        if (!empty($batchInsert)) {
            DB::table('assy_machines')->insert($batchInsert);
            $stats['inserted'] += count($batchInsert);
        }

        if (!empty($batchUpdate)) {
            DB::table('assy_machines')->upsert(
                $batchUpdate,
                ['mach_number'],
                ['mach_type', 'mach_area', 'updated_at']
            );
            $stats['updated'] += count($batchUpdate);
        }

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet, $existingNums);
        gc_collect_cycles();

        return $stats;
    }
}
