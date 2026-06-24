<?php

namespace App\Http\Controllers;

use App\Models\HealthCheck;
use App\Models\User;
use Illuminate\Http\Request;

class HealthCheckController extends Controller
{
    public function index(Request $request)
    {
        $query = HealthCheck::with('user');

        // Admin sees all, user sees own
        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        } else {
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('checked_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('checked_at', '<=', $request->date_to);
        }

        $records = $query->orderBy('checked_at', 'desc')->paginate(15)->withQueryString();
        $users = auth()->user()->isAdmin() ? User::orderBy('name')->get() : collect();

        return view('health-checks.index', compact('records', 'users'));
    }

    public function create()
    {
        $users = auth()->user()->isAdmin() ? User::orderBy('name')->get() : collect();
        return view('health-checks.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'           => auth()->user()->isAdmin() ? 'required|exists:users,id' : '',
            'weight'            => 'nullable|numeric|min:1|max:300',
            'systolic'          => 'nullable|integer|min:50|max:300',
            'diastolic'         => 'nullable|integer|min:30|max:200',
            'oxygen_saturation' => 'nullable|numeric|min:50|max:100',
            'body_temperature'  => 'nullable|numeric|min:30|max:45',
            'checked_at'        => 'required|date',
            'notes'             => 'nullable|string|max:500',
        ]);

        if (!auth()->user()->isAdmin()) {
            $validated['user_id'] = auth()->id();
        }

        HealthCheck::create($validated);

        return redirect()->route('health-checks.index')
            ->with('success', 'Data kesehatan berhasil disimpan.');
    }

    public function show(HealthCheck $healthCheck)
    {
        if (!auth()->user()->isAdmin() && $healthCheck->user_id !== auth()->id()) {
            abort(403);
        }
        return view('health-checks.show', compact('healthCheck'));
    }

    public function edit(HealthCheck $healthCheck)
    {
        if (!auth()->user()->isAdmin() && $healthCheck->user_id !== auth()->id()) {
            abort(403);
        }
        $users = auth()->user()->isAdmin() ? User::orderBy('name')->get() : collect();
        return view('health-checks.edit', compact('healthCheck', 'users'));
    }

    public function update(Request $request, HealthCheck $healthCheck)
    {
        if (!auth()->user()->isAdmin() && $healthCheck->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'user_id'           => auth()->user()->isAdmin() ? 'required|exists:users,id' : '',
            'weight'            => 'nullable|numeric|min:1|max:300',
            'systolic'          => 'nullable|integer|min:50|max:300',
            'diastolic'         => 'nullable|integer|min:30|max:200',
            'oxygen_saturation' => 'nullable|numeric|min:50|max:100',
            'body_temperature'  => 'nullable|numeric|min:30|max:45',
            'checked_at'        => 'required|date',
            'notes'             => 'nullable|string|max:500',
        ]);

        if (!auth()->user()->isAdmin()) {
            unset($validated['user_id']);
        }

        $healthCheck->update($validated);

        return redirect()->route('health-checks.index')
            ->with('success', 'Data kesehatan berhasil diperbarui.');
    }

    public function destroy(HealthCheck $healthCheck)
    {
        if (!auth()->user()->isAdmin() && $healthCheck->user_id !== auth()->id()) {
            abort(403);
        }
        $healthCheck->delete();
        return redirect()->route('health-checks.index')
            ->with('success', 'Data kesehatan berhasil dihapus.');
    }
}
