<?php

namespace App\Http\Controllers;

use App\Models\HealthCheck;
use App\Models\User;
use Illuminate\Http\Request;

class HealthCheckController extends Controller
{
    /** IDs that the current user is allowed to see/manage. */
    private function visibleUserIds(): \Illuminate\Support\Collection
    {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return User::pluck('id');
        }
        if ($user->isSupervisor()) {
            return $user->subordinates()->pluck('id')->push($user->id);
        }
        return collect([$user->id]);
    }

    private function canManage(HealthCheck $healthCheck): bool
    {
        return $this->visibleUserIds()->contains($healthCheck->user_id);
    }

    public function index(Request $request)
    {
        $user      = auth()->user();
        $canFilter = $user->isAdmin() || $user->isSupervisor();
        $visibleIds = $this->visibleUserIds();

        $query = HealthCheck::with('user')->whereIn('user_id', $visibleIds);

        if ($canFilter && $request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('checked_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('checked_at', '<=', $request->date_to);
        }

        $records     = $query->orderBy('checked_at', 'desc')->paginate(15)->withQueryString();
        $filterUsers = $canFilter ? User::whereIn('id', $visibleIds)->orderBy('name')->get() : collect();

        return view('health-checks.index', compact('records', 'filterUsers', 'canFilter'));
    }

    public function create()
    {
        $user        = auth()->user();
        $canFilter   = $user->isAdmin() || $user->isSupervisor();
        $filterUsers = $canFilter ? User::whereIn('id', $this->visibleUserIds())->orderBy('name')->get() : collect();
        return view('health-checks.create', compact('filterUsers', 'canFilter'));
    }

    public function store(Request $request)
    {
        $user      = auth()->user();
        $canFilter = $user->isAdmin() || $user->isSupervisor();

        $validated = $request->validate([
            'user_id'           => $canFilter ? 'required|exists:users,id' : '',
            'weight'            => 'nullable|numeric|min:1|max:300',
            'systolic'          => 'nullable|integer|min:50|max:300',
            'diastolic'         => 'nullable|integer|min:30|max:200',
            'oxygen_saturation' => 'nullable|numeric|min:50|max:100',
            'body_temperature'  => 'nullable|numeric|min:30|max:45',
            'checked_at'        => 'required|date',
            'notes'             => 'nullable|string|max:500',
        ]);

        if (!$canFilter) {
            $validated['user_id'] = $user->id;
        }

        // Supervisor can only add data for visible users
        if ($canFilter && !$user->isAdmin()) {
            abort_unless($this->visibleUserIds()->contains($validated['user_id']), 403);
        }

        HealthCheck::create($validated);

        return redirect()->route('health-checks.index')
            ->with('success', 'Data kesehatan berhasil disimpan.');
    }

    public function show(HealthCheck $healthCheck)
    {
        abort_unless($this->canManage($healthCheck), 403);
        return view('health-checks.show', compact('healthCheck'));
    }

    public function edit(HealthCheck $healthCheck)
    {
        abort_unless($this->canManage($healthCheck), 403);
        $user        = auth()->user();
        $canFilter   = $user->isAdmin() || $user->isSupervisor();
        $filterUsers = $canFilter ? User::whereIn('id', $this->visibleUserIds())->orderBy('name')->get() : collect();
        return view('health-checks.edit', compact('healthCheck', 'filterUsers', 'canFilter'));
    }

    public function update(Request $request, HealthCheck $healthCheck)
    {
        abort_unless($this->canManage($healthCheck), 403);

        $user      = auth()->user();
        $canFilter = $user->isAdmin() || $user->isSupervisor();

        $validated = $request->validate([
            'user_id'           => $canFilter ? 'required|exists:users,id' : '',
            'weight'            => 'nullable|numeric|min:1|max:300',
            'systolic'          => 'nullable|integer|min:50|max:300',
            'diastolic'         => 'nullable|integer|min:30|max:200',
            'oxygen_saturation' => 'nullable|numeric|min:50|max:100',
            'body_temperature'  => 'nullable|numeric|min:30|max:45',
            'checked_at'        => 'required|date',
            'notes'             => 'nullable|string|max:500',
        ]);

        if (!$canFilter) {
            unset($validated['user_id']);
        }

        $healthCheck->update($validated);

        return redirect()->route('health-checks.index')
            ->with('success', 'Data kesehatan berhasil diperbarui.');
    }

    public function destroy(HealthCheck $healthCheck)
    {
        abort_unless($this->canManage($healthCheck), 403);
        $healthCheck->delete();
        return redirect()->route('health-checks.index')
            ->with('success', 'Data kesehatan berhasil dihapus.');
    }
}

