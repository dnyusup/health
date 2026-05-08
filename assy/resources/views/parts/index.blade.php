<x-layouts.app>
    <x-slot:title>Part Registration</x-slot:title>
    <x-slot:header>Part Registration</x-slot:header>

    <div class="space-y-6">
        <!-- Filter & Search -->
        <form method="GET" action="{{ route('parts.index') }}" class="mb-4">
            <input type="hidden" name="sort" value="{{ $sortBy }}">
            <input type="hidden" name="dir" value="{{ $sortDir }}">
            <div class="flex flex-col sm:flex-row gap-3 items-center">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search part id, name, category..." class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 w-64">
                </div>
                <div>
                    <select name="category" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-all">
                        <i class="fas fa-search"></i> <span>Filter</span>
                    </button>
                </div>
                @if(request('search') || request('category'))
                <div>
                    <a href="{{ route('parts.index') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-slate-200 text-slate-700 rounded-lg font-medium hover:bg-slate-300 transition-all">
                        <i class="fas fa-times"></i> <span>Reset</span>
                    </a>
                </div>
                @endif
            </div>
        </form>

        <!-- Actions -->
        <div class="flex items-center gap-3 flex-wrap justify-end">
                <!-- Export -->
                <a href="{{ route('parts.export') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl font-medium hover:bg-emerald-100 transition-all">
                    <i class="fas fa-file-excel"></i>
                    <span>Export Excel</span>
                </a>
                <!-- Import -->
                <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-50 text-amber-700 border border-amber-200 rounded-xl font-medium hover:bg-amber-100 transition-all">
                    <i class="fas fa-file-upload"></i>
                    <span>Import Excel</span>
                </button>
                <!-- Add Part -->
                <a href="{{ route('parts.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-medium hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg shadow-blue-500/25">
                    <i class="fas fa-plus"></i>
                    <span>Add Part</span>
                </a>
        </div>

        <!-- Messages -->
        @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-500"></i>
                <p class="text-emerald-700">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('import_errors'))
        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
                <div>
                    <p class="text-amber-800 font-medium mb-1">Beberapa baris tidak dapat diimport:</p>
                    <p class="text-amber-700 text-sm">{{ session('import_errors') }}</p>
                </div>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="p-4 rounded-xl bg-red-50 border border-red-200">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-red-500"></i>
                <p class="text-red-700">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        <!-- Table -->
        @php
            $sortLink = fn($col) => request()->fullUrlWithQuery(['sort' => $col, 'dir' => ($sortBy === $col && $sortDir === 'asc') ? 'desc' : 'asc']);
            $sortIcon = function($col) use ($sortBy, $sortDir) {
                if ($sortBy !== $col) return '<i class="fas fa-sort ml-1 text-slate-300"></i>';
                return $sortDir === 'asc'
                    ? '<i class="fas fa-sort-up ml-1 text-blue-500"></i>'
                    : '<i class="fas fa-sort-down ml-1 text-blue-500"></i>';
            };
        @endphp
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                <a href="{{ $sortLink('created_at') }}" class="flex items-center hover:text-blue-600 transition-colors">
                                    Created On {!! $sortIcon('created_at') !!}
                                </a>
                            </th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                <a href="{{ $sortLink('part_id') }}" class="flex items-center hover:text-blue-600 transition-colors">
                                    Part ID {!! $sortIcon('part_id') !!}
                                </a>
                            </th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                <a href="{{ $sortLink('category') }}" class="flex items-center hover:text-blue-600 transition-colors">
                                    Category {!! $sortIcon('category') !!}
                                </a>
                            </th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                <a href="{{ $sortLink('part_name') }}" class="flex items-center hover:text-blue-600 transition-colors">
                                    Part Name {!! $sortIcon('part_name') !!}
                                </a>
                            </th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                <a href="{{ $sortLink('part_detail') }}" class="flex items-center hover:text-blue-600 transition-colors">
                                    Part Detail {!! $sortIcon('part_detail') !!}
                                </a>
                            </th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Created By</th>
                            <th class="px-4 py-4 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($parts as $part)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-4 text-sm text-slate-500 whitespace-nowrap">
                                {{ $part->created_at->format('m/d/Y H:i:s') }}
                            </td>
                            <td class="px-4 py-4">
                                <a href="{{ route('parts.show', $part) }}" class="text-sm font-semibold text-blue-600 hover:underline">
                                    {{ $part->part_id }}
                                </a>
                            </td>
                            <td class="px-4 py-4 text-sm text-slate-700">{{ $part->category }}</td>
                            <td class="px-4 py-4 text-sm text-slate-700">{{ $part->part_name }}</td>
                            <td class="px-4 py-4 text-sm text-slate-500">{{ $part->part_detail ?? '-' }}</td>
                            <td class="px-4 py-4 text-sm text-slate-700">{{ $part->creator->name ?? '-' }}</td>
                            <td class="px-4 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('parts.edit', $part) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-50 text-amber-600 rounded-lg text-xs font-medium hover:bg-amber-100 transition-colors">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    @if(auth()->user()->isAdmin())
                                    <form action="{{ route('parts.destroy', $part) }}" method="POST" onsubmit="return confirm('Hapus part ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-medium hover:bg-red-100 transition-colors">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <i class="fas fa-box-open text-4xl mb-3 block"></i>
                                No parts registered yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($parts->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $parts->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Import Modal -->
    <div id="importModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-6 space-y-5">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800">
                    <i class="fas fa-file-upload text-amber-500 mr-2"></i> Import Excel
                </h3>
                <button onclick="document.getElementById('importModal').classList.add('hidden')"
                        class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-700 space-y-1">
                <p class="font-semibold"><i class="fas fa-info-circle mr-1"></i> Format kolom Excel:</p>
                <p>Kolom A: <strong>Part ID</strong> (wajib, unique)</p>
                <p>Kolom B: <strong>Category</strong> (wajib)</p>
                <p>Kolom C: <strong>Part Name</strong> (wajib)</p>
                <p>Kolom D: <strong>Part Detail</strong> (opsional)</p>
                <p class="text-xs text-blue-600 mt-2">Jika Part ID sudah ada, data akan <strong>diperbarui</strong>. Jika belum, akan <strong>ditambahkan</strong>.</p>
            </div>

            <form action="{{ route('parts.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Pilih File Excel <span class="text-red-500">*</span>
                        <span class="text-slate-400 font-normal">(maks. 50MB)</span>
                    </label>
                    <input type="file" name="file" accept=".xlsx,.xls" required
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 file:font-medium hover:file:bg-blue-100">
                    @error('file')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex gap-3 justify-end pt-2">
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                            class="px-5 py-2.5 text-slate-600 bg-slate-100 rounded-xl font-medium hover:bg-slate-200 transition-all">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 text-white rounded-xl font-medium hover:from-amber-600 hover:to-amber-700 transition-all shadow-lg shadow-amber-500/25">
                        <i class="fas fa-upload mr-2"></i> Upload & Import
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-layouts.app>
