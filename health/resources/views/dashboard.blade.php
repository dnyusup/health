<x-layouts.app>
<x-slot:title>Dashboard</x-slot:title>
<x-slot:header>Dashboard</x-slot:header>

@php $user = auth()->user(); @endphp

@if($user->isAdmin() || $user->role === 'supervisor')
{{-- ===== ADMIN / SUPERVISOR VIEW ===== --}}
<div class="space-y-6">

    {{-- Stats Row --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center mb-3">
                <i class="fas fa-users text-emerald-600 text-base"></i>
            </div>
            <p class="text-xs text-gray-500 font-medium leading-tight">Total Karyawan</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalEmployees) }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <i class="fas fa-heartbeat text-blue-600 text-base"></i>
            </div>
            <p class="text-xs text-gray-500 font-medium leading-tight">Cek Hari Ini</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalChecksToday) }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center mb-3">
                <i class="fas fa-calendar-check text-violet-600 text-base"></i>
            </div>
            <p class="text-xs text-gray-500 font-medium leading-tight">Cek Bulan Ini</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalChecksMonth) }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center mb-3">
                <i class="fas fa-exclamation-triangle text-red-500 text-base"></i>
            </div>
            <p class="text-xs text-gray-500 font-medium leading-tight">Abnormal Hari Ini</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($abnormalToday) }}</p>
        </div>
    </div>

    {{-- Alert: belum cek hari ini --}}
    @if($notCheckedToday > 0)
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4">
        <div class="flex items-start gap-3">
            <i class="fas fa-clock text-amber-500 text-lg mt-0.5 shrink-0"></i>
            <div class="flex-1 min-w-0">
                <p class="text-amber-800 text-sm font-medium">
                    <span class="font-bold">{{ $notCheckedToday }} karyawan</span> belum melakukan pemeriksaan kesehatan hari ini.
                </p>
                <a href="{{ route('health-checks.index') }}" class="mt-1 inline-block text-xs font-semibold text-amber-700 hover:text-amber-900 underline">Lihat detail →</a>
            </div>
        </div>
    </div>
    @endif

    {{-- Recent Health Checks --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-800">Pemeriksaan Terbaru</h2>
            <a href="{{ route('health-checks.index') }}" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">Lihat semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-gray-500 uppercase tracking-wide bg-gray-50">
                        <th class="text-left px-6 py-3 font-medium">Karyawan</th>
                        <th class="text-left px-6 py-3 font-medium">Tanggal</th>
                        <th class="text-center px-4 py-3 font-medium">Tensi</th>
                        <th class="text-center px-4 py-3 font-medium">SpO₂</th>
                        <th class="text-center px-4 py-3 font-medium">Suhu</th>
                        <th class="text-center px-4 py-3 font-medium">BB (kg)</th>
                        <th class="text-center px-4 py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentChecks as $check)
                    @php
                        $bpStatus = $check->getBloodPressureStatus();
                        $o2Status = $check->getOxygenStatus();
                        $tempStatus = $check->getTemperatureStatus();
                        $isAbnormal = in_array($bpStatus, ['high1','high2']) || in_array($o2Status, ['low','critical']) || in_array($tempStatus, ['fever','high_fever']);
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-semibold text-xs shrink-0">
                                    {{ strtoupper(substr($check->user->name ?? '?', 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 text-sm">{{ $check->user->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-400">{{ $check->user->user_id ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-3.5 text-gray-600 whitespace-nowrap">
                            {{ $check->checked_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            @if($check->systolic && $check->diastolic)
                                <span class="font-medium {{ in_array($bpStatus,['high1','high2']) ? 'text-red-600' : ($bpStatus === 'elevated' ? 'text-amber-600' : 'text-gray-700') }}">
                                    {{ $check->systolic }}/{{ $check->diastolic }}
                                </span>
                            @else
                                <span class="text-gray-300">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            @if($check->oxygen_saturation)
                                <span class="font-medium {{ $o2Status !== 'normal' ? 'text-red-600' : 'text-gray-700' }}">
                                    {{ number_format($check->oxygen_saturation, 1) }}%
                                </span>
                            @else
                                <span class="text-gray-300">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            @if($check->body_temperature)
                                <span class="font-medium {{ in_array($tempStatus,['fever','high_fever']) ? 'text-red-600' : ($tempStatus === 'low' ? 'text-blue-600' : 'text-gray-700') }}">
                                    {{ number_format($check->body_temperature, 1) }}°C
                                </span>
                            @else
                                <span class="text-gray-300">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            @if($check->weight)
                                <span class="text-gray-700">{{ number_format($check->weight, 1) }}</span>
                            @else
                                <span class="text-gray-300">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            @if($isAbnormal)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                    <i class="fas fa-circle text-[6px]"></i> Abnormal
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                    <i class="fas fa-circle text-[6px]"></i> Normal
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                            <i class="fas fa-heartbeat text-3xl mb-2 block opacity-30"></i>
                            Belum ada data pemeriksaan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@else
{{-- ===== REGULAR USER VIEW ===== --}}
<div class="space-y-6">

    {{-- Stats Row --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center mb-3">
                <i class="fas fa-heartbeat text-emerald-600 text-base"></i>
            </div>
            <p class="text-xs text-gray-500 font-medium leading-tight">Cek Bulan Ini</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $myChecksMonth }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                <i class="fas fa-history text-blue-600 text-base"></i>
            </div>
            <p class="text-xs text-gray-500 font-medium leading-tight">Total Pemeriksaan</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $myChecksTotal }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center mb-3">
                <i class="fas fa-calendar-day text-violet-600 text-base"></i>
            </div>
            <p class="text-xs text-gray-500 font-medium leading-tight">Terakhir Cek</p>
            <p class="text-sm font-bold text-gray-800 mt-1">
                {{ $myLastCheck ? $myLastCheck->checked_at->format('d M Y') : '-' }}
            </p>
        </div>
    </div>

    {{-- Last Check Summary --}}
    @if($myLastCheck)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-800">Hasil Pemeriksaan Terakhir</h2>
            <span class="text-xs text-gray-400">{{ $myLastCheck->checked_at->format('d M Y, H:i') }}</span>
        </div>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            @php
                $bpStatus = $myLastCheck->getBloodPressureStatus();
                $o2Status = $myLastCheck->getOxygenStatus();
                $tempStatus = $myLastCheck->getTemperatureStatus();
            @endphp
            <div class="rounded-xl p-4 {{ in_array($bpStatus,['high1','high2']) ? 'bg-red-50' : ($bpStatus === 'elevated' ? 'bg-amber-50' : 'bg-emerald-50') }}">
                <p class="text-xs text-gray-500 mb-1">Tekanan Darah</p>
                <p class="text-lg font-bold {{ in_array($bpStatus,['high1','high2']) ? 'text-red-700' : ($bpStatus === 'elevated' ? 'text-amber-700' : 'text-emerald-700') }}">
                    {{ $myLastCheck->systolic && $myLastCheck->diastolic ? $myLastCheck->systolic.'/'.$myLastCheck->diastolic : '-' }}
                </p>
                <p class="text-xs mt-1 {{ in_array($bpStatus,['high1','high2']) ? 'text-red-500' : ($bpStatus === 'elevated' ? 'text-amber-500' : 'text-emerald-500') }}">
                    mmHg
                </p>
            </div>
            <div class="rounded-xl p-4 {{ $o2Status !== 'normal' ? 'bg-red-50' : 'bg-emerald-50' }}">
                <p class="text-xs text-gray-500 mb-1">Saturasi O₂</p>
                <p class="text-lg font-bold {{ $o2Status !== 'normal' ? 'text-red-700' : 'text-emerald-700' }}">
                    {{ $myLastCheck->oxygen_saturation ? number_format($myLastCheck->oxygen_saturation, 1).'%' : '-' }}
                </p>
                <p class="text-xs mt-1 {{ $o2Status !== 'normal' ? 'text-red-500' : 'text-emerald-500' }}">SpO₂</p>
            </div>
            <div class="rounded-xl p-4 {{ in_array($tempStatus,['fever','high_fever']) ? 'bg-red-50' : ($tempStatus === 'low' ? 'bg-blue-50' : 'bg-emerald-50') }}">
                <p class="text-xs text-gray-500 mb-1">Suhu Tubuh</p>
                <p class="text-lg font-bold {{ in_array($tempStatus,['fever','high_fever']) ? 'text-red-700' : ($tempStatus === 'low' ? 'text-blue-700' : 'text-emerald-700') }}">
                    {{ $myLastCheck->body_temperature ? number_format($myLastCheck->body_temperature, 1).'°C' : '-' }}
                </p>
                <p class="text-xs mt-1 {{ in_array($tempStatus,['fever','high_fever']) ? 'text-red-500' : ($tempStatus === 'low' ? 'text-blue-500' : 'text-emerald-500') }}">Celcius</p>
            </div>
            <div class="rounded-xl p-4 bg-gray-50">
                <p class="text-xs text-gray-500 mb-1">Berat Badan</p>
                <p class="text-lg font-bold text-gray-700">
                    {{ $myLastCheck->weight ? number_format($myLastCheck->weight, 1).' kg' : '-' }}
                </p>
                <p class="text-xs mt-1 text-gray-400">Kilogram</p>
            </div>
        </div>
        @if($myLastCheck->notes)
        <div class="mt-4 p-3 bg-gray-50 rounded-xl text-sm text-gray-600">
            <i class="fas fa-notes-medical text-gray-400 mr-2"></i>{{ $myLastCheck->notes }}
        </div>
        @endif
    </div>
    @else
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center">
        <i class="fas fa-heartbeat text-4xl text-gray-200 mb-3 block"></i>
        <p class="text-gray-500 font-medium">Belum ada data pemeriksaan.</p>
        <a href="{{ route('health-checks.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition-colors">
            <i class="fas fa-plus"></i> Tambah Pemeriksaan
        </a>
    </div>
    @endif

    {{-- Recent Checks --}}
    @if($myRecentChecks->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-800">Riwayat Pemeriksaan</h2>
            <a href="{{ route('health-checks.index') }}" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">Lihat semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-gray-500 uppercase tracking-wide bg-gray-50">
                        <th class="text-left px-6 py-3 font-medium">Tanggal</th>
                        <th class="text-center px-4 py-3 font-medium">Tensi</th>
                        <th class="text-center px-4 py-3 font-medium">SpO₂</th>
                        <th class="text-center px-4 py-3 font-medium">Suhu</th>
                        <th class="text-center px-4 py-3 font-medium">BB (kg)</th>
                        <th class="text-center px-4 py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($myRecentChecks as $check)
                    @php
                        $bpS = $check->getBloodPressureStatus();
                        $o2S = $check->getOxygenStatus();
                        $tmpS = $check->getTemperatureStatus();
                        $abnormal = in_array($bpS, ['high1','high2']) || in_array($o2S, ['low','critical']) || in_array($tmpS, ['fever','high_fever']);
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3.5 text-gray-700 whitespace-nowrap">{{ $check->checked_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3.5 text-center font-medium {{ in_array($bpS,['high1','high2']) ? 'text-red-600' : ($bpS === 'elevated' ? 'text-amber-600' : 'text-gray-700') }}">
                            {{ $check->systolic && $check->diastolic ? $check->systolic.'/'.$check->diastolic : '-' }}
                        </td>
                        <td class="px-4 py-3.5 text-center font-medium {{ $o2S !== 'normal' ? 'text-red-600' : 'text-gray-700' }}">
                            {{ $check->oxygen_saturation ? number_format($check->oxygen_saturation,1).'%' : '-' }}
                        </td>
                        <td class="px-4 py-3.5 text-center font-medium {{ in_array($tmpS,['fever','high_fever']) ? 'text-red-600' : 'text-gray-700' }}">
                            {{ $check->body_temperature ? number_format($check->body_temperature,1).'°C' : '-' }}
                        </td>
                        <td class="px-4 py-3.5 text-center text-gray-700">{{ $check->weight ? number_format($check->weight,1) : '-' }}</td>
                        <td class="px-4 py-3.5 text-center">
                            @if($abnormal)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                    <i class="fas fa-circle text-[6px]"></i> Abnormal
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                    <i class="fas fa-circle text-[6px]"></i> Normal
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endif
</x-layouts.app>

