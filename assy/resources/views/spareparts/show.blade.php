<x-layouts.app>
    <x-slot:title>Sparepart Details</x-slot:title>
    <x-slot:header>Sparepart Details</x-slot:header>

    <div class="max-w-4xl mx-auto">
        <!-- Sparepart Info -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $sparepart->material_code }}</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">{{ $sparepart->description }}</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('transactions.create', ['sparepart_id' => $sparepart->id]) }}" 
                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <i class="fas fa-exchange-alt mr-1"></i>Transaction
                    </a>
                    <a href="{{ route('spareparts.edit', $sparepart) }}" 
                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </a>
                </div>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <dt class="text-sm font-medium text-gray-500">Material Code</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $sparepart->material_code }}</dd>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <dt class="text-sm font-medium text-gray-500">Bin Location</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $sparepart->bin_location ?: '-' }}</dd>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <dt class="text-sm font-medium text-gray-500">Old Material No.</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $sparepart->old_material_no ?: '-' }}</dd>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <dt class="text-sm font-medium text-gray-500">Category</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $sparepart->category->name ?? '-' }}</dd>
                    </div>
                    <div class="{{ $sparepart->isLowStock() ? 'bg-red-50' : 'bg-green-50' }} rounded-lg p-4">
                        <dt class="text-sm font-medium {{ $sparepart->isLowStock() ? 'text-red-600' : 'text-green-600' }}">Current Stock</dt>
                        <dd class="mt-1 text-2xl font-bold {{ $sparepart->isLowStock() ? 'text-red-700' : 'text-green-700' }}">
                            {{ number_format($sparepart->stock, 0) }} {{ $sparepart->unit }}
                            @if($sparepart->isLowStock())
                                <i class="fas fa-exclamation-triangle text-lg ml-1"></i>
                            @endif
                        </dd>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <dt class="text-sm font-medium text-gray-500">Minimum Stock</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ number_format($sparepart->min_stock, 0) }} {{ $sparepart->unit }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Transaction History -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    <i class="fas fa-history mr-2"></i>Transaction History
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Stock Before</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Stock After</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($sparepart->transactions->sortByDesc('created_at')->take(20) as $transaction)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $transaction->created_at->format('d M Y') }}
                                    <br><span class="text-gray-500 text-xs">{{ $transaction->created_at->format('H:i') }}</span>
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
                                    {{ number_format($transaction->stock_before, 0) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
                                    {{ number_format($transaction->stock_after, 0) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 hidden md:table-cell">
                                    {{ Str::limit($transaction->notes, 50) ?: '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-3xl mb-2 text-gray-300"></i>
                                    <p>No transactions yet</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('spareparts.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>
</x-layouts.app>
