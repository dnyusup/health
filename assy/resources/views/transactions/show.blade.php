<x-layouts.app>
    <x-slot:title>Transaction Details</x-slot:title>
    <x-slot:header>Transaction Details</x-slot:header>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        @if($transaction->type === 'in')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-arrow-down mr-2"></i>Stock In
                            </span>
                        @elseif($transaction->type === 'out')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <i class="fas fa-arrow-up mr-2"></i>Stock Out
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-edit mr-2"></i>Stock Adjustment
                            </span>
                        @endif
                    </h3>
                    <span class="text-sm text-gray-500">{{ $transaction->created_at->format('d M Y H:i') }}</span>
                </div>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4 sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Sparepart</dt>
                        <dd class="mt-1">
                            <a href="{{ route('spareparts.show', $transaction->sparepart) }}" class="text-lg font-semibold text-primary-600 hover:text-primary-800">
                                {{ $transaction->sparepart->material_code }}
                            </a>
                            <p class="text-sm text-gray-600">{{ $transaction->sparepart->description }}</p>
                        </dd>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <dt class="text-sm font-medium text-gray-500">Quantity</dt>
                        <dd class="mt-1 text-2xl font-bold text-gray-900">
                            {{ number_format($transaction->quantity, 0) }} {{ $transaction->sparepart->unit }}
                        </dd>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <dt class="text-sm font-medium text-gray-500">Stock Change</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">
                            {{ number_format($transaction->stock_before, 0) }} 
                            <i class="fas fa-arrow-right text-gray-400 mx-2"></i>
                            {{ number_format($transaction->stock_after, 0) }}
                        </dd>
                    </div>
                    
                    @if($transaction->reference_no)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <dt class="text-sm font-medium text-gray-500">Reference No.</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $transaction->reference_no }}</dd>
                        </div>
                    @endif
                    
                    @if($transaction->user)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <dt class="text-sm font-medium text-gray-500">By</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $transaction->user->name }}</dd>
                        </div>
                    @endif
                    
                    @if($transaction->notes)
                        <div class="bg-gray-50 rounded-lg p-4 sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Notes</dt>
                            <dd class="mt-1 text-gray-900">{{ $transaction->notes }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>
</x-layouts.app>
