<x-layouts.app>
    <x-slot:title>Add Machine</x-slot:title>

    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-4">
            <a href="{{ route('machines.index') }}"
               class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Add New Machine</h1>
                <p class="text-slate-500 mt-1">Register a new machine into the system</p>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <form action="{{ route('machines.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Mach Number -->
                    <div>
                        <label for="mach_number" class="block text-sm font-medium text-slate-700 mb-2">
                            Mach Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="mach_number"
                               name="mach_number"
                               value="{{ old('mach_number') }}"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all uppercase @error('mach_number') border-red-500 @enderror"
                               placeholder="e.g. MC-001, L1-ASS-01"
                               required>
                        @error('mach_number')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mach Type -->
                    @php
                        $machTypeList = ['HUXB','HUXC','MUXC','JUPC','JUPB','BA','CA','IPH','ISC','NDB','NDE','SHX','BF','BFM','HENX','KT','HKL','HKM','Others'];
                        $selectedMachType = old('mach_type', '');
                    @endphp
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Mach Type <span class="text-red-500">*</span>
                        </label>
                        <input type="hidden" name="mach_type" id="mach_type_value" value="{{ $selectedMachType }}">
                        <div class="relative" id="machTypeDropdown">
                            <button type="button" onclick="machTypeToggle()"
                                    class="w-full px-4 py-3 rounded-xl border {{ $errors->has('mach_type') ? 'border-red-500' : 'border-slate-200' }} bg-white text-left flex items-center justify-between hover:border-blue-400 transition-all outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                                <span id="machTypeLabel" class="{{ $selectedMachType ? 'text-slate-700' : 'text-slate-400' }}">
                                    {{ $selectedMachType ?: 'Pilih Mach Type...' }}
                                </span>
                                <i class="fas fa-chevron-down text-slate-400 text-xs transition-transform duration-200" id="machTypeChevron"></i>
                            </button>
                            <div id="machTypePanel" class="hidden absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden">
                                <div class="p-2 border-b border-slate-100">
                                    <input type="text" id="machTypeSearch" placeholder="Search type..."
                                           oninput="machTypeFilter(this.value)"
                                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 outline-none focus:border-blue-500">
                                </div>
                                <ul id="machTypeList" class="max-h-52 overflow-y-auto py-1">
                                    @foreach($machTypeList as $type)
                                    <li class="mach-type-item">
                                        <button type="button" onclick="machTypeSelect('{{ $type }}')"
                                                class="w-full text-left px-4 py-2.5 text-sm {{ $selectedMachType === $type ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-700 hover:bg-slate-50' }} transition-colors">
                                            {{ $type }}
                                        </button>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @error('mach_type')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Mach Area -->
                <div>
                    <label for="mach_area" class="block text-sm font-medium text-slate-700 mb-2">
                        Mach Area <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="mach_area"
                           name="mach_area"
                           value="{{ old('mach_area') }}"
                           list="mach-area-list"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all @error('mach_area') border-red-500 @enderror"
                           placeholder="e.g. Line 1, Line 2, Warehouse"
                           required>
                    <datalist id="mach-area-list">
                        @foreach($machAreas as $area)
                            <option value="{{ $area }}">
                        @endforeach
                    </datalist>
                    @error('mach_area')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('machines.index') }}"
                       class="px-5 py-2.5 text-slate-600 bg-slate-100 rounded-xl font-medium hover:bg-slate-200 transition-all">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-medium hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg shadow-blue-500/25">
                        <i class="fas fa-save mr-2"></i> Save Machine
                    </button>
                </div>
            </form>
        </div>
    </div>

<script>
function machTypeToggle() {
    const panel = document.getElementById('machTypePanel');
    const chevron = document.getElementById('machTypeChevron');
    const isHidden = panel.classList.contains('hidden');
    panel.classList.toggle('hidden');
    chevron.classList.toggle('rotate-180');
    if (isHidden) {
        const search = document.getElementById('machTypeSearch');
        search.value = '';
        machTypeFilter('');
        search.focus();
    }
}

function machTypeSelect(value) {
    document.getElementById('mach_type_value').value = value;
    const label = document.getElementById('machTypeLabel');
    label.textContent = value;
    label.className = 'text-slate-700';
    document.getElementById('machTypePanel').classList.add('hidden');
    document.getElementById('machTypeChevron').classList.remove('rotate-180');
    document.querySelectorAll('.mach-type-item button').forEach(btn => {
        const isSelected = btn.textContent.trim() === value;
        btn.className = 'w-full text-left px-4 py-2.5 text-sm transition-colors ' +
            (isSelected ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-700 hover:bg-slate-50');
    });
}

function machTypeFilter(query) {
    document.querySelectorAll('.mach-type-item').forEach(item => {
        const text = item.querySelector('button').textContent.trim().toLowerCase();
        item.style.display = text.includes(query.toLowerCase()) ? '' : 'none';
    });
}

document.addEventListener('click', function(e) {
    const dd = document.getElementById('machTypeDropdown');
    if (dd && !dd.contains(e.target)) {
        document.getElementById('machTypePanel').classList.add('hidden');
        document.getElementById('machTypeChevron').classList.remove('rotate-180');
    }
});
</script>

</x-layouts.app>
