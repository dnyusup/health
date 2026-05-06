<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\StockTransaction;
use App\Models\User;
use App\Services\TransactionExcelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = StockTransaction::with(['sparepart', 'user', 'changedByUser']);

        // Default filter values
        $defaultType = $request->has('type') ? $request->type : 'out';
        $defaultStatuses = $request->has('status') ? (array)$request->status : ['new', 'changed'];
        $defaultFrom = $request->has('date_from') ? $request->date_from : null;
        $defaultTo = $request->has('date_to') ? $request->date_to : null;

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                // Search by material code or description
                $q->whereHas('sparepart', function ($sq) use ($search) {
                    $sq->where('material_code', 'like', "%{$search}%")
                       ->orWhere('description', 'like', "%{$search}%");
                })
                // Search by order number (reference_no)
                ->orWhere('reference_no', 'like', "%{$search}%")
                // Search by user name
                ->orWhereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%");
                });
            });
        }


        if ($defaultType) {
            $query->where('type', $defaultType);
        }

        if ($defaultStatuses) {
            $query->whereIn('status', $defaultStatuses);
        }

        if ($defaultFrom) {
            $query->whereDate('created_at', '>=', $defaultFrom);
        }

        if ($defaultTo) {
            $query->whereDate('created_at', '<=', $defaultTo);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $transactions = $query->latest()->paginate(20)->withQueryString();
        $users = User::orderBy('name')->get();

        return view('transactions.index', compact('transactions', 'users'))
            ->with([
                'defaultType' => $defaultType,
                'defaultStatuses' => $defaultStatuses,
                'defaultFrom' => $defaultFrom,
                'defaultTo' => $defaultTo,
            ]);
    }

    public function create(Request $request)
    {
        $sparepart = null;
        if ($request->filled('sparepart_id')) {
            $sparepart = Sparepart::findOrFail($request->sparepart_id);
        }
        $spareparts = Sparepart::orderBy('material_code')->get();
        $currentUserId = Auth::id();
        $allUsers = \App\Models\User::orderBy('name')->get();
        $currentUser = $allUsers->firstWhere('id', $currentUserId);
        $users = collect();
        if ($currentUser) {
            $users->push($currentUser);
        }
        $users = $users->merge($allUsers->filter(fn($u) => $u->id !== $currentUserId));
        return view('transactions.create', compact('spareparts', 'sparepart', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sparepart_id' => 'required|exists:spareparts,id',
            'type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|numeric|min:0.01',
            'reference_no' => [
                'required',
                'numeric',
                'digits_between:8,10',
            ],
            'user_id' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $sparepart = Sparepart::findOrFail($validated['sparepart_id']);
        $stockBefore = $sparepart->stock;

        // Calculate new stock
        if ($validated['type'] === 'in') {
            $stockAfter = $stockBefore + $validated['quantity'];
        } elseif ($validated['type'] === 'out') {
            // Allow stock to go negative
            $stockAfter = $stockBefore - $validated['quantity'];
        } else {
            $stockAfter = $validated['quantity'];
        }

        DB::transaction(function () use ($validated, $sparepart, $stockBefore, $stockAfter) {
            StockTransaction::create([
                'sparepart_id' => $validated['sparepart_id'],
                'user_id' => $validated['user_id'],
                'type' => $validated['type'],
                'quantity' => $validated['quantity'],
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'reference_no' => $validated['reference_no'],
                'notes' => $validated['notes'],
                'changed_by' => Auth::id(),
                'changed_at' => now(),
            ]);

            $sparepart->update(['stock' => $stockAfter]);
        });

        return redirect()->route('transactions.index')
            ->with('success', 'Stock transaction recorded successfully.');
    }

    public function show(StockTransaction $transaction)
    {
        $transaction->load(['sparepart', 'user']);
        return view('transactions.show', compact('transaction'));
    }

    public function edit(StockTransaction $transaction)
    {
        // Cannot edit confirmed transactions
        if ($transaction->status === 'confirmed') {
            return redirect()->route('transactions.index')
                ->with('error', 'Confirmed transactions cannot be edited.');
        }

        // Semua user bisa edit transaction apapun selama belum confirmed

        $transaction->load(['sparepart', 'user']);
        $spareparts = Sparepart::orderBy('material_code')->get();
        $users = \App\Models\User::orderBy('name')->get();
        return view('transactions.edit', compact('transaction', 'spareparts', 'users'));
    }

    public function update(Request $request, StockTransaction $transaction)
    {
        // Cannot edit confirmed transactions
        if ($transaction->status === 'confirmed') {
            return redirect()->route('transactions.index')
                ->with('error', 'Confirmed transactions cannot be edited.');
        }

        // Semua user bisa edit transaction apapun selama belum confirmed

        $validated = $request->validate([
            'sparepart_id' => 'required|exists:spareparts,id',
            'type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|numeric|min:0.01',
            'reference_no' => [
                'required',
                'numeric',
                'digits_between:8,10',
            ],
            'user_id' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $oldSparepart = Sparepart::findOrFail($transaction->sparepart_id);
        $newSparepart = Sparepart::findOrFail($validated['sparepart_id']);

        DB::transaction(function () use ($validated, $transaction, $oldSparepart, $newSparepart) {
            // Revert old transaction effect on old sparepart
            if ($transaction->type === 'in') {
                $oldSparepart->stock -= $transaction->quantity;
            } elseif ($transaction->type === 'out') {
                $oldSparepart->stock += $transaction->quantity;
            } else {
                $oldSparepart->stock = $transaction->stock_before;
            }
            $oldSparepart->save();

            // Calculate new stock for new sparepart (allow negative stock for 'out', same as store)
            $stockBefore = $newSparepart->stock;
            if ($validated['type'] === 'in') {
                $stockAfter = $stockBefore + $validated['quantity'];
            } elseif ($validated['type'] === 'out') {
                // Allow stock to go negative
                $stockAfter = $stockBefore - $validated['quantity'];
            } else {
                $stockAfter = $validated['quantity'];
            }

            // Update transaction
            $transaction->update([
                'sparepart_id' => $validated['sparepart_id'],
                'user_id' => $validated['user_id'],
                'type' => $validated['type'],
                'quantity' => $validated['quantity'],
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'reference_no' => $validated['reference_no'],
                'notes' => $validated['notes'],
                'status' => 'changed',
                'changed_by' => Auth::id(),
                'changed_at' => now(),
            ]);

            // Update new sparepart stock
            $newSparepart->stock = $stockAfter;
            $newSparepart->save();
        });

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction updated successfully.');
    }

    public function confirm(StockTransaction $transaction)
    {
        // Only admin can confirm
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Only admin can confirm transactions.');
        }

        $transaction->update([
            'status' => 'confirmed',
            'changed_by' => Auth::id(),
            'changed_at' => now(),
        ]);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction confirmed successfully.');
    }

    public function cancel(StockTransaction $transaction)
    {

        // Cannot cancel confirmed or already canceled transactions
        if ($transaction->status === 'confirmed') {
            return redirect()->route('transactions.index')
                ->with('error', 'Confirmed transactions cannot be canceled.');
        }

        if ($transaction->status === 'canceled') {
            return redirect()->route('transactions.index')
                ->with('error', 'Transaction is already canceled.');
        }

        $sparepart = Sparepart::findOrFail($transaction->sparepart_id);

        DB::transaction(function () use ($transaction, $sparepart) {
            // Revert stock based on transaction type
            if ($transaction->type === 'in') {
                // Revert IN: subtract the quantity that was added
                $sparepart->stock -= $transaction->quantity;
            } elseif ($transaction->type === 'out') {
                // Revert OUT: add back the quantity that was subtracted
                $sparepart->stock += $transaction->quantity;
            } else {
                // Revert Adjustment: restore to stock_before
                $sparepart->stock = $transaction->stock_before;
            }
            $sparepart->save();

            // Update transaction status to canceled
            $transaction->update([
                'status' => 'canceled',
                'changed_by' => Auth::id(),
                'changed_at' => now(),
            ]);
        });

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction canceled and stock reverted successfully.');
    }

    public function export(Request $request, TransactionExcelService $excelService)
    {
        $query = StockTransaction::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('sparepart', function ($sq) use ($search) {
                    $sq->where('material_code', 'like', "%{$search}%")
                       ->orWhere('description', 'like', "%{$search}%");
                })
                ->orWhere('reference_no', 'like', "%{$search}%")
                ->orWhereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $statuses = $request->status;
            if (is_array($statuses)) {
                $query->whereIn('status', $statuses);
            } else {
                $query->where('status', $statuses);
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Jika confirm_all, update status setelah export
        $filename = 'transactions_' . date('Y-m-d_His') . '.xlsx';
        $downloadResponse = $excelService->download(clone $query, $filename);

        if ($request->boolean('confirm_all') && Auth::user()->isAdmin()) {
            $idsToConfirm = (clone $query)->whereIn('status', ['new', 'changed'])->pluck('id');
            if ($idsToConfirm->count() > 0) {
                StockTransaction::whereIn('id', $idsToConfirm)
                    ->update([
                        'status' => 'confirmed',
                        'changed_by' => Auth::id(),
                        'changed_at' => now(),
                    ]);
            }
        }

        return $downloadResponse;
    }
}
