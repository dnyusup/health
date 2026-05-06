<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\Category;
use App\Services\SparepartExcelService;
use Illuminate\Http\Request;

class SparepartController extends Controller
{
    public function index(Request $request)
    {
        $query = Sparepart::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('material_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('bin_location', 'like', "%{$search}%")
                  ->orWhere('old_material_no', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->low_stock) {
            $query->whereColumn('stock', '<=', 'min_stock');
        }

        $spareparts = $query->orderBy('material_code')->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('spareparts.index', compact('spareparts', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('spareparts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'material_code' => 'required|string|max:255|unique:spareparts',
            'bin_location' => 'nullable|string|max:255',
            'old_material_no' => 'nullable|string|max:255',
            'description' => 'required|string|max:255',
            'stock' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'min_stock' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        Sparepart::create($validated);

        return redirect()->route('spareparts.index')
            ->with('success', 'Sparepart added successfully.');
    }

    public function show(Sparepart $sparepart)
    {
        $sparepart->load(['category', 'transactions.user']);
        return view('spareparts.show', compact('sparepart'));
    }

    public function edit(Sparepart $sparepart)
    {
        $categories = Category::orderBy('name')->get();
        return view('spareparts.edit', compact('sparepart', 'categories'));
    }

    public function update(Request $request, Sparepart $sparepart)
    {
        $validated = $request->validate([
            'material_code' => 'required|string|max:255|unique:spareparts,material_code,' . $sparepart->id,
            'bin_location' => 'nullable|string|max:255',
            'old_material_no' => 'nullable|string|max:255',
            'description' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'min_stock' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $sparepart->update($validated);

        return redirect()->route('spareparts.index')
            ->with('success', 'Sparepart updated successfully.');
    }

    public function destroy(Sparepart $sparepart)
    {
        $sparepart->delete();

        return redirect()->route('spareparts.index')
            ->with('success', 'Sparepart deleted successfully.');
    }

    /**
     * Export spareparts to Excel template
     */
    public function exportExcel(SparepartExcelService $excelService)
    {
        $spreadsheet = $excelService->exportTemplate();
        $filename = 'spareparts_' . date('Y-m-d_His') . '.xlsx';
        $excelService->download($spreadsheet, $filename);
    }

    /**
     * Import spareparts from Excel file
     */
    public function importExcel(Request $request, SparepartExcelService $excelService)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:51200', // 50MB max
        ]);

        $file = $request->file('excel_file');
        $result = $excelService->importFromFile($file->getRealPath());

        $message = "Import completed: {$result['imported']} new records, {$result['updated']} updated.";
        
        if (!empty($result['errors'])) {
            return redirect()->route('spareparts.index')
                ->with('warning', $message)
                ->with('import_errors', $result['errors']);
        }

        return redirect()->route('spareparts.index')
            ->with('success', $message);
    }
}
