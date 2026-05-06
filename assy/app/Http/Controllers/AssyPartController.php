<?php

namespace App\Http\Controllers;

use App\Models\AssyPart;
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

        $parts = $query->latest()->paginate(20)->withQueryString();
        $categories = AssyPart::select('category')->distinct()->orderBy('category')->pluck('category');

        return view('parts.index', compact('parts', 'categories'));
    }

    public function create()
    {
        $categories = AssyPart::select('category')->distinct()->orderBy('category')->pluck('category');
        return view('parts.create', compact('categories'));
    }

    public function store(Request $request)
    {
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
}
