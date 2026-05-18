<x-layouts.app>
    <x-slot:title>Machine Registration</x-slot:title>
    <x-slot:header>Machine Registration</x-slot:header>

    <div class="space-y-6">
        <!-- Filter & Search -->
        <form method="GET" action="{{ route('machines.index') }}" class="mb-4">
            <div class="flex flex-col sm:flex-row gap-3 items-center">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search mach number, type, area..."
                           class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 w-72">
                </div>
                <div>
                    <select name="mach_area" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Areas</option>
                        @foreach($machAreas as $area)
                            <option value="{{ $area }}" {{ request('mach_area') === $area ? 'selected' : '' }}>{{ $area }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-all">
                        <i class="fas fa-search"></i> <span>Filter</span>
                    </button>
                </div>
                @if(request('search') || request('mach_area'))
                <div>
                    <a href="{{ route('machines.index') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-slate-200 text-slate-700 rounded-lg font-medium hover:bg-slate-300 transition-all">
                        <i class="fas fa-times"></i> <span>Reset</span>
                    </a>
                </div>
                @endif
            </div>
        </form>

        <!-- Actions -->
        <div class="flex items-center gap-3 flex-wrap justify-end">
                <!-- Export -->
                <a href="{{ route('machines.export') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl font-medium hover:bg-emerald-100 transition-all">
                    <i class="fas fa-file-excel"></i>
                    <span>Export Excel</span>
                </a>
                <!-- Import -->
                @if(auth()->user()->hasAssypartRole())
                <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-50 text-amber-700 border border-amber-200 rounded-xl font-medium hover:bg-amber-100 transition-all">
                    <i class="fas fa-file-upload"></i>
                    <span>Import Excel</span>
                </button>
                @endif
                <!-- Add Machine -->
                @if(auth()->user()->hasAssypartRole())
                <a href="{{ route('machines.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-medium hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg shadow-blue-500/25">
                    <i class="fas fa-plus"></i>
                    <span>Add Machine</span>
                </a>
                @endif
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
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Created On</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Mach Number</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Mach Type</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Mach Area</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Created By</th>
                            <th class="px-4 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($machines as $machine)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-slate-500 whitespace-nowrap">
                                {{ $machine->created_at?->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('machines.show', $machine) }}"
                                   class="font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                                    {{ $machine->mach_number }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ $machine->mach_type }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                    {{ $machine->mach_area }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-500">{{ $machine->creator->name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('machines.edit', $machine) }}"
                                       class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                       title="Edit">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    @if(auth()->user()->isAdmin())
                                    <form action="{{ route('machines.destroy', $machine) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus machine {{ $machine->mach_number }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Delete">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                <i class="fas fa-industry text-4xl mb-3 block opacity-30"></i>
                                <p class="text-sm">Belum ada data machine.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($machines->hasPages())
            <div class="px-4 py-4 border-t border-slate-200">
                {{ $machines->links() }}
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
                <p>Kolom A: <strong>Mach Number</strong> (wajib, unique)</p>
                <p>Kolom B: <strong>Mach Type</strong> (wajib)</p>
                <p>Kolom C: <strong>Mach Area</strong> (wajib)</p>
                <p class="text-xs text-blue-600 mt-2">Jika Mach Number sudah ada, data akan <strong>diperbarui</strong>. Jika belum, akan <strong>ditambahkan</strong>.</p>
            </div>

            <form action="{{ route('machines.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
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
