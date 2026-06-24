<x-layouts.app>
    <x-slot:title>Detail Kesehatan</x-slot:title>

    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('health-checks.index') }}"
                   class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Detail Kesehatan</h1>
                    <p class="text-slate-500 mt-1">{{ $healthCheck->checked_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
            <a href="{{ route('health-checks.edit', $healthCheck) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-amber-100 text-amber-700 rounded-xl font-medium hover:bg-amber-200 transition-colors">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>

        @if(auth()->user()->isAdmin())
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-400 to-slate-500 flex items-center justify-center text-white font-semibold">
                {{ strtoupper(substr($healthCheck->user->name, 0, 2)) }}
            </div>
            <div>
                <p class="font-semibold text-slate-800">{{ $healthCheck->user->name }}</p>
                <p class="text-xs text-slate-500 font-mono">{{ $healthCheck->user->user_id }}</p>
            </div>
        </div>
        @endif

        <!-- Parameter Cards -->
        <div class="grid grid-cols-2 gap-4">
            <!-- Berat Badan -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-weight text-blue-600"></i>
                    </div>
                    <p class="text-sm font-medium text-slate-600">Berat Badan</p>
                </div>
                @if($healthCheck->weight)
                <p class="text-3xl font-bold text-slate-800">{{ $healthCheck->weight }}</p>
                <p class="text-sm text-slate-500 mt-1">kilogram</p>
                @else
                <p class="text-slate-400 text-lg">-</p>
                @endif
            </div>

            <!-- Temperatur -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl {{ $healthCheck->body_temperature ? ($healthCheck->getTemperatureStatus() === 'normal' ? 'bg-green-100' : 'bg-orange-100') : 'bg-slate-100' }} flex items-center justify-center">
                        <i class="fas fa-thermometer-half {{ $healthCheck->body_temperature ? ($healthCheck->getTemperatureStatus() === 'normal' ? 'text-green-600' : 'text-orange-600') : 'text-slate-400' }}"></i>
                    </div>
                    <p class="text-sm font-medium text-slate-600">Temperatur</p>
                </div>
                @if($healthCheck->body_temperature)
                @php $ts = $healthCheck->getTemperatureStatus() @endphp
                <p class="text-3xl font-bold {{ $ts === 'normal' ? 'text-green-700' : ($ts === 'low' ? 'text-blue-700' : 'text-orange-700') }}">
                    {{ $healthCheck->body_temperature }}°
                </p>
                <p class="text-sm mt-1 {{ $ts === 'normal' ? 'text-green-500' : ($ts === 'low' ? 'text-blue-500' : 'text-orange-500') }}">
                    {{ $ts === 'low' ? 'Rendah' : ($ts === 'normal' ? 'Normal' : ($ts === 'fever' ? 'Demam' : 'Demam Tinggi')) }}
                </p>
                @else
                <p class="text-slate-400 text-lg">-</p>
                @endif
            </div>

            <!-- Tensi Darah -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl {{ $healthCheck->systolic ? ($healthCheck->getBloodPressureStatus() === 'normal' ? 'bg-green-100' : 'bg-red-100') : 'bg-slate-100' }} flex items-center justify-center">
                        <i class="fas fa-heartbeat {{ $healthCheck->systolic ? ($healthCheck->getBloodPressureStatus() === 'normal' ? 'text-green-600' : 'text-red-600') : 'text-slate-400' }}"></i>
                    </div>
                    <p class="text-sm font-medium text-slate-600">Tensi Darah</p>
                </div>
                @if($healthCheck->systolic && $healthCheck->diastolic)
                @php $bp = $healthCheck->getBloodPressureStatus() @endphp
                <p class="text-3xl font-bold {{ $bp === 'normal' ? 'text-green-700' : ($bp === 'elevated' ? 'text-yellow-700' : 'text-red-700') }}">
                    {{ $healthCheck->systolic }}/{{ $healthCheck->diastolic }}
                </p>
                <p class="text-sm mt-1 {{ $bp === 'normal' ? 'text-green-500' : ($bp === 'elevated' ? 'text-yellow-500' : 'text-red-500') }}">
                    mmHg &mdash; {{ $bp === 'normal' ? 'Normal' : ($bp === 'elevated' ? 'Meningkat' : ($bp === 'high1' ? 'Hipertensi Tkt 1' : 'Hipertensi Tkt 2')) }}
                </p>
                @else
                <p class="text-slate-400 text-lg">-</p>
                @endif
            </div>

            <!-- Saturasi Oksigen -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl {{ $healthCheck->oxygen_saturation ? ($healthCheck->getOxygenStatus() === 'normal' ? 'bg-green-100' : 'bg-yellow-100') : 'bg-slate-100' }} flex items-center justify-center">
                        <i class="fas fa-lungs {{ $healthCheck->oxygen_saturation ? ($healthCheck->getOxygenStatus() === 'normal' ? 'text-green-600' : 'text-yellow-600') : 'text-slate-400' }}"></i>
                    </div>
                    <p class="text-sm font-medium text-slate-600">Saturasi Oksigen</p>
                </div>
                @if($healthCheck->oxygen_saturation)
                @php $os = $healthCheck->getOxygenStatus() @endphp
                <p class="text-3xl font-bold {{ $os === 'normal' ? 'text-green-700' : ($os === 'low' ? 'text-yellow-700' : 'text-red-700') }}">
                    {{ $healthCheck->oxygen_saturation }}%
                </p>
                <p class="text-sm mt-1 {{ $os === 'normal' ? 'text-green-500' : ($os === 'low' ? 'text-yellow-500' : 'text-red-500') }}">
                    SpO2 &mdash; {{ $os === 'normal' ? 'Normal' : ($os === 'low' ? 'Rendah' : 'Kritis') }}
                </p>
                @else
                <p class="text-slate-400 text-lg">-</p>
                @endif
            </div>
        </div>

        <!-- Catatan -->
        @if($healthCheck->notes)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <p class="text-sm font-medium text-slate-600 mb-2"><i class="fas fa-sticky-note mr-2 text-slate-400"></i>Catatan</p>
            <p class="text-slate-700">{{ $healthCheck->notes }}</p>
        </div>
        @endif
    </div>
</x-layouts.app>
