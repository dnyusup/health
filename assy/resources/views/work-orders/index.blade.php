<x-layouts.app>
    <x-slot:title>Work Order Pembongkaran</x-slot:title>
    <x-slot:header>Work Order</x-slot:header>

    <div class="space-y-6">
        <!-- Filter -->
        <form method="GET" action="{{ route('work-orders.index') }}" class="mb-4">
            <div class="flex flex-col sm:flex-row gap-3 items-center flex-wrap">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search order no, mach, part, kerusakan..."
                       class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 w-72">
                <select name="order_type" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Order Type</option>
                    <option value="ZSPM" {{ request('order_type') === 'ZSPM' ? 'selected' : '' }}>ZSPM</option>
                    <option value="ZSBM" {{ request('order_type') === 'ZSBM' ? 'selected' : '' }}>ZSBM</option>
                </select>
                <!-- Status multi-select dropdown -->
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    @php $selectedStatuses = (array) request('status', []); @endphp
                    <button type="button" @click="open = !open"
                            class="flex items-center gap-2 rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white hover:border-slate-400 transition-all min-w-[130px]">
                        <span class="flex-1 text-left text-slate-700">
                            @if(count($selectedStatuses))
                                {{ implode(', ', $selectedStatuses) }}
                            @else
                                All Status
                            @endif
                        </span>
                        <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                    </button>
                    <div x-show="open" x-transition
                         class="absolute z-20 mt-1 bg-white border border-slate-200 rounded-xl shadow-lg p-2 space-y-1 min-w-[160px]">
                        @foreach(['Open','On Progress','Closed','Installed','Scrap'] as $s)
                        <label class="flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-slate-50 cursor-pointer text-sm text-slate-700">
                            <input type="checkbox" name="status[]" value="{{ $s }}"
                                   {{ in_array($s, $selectedStatuses) ? 'checked' : '' }}
                                   class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            {{ $s }}
                        </label>
                        @endforeach
                    </div>
                </div>
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-all">
                    <i class="fas fa-search"></i> Filter
                </button>
                @if(request('search') || request('order_type') || count((array)request('status', [])))
                <a href="{{ route('work-orders.index') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-slate-200 text-slate-700 rounded-lg font-medium hover:bg-slate-300 transition-all">
                    <i class="fas fa-times"></i> Reset
                </a>
                @endif
            </div>
        </form>

        <!-- Actions -->
        <div class="flex items-center gap-3 flex-wrap justify-end">
                <a href="{{ route('work-orders.export') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl font-medium hover:bg-emerald-100 transition-all">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                @if(auth()->user()->isAdmin())
                <button type="button" onclick="document.getElementById('importWOModal').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-50 text-amber-700 border border-amber-200 rounded-xl font-medium hover:bg-amber-100 transition-all">
                    <i class="fas fa-file-upload"></i> Import Excel
                </button>
                @endif
                @if(auth()->user()->isAdmin() || auth()->user()->isShopfloor())
                <a href="{{ route('work-orders.create') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-medium hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg shadow-blue-500/25">
                    <i class="fas fa-plus"></i> Buat Work Order
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

        @if(session('import_errors') && count(session('import_errors')))
        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
                <div>
                    <p class="font-medium text-amber-800 mb-2">Beberapa baris gagal diimpor:</p>
                    <ul class="text-sm text-amber-700 space-y-1 list-disc list-inside max-h-40 overflow-y-auto">
                        @foreach(session('import_errors') as $err)
                        <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Tanggal Bongkar</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Order Number</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Order Type</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Mach Number</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Pos</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Part ID</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Part Name</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Kerusakan</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">PIC Bongkar</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Remark</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($workOrders as $wo)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-3 py-3 text-slate-600">{{ $wo->tanggal_bongkar?->format('d/m/Y') }}</td>
                            <td class="px-3 py-3">
                                <a href="{{ route('work-orders.show', $wo) }}"
                                   class="font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                                    {{ $wo->order_number ?: '-' }}
                                </a>
                            </td>
                            <td class="px-3 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold
                                    {{ $wo->order_type === 'ZSPM' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                    {{ $wo->order_type }}
                                </span>
                            </td>
                            <td class="px-3 py-3 font-medium text-slate-700">{{ $wo->mach_number }}</td>
                            <td class="px-3 py-3 text-slate-600">{{ $wo->pos ?: '-' }}</td>
                            <td class="px-3 py-3 font-mono text-slate-700">{{ $wo->part_id ?: '-' }}</td>
                            <td class="px-3 py-3 text-slate-700 max-w-[200px] truncate" title="{{ $wo->part_name }}">{{ $wo->part_name ?: '-' }}</td>
                            <td class="px-3 py-3 text-slate-700 max-w-[160px] truncate" title="{{ $wo->kerusakan }}">{{ $wo->kerusakan ?: '-' }}</td>
                            <td class="px-3 py-3 font-medium text-slate-700">{{ $wo->pic->name ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-500 max-w-[160px] truncate" title="{{ $wo->remark }}">{{ $wo->remark ?: '-' }}</td>
                            <td class="px-3 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                    {{ $wo->status === 'Open' ? 'bg-emerald-100 text-emerald-700' : ($wo->status === 'On Progress' ? 'bg-amber-100 text-amber-700' : ($wo->status === 'Installed' ? 'bg-blue-100 text-blue-700' : ($wo->status === 'Scrap' ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-600'))) }}">
                                    {{ $wo->status }}
                                </span>
                            </td>
                            <td class="px-3 py-3">
                                <div class="flex items-center justify-center gap-1">
                                    @if(auth()->user()->isAdmin() || auth()->user()->isShopfloor())
                                    <a href="{{ route('work-orders.edit', $wo) }}"
                                       class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    @endif
                                    @if(auth()->user()->isAdmin())
                                    <form action="{{ route('work-orders.destroy', $wo) }}" method="POST"
                                          onsubmit="return confirm('Yakin hapus work order ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="15" class="px-4 py-12 text-center text-slate-400">
                                <i class="fas fa-clipboard-list text-4xl mb-3 block opacity-30"></i>
                                <p class="text-sm">Belum ada Work Order.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($workOrders->hasPages())
            <div class="px-4 py-4 border-t border-slate-200">
                {{ $workOrders->links() }}
            </div>
            @endif
        </div>
    </div>

@if(auth()->user()->isAdmin())
<!-- Import Modal -->
<div id="importWOModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center px-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
                    <i class="fas fa-file-upload text-amber-600"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800">Import Work Orders</h3>
                    <p class="text-xs text-slate-500">Upload file Excel sesuai template</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('importWOModal').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('work-orders.import') }}" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-4">
            @csrf
            <div class="p-3 rounded-xl bg-blue-50 border border-blue-200 text-sm text-blue-700">
                <p><i class="fas fa-info-circle mr-1"></i> Gunakan file <strong>Export Excel</strong> sebagai template. Kolom auto (Mach Type, Created By/On, Repaired By/At, Installed By/At, Type Install) tidak perlu diisi. Status akan dibaca dari kolom N.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">File Excel <span class="text-red-500">*</span></label>
                <input type="file" name="import_file" accept=".xlsx,.xls" required
                       class="w-full text-sm text-slate-600 border border-slate-200 rounded-xl px-3 py-2.5 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 transition-all">
                @error('import_file') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="document.getElementById('importWOModal').classList.add('hidden')"
                        class="flex-1 px-4 py-2.5 text-slate-600 bg-slate-100 rounded-xl font-medium hover:bg-slate-200 transition-all">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 text-white rounded-xl font-medium hover:from-amber-600 hover:to-amber-700 transition-all">
                    <i class="fas fa-upload mr-2"></i> Upload & Import
                </button>
            </div>
        </form>
    </div>
</div>
@endif

</x-layouts.app>
