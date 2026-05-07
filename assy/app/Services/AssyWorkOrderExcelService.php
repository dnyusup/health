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
     * Import work orders from Excel export format.
     * Row 1 = header (A=Tanggal Bongkar … AD=Installed At).
     * Data starts from row 2.
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

        if ($highestRow < 2) {
            $stats['errors'][] = 'File kosong atau tidak ada data (mulai dari baris 2).';
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

        // Export column layout (row 1 = headers):
        // A=Tanggal Bongkar, B=Order Number, C=Order Type, D=Mach Number,
        // E=Mach Type(auto), F=Pos, G=Part ID, H=Part Name, I=Category, J=Part Detail,
        // K=Kerusakan, L=PIC Bongkar, M=Remark, N=Status(auto), O=Created By(auto), P=Created On(auto),
        // Q=Tgl Assembling, R=Action Assembling, S=PIC Assembling, T=Remark Assembling,
        // U=Repaired By(auto), V=Repaired At(auto),
        // W=Tgl Pasang, X=Mesin Install, Y=Type Install(auto), Z=Pos Install,
        // AA=PIC Pasang, AB=Remark Pemasangan, AC=Installed By(auto), AD=Installed At(auto)
        for ($rowNum = 2; $rowNum <= $highestRow; $rowNum++) {
            $get = fn(string $col) => trim((string) ($worksheet->getCell($col . $rowNum)->getValue() ?? ''));

            $tanggalBongkar = $parseDate($get('A'));
            $orderType      = strtoupper($get('C'));
            $machNumber     = strtoupper($get('D'));
            $picBongkarName = $get('L');

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
            $pos         = $get('F') ?: null;
            $partId      = $get('G') ? strtoupper($get('G')) : null;
            $partName    = $get('H') ?: null;
            $category    = $get('I') ?: null;
            $partDetail  = $get('J') ?: null;
            $kerusakan   = $get('K') ?: null;
            $remark      = $get('M') ?: null;

            // Repair fields (Q-T, U=auto)
            $tanggalAssembling  = $parseDate($get('Q'));
            $actionAssembling   = $get('R') ?: null;
            $picAsmRaw          = $get('S');
            $remarkAssembling   = $get('T') ?: null;
            $picAssemblingIds   = $picAsmRaw !== '' ? $resolvePicIds($picAsmRaw) : [];

            // Install fields (W-AB, Y/AC/AD=auto)
            $tanggalPasang      = $parseDate($get('W'));
            $installMachNumber  = strtoupper($get('X')) ?: null;
            $installPos         = $get('Z') ?: null;
            $picPasangRaw       = $get('AA');
            $remarkPemasangan   = $get('AB') ?: null;
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

