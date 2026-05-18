<?php

namespace App\Http\Controllers;

use App\Models\AssyVendor;
use Illuminate\Http\Request;

class AssyVendorController extends Controller
{
    public function index(Request $request)
    {
        $query = AssyVendor::with('creator');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('vendor_id',   'like', "%{$search}%")
                  ->orWhere('vendor_name', 'like', "%{$search}%")
                  ->orWhere('pic_vendor',  'like', "%{$search}%")
                  ->orWhere('email',       'like', "%{$search}%")
                  ->orWhere('telp',        'like', "%{$search}%");
            });
        }

        $vendors = $query->latest()->paginate(20)->withQueryString();

        return view('vendors.index', compact('vendors'));
    }

    public function create()
    {
        return view('vendors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor_id'   => 'required|string|max:50|unique:assy_vendors,vendor_id',
            'vendor_name' => 'required|string|max:150',
            'pic_vendor'  => 'nullable|string|max:150',
            'email'       => 'nullable|email|max:150',
            'telp'        => 'nullable|string|max:50',
        ]);

        AssyVendor::create([
            'vendor_id'   => strtoupper(trim($request->vendor_id)),
            'vendor_name' => $request->vendor_name,
            'pic_vendor'  => $request->pic_vendor,
            'email'       => $request->email,
            'telp'        => $request->telp,
            'created_by'  => auth()->id(),
        ]);

        return redirect()->route('vendors.index')->with('success', 'Vendor berhasil ditambahkan.');
    }

    public function show(AssyVendor $vendor)
    {
        $vendor->load('creator');
        return view('vendors.show', compact('vendor'));
    }

    public function edit(AssyVendor $vendor)
    {
        return view('vendors.edit', compact('vendor'));
    }

    public function update(Request $request, AssyVendor $vendor)
    {
        $request->validate([
            'vendor_id'   => 'required|string|max:50|unique:assy_vendors,vendor_id,' . $vendor->id,
            'vendor_name' => 'required|string|max:150',
            'pic_vendor'  => 'nullable|string|max:150',
            'email'       => 'nullable|email|max:150',
            'telp'        => 'nullable|string|max:50',
        ]);

        $vendor->update([
            'vendor_id'   => strtoupper(trim($request->vendor_id)),
            'vendor_name' => $request->vendor_name,
            'pic_vendor'  => $request->pic_vendor,
            'email'       => $request->email,
            'telp'        => $request->telp,
        ]);

        return redirect()->route('vendors.index')->with('success', 'Vendor berhasil diperbarui.');
    }

    public function destroy(AssyVendor $vendor)
    {
        $vendor->delete();
        return redirect()->route('vendors.index')->with('success', 'Vendor berhasil dihapus.');
    }
}
