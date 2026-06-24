<x-layouts.app>
    <x-slot:title>Input Data Kesehatan</x-slot:title>

    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-4">
            <a href="{{ route('health-checks.index') }}"
               class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Input Data Kesehatan</h1>
                <p class="text-slate-500 mt-1">Catat hasil pemeriksaan kesehatan</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <form action="{{ route('health-checks.store') }}" method="POST" class="space-y-6">
                @csrf

                @if(auth()->user()->isAdmin())
                <!-- User -->
                <div>
                    <label for="user_id" class="block text-sm font-medium text-slate-700 mb-2">
                        User <span class="text-red-500">*</span>
                    </label>
                    <select id="user_id" name="user_id"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all @error('user_id') border-red-500 @enderror"
                            required>
                        <option value="">-- Pilih User --</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->user_id }})</option>
                        @endforeach
                    </select>
                    @error('user_id')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                </div>
                @endif

                <!-- Waktu Pemeriksaan -->
                <div>
                    <label for="checked_at" class="block text-sm font-medium text-slate-700 mb-2">
                        Waktu Pemeriksaan <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" id="checked_at" name="checked_at"
                           value="{{ old('checked_at', now()->format('Y-m-d\TH:i')) }}"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all @error('checked_at') border-red-500 @enderror"
                           required>
                    @error('checked_at')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Berat Badan -->
                    <div>
                        <label for="weight" class="block text-sm font-medium text-slate-700 mb-2">
                            <i class="fas fa-weight text-slate-400 mr-1"></i> Berat Badan (kg)
                        </label>
                        <input type="number" id="weight" name="weight"
                               value="{{ old('weight') }}"
                               step="0.1" min="1" max="300"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all @error('weight') border-red-500 @enderror"
                               placeholder="e.g. 65.5">
                        @error('weight')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <!-- Temperatur -->
                    <div>
                        <label for="body_temperature" class="block text-sm font-medium text-slate-700 mb-2">
                            <i class="fas fa-thermometer-half text-slate-400 mr-1"></i> Temperatur Badan (°C)
                        </label>
                        <input type="number" id="body_temperature" name="body_temperature"
                               value="{{ old('body_temperature') }}"
                               step="0.1" min="30" max="45"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all @error('body_temperature') border-red-500 @enderror"
                               placeholder="e.g. 36.5">
                        @error('body_temperature')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <!-- Tensi Sistolik -->
                    <div>
                        <label for="systolic" class="block text-sm font-medium text-slate-700 mb-2">
                            <i class="fas fa-heartbeat text-slate-400 mr-1"></i> Tensi Sistolik (mmHg)
                        </label>
                        <input type="number" id="systolic" name="systolic"
                               value="{{ old('systolic') }}"
                               min="50" max="300"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all @error('systolic') border-red-500 @enderror"
                               placeholder="e.g. 120">
                        @error('systolic')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <!-- Tensi Diastolik -->
                    <div>
                        <label for="diastolic" class="block text-sm font-medium text-slate-700 mb-2">
                            <i class="fas fa-heartbeat text-slate-400 mr-1"></i> Tensi Diastolik (mmHg)
                        </label>
                        <input type="number" id="diastolic" name="diastolic"
                               value="{{ old('diastolic') }}"
                               min="30" max="200"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all @error('diastolic') border-red-500 @enderror"
                               placeholder="e.g. 80">
                        @error('diastolic')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <!-- Saturasi Oksigen -->
                    <div>
                        <label for="oxygen_saturation" class="block text-sm font-medium text-slate-700 mb-2">
                            <i class="fas fa-lungs text-slate-400 mr-1"></i> Saturasi Oksigen SpO2 (%)
                        </label>
                        <input type="number" id="oxygen_saturation" name="oxygen_saturation"
                               value="{{ old('oxygen_saturation') }}"
                               step="0.1" min="50" max="100"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all @error('oxygen_saturation') border-red-500 @enderror"
                               placeholder="e.g. 98">
                        @error('oxygen_saturation')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                <!-- Catatan -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-slate-700 mb-2">
                        Catatan <span class="text-slate-400">(opsional)</span>
                    </label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all @error('notes') border-red-500 @enderror"
                              placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                    @error('notes')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <a href="{{ route('health-checks.index') }}"
                       class="px-5 py-2.5 text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-xl font-medium transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-5 py-2.5 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl font-medium hover:from-green-600 hover:to-green-700 transition-all shadow-lg shadow-green-500/25">
                        <i class="fas fa-save mr-2"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
