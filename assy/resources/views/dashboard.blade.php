<x-layouts.app>
<x-slot:title>Dashboard</x-slot:title>
<x-slot:header>Dashboard</x-slot:header>

<div class="space-y-6">

    {{-- Welcome Banner --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 p-6 shadow-lg">
        <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-white/5"></div>
        <div class="absolute -right-4 bottom-0 w-32 h-32 rounded-full bg-white/5"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white">Selamat datang, {{ auth()->user()->name }}! 👋</h2>
                <p class="text-blue-200 mt-1 text-sm">{{ now()->translatedFormat('l, d F Y') }} &mdash; Assy Part Track &amp; Trace</p>
            </div>
            <div class="hidden sm:flex items-center gap-3">
                <div class="text-right">
                    <p class="text-xs text-blue-300 uppercase tracking-widest">WO Bulan Ini</p>
                    <p class="text-3xl font-extrabold text-white">{{ number_format($woThisMonth) }}</p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-white/10 flex items-center justify-center">
                    <i class="fas fa-clipboard-check text-white text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Primary Stats Row --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Total WO --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Work Order</p>
                <p class="text-2xl font-bold text-gray-800 mt-0.5">{{ number_format($totalWO) }}</p>
            </div>
        </div>
        {{-- Total Parts --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center shrink-0">
                <i class="fas fa-cog text-purple-600 text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Part</p>
                <p class="text-2xl font-bold text-gray-800 mt-0.5">{{ number_format($totalParts) }}</p>
            </div>
        </div>
        {{-- Total Machines --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center shrink-0">
                <i class="fas fa-industry text-orange-500 text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Mesin</p>
                <p class="text-2xl font-bold text-gray-800 mt-0.5">{{ number_format($totalMachines) }}</p>
            </div>
        </div>
        {{-- Total Users --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center shrink-0">
                <i class="fas fa-users text-green-600 text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total User</p>
                <p class="text-2xl font-bold text-gray-800 mt-0.5">{{ number_format($totalUsers) }}</p>
            </div>
        </div>
    </div>

    {{-- WO Status Cards --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
        <a href="{{ route('work-orders.index', ['status' => 'Open']) }}" class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-4 hover:shadow-md hover:border-yellow-300 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">Open</span>
                <i class="fas fa-folder-open text-yellow-400 group-hover:scale-110 transition-transform"></i>
            </div>
            <p class="text-3xl font-extrabold text-gray-800">{{ number_format($woOpen) }}</p>
            <p class="text-xs text-gray-400 mt-1">Work Order aktif</p>
        </a>
        <a href="{{ route('work-orders.index', ['status' => 'On Progress']) }}" class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-4 hover:shadow-md hover:border-blue-300 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">On Progress</span>
                <i class="fas fa-tools text-blue-400 group-hover:scale-110 transition-transform"></i>
            </div>
            <p class="text-3xl font-extrabold text-gray-800">{{ number_format($woOnProgress) }}</p>
            <p class="text-xs text-gray-400 mt-1">Sedang dikerjakan</p>
        </a>
        <a href="{{ route('ready-stock.index') }}" class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-4 hover:shadow-md hover:border-green-300 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Ready Stock</span>
                <i class="fas fa-boxes text-green-400 group-hover:scale-110 transition-transform"></i>
            </div>
            <p class="text-3xl font-extrabold text-gray-800">{{ number_format($woClosed) }}</p>
            <p class="text-xs text-gray-400 mt-1">Part siap pakai</p>
        </a>
        <a href="{{ route('work-orders.index', ['status' => 'Installed']) }}" class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-4 hover:shadow-md hover:border-indigo-300 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">Installed</span>
                <i class="fas fa-check-circle text-indigo-400 group-hover:scale-110 transition-transform"></i>
            </div>
            <p class="text-3xl font-extrabold text-gray-800">{{ number_format($woInstalled) }}</p>
            <p class="text-xs text-gray-400 mt-1">Sudah terpasang</p>
        </a>
        <a href="{{ route('work-orders.index', ['status' => 'Scrap']) }}" class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-4 hover:shadow-md hover:border-red-300 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Scrap</span>
                <i class="fas fa-trash-alt text-red-400 group-hover:scale-110 transition-transform"></i>
            </div>
            <p class="text-3xl font-extrabold text-gray-800">{{ number_format($woScrap) }}</p>
            <p class="text-xs text-gray-400 mt-1">Tidak terpakai</p>
        </a>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Monthly WO Trend (bar chart) --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Tren Work Order</h3>
                    <p class="text-xs text-gray-400">6 Bulan terakhir</p>
                </div>
                <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                    <i class="fas fa-chart-bar text-blue-500"></i>
                </div>
            </div>
            <canvas id="chartMonthly" height="90"></canvas>
        </div>

        {{-- Status Donut --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Status WO</h3>
                    <p class="text-xs text-gray-400">Distribusi keseluruhan</p>
                </div>
                <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center">
                    <i class="fas fa-chart-pie text-indigo-500"></i>
                </div>
            </div>
            <div class="flex-1 flex items-center justify-center">
                <canvas id="chartStatus" style="max-height:200px"></canvas>
            </div>
            <div class="mt-4 space-y-1.5">
                @foreach([
                    ['Open', '#f59e0b', $woOpen],
                    ['On Progress', '#3b82f6', $woOnProgress],
                    ['Ready Stock', '#10b981', $woClosed],
                    ['Installed', '#6366f1', $woInstalled],
                    ['Scrap', '#ef4444', $woScrap],
                ] as [$label, $color, $count])
                <div class="flex items-center justify-between text-xs">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:{{ $color }}"></span>
                        <span class="text-gray-600">{{ $label }}</span>
                    </span>
                    <span class="font-semibold text-gray-800">{{ number_format($count) }}</span>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- Category Chart + Recent WO --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Top Categories --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Top Kategori Part</h3>
                    <p class="text-xs text-gray-400">Berdasarkan jumlah WO</p>
                </div>
                <div class="w-9 h-9 rounded-xl bg-orange-50 flex items-center justify-center">
                    <i class="fas fa-tags text-orange-500"></i>
                </div>
            </div>
            @php $maxCat = $woByCategory->max('total') ?: 1; @endphp
            <div class="space-y-3">
                @forelse($woByCategory as $cat)
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-700 font-medium truncate max-w-[160px]" title="{{ $cat->category }}">{{ $cat->category }}</span>
                        <span class="text-gray-500 ml-2 shrink-0">{{ $cat->total }}</span>
                    </div>
                    <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-2 rounded-full bg-gradient-to-r from-blue-500 to-indigo-400 transition-all"
                             style="width: {{ round($cat->total / $maxCat * 100) }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-4">Belum ada data</p>
                @endforelse
            </div>
        </div>

        {{-- Recent Work Orders --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Work Order Terbaru</h3>
                    <p class="text-xs text-gray-400">5 WO terakhir</p>
                </div>
                <a href="{{ route('work-orders.index') }}"
                   class="text-xs text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1">
                    Lihat semua <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="pb-2 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">No. Order</th>
                            <th class="pb-2 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Part</th>
                            <th class="pb-2 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide hidden sm:table-cell">Mesin</th>
                            <th class="pb-2 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide hidden md:table-cell">Tanggal</th>
                            <th class="pb-2 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentWO as $wo)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-3 font-mono text-xs text-gray-700">
                                <a href="{{ route('work-orders.show', $wo) }}" class="hover:text-blue-600">
                                    {{ $wo->order_number ?? '#'.$wo->id }}
                                </a>
                            </td>
                            <td class="py-3 text-gray-700 max-w-[140px]">
                                <span class="truncate block" title="{{ $wo->part_name }}">{{ $wo->part_name ?? '-' }}</span>
                                <span class="text-xs text-gray-400">{{ $wo->part_id }}</span>
                            </td>
                            <td class="py-3 text-gray-500 text-xs hidden sm:table-cell">{{ $wo->mach_number ?? '-' }}</td>
                            <td class="py-3 text-gray-500 text-xs hidden md:table-cell">
                                {{ $wo->tanggal_bongkar?->format('d M Y') ?? '-' }}
                            </td>
                            <td class="py-3">
                                @php
                                    $badge = match($wo->status) {
                                        'Open'        => 'bg-yellow-100 text-yellow-700',
                                        'On Progress' => 'bg-blue-100 text-blue-700',
                                        'Closed'      => 'bg-green-100 text-green-700',
                                        'Installed'   => 'bg-indigo-100 text-indigo-700',
                                        'Scrap'       => 'bg-red-100 text-red-700',
                                        default       => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $badge }}">
                                    {{ $wo->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400 text-sm">Belum ada work order</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-base font-semibold text-gray-800 mb-4">
            <i class="fas fa-bolt text-yellow-400 mr-1.5"></i> Quick Actions
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <a href="{{ route('work-orders.create') }}"
               class="flex flex-col items-center gap-2 p-4 rounded-xl bg-blue-50 hover:bg-blue-100 transition-colors group">
                <div class="w-10 h-10 rounded-lg bg-blue-500 flex items-center justify-center group-hover:scale-105 transition-transform shadow">
                    <i class="fas fa-plus text-white"></i>
                </div>
                <span class="text-xs font-semibold text-blue-700 text-center">Buat Work Order</span>
            </a>
            <a href="{{ route('ready-stock.index') }}"
               class="flex flex-col items-center gap-2 p-4 rounded-xl bg-green-50 hover:bg-green-100 transition-colors group">
                <div class="w-10 h-10 rounded-lg bg-green-500 flex items-center justify-center group-hover:scale-105 transition-transform shadow">
                    <i class="fas fa-boxes text-white"></i>
                </div>
                <span class="text-xs font-semibold text-green-700 text-center">Ready Stock</span>
            </a>
            <a href="{{ route('parts.index') }}"
               class="flex flex-col items-center gap-2 p-4 rounded-xl bg-purple-50 hover:bg-purple-100 transition-colors group">
                <div class="w-10 h-10 rounded-lg bg-purple-500 flex items-center justify-center group-hover:scale-105 transition-transform shadow">
                    <i class="fas fa-cog text-white"></i>
                </div>
                <span class="text-xs font-semibold text-purple-700 text-center">Data Part</span>
            </a>
            <a href="{{ route('machines.index') }}"
               class="flex flex-col items-center gap-2 p-4 rounded-xl bg-orange-50 hover:bg-orange-100 transition-colors group">
                <div class="w-10 h-10 rounded-lg bg-orange-500 flex items-center justify-center group-hover:scale-105 transition-transform shadow">
                    <i class="fas fa-industry text-white"></i>
                </div>
                <span class="text-xs font-semibold text-orange-700 text-center">Data Mesin</span>
            </a>
        </div>
    </div>

</div>

{{-- Chart.js Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Monthly Trend Bar Chart
    const ctxMonthly = document.getElementById('chartMonthly');
    if (ctxMonthly) {
        new Chart(ctxMonthly, {
            type: 'bar',
            data: {
                labels: {!! json_encode($monthLabels) !!},
                datasets: [{
                    label: 'Work Order',
                    data: {!! json_encode($monthData) !!},
                    backgroundColor: 'rgba(59,130,246,0.15)',
                    borderColor: 'rgba(59,130,246,1)',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    hoverBackgroundColor: 'rgba(59,130,246,0.35)',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' ' + ctx.parsed.y + ' WO'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, color: '#9ca3af', font: { size: 11 } },
                        grid: { color: '#f3f4f6' }
                    },
                    x: {
                        ticks: { color: '#9ca3af', font: { size: 11 } },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // Status Donut Chart
    const ctxStatus = document.getElementById('chartStatus');
    if (ctxStatus) {
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Open', 'On Progress', 'Ready Stock', 'Installed', 'Scrap'],
                datasets: [{
                    data: [
                        {{ $woOpen }},
                        {{ $woOnProgress }},
                        {{ $woClosed }},
                        {{ $woInstalled }},
                        {{ $woScrap }}
                    ],
                    backgroundColor: ['#f59e0b','#3b82f6','#10b981','#6366f1','#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' ' + ctx.label + ': ' + ctx.parsed + ' WO'
                        }
                    }
                }
            }
        });
    }

});
</script>

</x-layouts.app>

