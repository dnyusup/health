<x-layouts.app>
    <x-slot:title>Edit Part</x-slot:title>

    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-4">
            <a href="{{ route('parts.index') }}"
               class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Edit Part</h1>
                <p class="text-slate-500 mt-1">Update part information</p>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <form action="{{ route('parts.update', $part) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Part ID -->
                    <div>
                        <label for="part_id" class="block text-sm font-medium text-slate-700 mb-2">
                            Part ID <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="part_id"
                               name="part_id"
                               value="{{ old('part_id', $part->part_id) }}"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all uppercase @error('part_id') border-red-500 @enderror"
                               required>
                        @error('part_id')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-slate-700 mb-2">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="category"
                               name="category"
                               value="{{ old('category', $part->category) }}"
                               list="category-list"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all @error('category') border-red-500 @enderror"
                               required>
                        <datalist id="category-list">
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">
                            @endforeach
                        </datalist>
                        @error('category')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Part Name -->
                <div>
                    <label for="part_name" class="block text-sm font-medium text-slate-700 mb-2">
                        Part Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="part_name"
                           name="part_name"
                           value="{{ old('part_name', $part->part_name) }}"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all @error('part_name') border-red-500 @enderror"
                           required>
                    @error('part_name')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Part Detail -->
                <div>
                    <label for="part_detail" class="block text-sm font-medium text-slate-700 mb-2">
                        Part Detail <span class="text-slate-400">(optional)</span>
                    </label>
                    <input type="text"
                           id="part_detail"
                           name="part_detail"
                           value="{{ old('part_detail', $part->part_detail) }}"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all @error('part_detail') border-red-500 @enderror">
                    @error('part_detail')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('parts.index') }}"
                       class="px-5 py-2.5 text-slate-600 bg-slate-100 rounded-xl font-medium hover:bg-slate-200 transition-all">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-medium hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg shadow-blue-500/25">
                        <i class="fas fa-save mr-2"></i> Update Part
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
