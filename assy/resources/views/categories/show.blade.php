<x-layouts.app>
    <x-slot:title>Category Details</x-slot:title>
    <x-slot:header>Category Details: {{ $category->name }}</x-slot:header>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $category->name }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ $category->description ?: 'No description' }}</p>
                </div>
                <a href="{{ route('categories.edit', $category) }}" 
                   class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                    <i class="fas fa-edit mr-1"></i>Edit
                </a>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <h4 class="text-sm font-medium text-gray-500 mb-4">Spareparts in This Category ({{ $category->spareparts->count() }})</h4>
                @if($category->spareparts->isEmpty())
                    <p class="text-center text-gray-500 py-4">No spareparts in this category yet</p>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach($category->spareparts as $sparepart)
                            <li class="py-3 flex justify-between items-center">
                                <div>
                                    <a href="{{ route('spareparts.show', $sparepart) }}" class="text-sm font-medium text-primary-600 hover:text-primary-800">
                                        {{ $sparepart->material_code }}
                                    </a>
                                    <p class="text-sm text-gray-500">{{ Str::limit($sparepart->description, 50) }}</p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sparepart->isLowStock() ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                    {{ number_format($sparepart->stock, 0) }} {{ $sparepart->unit }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <a href="{{ route('categories.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>
</x-layouts.app>
