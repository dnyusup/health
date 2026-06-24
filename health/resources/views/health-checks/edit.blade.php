<x-layouts.app>
    <x-slot:title>Edit Data Kesehatan</x-slot:title>

    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-4">
            <a href="{{ route('health-checks.index') }}"
               class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Edit Data Kesehatan</h1>
                <p class="text-slate-500 mt-1">Perbarui hasil pemeriksaan</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <form action="{{ route('health-checks.update', $healthCheck) }}" method="POST" class="space-y-6">
                @csrf @method('PUT')

                @if(auth()->user()->isAdmin())
                <div>
                    <label for="user_id" class="block text-sm font-medium text-slate-700 mb-2">
                        User <span class="text-red-500">*</span>
                    </label>
                    <select id="user_id" name="user_id"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all"
                            required>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ old('user_id', $healthCheck->user_id) == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->user_id }})</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div>
                    <label for="checked_at" class="block text-sm font-medium text-slate-700 mb-2">
                        Waktu Pemeriksaan <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" id="checked_at" name="checked_at"
                           value="{{ old('checked_at', $healthCheck->checked_at->format('Y-m-d\TH:i')) }}"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all"
                           required>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="weight" class="block text-sm font-medium text-slate-700 mb-2">
                            <i class="fas fa-weight text-slate-400 mr-1"></i> Berat Badan (kg)
                        </label>
                        <input type="number" id="weight" name="weight"
                               value="{{ old('weight', $healthCheck->weight) }}"
                               step="0.1" min="1" max="300"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all"
                               placeholder="e.g. 65.5">
                    </div>

                    <div>
                        <label for="body_temperature" class="block text-sm font-medium text-slate-700 mb-2">
                            <i class="fas fa-thermometer-half text-slate-400 mr-1"></i> Temperatur Badan (°C)
                        </label>
                        <input type="number" id="body_temperature" name="body_temperature"
                               value="{{ old('body_temperature', $healthCheck->body_temperature) }}"
                               step="0.1" min="30" max="45"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all"
                               placeholder="e.g. 36.5">
                    </div>

                    <div>
                        <label for="systolic" class="block text-sm font-medium text-slate-700 mb-2">
                            <i class="fas fa-heartbeat text-slate-400 mr-1"></i> Tensi Sistolik (mmHg)
                        </label>
                        <input type="number" id="systolic" name="systolic"
                               value="{{ old('systolic', $healthCheck->systolic) }}"
                               min="50" max="300"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all"
                               placeholder="e.g. 120">
                    </div>

                    <div>
                        <label for="diastolic" class="block text-sm font-medium text-slate-700 mb-2">
                            <i class="fas fa-heartbeat text-slate-400 mr-1"></i> Tensi Diastolik (mmHg)
                        </label>
                        <input type="number" id="diastolic" name="diastolic"
                               value="{{ old('diastolic', $healthCheck->diastolic) }}"
                               min="30" max="200"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all"
                               placeholder="e.g. 80">
                    </div>

                    <div>
                        <label for="oxygen_saturation" class="block text-sm font-medium text-slate-700 mb-2">
                            <i class="fas fa-lungs text-slate-400 mr-1"></i> Saturasi Oksigen SpO2 (%)
                        </label>
                        <input type="number" id="oxygen_saturation" name="oxygen_saturation"
                               value="{{ old('oxygen_saturation', $healthCheck->oxygen_saturation) }}"
                               step="0.1" min="50" max="100"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all"
                               placeholder="e.g. 98">
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-slate-700 mb-2">
                        Catatan <span class="text-slate-400">(opsional)</span>
                    </label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all"
                              placeholder="Catatan tambahan...">{{ old('notes', $healthCheck->notes) }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <a href="{{ route('health-checks.index') }}"
                       class="px-5 py-2.5 text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-xl font-medium transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-medium hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg shadow-blue-500/25">
                        <i class="fas fa-save mr-2"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
