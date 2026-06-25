<x-layouts.app>
    <x-slot:title>Health Monitoring</x-slot:title>
    <x-slot:header>Health Monitoring</x-slot:header>

    <div class="space-y-6">
        <!-- Filter -->
        <form method="GET" action="{{ route('health-checks.index') }}" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
            <div class="flex flex-wrap gap-3 items-end">

                @if($canFilter)
                {{-- Searchable user dropdown --}}
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
                               @focus="open = true"
                               @keydown.escape="open = false"
                               :placeholder="selectedId ? selectedName : 'Cari nama / ID...'"
                               :class="selectedId && !search ? 'text-slate-800 font-medium' : ''"
                               class="w-full rounded-lg border border-slate-300 pl-3 pr-8 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500 outline-none"
                               autocomplete="off">
                        <button type="button" @click="select(null); search = ''" x-show="selectedId || search"
                                class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                    <div x-show="open" @click.outside="open = false"
                         class="absolute z-20 mt-1 w-full max-h-56 overflow-y-auto bg-white border border-slate-200 rounded-xl shadow-lg">
                        <div @click="select(null)" class="px-3 py-2 text-sm text-slate-500 hover:bg-slate-50 cursor-pointer border-b border-slate-100">
                            Semua Karyawan
                        </div>
                        <template x-for="u in filtered" :key="u.id">
                            <div @click="select(u)"
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
                @endif

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                </div>
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-all">
                    <i class="fas fa-search"></i> Filter
                </button>
                @if(request()->hasAny(['user_id','date_from','date_to']))
                <a href="{{ route('health-checks.index') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-300">
                    <i class="fas fa-times"></i> Reset
                </a>
                @endif
            </div>
        </form>

        <!-- Actions -->
        <div class="flex items-center justify-end">
            <a href="{{ route('health-checks.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl font-medium hover:from-green-600 hover:to-green-700 transition-all shadow-lg shadow-green-500/25">
                <i class="fas fa-plus"></i>
                <span>Input Data Kesehatan</span>
            </a>
        </div>

        <!-- Alerts -->
        @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-500"></i>
            <p class="text-emerald-700">{{ session('success') }}</p>
        </div>
        @endif

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            @if($canFilter)
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Karyawan</th>
                            @endif
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
                            @if($canFilter)
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
                            @endif
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
                            <td colspan="{{ $canFilter ? 7 : 6 }}" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                        <i class="fas fa-heartbeat text-2xl text-slate-400"></i>
                                    </div>
                                    <p class="text-slate-500 font-medium">Belum ada data kesehatan</p>
                                    <p class="text-slate-400 text-sm mt-1">Mulai input data kesehatan Anda</p>
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
</x-layouts.app>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-all">
                    <i class="fas fa-search"></i> Filter
                </button>
                @if(request()->hasAny(['user_id','date_from','date_to']))
                <a href="{{ route('health-checks.index') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-300">
                    <i class="fas fa-times"></i> Reset
                </a>
                @endif
            </div>
        </form>

        <!-- Actions -->
        <div class="flex items-center justify-end">
            <a href="{{ route('health-checks.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl font-medium hover:from-green-600 hover:to-green-700 transition-all shadow-lg shadow-green-500/25">
                <i class="fas fa-plus"></i>
                <span>Input Data Kesehatan</span>
            </a>
        </div>

        <!-- Alerts -->
        @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-500"></i>
            <p class="text-emerald-700">{{ session('success') }}</p>
        </div>
        @endif

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            @if(auth()->user()->isAdmin())
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">User</th>
                            @endif
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
                            @if(auth()->user()->isAdmin())
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-slate-400 to-slate-500 flex items-center justify-center text-white text-xs font-semibold">
                                        {{ strtoupper(substr($record->user->name, 0, 2)) }}
                                    </div>
                                    <span class="text-sm font-medium text-slate-700">{{ $record->user->name }}</span>
                                </div>
                            </td>
                            @endif
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $record->checked_at->format('d M Y, H:i') }}</td>
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
                            <td colspan="{{ auth()->user()->isAdmin() ? 7 : 6 }}" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                        <i class="fas fa-heartbeat text-2xl text-slate-400"></i>
                                    </div>
                                    <p class="text-slate-500 font-medium">Belum ada data kesehatan</p>
                                    <p class="text-slate-400 text-sm mt-1">Mulai input data kesehatan Anda</p>
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
</x-layouts.app>
