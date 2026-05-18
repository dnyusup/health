<?php

namespace App\Http\Controllers;

use App\Models\AssyPart;
use App\Services\AssyPartExcelService;
use Illuminate\Http\Request;

class AssyPartController extends Controller
{
    public function index(Request $request)
    {
        $query = AssyPart::with('creator');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('part_id', 'like', "%{$search}%")
                  ->orWhere('part_name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('part_detail', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $sortable = ['created_at', 'part_id', 'category', 'part_name', 'part_detail'];
        $sortBy  = in_array($request->sort, $sortable) ? $request->sort : 'part_id';
        $sortDir = $request->dir === 'asc' ? 'asc' : 'desc';

        $parts = $query->orderBy($sortBy, $sortDir)->paginate(20)->withQueryString();
        $categories = AssyPart::select('category')->distinct()->orderBy('category')->pluck('category');

        return view('parts.index', compact('parts', 'categories', 'sortBy', 'sortDir'));
    }

    public function create()
    {
        abort_if(!auth()->user()->hasAssypartRole(), 403, 'Anda tidak memiliki akses untuk menambahkan data.');
        $categories = AssyPart::select('category')->distinct()->orderBy('category')->pluck('category');
        return view('parts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->hasAssypartRole(), 403, 'Anda tidak memiliki akses untuk menambahkan data.');
        $request->validate([
            'part_id'     => 'required|string|max:50|unique:assy_parts,part_id',
            'category'    => 'required|string|max:100',
            'part_name'   => 'required|string|max:255',
            'part_detail' => 'nullable|string|max:255',
        ]);

        AssyPart::create([
            'part_id'     => strtoupper($request->part_id),
            'category'    => $request->category,
            'part_name'   => $request->part_name,
            'part_detail' => $request->part_detail,
            'created_by'  => auth()->id(),
        ]);

        return redirect()->route('parts.index')->with('success', 'Part berhasil ditambahkan.');
    }

    public function show(AssyPart $part)
    {
        $part->load('creator');
        return view('parts.show', compact('part'));
    }

    public function edit(AssyPart $part)
    {
        $categories = AssyPart::select('category')->distinct()->orderBy('category')->pluck('category');
        return view('parts.edit', compact('part', 'categories'));
    }

    public function update(Request $request, AssyPart $part)
    {
        $request->validate([
            'part_id'     => 'required|string|max:50|unique:assy_parts,part_id,' . $part->id,
            'category'    => 'required|string|max:100',
            'part_name'   => 'required|string|max:255',
            'part_detail' => 'nullable|string|max:255',
        ]);

        $part->update([
            'part_id'     => strtoupper($request->part_id),
            'category'    => $request->category,
            'part_name'   => $request->part_name,
            'part_detail' => $request->part_detail,
        ]);

        return redirect()->route('parts.index')->with('success', 'Part berhasil diperbarui.');
    }

    public function destroy(AssyPart $part)
    {
        $part->delete();
        return redirect()->route('parts.index')->with('success', 'Part berhasil dihapus.');
    }

    public function exportExcel(AssyPartExcelService $service)
    {
        ini_set('memory_limit', '256M');
        set_time_limit(300);
        ob_end_clean();
        $service->export();
        exit;
    }

    public function importExcel(Request $request, AssyPartExcelService $service)
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
            $errorList = implode(' | ', array_slice($stats['errors'], 0, 5));
            $more = count($stats['errors']) > 5 ? ' ... dan ' . (count($stats['errors']) - 5) . ' error lainnya.' : '';
            return redirect()->route('parts.index')
                ->with('success', $message)
                ->with('import_errors', $errorList . $more);
        }

        return redirect()->route('parts.index')->with('success', $message);
    }
}
