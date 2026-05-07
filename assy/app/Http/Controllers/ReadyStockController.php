<?php

namespace App\Http\Controllers;

use App\Models\AssyWorkOrder;
use Illuminate\Http\Request;

class ReadyStockController extends Controller
{
    public function index(Request $request)
    {
        $query = AssyWorkOrder::with(['pic', 'repairedBy'])
            ->where('status', 'Closed');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('part_id',   'like', "%{$search}%")
                  ->orWhere('part_name', 'like', "%{$search}%")
                  ->orWhere('order_number', 'like', "%{$search}%")
                  ->orWhere('mach_number',  'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('order_type')) {
            $query->where('order_type', $request->order_type);
        }

        $readyStocks = $query->orderByDesc('tanggal_assembling')
                             ->orderByDesc('id')
                             ->paginate(25)
                             ->withQueryString();

        return view('ready-stock.index', compact('readyStocks'));
    }
}
