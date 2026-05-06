<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\Category;
use App\Models\StockTransaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSpareparts = Sparepart::count();
        $totalCategories = Category::count();
        $lowStockCount = Sparepart::whereColumn('stock', '<=', 'min_stock')->count();
        $recentTransactions = StockTransaction::with(['sparepart', 'user'])
            ->latest()
            ->take(10)
            ->get();
        $lowStockItems = Sparepart::whereColumn('stock', '<=', 'min_stock')
            ->orderBy('stock')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalSpareparts',
            'totalCategories', 
            'lowStockCount',
            'recentTransactions',
            'lowStockItems'
        ));
    }
}
