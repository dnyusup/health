<x-layouts.app>
    <x-slot:title>Spareparts</x-slot:title>
    <x-slot:header>Spareparts List</x-slot:header>

    <!-- Search & Filter -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <form method="GET" action="{{ route('spareparts.index') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               placeholder="Code, description, bin..."
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-3 py-2 border">
                    </div>
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category" id="category" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-3 py-2 border">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center">
                            <input type="checkbox" name="low_stock" value="1" {{ request('low_stock') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <span class="ml-2 text-sm text-gray-600">Low Stock Only</span>
                        </label>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                        <a href="{{ route('spareparts.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
        <p class="text-sm text-gray-600">
            Showing {{ $spareparts->firstItem() ?? 0 }} - {{ $spareparts->lastItem() ?? 0 }} of {{ $spareparts->total() }} spareparts
        </p>
        <div class="flex flex-wrap items-center gap-2">
            @if(auth()->user()->isAdmin())
                <!-- Export Excel Button -->
                <a href="{{ route('spareparts.export') }}" 
                   class="inline-flex items-center px-4 py-2 border border-emerald-300 text-sm font-medium rounded-md shadow-sm text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition-colors">
                    <i class="fas fa-file-excel mr-2"></i>Export Excel
                </a>
                <!-- Import Excel Button -->
                <button type="button" 
                    onclick="console.log('import click'); document.getElementById('importModal').classList.remove('hidden')"
                    class="inline-flex items-center px-4 py-2 border border-blue-300 text-sm font-medium rounded-md shadow-sm text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors">
                <i class="fas fa-file-upload mr-2"></i>Import Excel
                </button>
                <a href="{{ route('spareparts.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700">
                    <i class="fas fa-plus mr-2"></i>Add Sparepart
                </a>
            @endif
        </div>
    </div>

    <!-- Import Errors -->
    @if(session('import_errors'))
    <div class="mb-4 p-4 rounded-xl bg-amber-50 border border-amber-200">
        <div class="flex items-start gap-3">
            <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
            <div>
                <p class="font-medium text-amber-800">Some rows had issues:</p>
                <ul class="mt-2 text-sm text-amber-700 list-disc list-inside">
                    @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bin</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Description</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Category</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($spareparts as $sparepart)
                        <tr class="hover:bg-gray-50 {{ $sparepart->isLowStock() ? 'bg-red-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $sparepart->material_code }}</div>
                                <div class="text-sm text-gray-500 lg:hidden">{{ Str::limit($sparepart->description, 30) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $sparepart->bin_location ?: '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 hidden lg:table-cell">
                                <span title="{{ $sparepart->description }}">{{ Str::limit($sparepart->description, 40) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sparepart->isLowStock() ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                    {{ number_format($sparepart->stock, 0) }} {{ $sparepart->unit }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
                                {{ $sparepart->category->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    @if(auth()->user()->isAdmin())
                                        <a href="{{ route('transactions.create', ['sparepart_id' => $sparepart->id]) }}" 
                                           class="text-green-600 hover:text-green-900" title="Transaction">
                                            <i class="fas fa-exchange-alt"></i>
                                        </a>
                                        <a href="{{ route('spareparts.show', $sparepart) }}" 
                                           class="text-primary-600 hover:text-primary-900" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('spareparts.edit', $sparepart) }}" 
                                           class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('spareparts.destroy', $sparepart) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this sparepart?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="text-gray-400 cursor-not-allowed" title="View Only" disabled>
                                            <i class="fas fa-eye-slash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-box-open text-4xl mb-4 text-gray-300"></i>
                                <p>No spareparts yet</p>
                                <a href="{{ route('spareparts.create') }}" class="text-primary-600 hover:text-primary-800 mt-2 inline-block">
                                    Add your first sparepart <i class="fas fa-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($spareparts->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $spareparts->links() }}
            </div>
        @endif
    </div>

    <!-- Import Modal -->
    <div id="importModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
            <!-- DEBUG: Add visible border and background to modal panel -->
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('importModal').classList.add('hidden')"></div>

            <!-- Modal panel -->
            <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border-4 border-red-500 bg-yellow-100">
                <form action="{{ route('spareparts.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900" id="modal-title">
                                <i class="fas fa-file-excel text-emerald-500 mr-2"></i>
                                Import Spareparts from Excel
                            </h3>
                            <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" 
                                    class="text-gray-400 hover:text-gray-500">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="p-4 rounded-xl bg-blue-50 border border-blue-200">
                                <div class="flex gap-3">
                                    <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                                    <div class="text-sm text-blue-700">
                                        <p class="font-medium mb-1">How it works:</p>
                                        <ol class="list-decimal list-inside space-y-1">
                                            <li>Download the Excel template using "Export Excel"</li>
                                            <li>Edit data or add new rows in the "Spareparts" sheet</li>
                                            <li>Save and upload the file here</li>
                                        </ol>
                                        <p class="mt-2 text-xs">Note: Existing records (matched by Material Code) will be updated.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label for="excel_file" class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Excel File (.xlsx)
                                </label>
                                <input type="file" 
                                       id="excel_file" 
                                       name="excel_file" 
                                       accept=".xlsx,.xls"
                                       required
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-gray-200 rounded-xl">
                                <p class="mt-1 text-xs text-gray-500">Maximum file size: 10MB</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                        <button type="button" 
                                onclick="document.getElementById('importModal').classList.add('hidden')"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-xl hover:bg-blue-700 transition-colors">
                            <i class="fas fa-upload mr-2"></i>
                            Import Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
