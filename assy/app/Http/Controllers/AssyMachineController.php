<?php

namespace App\Http\Controllers;

use App\Models\AssyMachine;
use App\Services\AssyMachineExcelService;
use Illuminate\Http\Request;

class AssyMachineController extends Controller
{
    public function index(Request $request)
    {
        $query = AssyMachine::with('creator');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('mach_number', 'like', "%{$search}%")
                  ->orWhere('mach_type', 'like', "%{$search}%")
                  ->orWhere('mach_area', 'like', "%{$search}%");
            });
        }

        if ($request->filled('mach_area')) {
            $query->where('mach_area', $request->mach_area);
        }

        $machines  = $query->latest()->paginate(20)->withQueryString();
        $machAreas = AssyMachine::select('mach_area')->distinct()->orderBy('mach_area')->pluck('mach_area');

        return view('machines.index', compact('machines', 'machAreas'));
    }

    public function create()
    {
        abort_if(!auth()->user()->hasAssypartRole(), 403, 'Anda tidak memiliki akses untuk menambahkan data.');
        $machTypes = AssyMachine::select('mach_type')->distinct()->orderBy('mach_type')->pluck('mach_type');
        $machAreas = AssyMachine::select('mach_area')->distinct()->orderBy('mach_area')->pluck('mach_area');
        return view('machines.create', compact('machTypes', 'machAreas'));
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->hasAssypartRole(), 403, 'Anda tidak memiliki akses untuk menambahkan data.');
        $request->validate([
            'mach_number' => 'required|string|max:50|unique:assy_machines,mach_number',
            'mach_type'   => 'required|string|max:100',
            'mach_area'   => 'required|string|max:100',
        ]);

        AssyMachine::create([
            'mach_number' => strtoupper($request->mach_number),
            'mach_type'   => $request->mach_type,
            'mach_area'   => $request->mach_area,
            'created_by'  => auth()->id(),
        ]);

        return redirect()->route('machines.index')->with('success', 'Machine berhasil ditambahkan.');
    }

    public function show(AssyMachine $machine)
    {
        $machine->load('creator');
        return view('machines.show', compact('machine'));
    }

    public function edit(AssyMachine $machine)
    {
        $machTypes = AssyMachine::select('mach_type')->distinct()->orderBy('mach_type')->pluck('mach_type');
        $machAreas = AssyMachine::select('mach_area')->distinct()->orderBy('mach_area')->pluck('mach_area');
        return view('machines.edit', compact('machine', 'machTypes', 'machAreas'));
    }

    public function update(Request $request, AssyMachine $machine)
    {
        $request->validate([
            'mach_number' => 'required|string|max:50|unique:assy_machines,mach_number,' . $machine->id,
            'mach_type'   => 'required|string|max:100',
            'mach_area'   => 'required|string|max:100',
        ]);

        $machine->update([
            'mach_number' => strtoupper($request->mach_number),
            'mach_type'   => $request->mach_type,
            'mach_area'   => $request->mach_area,
        ]);

        return redirect()->route('machines.index')->with('success', 'Machine berhasil diperbarui.');
    }

    public function destroy(AssyMachine $machine)
    {
        $machine->delete();
        return redirect()->route('machines.index')->with('success', 'Machine berhasil dihapus.');
    }

    public function exportExcel(AssyMachineExcelService $service)
    {
        ini_set('memory_limit', '256M');
        set_time_limit(300);
        ob_end_clean();
        $service->export();
        exit;
    }

    public function importExcel(Request $request, AssyMachineExcelService $service)
    {
        abort_if(!auth()->user()->hasAssypartRole(), 403, 'Anda tidak memiliki akses untuk mengimpor data.');
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:51200',
        ]);

        ini_set('memory_limit', '512M');
        set_time_limit(600);

        $stats = $service->import($request->file('file'), auth()->id());

        $message = "Import selesai: {$stats['inserted']} ditambahkan, {$stats['updated']} diperbarui, {$stats['skipped']} dilewati.";

        if (!empty($stats['errors'])) {
            $errorSummary = implode(' | ', array_slice($stats['errors'], 0, 5));
            if (count($stats['errors']) > 5) {
                $errorSummary .= ' ... dan ' . (count($stats['errors']) - 5) . ' error lainnya.';
            }
            return redirect()->route('machines.index')
                ->with('success', $message)
                ->with('import_errors', $errorSummary);
        }

        return redirect()->route('machines.index')->with('success', $message);
    }
}
