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

        if ($user->isAdmin() || $user->isSupervisor()) {
            // Scope: admin sees all, supervisor sees only their subordinates
            $subordinateIds = $user->isAdmin()
                ? null
                : $user->subordinates()->pluck('id');

            $userQuery = $user->isAdmin()
                ? User::where('role', 'user')
                : User::whereIn('id', $subordinateIds);

            $checkQuery = fn() => $user->isAdmin()
                ? HealthCheck::query()
                : HealthCheck::whereIn('user_id', $subordinateIds);

            $totalEmployees    = $userQuery->count();
            $totalChecksToday  = $checkQuery()->whereDate('checked_at', $today)->count();
            $totalChecksMonth  = $checkQuery()->where('checked_at', '>=', $monthStart)->count();

            $abnormalToday = $checkQuery()->whereDate('checked_at', $today)
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

            $recentChecks = $checkQuery()->with('user')
                ->latest('checked_at')
                ->limit(10)
                ->get();

            $checkedUserIds   = $checkQuery()->whereDate('checked_at', $today)->pluck('user_id');
            $notCheckedToday  = (clone $userQuery)->whereNotIn('id', $checkedUserIds)->count();

            // Supervisor's own health data
            $myLastCheck   = HealthCheck::where('user_id', $user->id)->latest('checked_at')->first();
            $myChecksMonth = HealthCheck::where('user_id', $user->id)->where('checked_at', '>=', $monthStart)->count();

            return view('dashboard', compact(
                'totalEmployees',
                'totalChecksToday',
                'totalChecksMonth',
                'abnormalToday',
                'recentChecks',
                'notCheckedToday',
                'myLastCheck',
                'myChecksMonth'
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
