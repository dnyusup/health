<?php

namespace App\Http\Controllers;

use App\Models\AssyMachine;
use App\Models\AssyPart;
use App\Models\AssyWorkOrder;
use App\Models\User;
use App\Services\AssyWorkOrderExcelService;
use Illuminate\Http\Request;

class AssyWorkOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = AssyWorkOrder::with(['pic']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('mach_number', 'like', "%{$search}%")
                  ->orWhere('part_id', 'like', "%{$search}%")
                  ->orWhere('part_name', 'like', "%{$search}%")
                  ->orWhere('kerusakan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('order_type')) {
            $query->where('order_type', $request->order_type);
        }

        $workOrders = $query->orderByDesc('tanggal_bongkar')->orderByDesc('id')
                            ->paginate(25)->withQueryString();

        return view('work-orders.index', compact('workOrders'));
    }

    public function create()
    {
        $machines = AssyMachine::orderBy('mach_number')->get(['id', 'mach_number', 'mach_type']);
        $users    = User::orderBy('name')->get(['id', 'name']);
        return view('work-orders.create', compact('machines', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_bongkar' => 'required|date',
            'order_type'      => 'required|in:ZSPM,ZSBM',
            'machine_id'      => 'required|exists:assy_machines,id',
            'pic_bongkar'     => 'required|exists:users,id',
        ]);

        $machine = AssyMachine::findOrFail($request->machine_id);

        AssyWorkOrder::create([
            'tanggal_bongkar' => $request->tanggal_bongkar,
            'order_number'    => $request->order_number,
            'order_type'      => $request->order_type,
            'machine_id'      => $machine->id,
            'mach_number'     => $machine->mach_number,
            'mach_type'       => $machine->mach_type,
            'pos'             => $request->pos,
            'part_id'         => $request->part_id ? strtoupper($request->part_id) : null,
            'part_name'       => $request->part_name,
            'category'        => $request->category,
            'part_detail'     => $request->part_detail,
            'kerusakan'       => $request->kerusakan,
            'pic_bongkar'     => $request->pic_bongkar,
            'remark'          => $request->remark,
            'status'          => 'Open',
            'created_by'      => auth()->id(),
        ]);

        return redirect()->route('work-orders.index')->with('success', 'Work Order berhasil dibuat.');
    }

    public function show(AssyWorkOrder $work_order)
    {
        $work_order->load(['machine', 'pic', 'creator']);
        return view('work-orders.show', compact('work_order'));
    }

    public function edit(AssyWorkOrder $work_order)
    {
        $machines = AssyMachine::orderBy('mach_number')->get(['id', 'mach_number', 'mach_type']);
        $users    = User::orderBy('name')->get(['id', 'name']);
        return view('work-orders.edit', compact('work_order', 'machines', 'users'));
    }

    public function update(Request $request, AssyWorkOrder $work_order)
    {
        $request->validate([
            'tanggal_bongkar' => 'required|date',
            'order_type'      => 'required|in:ZSPM,ZSBM',
            'machine_id'      => 'required|exists:assy_machines,id',
            'pic_bongkar'     => 'required|exists:users,id',
            'status'          => 'required|in:Open,Closed',
        ]);

        $machine = AssyMachine::findOrFail($request->machine_id);

        $work_order->update([
            'tanggal_bongkar' => $request->tanggal_bongkar,
            'order_number'    => $request->order_number,
            'order_type'      => $request->order_type,
            'machine_id'      => $machine->id,
            'mach_number'     => $machine->mach_number,
            'mach_type'       => $machine->mach_type,
            'pos'             => $request->pos,
            'part_id'         => $request->part_id ? strtoupper($request->part_id) : null,
            'part_name'       => $request->part_name,
            'category'        => $request->category,
            'part_detail'     => $request->part_detail,
            'kerusakan'       => $request->kerusakan,
            'pic_bongkar'     => $request->pic_bongkar,
            'remark'          => $request->remark,
            'status'          => $request->status,
        ]);

        return redirect()->route('work-orders.index')->with('success', 'Work Order berhasil diperbarui.');
    }

    public function destroy(AssyWorkOrder $work_order)
    {
        $work_order->delete();
        return redirect()->route('work-orders.index')->with('success', 'Work Order berhasil dihapus.');
    }

    public function exportExcel(AssyWorkOrderExcelService $service)
    {
        ini_set('memory_limit', '256M');
        set_time_limit(300);
        ob_end_clean();
        $service->export();
        exit;
    }

    /**
     * AJAX: search parts by part_id or part_name (for autocomplete).
     */
    public function partLookup(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (strlen($q) < 1) {
            return response()->json([]);
        }

        $parts = AssyPart::where('part_id', 'like', "{$q}%")
                    ->orWhere('part_id', 'like', "%{$q}%")
                    ->orWhere('part_name', 'like', "%{$q}%")
                    ->orderByRaw("CASE WHEN part_id LIKE ? THEN 0 ELSE 1 END", ["{$q}%"])
                    ->limit(15)
                    ->get(['part_id', 'part_name', 'category', 'part_detail']);

        return response()->json($parts);
    }
}
