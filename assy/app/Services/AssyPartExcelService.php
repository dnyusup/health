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
    private const CHUNK_SIZE = 1000;

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
     * Import parts from Excel using ChunkReadFilter for memory efficiency.
     * Uses upsert (insert or update by part_id) for idempotent imports.
     *
     * @return array{inserted: int, updated: int, skipped: int, errors: array}
     */
    public function import(UploadedFile $file, int $userId): array
    {
        $stats = ['inserted' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];

        $reader = IOFactory::createReaderForFile($file->getPathname());
        $reader->setReadDataOnly(true);

        // First pass: get total row count
        $reader->setReadFilter(new ChunkReadFilter(2, 2));
        $spreadsheet = $reader->load($file->getPathname());
        $totalRows = $spreadsheet->getActiveSheet()->getHighestDataRow();
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        if ($totalRows < 2) {
            $stats['errors'][] = 'File kosong atau tidak ada data.';
            return $stats;
        }

        // Get existing part_ids for quick lookup
        $existingPartIds = DB::table('assy_parts')->pluck('id', 'part_id')->toArray();

        // Process in chunks
        $startRow = 2;
        while ($startRow <= $totalRows) {
            $filter = new ChunkReadFilter($startRow, self::CHUNK_SIZE);
            $reader->setReadFilter($filter);

            $spreadsheet = $reader->load($file->getPathname());
            $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);

            $toInsert = [];
            $toUpdate = [];
            $now = now()->toDateTimeString();

            foreach ($rows as $index => $row) {
                // Skip header row if included
                if ($startRow === 2 && $index === 0 && strtolower(trim($row[0] ?? '')) === 'part id') {
                    continue;
                }

                $partId = strtoupper(trim($row[0] ?? ''));

                if (empty($partId)) {
                    $stats['skipped']++;
                    continue;
                }

                $category = trim($row[1] ?? '');
                $partName = trim($row[2] ?? '');
                $partDetail = trim($row[3] ?? '') ?: null;

                if (empty($category) || empty($partName)) {
                    $rowNum = $startRow + $index;
                    $stats['errors'][] = "Baris {$rowNum}: Part ID '{$partId}' - Category dan Part Name wajib diisi.";
                    $stats['skipped']++;
                    continue;
                }

                $data = [
                    'part_id'    => $partId,
                    'category'   => $category,
                    'part_name'  => $partName,
                    'part_detail' => $partDetail,
                    'updated_at' => $now,
                ];

                if (isset($existingPartIds[$partId])) {
                    $toUpdate[] = $data;
                } else {
                    $data['created_by'] = $userId;
                    $data['created_at'] = $now;
                    $toInsert[] = $data;
                    $existingPartIds[$partId] = true; // prevent duplicate in same import
                }
            }

            // Batch insert new records
            if (!empty($toInsert)) {
                foreach (array_chunk($toInsert, 500) as $chunk) {
                    DB::table('assy_parts')->insert($chunk);
                    $stats['inserted'] += count($chunk);
                }
            }

            // Batch update existing records
            if (!empty($toUpdate)) {
                DB::transaction(function () use ($toUpdate, &$stats) {
                    foreach (array_chunk($toUpdate, 500) as $chunk) {
                        // upsert by part_id, update other columns
                        DB::table('assy_parts')->upsert(
                            $chunk,
                            ['part_id'],
                            ['category', 'part_name', 'part_detail', 'updated_at']
                        );
                        $stats['updated'] += count($chunk);
                    }
                });
            }

            $startRow += self::CHUNK_SIZE;
            gc_collect_cycles();
        }

        return $stats;
    }
}

/**
 * ChunkReadFilter: reads only a specific range of rows from Excel.
 * This prevents loading the entire file into memory at once.
 */
class ChunkReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    private int $startRow;
    private int $endRow;

    public function __construct(int $startRow, int $chunkSize)
    {
        $this->startRow = $startRow;
        $this->endRow   = $startRow + $chunkSize - 1;
    }

    public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool
    {
        // Always include header row so column mapping stays consistent
        if ($row === 1) {
            return true;
        }
        return $row >= $this->startRow && $row <= $this->endRow;
    }
}
