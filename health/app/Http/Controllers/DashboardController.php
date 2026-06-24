<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\HealthCheck;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth();

        if ($user->isAdmin() || $user->role === 'supervisor') {
            $totalEmployees = User::where('role', 'user')->count();
            $totalChecksToday = HealthCheck::whereDate('checked_at', $today)->count();
            $totalChecksMonth = HealthCheck::where('checked_at', '>=', $monthStart)->count();

            // Abnormal readings today: BP high, O2 low, or fever
            $abnormalToday = HealthCheck::whereDate('checked_at', $today)
                ->where(function ($q) {
                    $q->where(function ($q2) {
                            $q2->whereNotNull('systolic')->where('systolic', '>=', 140);
                        })
                        ->orWhere(function ($q2) {
                            $q2->whereNotNull('oxygen_saturation')->where('oxygen_saturation', '<', 95);
                        })
                        ->orWhere(function ($q2) {
                            $q2->whereNotNull('body_temperature')->where('body_temperature', '>', 37.5);
                        });
                })->count();

            $recentChecks = HealthCheck::with('user')
                ->latest('checked_at')
                ->limit(10)
                ->get();

            // Employees not yet checked today
            $checkedUserIds = HealthCheck::whereDate('checked_at', $today)->pluck('user_id');
            $notCheckedToday = User::where('role', 'user')
                ->whereNotIn('id', $checkedUserIds)
                ->count();

            return view('dashboard', compact(
                'totalEmployees',
                'totalChecksToday',
                'totalChecksMonth',
                'abnormalToday',
                'recentChecks',
                'notCheckedToday'
            ));
        }

        // Regular user: show own stats
        $myLastCheck = HealthCheck::where('user_id', $user->id)->latest('checked_at')->first();
        $myChecksMonth = HealthCheck::where('user_id', $user->id)
            ->where('checked_at', '>=', $monthStart)->count();
        $myChecksTotal = HealthCheck::where('user_id', $user->id)->count();
        $myRecentChecks = HealthCheck::where('user_id', $user->id)
            ->latest('checked_at')->limit(5)->get();

        return view('dashboard', compact(
            'myLastCheck',
            'myChecksMonth',
            'myChecksTotal',
            'myRecentChecks'
        ));
    }
}
