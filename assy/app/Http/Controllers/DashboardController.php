<?php

namespace App\Http\Controllers;

use App\Models\AssyMachine;
use App\Models\AssyPart;
use App\Models\AssyWorkOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Summary counts
        $totalUsers    = User::count();
        $totalParts    = AssyPart::count();
        $totalMachines = AssyMachine::count();
        $totalWO       = AssyWorkOrder::count();

        // Work Order by status
        $woByStatus = AssyWorkOrder::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $woOpen       = $woByStatus->get('Open', 0);
        $woOnProgress = $woByStatus->get('On Progress', 0);
        $woClosed     = $woByStatus->get('Closed', 0);
        $woInstalled  = $woByStatus->get('Installed', 0);
        $woScrap      = $woByStatus->get('Scrap', 0);

        // WO per month last 6 months
        $woMonthly = AssyWorkOrder::select(
                DB::raw('DATE_FORMAT(tanggal_bongkar, "%Y-%m") as month'),
                DB::raw('count(*) as total')
            )
            ->whereNotNull('tanggal_bongkar')
            ->where('tanggal_bongkar', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // Fill missing months
        $monthLabels = [];
        $monthData   = [];
        for ($i = 5; $i >= 0; $i--) {
            $key           = now()->subMonths($i)->format('Y-m');
            $monthLabels[] = now()->subMonths($i)->format('M Y');
            $monthData[]   = $woMonthly->get($key, 0);
        }

        // WO by category (top 5)
        $woByCategory = AssyWorkOrder::select('category', DB::raw('count(*) as total'))
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(7)
            ->get();

        // WO by order_type
        $woByType = AssyWorkOrder::select('order_type', DB::raw('count(*) as total'))
            ->whereNotNull('order_type')
            ->groupBy('order_type')
            ->pluck('total', 'order_type');

        // Recent 5 work orders
        $recentWO = AssyWorkOrder::with('machine')
            ->orderByDesc('tanggal_bongkar')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        // WO completed this month
        $woThisMonth = AssyWorkOrder::whereMonth('tanggal_bongkar', now()->month)
            ->whereYear('tanggal_bongkar', now()->year)
            ->count();

        return view('dashboard', compact(
            'totalUsers', 'totalParts', 'totalMachines', 'totalWO',
            'woOpen', 'woOnProgress', 'woClosed', 'woInstalled', 'woScrap',
            'monthLabels', 'monthData',
            'woByCategory', 'woByType',
            'recentWO', 'woThisMonth'
        ));
    }
}
