<?php

namespace App\Services;

use App\Models\AssyPart;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AssyPartExcelService
{
    /**
     * Export all parts to Excel using cursor() for memory efficiency.
     * Streams directly to browser without loading all data into memory.
     */
    public function export(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Parts');

        // Header row
        $headers = ['Part ID', 'Category', 'Part Name', 'Part Detail', 'Created By', 'Created On'];
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
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(20);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(35);
        $sheet->getColumnDimension('D')->setWidth(35);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(22);

        // Write data using cursor (memory efficient for large datasets)
        $row = 2;
        foreach (AssyPart::with('creator')->cursor() as $part) {
            $sheet->setCellValue('A' . $row, $part->part_id);
            $sheet->setCellValue('B' . $row, $part->category);
            $sheet->setCellValue('C' . $row, $part->part_name);
            $sheet->setCellValue('D' . $row, $part->part_detail ?? '');
            $sheet->setCellValue('E' . $row, $part->creator->name ?? '');
            $sheet->setCellValue('F' . $row, $part->created_at?->format('m/d/Y H:i:s') ?? '');
            $row++;
        }

        // Disable formula pre-calculation for performance
        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);

        // Stream to browser
        $filename = 'assy_parts_' . now()->format('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    }

    /**
     * Import parts from Excel.
     * Reads the file once using row iterator, batches DB writes every 500 rows.
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

        // Pre-load existing part_ids (id => part_id) for insert/update detection
        $existingPartIds = DB::table('assy_parts')->pluck('id', 'part_id')->toArray();

        $batchInsert = [];
        $batchUpdate = [];
        $now         = now()->toDateTimeString();
        $processed   = 0;

        // Start from row 2 to skip header
        foreach ($worksheet->getRowIterator(2) as $excelRow) {
            $cellIterator = $excelRow->getCellIterator('A', 'D');
            $cellIterator->setIterateOnlyExistingCells(false);

            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }

            $partId = strtoupper(trim($cells[0] ?? ''));

            if (empty($partId)) {
                $stats['skipped']++;
                continue;
            }

            $category   = trim($cells[1] ?? '');
            $partName   = trim($cells[2] ?? '');
            $partDetail = trim($cells[3] ?? '') ?: null;

            if (empty($category) || empty($partName)) {
                $rowNum = $excelRow->getRowIndex();
                $stats['errors'][] = "Baris {$rowNum}: '{$partId}' - Category dan Part Name wajib diisi.";
                $stats['skipped']++;
                continue;
            }

            $data = [
                'part_id'     => $partId,
                'category'    => $category,
                'part_name'   => $partName,
                'part_detail' => $partDetail,
                'updated_at'  => $now,
            ];

            if (isset($existingPartIds[$partId])) {
                $batchUpdate[] = $data;
            } else {
                $data['created_by'] = $userId;
                $data['created_at'] = $now;
                $batchInsert[]      = $data;
                $existingPartIds[$partId] = true; // prevent duplicate in same batch
            }

            $processed++;

            // Flush insert batch every 500 rows
            if (count($batchInsert) >= 500) {
                DB::table('assy_parts')->insert($batchInsert);
                $stats['inserted'] += count($batchInsert);
                $batchInsert = [];
            }

            // Flush update batch every 500 rows
            if (count($batchUpdate) >= 500) {
                DB::table('assy_parts')->upsert(
                    $batchUpdate,
                    ['part_id'],
                    ['category', 'part_name', 'part_detail', 'updated_at']
                );
                $stats['updated'] += count($batchUpdate);
                $batchUpdate = [];
            }

            // Free cyclic references periodically
            if ($processed % 5000 === 0) {
                gc_collect_cycles();
            }
        }

        // Flush remaining rows
        if (!empty($batchInsert)) {
            DB::table('assy_parts')->insert($batchInsert);
            $stats['inserted'] += count($batchInsert);
        }

        if (!empty($batchUpdate)) {
            DB::table('assy_parts')->upsert(
                $batchUpdate,
                ['part_id'],
                ['category', 'part_name', 'part_detail', 'updated_at']
            );
            $stats['updated'] += count($batchUpdate);
        }

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet, $existingPartIds);
        gc_collect_cycles();

        return $stats;
    }
}
