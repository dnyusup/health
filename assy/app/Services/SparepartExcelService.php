<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Sparepart;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Illuminate\Support\Facades\DB;

class SparepartExcelService
{
    /**
     * Export spareparts to Excel template with existing data
     */
    public function exportTemplate(): Spreadsheet
    {
        // Increase execution time and memory for large exports
        set_time_limit(300); // 5 minutes
        ini_set('memory_limit', '512M');
        
        $spreadsheet = new Spreadsheet();
        
        // Create main data sheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Spareparts');

        // Define headers
        $headers = ['Material Code', 'Bin Location', 'Old Material No', 'Description', 'Stock', 'Unit', 'Min Stock', 'Category'];
        
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
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(50);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(20);

        // Get categories for lookup
        $categories = Category::pluck('name', 'id')->toArray();

        // Process spareparts in chunks for memory efficiency
        $row = 2;
        Sparepart::with('category')
            ->orderBy('material_code')
            ->chunk(1000, function ($spareparts) use ($sheet, &$row) {
                $data = [];
                foreach ($spareparts as $sparepart) {
                    $data[] = [
                        $sparepart->material_code,
                        $sparepart->bin_location,
                        $sparepart->old_material_no,
                        $sparepart->description,
                        $sparepart->stock,
                        $sparepart->unit,
                        $sparepart->min_stock,
                        $sparepart->category?->name ?? '',
                    ];
                }
                // Use fromArray for bulk insert (much faster)
                $sheet->fromArray($data, null, "A{$row}");
                $row += count($data);
            });

        $lastDataRow = $row - 1;

        // Create Categories reference sheet
        $categorySheet = $spreadsheet->createSheet();
        $categorySheet->setTitle('Categories');
        
        $categorySheet->setCellValue('A1', 'Category Name');
        $categorySheet->setCellValue('B1', 'Description');
        $categorySheet->getStyle('A1:B1')->applyFromArray($headerStyle);
        $categorySheet->getColumnDimension('A')->setWidth(25);
        $categorySheet->getColumnDimension('B')->setWidth(50);

        $allCategories = Category::orderBy('name')->get();
        $catRow = 2;
        foreach ($allCategories as $category) {
            $categorySheet->setCellValue("A{$catRow}", $category->name);
            $categorySheet->setCellValue("B{$catRow}", $category->description);
            $catRow++;
        }

        // Add category dropdown validation only to empty rows for new entries (not all data rows - too slow)
        $emptyRowsStart = $lastDataRow + 1;
        $emptyRowsEnd = $emptyRowsStart + 100; // Only 100 empty rows for new entries
        
        if ($allCategories->count() > 0) {
            $categoryList = $allCategories->pluck('name')->implode(',');
            $validation = $sheet->getCell("H{$emptyRowsStart}")->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1('"' . $categoryList . '"');
            
            // Apply only to empty rows
            for ($i = $emptyRowsStart; $i <= $emptyRowsEnd; $i++) {
                $sheet->getCell("H{$i}")->setDataValidation(clone $validation);
            }
        }

        // Create Instructions sheet
        $instructionSheet = $spreadsheet->createSheet();
        $instructionSheet->setTitle('Instructions');
        
        $instructions = [
            ['SPART - Sparepart Data Import Instructions'],
            [''],
            ['Column Descriptions:'],
            ['Material Code - Unique identifier for the sparepart (Required)'],
            ['Bin Location - Storage location code'],
            ['Old Material No - Previous/legacy material number'],
            ['Description - Full description of the sparepart (Required)'],
            ['Stock - Current stock quantity (number)'],
            ['Unit - Unit of measurement (e.g., PCS, SET, MTR)'],
            ['Min Stock - Minimum stock level for alerts'],
            ['Category - Select from dropdown (see Categories sheet)'],
            [''],
            ['Instructions:'],
            ['1. Edit existing data or add new rows below existing data'],
            ['2. Material Code must be unique - duplicates will update existing records'],
            ['3. Use the Category dropdown to select valid categories'],
            ['4. Stock and Min Stock should be numbers'],
            ['5. Save the file and upload it back to the application'],
            [''],
            ['Note: Do not modify the header row or column order'],
        ];

        foreach ($instructions as $idx => $instruction) {
            $instructionSheet->setCellValue('A' . ($idx + 1), $instruction[0]);
        }
        
        $instructionSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $instructionSheet->getStyle('A3')->getFont()->setBold(true);
        $instructionSheet->getStyle('A13')->getFont()->setBold(true);
        $instructionSheet->getColumnDimension('A')->setWidth(70);

        // Set active sheet back to Spareparts
        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    /**
     * Import spareparts from Excel file
     */
    public function importFromFile(string $filePath): array
    {
        // Increase execution time for large imports
        set_time_limit(300); // 5 minutes
        ini_set('memory_limit', '512M');
        
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getSheetByName('Spareparts') ?? $spreadsheet->getActiveSheet();
        
        $highestRow = $sheet->getHighestDataRow();
        
        $imported = 0;
        $updated = 0;
        $errors = [];

        // Get category mapping
        $categories = Category::pluck('id', 'name')->toArray();
        
        // Get all existing material codes for faster lookup
        $existingMaterials = Sparepart::pluck('id', 'material_code')->toArray();

        // Collect data in batches
        $batchSize = 500;
        $insertBatch = [];
        $updateBatch = [];

        DB::beginTransaction();
        
        try {
            for ($row = 2; $row <= $highestRow; $row++) {
                $materialCode = trim((string) $sheet->getCell("A{$row}")->getValue());
                
                // Skip empty rows
                if (empty($materialCode)) {
                    continue;
                }

                $binLocation = trim((string) $sheet->getCell("B{$row}")->getValue());
                $oldMaterialNo = trim((string) $sheet->getCell("C{$row}")->getValue());
                $description = trim((string) $sheet->getCell("D{$row}")->getValue());
                $stock = $sheet->getCell("E{$row}")->getValue();
                $unit = trim((string) $sheet->getCell("F{$row}")->getValue());
                $minStock = $sheet->getCell("G{$row}")->getValue();
                $categoryName = trim((string) $sheet->getCell("H{$row}")->getValue());

                // Validate required fields
                if (empty($description)) {
                    $errors[] = "Row {$row}: Description is required for material code '{$materialCode}'";
                    continue;
                }

                // Get category ID
                $categoryId = null;
                if (!empty($categoryName)) {
                    $categoryId = $categories[$categoryName] ?? null;
                    if ($categoryId === null) {
                        // Create category if not exists
                        $category = Category::create(['name' => $categoryName]);
                        $categories[$categoryName] = $category->id;
                        $categoryId = $category->id;
                    }
                }

                // Prepare data
                $data = [
                    'material_code' => $materialCode,
                    'bin_location' => $binLocation ?: null,
                    'old_material_no' => $oldMaterialNo ?: null,
                    'description' => $description,
                    'stock' => is_numeric($stock) ? (float) $stock : 0,
                    'unit' => $unit ?: 'PCS',
                    'min_stock' => is_numeric($minStock) ? (float) $minStock : 0,
                    'category_id' => $categoryId,
                ];

                // Check if exists using array lookup (much faster than DB query)
                if (isset($existingMaterials[$materialCode])) {
                    $data['id'] = $existingMaterials[$materialCode];
                    $updateBatch[] = $data;
                    $updated++;
                } else {
                    $data['created_at'] = now();
                    $data['updated_at'] = now();
                    $insertBatch[] = $data;
                    $existingMaterials[$materialCode] = true; // Mark as exists for duplicate check
                    $imported++;
                }

                // Process batches
                if (count($insertBatch) >= $batchSize) {
                    Sparepart::insert($insertBatch);
                    $insertBatch = [];
                }
                
                if (count($updateBatch) >= $batchSize) {
                    foreach ($updateBatch as $updateData) {
                        $id = $updateData['id'];
                        unset($updateData['id'], $updateData['material_code']);
                        $updateData['updated_at'] = now();
                        Sparepart::where('id', $id)->update($updateData);
                    }
                    $updateBatch = [];
                }
            }

            // Process remaining batches
            if (!empty($insertBatch)) {
                Sparepart::insert($insertBatch);
            }
            
            if (!empty($updateBatch)) {
                foreach ($updateBatch as $updateData) {
                    $id = $updateData['id'];
                    unset($updateData['id'], $updateData['material_code']);
                    $updateData['updated_at'] = now();
                    Sparepart::where('id', $id)->update($updateData);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $errors[] = "Import failed: " . $e->getMessage();
        }

        return [
            'imported' => $imported,
            'updated' => $updated,
            'errors' => $errors,
        ];
    }

    /**
     * Download spreadsheet as file
     */
    public function download(Spreadsheet $spreadsheet, string $filename): void
    {
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}
