<x-layouts.app>
    <x-slot:title>Add Sparepart</x-slot:title>
    <x-slot:header>Add New Sparepart</x-slot:header>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow rounded-lg">
            <form action="{{ route('spareparts.store') }}" method="POST" class="px-4 py-5 sm:p-6">
                @csrf
                
                <div class="grid grid-cols-1 gap-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="material_code" class="block text-sm font-medium text-gray-700 mb-1">Material Code *</label>
                            <input type="text" name="material_code" id="material_code" value="{{ old('material_code') }}" required
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-3 py-2 border @error('material_code') border-red-500 @enderror"
                                   placeholder="e.g. 2505442">
                            @error('material_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="bin_location" class="block text-sm font-medium text-gray-700 mb-1">Bin Location</label>
                            <input type="text" name="bin_location" id="bin_location" value="{{ old('bin_location') }}"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-3 py-2 border"
                                   placeholder="e.g. ISC 13 D6">
                        </div>
                    </div>

                    <div>
                        <label for="old_material_no" class="block text-sm font-medium text-gray-700 mb-1">Old Material No.</label>
                        <input type="text" name="old_material_no" id="old_material_no" value="{{ old('old_material_no') }}"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-3 py-2 border"
                               placeholder="e.g. 35200060000">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                        <input type="text" name="description" id="description" value="{{ old('description') }}" required
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-3 py-2 border @error('description') border-red-500 @enderror"
                               placeholder="e.g. BALL 6mm DIN5401">
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Initial Stock *</label>
                            <input type="number" name="stock" id="stock" value="{{ old('stock', 0) }}" required min="0" step="0.01"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-3 py-2 border @error('stock') border-red-500 @enderror">
                            @error('stock')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="unit" class="block text-sm font-medium text-gray-700 mb-1">Unit *</label>
                            <input type="text" name="unit" id="unit" value="{{ old('unit', 'PC') }}" required
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-3 py-2 border"
                                   placeholder="PC, SET, MTR">
                        </div>
                        
                        <div>
                            <label for="min_stock" class="block text-sm font-medium text-gray-700 mb-1">Minimum Stock</label>
                            <input type="number" name="min_stock" id="min_stock" value="{{ old('min_stock', 0) }}" min="0" step="0.01"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-3 py-2 border">
                        </div>
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category_id" id="category_id"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-3 py-2 border">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <i class="fas fa-save mr-2"></i>Save
                    </button>
                    <a href="{{ route('spareparts.index') }}" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
