<?php

namespace App\Services;

use App\Models\AssyMachine;
use App\Models\AssyWorkOrder;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
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

    /**
     * Download import template with column instructions.
     * Required columns = blue header, auto columns = gray header.
     */
    public function exportTemplate(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Import Template');

        // Row 1: column type label (WAJIB / AUTO)
        // Row 2: column name header
        // Row 3+: sample data row

        $columns = [
            // [header, type, sampleValue]
            // type: 'required' | 'auto'
            ['Tanggal Bongkar',   'required', '31/12/2026'],
            ['Order Number',      'optional', 'WO-001'],
            ['Order Type',        'required', 'ZSPM'],
            ['Mach Number',       'required', 'ISC-1'],
            ['Pos',               'optional', '1'],
            ['Part ID',           'optional', 'S171'],
            ['Part Name',         'optional', 'Screw Pump WAP'],
            ['Category',          'optional', 'Screw Pump'],
            ['Part Detail',       'optional', '0.4 Kw'],
            ['Kerusakan',         'optional', 'Bearing rusak'],
            ['PIC Bongkar (Nama)','required', 'DENI YUSUP'],
            ['Remark',            'optional', ''],
            // Repair
            ['Tgl Assembling',    'optional', '01/01/2027'],
            ['Action Assembling', 'optional', 'Ganti bearing'],
            ['PIC Assembling (Nama)', 'optional', 'ADI ISKANDAR, RADENAL TRINOVA'],
            ['Remark Assembling', 'optional', ''],
            // Install
            ['Tgl Pasang',        'optional', '05/01/2027'],
            ['Mesin Install',     'optional', 'ISC-2'],
            ['Pos Install',       'optional', '3'],
            ['PIC Pasang (Nama)', 'optional', 'ADI ISKANDAR'],
            ['Remark Pemasangan', 'optional', ''],
        ];

        $requiredStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];
        $optionalStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => '374151']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];
        $sectionRepairStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D97706']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];
        $sectionInstallStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];

        // Row 1: type label | Row 2: column name | Row 3: sample
        foreach ($columns as $i => $col) {
            $colLetter = Coordinate::stringFromColumnIndex($i + 1);
            $typeLabel = $col[1] === 'required' ? '★ WAJIB DIISI' : 'opsional';
            $sheet->setCellValue($colLetter . '1', $typeLabel);
            $sheet->setCellValue($colLetter . '2', $col[0]);
            $sheet->setCellValue($colLetter . '3', $col[2]);

            // Style row 1 (type indicator)
            if ($col[1] === 'required') {
                $sheet->getStyle($colLetter . '1')->applyFromArray($requiredStyle);
            } else {
                $sheet->getStyle($colLetter . '1')->applyFromArray($optionalStyle);
            }

            // Style row 2 (section colors)
            if ($i <= 11) {
                // Pembongkaran section — blue
                $sheet->getStyle($colLetter . '2')->applyFromArray($requiredStyle);
            } elseif ($i <= 15) {
                // Repair section — amber
                $sheet->getStyle($colLetter . '2')->applyFromArray($sectionRepairStyle);
            } else {
                // Install section — green
                $sheet->getStyle($colLetter . '2')->applyFromArray($sectionInstallStyle);
            }

            $sheet->getColumnDimension($colLetter)->setWidth(22);
        }

        // Freeze rows 1-2
        $sheet->freezePane('A3');
        $sheet->getRowDimension(1)->setRowHeight(18);
        $sheet->getRowDimension(2)->setRowHeight(18);

        // Auto-generated columns legend in a second sheet
        $infoSheet = $spreadsheet->createSheet();
        $infoSheet->setTitle('Keterangan');
        $infoSheet->setCellValue('A1', 'Keterangan Import Work Order');
        $infoSheet->setCellValue('A3', '★ WAJIB DIISI');
        $infoSheet->setCellValue('B3', 'Kolom harus diisi, baris akan diskip jika kosong');
        $infoSheet->setCellValue('A4', 'opsional');
        $infoSheet->setCellValue('B4', 'Kolom boleh kosong');
        $infoSheet->setCellValue('A6', 'Kolom AUTO (tidak perlu diisi):');
        $infoSheet->setCellValue('A7', '- Status');
        $infoSheet->setCellValue('B7', 'Otomatis: Open (jika tgl_assembling kosong), On Progress/Closed (dari data assembling)');
        $infoSheet->setCellValue('A8', '- Mach Type');
        $infoSheet->setCellValue('B8', 'Otomatis diambil dari Mach Number yang terdaftar di sistem');
        $infoSheet->setCellValue('A9', '- Mesin Type Install');
        $infoSheet->setCellValue('B9', 'Otomatis diambil dari Mesin Install yang terdaftar di sistem');
        $infoSheet->setCellValue('A10', '- Created By / Created On');
        $infoSheet->setCellValue('B10', 'Otomatis: user yang melakukan import & waktu import');
        $infoSheet->setCellValue('A11', '- Repaired By / Repaired At');
        $infoSheet->setCellValue('B11', 'Otomatis: user yang melakukan import & waktu import (jika ada data assembling)');
        $infoSheet->setCellValue('A12', '- Installed By / Installed At');
        $infoSheet->setCellValue('B12', 'Otomatis: user yang melakukan import & waktu import (jika ada data pemasangan)');
        $infoSheet->setCellValue('A14', 'Format Tanggal: DD/MM/YYYY (contoh: 31/12/2026)');
        $infoSheet->setCellValue('A15', 'Order Type yang valid: ZSPM atau ZSBM');
        $infoSheet->setCellValue('A16', 'PIC Bongkar / PIC Assembling / PIC Pasang: isi dengan nama lengkap user (case-insensitive), pisahkan dengan koma jika lebih dari satu');
        $infoSheet->getColumnDimension('A')->setWidth(30);
        $infoSheet->getColumnDimension('B')->setWidth(80);
        $infoSheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 13]]);
        $infoSheet->getStyle('A3:A12')->applyFromArray(['font' => ['bold' => true]]);

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);

        $filename = 'assy_work_orders_import_template.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    }

    /**
     * Import work orders from Excel.
     * Reads rows starting from row 3 (row1=type, row2=header).
     * PIC fields resolved by name (case-insensitive).
     *
     * @return array{inserted: int, skipped: int, errors: array}
     */
    public function import(UploadedFile $file, int $userId): array
    {
        $stats = ['inserted' => 0, 'skipped' => 0, 'errors' => []];

        $reader = IOFactory::createReaderForFile($file->getPathname());
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file->getPathname());
        $worksheet   = $spreadsheet->getActiveSheet();
        $highestRow  = $worksheet->getHighestDataRow();

        if ($highestRow < 3) {
            $stats['errors'][] = 'File kosong atau tidak ada data (mulai dari baris 3).';
            $spreadsheet->disconnectWorksheets();
            return $stats;
        }

        // Pre-load lookup maps
        $userMap    = User::pluck('id', DB::raw('LOWER(name)'))->toArray();
        // keyed by lower(name) => id, handle duplicates (first wins)
        $userMapRaw = User::select('id', 'name')->get();
        $userByName = [];
        foreach ($userMapRaw as $u) {
            $key = strtolower(trim($u->name));
            if (!isset($userByName[$key])) {
                $userByName[$key] = $u->id;
            }
        }

        $machineMap = AssyMachine::select('id', 'mach_number', 'mach_type')
            ->get()
            ->keyBy(fn($m) => strtolower(trim($m->mach_number)));

        $now = now()->toDateTimeString();

        $resolvePicIds = function (string $raw) use ($userByName): array {
            $ids = [];
            foreach (explode(',', $raw) as $name) {
                $key = strtolower(trim($name));
                if ($key !== '' && isset($userByName[$key])) {
                    $ids[] = $userByName[$key];
                }
            }
            return array_unique($ids);
        };

        $parseDate = function ($val): ?string {
            if (empty($val)) return null;
            // Handle Excel serial number
            if (is_numeric($val)) {
                try {
                    return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val)
                        ->format('Y-m-d');
                } catch (\Exception $e) {
                    return null;
                }
            }
            // Try DD/MM/YYYY
            $d = \DateTime::createFromFormat('d/m/Y', trim($val));
            if ($d) return $d->format('Y-m-d');
            // Try other common formats
            try {
                return (new \DateTime(trim($val)))->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        };

        $batchInsert = [];
        $processed   = 0;

        for ($rowNum = 3; $rowNum <= $highestRow; $rowNum++) {
            $get = fn(string $col) => trim((string) ($worksheet->getCell($col . $rowNum)->getValue() ?? ''));

            $tanggalBongkar = $parseDate($get('A'));
            $orderType      = strtoupper($get('C'));
            $machNumber     = strtoupper($get('D'));
            $picBongkarName = $get('K');

            // Validate required columns
            if (!$tanggalBongkar) {
                $stats['errors'][] = "Baris {$rowNum}: Tanggal Bongkar tidak valid atau kosong.";
                $stats['skipped']++;
                continue;
            }
            if (!in_array($orderType, ['ZSPM', 'ZSBM'])) {
                $stats['errors'][] = "Baris {$rowNum}: Order Type '{$orderType}' tidak valid (ZSPM/ZSBM).";
                $stats['skipped']++;
                continue;
            }
            if (empty($machNumber)) {
                $stats['errors'][] = "Baris {$rowNum}: Mach Number wajib diisi.";
                $stats['skipped']++;
                continue;
            }
            if (empty($picBongkarName)) {
                $stats['errors'][] = "Baris {$rowNum}: PIC Bongkar wajib diisi.";
                $stats['skipped']++;
                continue;
            }

            // Resolve machine
            $machKey = strtolower($machNumber);
            if (!isset($machineMap[$machKey])) {
                $stats['errors'][] = "Baris {$rowNum}: Mach Number '{$machNumber}' tidak ditemukan di sistem.";
                $stats['skipped']++;
                continue;
            }
            $machine = $machineMap[$machKey];

            // Resolve PIC Bongkar (single user)
            $picBongkarKey = strtolower(trim($picBongkarName));
            if (!isset($userByName[$picBongkarKey])) {
                $stats['errors'][] = "Baris {$rowNum}: PIC Bongkar '{$picBongkarName}' tidak ditemukan.";
                $stats['skipped']++;
                continue;
            }
            $picBongkarId = $userByName[$picBongkarKey];

            // Optional fields — pembongkaran
            $orderNumber = $get('B') ?: null;
            $pos         = $get('E') ?: null;
            $partId      = $get('F') ? strtoupper($get('F')) : null;
            $partName    = $get('G') ?: null;
            $category    = $get('H') ?: null;
            $partDetail  = $get('I') ?: null;
            $kerusakan   = $get('J') ?: null;
            $remark      = $get('L') ?: null;

            // Repair fields
            $tanggalAssembling  = $parseDate($get('M'));
            $actionAssembling   = $get('N') ?: null;
            $picAsmRaw          = $get('O');
            $remarkAssembling   = $get('P') ?: null;
            $picAssemblingIds   = $picAsmRaw !== '' ? $resolvePicIds($picAsmRaw) : [];

            // Install fields
            $tanggalPasang      = $parseDate($get('Q'));
            $installMachNumber  = strtoupper($get('R')) ?: null;
            $installPos         = $get('S') ?: null;
            $picPasangRaw       = $get('T');
            $remarkPemasangan   = $get('U') ?: null;
            $picPasangIds       = $picPasangRaw !== '' ? $resolvePicIds($picPasangRaw) : [];

            // Resolve install machine
            $installMachineId   = null;
            $installMachType    = null;
            if ($installMachNumber) {
                $iKey = strtolower($installMachNumber);
                if (isset($machineMap[$iKey])) {
                    $installMachineId  = $machineMap[$iKey]->id;
                    $installMachType   = $machineMap[$iKey]->mach_type;
                }
            }

            // Determine status
            if ($tanggalPasang && $installMachineId) {
                $status = 'Installed';
            } elseif ($tanggalAssembling && $actionAssembling) {
                $status = $tanggalAssembling ? 'Closed' : 'On Progress';
            } else {
                $status = 'Open';
            }

            $row = [
                'tanggal_bongkar'     => $tanggalBongkar,
                'order_number'        => $orderNumber,
                'order_type'          => $orderType,
                'machine_id'          => $machine->id,
                'mach_number'         => $machine->mach_number,
                'mach_type'           => $machine->mach_type,
                'pos'                 => $pos,
                'part_id'             => $partId,
                'part_name'           => $partName,
                'category'            => $category,
                'part_detail'         => $partDetail,
                'kerusakan'           => $kerusakan,
                'pic_bongkar'         => $picBongkarId,
                'remark'              => $remark,
                'status'              => $status,
                'created_by'          => $userId,
                'created_at'          => $now,
                'updated_at'          => $now,
                // Repair
                'tanggal_assembling'  => $tanggalAssembling,
                'action_assembling'   => $actionAssembling,
                'pic_assembling'      => $picAssemblingIds ? json_encode($picAssemblingIds) : null,
                'remark_assembling'   => $remarkAssembling,
                'repaired_by'         => $tanggalAssembling ? $userId : null,
                'repaired_at'         => $tanggalAssembling ? $now : null,
                // Install
                'tanggal_pasang'      => $tanggalPasang,
                'install_machine_id'  => $installMachineId,
                'install_mach_number' => $installMachNumber,
                'install_mach_type'   => $installMachType,
                'install_pos'         => $installPos,
                'pic_pasang'          => $picPasangIds ? json_encode($picPasangIds) : null,
                'remark_pemasangan'   => $remarkPemasangan,
                'installed_by'        => $tanggalPasang ? $userId : null,
                'installed_at'        => $tanggalPasang ? $now : null,
            ];

            $batchInsert[] = $row;
            $processed++;

            if (count($batchInsert) >= 200) {
                DB::table('assy_work_orders')->insert($batchInsert);
                $stats['inserted'] += count($batchInsert);
                $batchInsert = [];
            }

            if ($processed % 2000 === 0) {
                gc_collect_cycles();
            }
        }

        if (!empty($batchInsert)) {
            DB::table('assy_work_orders')->insert($batchInsert);
            $stats['inserted'] += count($batchInsert);
        }

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet, $machineMap, $userByName);
        gc_collect_cycles();

        return $stats;
    }
}

