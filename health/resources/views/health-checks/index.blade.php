<x-layouts.app>
<x-slot:title>Health Monitoring</x-slot:title>
<x-slot:header>Health Monitoring</x-slot:header>

<style>[x-cloak]{display:none!important}</style>

@php $initialTab = (request('view') === 'team' || request()->hasAny(['user_id','date_from','date_to'])) ? 'team' : 'myhealth'; @endphp

<div class="space-y-6" x-data="{ tab: '{{ $initialTab }}' }">

    {{-- Tab Toggle (supervisor/admin only) --}}
    @if($canFilter)
    <div class="flex items-center gap-2 bg-white rounded-2xl shadow-sm border border-slate-200 p-1.5 w-fit">
        <button x-on:click="tab = 'myhealth'"
                :class="tab === 'myhealth' ? 'bg-emerald-600 text-white shadow' : 'text-slate-600 hover:bg-slate-100'"
                class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium transition-all">
            <i class="fas fa-user-circle"></i> Kesehatan Saya
        </button>
        <button x-on:click="tab = 'team'"
                :class="tab === 'team' ? 'bg-emerald-600 text-white shadow' : 'text-slate-600 hover:bg-slate-100'"
                class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium transition-all">
            <i class="fas fa-users"></i> Tim Saya
            <span class="text-xs px-1.5 py-0.5 rounded-full"
                  :class="tab === 'team' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500'">
                {{ $filterUsers->count() }}
            </span>
        </button>
    </div>
    @endif

    {{-- ===== MY HEALTH TAB ===== --}}
    <div x-show="tab === 'myhealth'">

        @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-500"></i>
            <p class="text-emerald-700">{{ session('success') }}</p>
        </div>
        @endif

        @if($myLastCheck)
        @php
            $myBp   = $myLastCheck->getBloodPressureStatus();
            $myO2   = $myLastCheck->getOxygenStatus();
            $myTemp = $myLastCheck->getTemperatureStatus();
        @endphp

        {{-- Last check summary cards --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-slate-700">Hasil Pemeriksaan Terakhir</h3>
                <span class="text-xs text-slate-400">{{ $myLastCheck->checked_at->format('d M Y, H:i') }}</span>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-xl p-3.5 {{ in_array($myBp,['high1','high2']) ? 'bg-red-50' : ($myBp==='elevated' ? 'bg-amber-50' : 'bg-emerald-50') }}">
                    <p class="text-xs text-slate-500 mb-1">Tekanan Darah</p>
                    <p class="text-xl font-bold {{ in_array($myBp,['high1','high2']) ? 'text-red-700' : ($myBp==='elevated' ? 'text-amber-700' : 'text-emerald-700') }}">
                        {{ $myLastCheck->systolic && $myLastCheck->diastolic ? $myLastCheck->systolic.'/'.$myLastCheck->diastolic : '-' }}
                    </p>
                    <p class="text-xs mt-0.5 text-slate-400">mmHg</p>
                </div>
                <div class="rounded-xl p-3.5 {{ $myO2 !== 'normal' ? 'bg-red-50' : 'bg-emerald-50' }}">
                    <p class="text-xs text-slate-500 mb-1">Saturasi O₂</p>
                    <p class="text-xl font-bold {{ $myO2 !== 'normal' ? 'text-red-700' : 'text-emerald-700' }}">
                        {{ $myLastCheck->oxygen_saturation ? number_format($myLastCheck->oxygen_saturation,1).'%' : '-' }}
                    </p>
                    <p class="text-xs mt-0.5 text-slate-400">SpO₂</p>
                </div>
                <div class="rounded-xl p-3.5 {{ in_array($myTemp,['fever','high_fever']) ? 'bg-red-50' : ($myTemp==='low' ? 'bg-blue-50' : 'bg-emerald-50') }}">
                    <p class="text-xs text-slate-500 mb-1">Suhu Tubuh</p>
                    <p class="text-xl font-bold {{ in_array($myTemp,['fever','high_fever']) ? 'text-red-700' : ($myTemp==='low' ? 'text-blue-700' : 'text-emerald-700') }}">
                        {{ $myLastCheck->body_temperature ? number_format($myLastCheck->body_temperature,1).'°C' : '-' }}
                    </p>
                    <p class="text-xs mt-0.5 text-slate-400">Celcius</p>
                </div>
                <div class="rounded-xl p-3.5 bg-gray-50">
                    <p class="text-xs text-slate-500 mb-1">Berat Badan</p>
                    <p class="text-xl font-bold text-slate-700">
                        {{ $myLastCheck->weight ? number_format($myLastCheck->weight,1).' kg' : '-' }}
                    </p>
                    <p class="text-xs mt-0.5 text-slate-400">Kilogram</p>
                </div>
            </div>
            @if($myLastCheck->notes)
            <div class="mt-3 p-3 bg-gray-50 rounded-xl text-sm text-gray-600">
                <i class="fas fa-notes-medical text-gray-400 mr-1.5"></i>{{ $myLastCheck->notes }}
            </div>
            @endif
        </div>

        {{-- Charts --}}
        @if($myChecks->count() > 1)
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                <p class="text-sm font-semibold text-slate-700 mb-4">
                    <i class="fas fa-tint text-red-400 mr-1.5"></i> Tekanan Darah (mmHg)
                </p>
                <canvas id="chartBP"></canvas>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                <p class="text-sm font-semibold text-slate-700 mb-4">
                    <i class="fas fa-lungs text-cyan-500 mr-1.5"></i> Saturasi O₂ (%)
                </p>
                <canvas id="chartO2"></canvas>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                <p class="text-sm font-semibold text-slate-700 mb-4">
                    <i class="fas fa-thermometer-half text-orange-400 mr-1.5"></i> Suhu Tubuh (°C)
                </p>
                <canvas id="chartTemp"></canvas>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                <p class="text-sm font-semibold text-slate-700 mb-4">
                    <i class="fas fa-weight text-violet-500 mr-1.5"></i> Berat Badan (kg)
                </p>
                <canvas id="chartWeight"></canvas>
            </div>
        </div>
        @endif

        @else
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-10 text-center">
            <i class="fas fa-heartbeat text-4xl text-gray-200 mb-3 block"></i>
            <p class="text-slate-500 font-medium">Belum ada data pemeriksaan.</p>
            <p class="text-slate-400 text-sm mt-1">Mulai input data kesehatan Anda sekarang.</p>
        </div>
        @endif

        <div class="flex justify-end">
            <a href="{{ route('health-checks.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl font-medium hover:from-emerald-600 hover:to-emerald-700 transition-all shadow-lg shadow-emerald-500/25">
                <i class="fas fa-plus"></i> Input Data Kesehatan
            </a>
        </div>
    </div>

    {{-- ===== TEAM VIEW TAB ===== --}}
    @if($canFilter)
    <div x-show="tab === 'team'" x-cloak>

        {{-- Filter --}}
        <form method="GET" action="{{ route('health-checks.index') }}" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
            <input type="hidden" name="view" value="team">
            <div class="flex flex-wrap gap-3 items-end">

                <div x-data="{
                        open: false,
                        search: '',
                        selectedId: '{{ request('user_id') }}',
                        selectedName: '{{ $filterUsers->firstWhere('id', request('user_id'))?->name ?? '' }}',
                        users: {{ $filterUsers->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'uid' => $u->user_id])->values()->toJson() }},
                        get filtered() {
                            if (!this.search) return this.users;
                            const s = this.search.toLowerCase();
                            return this.users.filter(u => u.name.toLowerCase().includes(s) || u.uid.toLowerCase().includes(s));
                        },
                        select(u) { this.selectedId = u ? u.id : ''; this.selectedName = u ? u.name : ''; this.search = ''; this.open = false; }
                    }" class="relative min-w-[220px]">
                    <input type="hidden" name="user_id" :value="selectedId">
                    <label class="block text-xs font-medium text-slate-600 mb-1">Nama Karyawan</label>
                    <div class="relative">
                        <input type="text"
                               x-model="search"
                               x-on:focus="open = true"
                               x-on:keydown.escape="open = false"
                               :placeholder="selectedId ? selectedName : 'Cari nama / ID...'"
                               class="w-full rounded-lg border border-slate-300 pl-3 pr-8 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500 outline-none"
                               autocomplete="off">
                        <button type="button" x-on:click="select(null); search = ''" x-show="selectedId || search"
                                class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                    <div x-show="open" x-on:click.outside="open = false"
                         class="absolute z-20 mt-1 w-full max-h-56 overflow-y-auto bg-white border border-slate-200 rounded-xl shadow-lg">
                        <div x-on:click="select(null)" class="px-3 py-2 text-sm text-slate-500 hover:bg-slate-50 cursor-pointer border-b border-slate-100">
                            Semua Karyawan
                        </div>
                        <template x-for="u in filtered" :key="u.id">
                            <div x-on:click="select(u)"
                                 :class="selectedId == u.id ? 'bg-emerald-50 text-emerald-700' : 'text-slate-700 hover:bg-slate-50'"
                                 class="px-3 py-2 text-sm cursor-pointer flex items-center justify-between">
                                <span x-text="u.name"></span>
                                <span class="text-xs text-slate-400 ml-2" x-text="u.uid"></span>
                            </div>
                        </template>
                        <div x-show="filtered.length === 0" class="px-3 py-3 text-sm text-slate-400 text-center">
                            Tidak ditemukan
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                </div>
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-all">
                    <i class="fas fa-search"></i> Filter
                </button>
                @if(request()->hasAny(['user_id','date_from','date_to']))
                <a href="{{ route('health-checks.index') }}?view=team"
                   class="inline-flex items-center gap-2 px-3 py-2 bg-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-300">
                    <i class="fas fa-times"></i> Reset
                </a>
                @endif
            </div>
        </form>

        {{-- Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Karyawan</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Waktu</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Berat (kg)</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Tensi Darah</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">SpO2 (%)</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Suhu (°C)</th>
                            <th class="px-4 py-4 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($records as $record)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-400 to-emerald-500 flex items-center justify-center text-white text-xs font-semibold shrink-0">
                                        {{ strtoupper(substr($record->user->name ?? '?', 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-700">{{ $record->user->name ?? '-' }}</p>
                                        <p class="text-xs text-slate-400">{{ $record->user->user_id ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600 whitespace-nowrap">{{ $record->checked_at->format('d M Y, H:i') }}</td>
                            <td class="px-4 py-3">
                                @if($record->weight)
                                <span class="text-sm font-medium text-slate-800">{{ $record->weight }}</span>
                                @else
                                <span class="text-slate-400 text-sm">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($record->systolic && $record->diastolic)
                                @php $bp = $record->getBloodPressureStatus() @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                    {{ $bp === 'normal' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $bp === 'elevated' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $bp === 'high1' ? 'bg-orange-100 text-orange-700' : '' }}
                                    {{ $bp === 'high2' ? 'bg-red-100 text-red-700' : '' }}">
                                    {{ $record->systolic }}/{{ $record->diastolic }}
                                </span>
                                @else
                                <span class="text-slate-400 text-sm">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($record->oxygen_saturation)
                                @php $os = $record->getOxygenStatus() @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                    {{ $os === 'normal' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $os === 'low' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $os === 'critical' ? 'bg-red-100 text-red-700' : '' }}">
                                    {{ $record->oxygen_saturation }}%
                                </span>
                                @else
                                <span class="text-slate-400 text-sm">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($record->body_temperature)
                                @php $ts = $record->getTemperatureStatus() @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                    {{ $ts === 'low' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $ts === 'normal' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $ts === 'fever' ? 'bg-orange-100 text-orange-700' : '' }}
                                    {{ $ts === 'high_fever' ? 'bg-red-100 text-red-700' : '' }}">
                                    {{ $record->body_temperature }}°C
                                </span>
                                @else
                                <span class="text-slate-400 text-sm">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('health-checks.show', $record) }}"
                                       class="p-1.5 text-slate-400 hover:text-blue-500 hover:bg-blue-50 rounded-lg transition-colors" title="Detail">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="{{ route('health-checks.edit', $record) }}"
                                       class="p-1.5 text-slate-400 hover:text-amber-500 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <form action="{{ route('health-checks.destroy', $record) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Hapus data ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                        <i class="fas fa-heartbeat text-2xl text-slate-400"></i>
                                    </div>
                                    <p class="text-slate-500 font-medium">Belum ada data kesehatan</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($records->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $records->links() }}
            </div>
            @endif
        </div>

    </div>
    @endif

</div>

@if($myChecks->count() > 1)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const labels = {!! $myChecks->map(fn($c) => $c->checked_at->format('d/m'))->toJson() !!};
    const mkChart = (id, datasets, yMin, yMax) => {
        const el = document.getElementById(id);
        if (!el) return;
        new Chart(el, {
            type: 'line',
            data: { labels, datasets },
            options: {
                responsive: true,
                plugins: { legend: { position: 'top', labels: { boxWidth: 10, font: { size: 11 } } } },
                scales: {
                    y: { min: yMin, max: yMax, ticks: { font: { size: 10 } } },
                    x: { ticks: { font: { size: 10 }, maxRotation: 45 } }
                },
                elements: { point: { radius: 3, hoverRadius: 5 }, line: { tension: 0.4 } }
            }
        });
    };

    mkChart('chartBP', [
        { label: 'Sistolik', data: {!! $myChecks->pluck('systolic')->toJson() !!}, borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,0.08)', fill: true },
        { label: 'Diastolik', data: {!! $myChecks->pluck('diastolic')->toJson() !!}, borderColor: '#f97316', backgroundColor: 'transparent', fill: false }
    ], 40, 200);

    mkChart('chartO2', [
        { label: 'SpO₂ (%)', data: {!! $myChecks->pluck('oxygen_saturation')->toJson() !!}, borderColor: '#06b6d4', backgroundColor: 'rgba(6,182,212,0.08)', fill: true }
    ], 80, 100);

    mkChart('chartTemp', [
        { label: 'Suhu (°C)', data: {!! $myChecks->pluck('body_temperature')->toJson() !!}, borderColor: '#f97316', backgroundColor: 'rgba(249,115,22,0.08)', fill: true }
    ], 34, 42);

    mkChart('chartWeight', [
        { label: 'BB (kg)', data: {!! $myChecks->pluck('weight')->toJson() !!}, borderColor: '#8b5cf6', backgroundColor: 'rgba(139,92,246,0.08)', fill: true }
    ]);
</script>
@endif

</x-layouts.app>
