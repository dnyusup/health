<?php

namespace App\Services;

use App\Models\StockTransaction;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Database\Eloquent\Builder;

class TransactionExcelService
{
    /**
     * Export transactions to Excel
     */
    public function export(Builder $query): Spreadsheet
    {
        // Increase execution time and memory for large exports
        set_time_limit(300); // 5 minutes
        ini_set('memory_limit', '512M');
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Transactions');

        // Define headers
        $headers = ['Transaction Type', 'Date', 'Product Barcode', 'Material Description', 'Quantity', 'Satuan', 'Order Number', 'Name', 'Remark', 'Status', 'Changed By', 'Changed At'];

        // Set headers using fromArray (faster)
        $sheet->fromArray($headers, null, 'A1');

        // Style headers
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3B82F6'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('A1:L1')->applyFromArray($headerStyle);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(18);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(45);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(18);
        $sheet->getColumnDimension('H')->setWidth(25);
        $sheet->getColumnDimension('I')->setWidth(30);
        $sheet->getColumnDimension('J')->setWidth(12);
        $sheet->getColumnDimension('K')->setWidth(25);
        $sheet->getColumnDimension('L')->setWidth(20);

        // Process transactions in chunks for memory efficiency
        $row = 2;
        $query->with(['sparepart', 'user', 'changedByUser'])
            ->orderBy('created_at', 'desc')
            ->chunk(1000, function ($transactions) use ($sheet, &$row) {
                $data = [];
                foreach ($transactions as $transaction) {
                    $data[] = [
                        $transaction->type,
                        $transaction->created_at->format('j/n/Y H:i:s'),
                        $transaction->sparepart->material_code ?? '',
                        $transaction->sparepart->description ?? '',
                        $transaction->quantity,
                        $transaction->sparepart->unit ?? '',
                        $transaction->reference_no ?? '',
                        $transaction->user->name ?? '',
                        $transaction->notes ?? '',
                        $transaction->status ?? 'new',
                        $transaction->changedByUser->name ?? '',
                        $transaction->changed_at ? $transaction->changed_at->format('j/n/Y H:i:s') : '',
                    ];
                }
                // Use fromArray for bulk insert (much faster)
                $sheet->fromArray($data, null, "A{$row}");
                $row += count($data);
            });

        return $spreadsheet;
    }

    /**
     * Generate Excel file and return as download response
     */
    public function download(Builder $query, string $filename = 'transactions.xlsx')
    {
        $spreadsheet = $this->export($query);
        $writer = new Xlsx($spreadsheet);

        // Create temp file
        $tempFile = tempnam(sys_get_temp_dir(), 'transactions_');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
