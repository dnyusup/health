<x-layouts.app>
    <x-slot:title>Ready Stock</x-slot:title>
    <x-slot:header>Ready Stock</x-slot:header>

    <div class="space-y-6">
        <!-- Filter -->
        <form method="GET" action="{{ route('ready-stock.index') }}" class="mb-4">
            <div class="flex flex-col sm:flex-row gap-3 items-center flex-wrap">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search part, order no, machine, category..."
                       class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500 w-72">
                <select name="order_type" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">All Order Type</option>
                    <option value="ZSPM" {{ request('order_type') === 'ZSPM' ? 'selected' : '' }}>ZSPM</option>
                    <option value="ZSBM" {{ request('order_type') === 'ZSBM' ? 'selected' : '' }}>ZSBM</option>
                </select>
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg font-medium hover:bg-emerald-700 transition-all">
                    <i class="fas fa-search"></i> Filter
                </button>
                @if(request('search') || request('order_type'))
                <a href="{{ route('ready-stock.index') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-slate-200 text-slate-700 rounded-lg font-medium hover:bg-slate-300 transition-all">
                    <i class="fas fa-times"></i> Reset
                </a>
                @endif
            </div>
        </form>

        <!-- Actions -->
        <div class="flex items-center gap-2 justify-end">
                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl text-sm font-medium">
                    <i class="fas fa-boxes"></i>
                    {{ $readyStocks->total() }} part
                </span>
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

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Tgl Assembling</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Part ID</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Part Name</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Category</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Part Detail</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Action</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">PIC Assembling</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($readyStocks as $wo)
                        @php
                            $picNames = [];
                            if ($wo->pic_assembling) {
                                $users = \App\Models\User::whereIn('id', (array)$wo->pic_assembling)->pluck('name')->toArray();
                                $picNames = $users;
                            }
                        @endphp
                        <tr class="hover:bg-emerald-50/40 transition-colors">
                            <td class="px-3 py-3 text-slate-600">
                                {{ $wo->tanggal_assembling?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td class="px-3 py-3 font-mono text-slate-700">{{ $wo->part_id ?: '-' }}</td>
                            <td class="px-3 py-3 text-slate-800 font-medium max-w-[180px] truncate" title="{{ $wo->part_name }}">
                                {{ $wo->part_name ?: '-' }}
                            </td>
                            <td class="px-3 py-3 text-slate-600">{{ $wo->category ?: '-' }}</td>
                            <td class="px-3 py-3 text-slate-500 max-w-[140px] truncate" title="{{ $wo->part_detail }}">
                                {{ $wo->part_detail ?: '-' }}
                            </td>
                            <td class="px-3 py-3 max-w-[180px] truncate text-slate-700" title="{{ $wo->action_assembling }}">
                                {{ $wo->action_assembling ?: '-' }}
                            </td>
                            <td class="px-3 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($picNames as $name)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        {{ $name }}
                                    </span>
                                    @empty
                                    <span class="text-slate-400">-</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <a href="{{ route('work-orders.show', $wo) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition-all">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-slate-400">
                                    <i class="fas fa-box-open text-4xl"></i>
                                    <p class="font-medium">Belum ada part ready stock</p>
                                    <p class="text-sm">Part akan muncul di sini ketika work order berstatus <strong>Closed</strong></p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($readyStocks->hasPages())
            <div class="px-4 py-3 border-t border-slate-200">
                {{ $readyStocks->links() }}
            </div>
            @endif
        </div>
    </div>
</x-layouts.app>
