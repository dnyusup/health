<x-layouts.app>
    <x-slot:title>Dashboard</x-slot:title>
    <x-slot:header>Dashboard</x-slot:header>

    <!-- Quick Actions (moved to top) -->
    <div class="mb-8 bg-white shadow rounded-lg p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
            <i class="fas fa-bolt text-yellow-500 mr-2"></i>
            Quick Actions
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('spareparts.create') }}" class="flex flex-col items-center p-4 bg-primary-50 rounded-lg hover:bg-primary-100 transition">
                    <i class="fas fa-plus-circle text-primary-600 text-2xl mb-2"></i>
                    <span class="text-sm font-medium text-primary-700">Add Sparepart</span>
                </a>
                <a href="{{ route('transactions.create') }}" class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
                    <i class="fas fa-exchange-alt text-green-600 text-2xl mb-2"></i>
                    <span class="text-sm font-medium text-green-700">New Transaction</span>
                </a>
                <a href="{{ route('categories.create') }}" class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                    <i class="fas fa-folder-plus text-purple-600 text-2xl mb-2"></i>
                    <span class="text-sm font-medium text-purple-700">Add Category</span>
                </a>
                <a href="{{ route('spareparts.index', ['low_stock' => 1]) }}" class="flex flex-col items-center p-4 bg-red-50 rounded-lg hover:bg-red-100 transition">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl mb-2"></i>
                    <span class="text-sm font-medium text-red-700">Check Low Stock</span>
                </a>
            @else
                <a href="{{ route('transactions.create') }}" class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
                    <i class="fas fa-exchange-alt text-green-600 text-2xl mb-2"></i>
                    <span class="text-sm font-medium text-green-700">New Transaction</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 mb-8">
        <!-- Total Spareparts -->
        <div class="relative overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:px-6 sm:py-6">
            <dt>
                <div class="absolute rounded-md bg-primary-500 p-3">
                    <i class="fas fa-box text-white text-xl"></i>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-gray-500">Total Spareparts</p>
            </dt>
            <dd class="ml-16 flex items-baseline">
                <p class="text-2xl font-semibold text-gray-900">{{ number_format($totalSpareparts) }}</p>
            </dd>
        </div>

        <!-- Total Categories -->
        <div class="relative overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:px-6 sm:py-6">
            <dt>
                <div class="absolute rounded-md bg-green-500 p-3">
                    <i class="fas fa-tags text-white text-xl"></i>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-gray-500">Total Categories</p>
            </dt>
            <dd class="ml-16 flex items-baseline">
                <p class="text-2xl font-semibold text-gray-900">{{ number_format($totalCategories) }}</p>
            </dd>
        </div>

        <!-- Low Stock Items -->
        <div class="relative overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:px-6 sm:py-6">
            <dt>
                <div class="absolute rounded-md bg-red-500 p-3">
                    <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-gray-500">Low Stock</p>
            </dt>
            <dd class="ml-16 flex items-baseline">
                <p class="text-2xl font-semibold text-gray-900">{{ number_format($lowStockCount) }}</p>
                @if($lowStockCount > 0)
                    <a href="{{ route('spareparts.index', ['low_stock' => 1]) }}" class="ml-2 text-sm text-red-600 hover:text-red-800">
                        View <i class="fas fa-arrow-right"></i>
                    </a>
                @endif
            </dd>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Low Stock Items -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                    Low Stock Items
                </h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                @if($lowStockItems->isEmpty())
                    <p class="text-gray-500 text-center py-4">
                        <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i><br>
                        No low stock items
                    </p>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach($lowStockItems as $item)
                            <li class="py-3 flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $item->material_code }}</p>
                                    <p class="text-sm text-gray-500 truncate max-w-xs">{{ $item->description }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ number_format($item->stock, 0) }} {{ $item->unit }}
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    <i class="fas fa-history text-primary-500 mr-2"></i>
                    Recent Transactions
                </h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                @if($recentTransactions->isEmpty())
                    <p class="text-gray-500 text-center py-4">
                        <i class="fas fa-inbox text-gray-300 text-2xl mb-2"></i><br>
                        No transactions yet
                    </p>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach($recentTransactions as $transaction)
                            <li class="py-3 flex justify-between items-center">
                                <div class="flex-1 min-w-0 pr-4">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $transaction->sparepart->material_code ?? 'N/A' }}
                                    </p>
                                    <p class="text-xs text-gray-600 truncate">
                                        {{ Str::limit($transaction->sparepart->description ?? '', 40) }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ $transaction->created_at->format('d M Y H:i') }}
                                    </p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    @if($transaction->type === 'in')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-plus mr-1"></i>{{ number_format($transaction->quantity, 0) }}
                                        </span>
                                    @elseif($transaction->type === 'out')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-minus mr-1"></i>{{ number_format($transaction->quantity, 0) }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-edit mr-1"></i>Adj
                                        </span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions sudah dipindah ke atas dan diatur tampilannya sesuai role user -->
</x-layouts.app>
