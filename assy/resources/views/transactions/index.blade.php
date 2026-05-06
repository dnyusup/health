<x-layouts.app>
    <x-slot:title>Stock Transactions</x-slot:title>
    <x-slot:header>Stock Transaction History</x-slot:header>

    <!-- Search & Filter -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <form method="GET" action="{{ route('transactions.index') }}" id="filterForm">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               placeholder="Material, order no..."
                               class="filter-input block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-3 py-2 border">
                    </div>
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <select name="user_id" id="user_id" class="tom-select-name">
                            <option value="">All Names</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="type" id="type" 
                                class="filter-input block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-3 py-2 border">
                            <option value="">All Types</option>
                            <option value="in" {{ (request('type', $defaultType ?? '') == 'in') ? 'selected' : '' }}>In</option>
                            <option value="out" {{ (request('type', $defaultType ?? '') == 'out') ? 'selected' : '' }}>Out</option>
                            <option value="adjustment" {{ (request('type', $defaultType ?? '') == 'adjustment') ? 'selected' : '' }}>Adjustment</option>
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status[]" id="status-filter" multiple>
                            <option value="new" {{ in_array('new', (request('status', $defaultStatuses ?? []))) ? 'selected' : '' }}>New</option>
                            <option value="changed" {{ in_array('changed', (request('status', $defaultStatuses ?? []))) ? 'selected' : '' }}>Changed</option>
                            <option value="confirmed" {{ in_array('confirmed', (request('status', $defaultStatuses ?? []))) ? 'selected' : '' }}>Confirmed</option>
                            <option value="canceled" {{ in_array('canceled', (request('status', $defaultStatuses ?? []))) ? 'selected' : '' }}>Canceled</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                           <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                           <input type="date" name="date_from" id="date_from" value="{{ request('date_from', $defaultFrom ?? '') }}"
                               class="filter-input block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-3 py-2 border">
                    </div>
                    <div>
                           <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                           <input type="date" name="date_to" id="date_to" value="{{ request('date_to', $defaultTo ?? '') }}"
                               class="filter-input block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-3 py-2 border">
                    </div>
                    <div class="flex items-end">
                        <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 w-full justify-center">
                            <i class="fas fa-times mr-2"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    if (window.TomSelect) {
                        new TomSelect('#status-filter', {
                            plugins: ['remove_button'],
                            onChange: function() {
                                document.getElementById('filterForm').submit();
                            },
                            placeholder: 'Select status...'
                        });
                    }
                });
            </script>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
        <p class="text-sm text-gray-600">
            Showing {{ $transactions->firstItem() ?? 0 }} - {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} transactions
        </p>
        <div class="flex gap-2">
            <button type="button" onclick="openExportModal()" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-file-excel mr-2 text-green-600"></i>Export Excel
            </button>
            <a href="{{ route('transactions.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                <i class="fas fa-plus mr-2"></i>New Transaction
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Stock</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Order No.</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden xl:table-cell">Remark</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $transaction->created_at->format('d M Y') }}
                                <br><span class="text-gray-500 text-xs">{{ $transaction->created_at->format('H:i') }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('spareparts.show', $transaction->sparepart) }}" class="text-sm font-medium text-primary-600 hover:text-primary-800">
                                    {{ $transaction->sparepart->material_code ?? 'N/A' }}
                                </a>
                                <p class="text-sm text-gray-500">{{ Str::limit($transaction->sparepart->description ?? '', 25) }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($transaction->type === 'in')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-arrow-down mr-1"></i>In
                                    </span>
                                @elseif($transaction->type === 'out')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-arrow-up mr-1"></i>Out
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-edit mr-1"></i>Adjustment
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ number_format($transaction->quantity, 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
                                {{ number_format($transaction->stock_before, 0) }} → {{ number_format($transaction->stock_after, 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
                                {{ $transaction->reference_no ?: '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $transaction->user->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 hidden xl:table-cell">
                                {{ Str::limit($transaction->notes, 30) ?: '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($transaction->status === 'new')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 cursor-help"
                                          title="Created by {{ $transaction->user->name ?? 'Unknown' }} at {{ $transaction->created_at?->format('d M Y H:i') ?? '-' }}">
                                        <i class="fas fa-circle text-xs mr-1"></i>New
                                    </span>
                                @elseif($transaction->status === 'confirmed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 cursor-help" 
                                          title="Confirmed by {{ $transaction->changedByUser->name ?? 'Unknown' }} at {{ $transaction->changed_at?->format('d M Y H:i') ?? '-' }}">
                                        <i class="fas fa-check text-xs mr-1"></i>Confirmed
                                    </span>
                                @elseif($transaction->status === 'canceled')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 cursor-help"
                                          title="Canceled by {{ $transaction->changedByUser->name ?? 'Unknown' }} at {{ $transaction->changed_at?->format('d M Y H:i') ?? '-' }}">
                                        <i class="fas fa-ban text-xs mr-1"></i>Canceled
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 cursor-help"
                                          title="Changed by {{ $transaction->changedByUser->name ?? 'Unknown' }} at {{ $transaction->changed_at?->format('d M Y H:i') ?? '-' }}">
                                        <i class="fas fa-pen text-xs mr-1"></i>Changed
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex items-center gap-2">
                                    @php
                                        $isOwner = Auth::id() === $transaction->user_id;
                                        $isAdmin = Auth::user()->role === 'admin';
                                        $canEditOrCancel = !$transaction->status || !in_array($transaction->status, ['confirmed', 'canceled']);
                                    @endphp
                                    {{-- Edit button: only admin or owner can edit if not confirmed/canceled --}}
                                    @if($canEditOrCancel && ($isAdmin || $isOwner))
                                        <a href="{{ route('transactions.edit', $transaction) }}" class="text-blue-600 hover:text-blue-800" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @else
                                        <span class="text-gray-300" title="{{ in_array($transaction->status, ['confirmed', 'canceled']) ? 'Cannot edit ' . $transaction->status . ' transaction' : 'No permission' }}">
                                            <i class="fas fa-edit"></i>
                                        </span>
                                    @endif
                                    {{-- Confirm button: only admin can confirm --}}
                                    @if($canEditOrCancel && $isAdmin)
                                        <button type="button" 
                                                onclick="confirmTransaction({{ $transaction->id }}, '{{ $transaction->sparepart->material_code ?? '' }}')"
                                                class="text-green-600 hover:text-green-800" title="Confirm">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    @endif
                                    {{-- Cancel button: only admin or owner can cancel if not confirmed/canceled --}}
                                    @if($canEditOrCancel && ($isAdmin || $isOwner))
                                        <button type="button" 
                                                onclick="cancelTransaction({{ $transaction->id }}, '{{ $transaction->sparepart->material_code ?? '' }}')"
                                                class="text-red-600 hover:text-red-800" title="Cancel">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-exchange-alt text-4xl mb-4 text-gray-300"></i>
                                <p>No transactions yet</p>
                                <a href="{{ route('transactions.create') }}" class="text-primary-600 hover:text-primary-800 mt-2 inline-block">
                                    Create your first transaction <i class="fas fa-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($transactions->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>Confirm Transaction
                </h3>
            </div>
            <div class="px-6 py-4">
                <p class="text-gray-600">Are you sure you want to confirm this transaction?</p>
                <p class="text-sm text-gray-500 mt-2">
                    Material: <strong id="confirmMaterialCode"></strong>
                </p>
                <p class="text-sm text-amber-600 mt-3">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Once confirmed, this transaction cannot be edited anymore.
                </p>
            </div>
            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 rounded-b-lg">
                <button type="button" onclick="closeConfirmModal()" 
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 text-sm font-medium">
                    Cancel
                </button>
                <form id="confirmForm" method="POST">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 border border-transparent rounded-md text-white bg-green-600 hover:bg-green-700 text-sm font-medium">
                        <i class="fas fa-check mr-1"></i>Confirm
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Cancel Modal -->
    <div id="cancelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-times-circle text-red-500 mr-2"></i>Cancel Transaction
                </h3>
            </div>
            <div class="px-6 py-4">
                <p class="text-gray-600">Are you sure you want to cancel this transaction?</p>
                <p class="text-sm text-gray-500 mt-2">
                    Material: <strong id="cancelMaterialCode"></strong>
                </p>
                <p class="text-sm text-red-600 mt-3">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Stock will be reverted to the previous state. This action cannot be undone.
                </p>
            </div>
            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 rounded-b-lg">
                <button type="button" onclick="closeCancelModal()" 
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 text-sm font-medium">
                    Close
                </button>
                <form id="cancelForm" method="POST">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 border border-transparent rounded-md text-white bg-red-600 hover:bg-red-700 text-sm font-medium">
                        <i class="fas fa-times mr-1"></i>Cancel Transaction
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Export Modal -->
    <div id="exportModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-file-excel text-green-600 mr-2"></i>Export Transactions
                </h3>
            </div>
            <form method="POST" action="{{ route('transactions.export') }}" id="exportForm" target="exportFrame">
                @csrf
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="user_id" value="{{ request('user_id') }}">
                <input type="hidden" name="type" value="{{ request('type', $defaultType ?? '') }}">
                @php
                    $statuses = request('status');
                    if (!is_array($statuses)) {
                        $statuses = $statuses ? [$statuses] : [];
                    }
                    // Jika filter status kosong, gunakan defaultStatuses (array dari controller)
                    if (empty($statuses) && !empty($defaultStatuses)) {
                        $statuses = $defaultStatuses;
                    }
                @endphp
                @foreach($statuses as $status)
                    <input type="hidden" name="status[]" value="{{ $status }}">
                @endforeach
                <input type="hidden" name="date_from" value="{{ request('date_from', $defaultFrom ?? '') }}">
                <input type="hidden" name="date_to" value="{{ request('date_to', $defaultTo ?? '') }}">
                
                <div class="px-6 py-4">
                    <p class="text-gray-600 mb-4">Export {{ $transactions->total() }} transactions to Excel file.</p>
                    
                    @if(auth()->user()->isAdmin())
                    <label class="flex items-start gap-3 p-3 bg-amber-50 border border-amber-200 rounded-lg cursor-pointer hover:bg-amber-100">
                        <input type="checkbox" name="confirm_all" value="1" id="confirmAllCheckbox"
                               class="mt-0.5 rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <div>
                            <span class="text-sm font-medium text-gray-900">Confirm all new displayed transactions?</span>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                This will change all "New" and "Changed" transactions to "Confirmed" status.
                            </p>
                        </div>
                    </label>
                    @endif
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 rounded-b-lg">
                    <button type="button" onclick="closeExportModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 text-sm font-medium">
                        Cancel
                    </button>
                    <button type="submit" onclick="handleExport()"
                            class="px-4 py-2 border border-transparent rounded-md text-white bg-green-600 hover:bg-green-700 text-sm font-medium">
                        <i class="fas fa-download mr-1"></i>Export
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Hidden iframe for download -->
    <iframe name="exportFrame" id="exportFrame" style="display:none;"></iframe>

    <script>
        function confirmTransaction(id, materialCode) {
            document.getElementById('confirmMaterialCode').textContent = materialCode;
            document.getElementById('confirmForm').action = `/transactions/${id}/confirm`;
            document.getElementById('confirmModal').classList.remove('hidden');
        }

        function closeConfirmModal() {
            document.getElementById('confirmModal').classList.add('hidden');
        }

        function cancelTransaction(id, materialCode) {
            document.getElementById('cancelMaterialCode').textContent = materialCode;
            document.getElementById('cancelForm').action = `/transactions/${id}/cancel`;
            document.getElementById('cancelModal').classList.remove('hidden');
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').classList.add('hidden');
        }

        function openExportModal() {
            document.getElementById('exportModal').classList.remove('hidden');
        }

        function closeExportModal() {
            document.getElementById('exportModal').classList.add('hidden');
        }

        function handleExport() {
            // Close modal immediately
            setTimeout(closeExportModal, 100);
            // Check if confirm_all is checked, then reload page after delay to show updated status
            var confirmCheckbox = document.getElementById('confirmAllCheckbox');
            if (confirmCheckbox && confirmCheckbox.checked) {
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            }
        }

        // Close modal on background click
        document.getElementById('confirmModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeConfirmModal();
            }
        });

        document.getElementById('cancelModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCancelModal();
            }
        });

        document.getElementById('exportModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeExportModal();
            }
        });

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeConfirmModal();
                closeCancelModal();
                closeExportModal();
            }
        });

        // Auto-submit filter form on change
        document.querySelectorAll('#filterForm .filter-input').forEach(function(input) {
            if (input.tagName === 'SELECT' || input.type === 'date') {
                // Submit immediately on change for dropdowns and date inputs
                input.addEventListener('change', function() {
                    document.getElementById('filterForm').submit();
                });
            } else {
                // Debounce text input to avoid too many submissions
                let timeout;
                input.addEventListener('input', function() {
                    clearTimeout(timeout);
                    timeout = setTimeout(function() {
                        document.getElementById('filterForm').submit();
                    }, 500);
                });
            }
        });

        // Initialize Tom Select for searchable name dropdown
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof TomSelect !== 'undefined') {
                new TomSelect('.tom-select-name', {
                    create: false,
                    sortField: { field: 'text', direction: 'asc' },
                    placeholder: 'Search name...',
                    onChange: function() {
                        document.getElementById('filterForm').submit();
                    }
                });
            }
        });
    </script>
</x-layouts.app>
