<x-layouts.app>
    <x-slot:title>Buat Work Order</x-slot:title>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-4">
            <a href="{{ route('work-orders.index') }}"
               class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Buat Work Order Pembongkaran</h1>
                <p class="text-slate-500 mt-1">Status akan otomatis <span class="text-emerald-600 font-semibold">Open</span> saat dibuat</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <form action="{{ route('work-orders.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- ROW 1: Tanggal, Order Number, Order Type --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Tanggal Bongkar <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_bongkar" value="{{ old('tanggal_bongkar') }}" required
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all @error('tanggal_bongkar') border-red-500 @enderror">
                        @error('tanggal_bongkar') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Order Number</label>
                        <input type="text" name="order_number" value="{{ old('order_number') }}"
                               placeholder="e.g. ZSPM-2025-001"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Order Type <span class="text-red-500">*</span>
                        </label>
                        <select name="order_type" required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all bg-white @error('order_type') border-red-500 @enderror">
                            <option value="">-- Pilih --</option>
                            <option value="ZSPM" {{ old('order_type') === 'ZSPM' ? 'selected' : '' }}>ZSPM</option>
                            <option value="ZSBM" {{ old('order_type') === 'ZSBM' ? 'selected' : '' }}>ZSBM</option>
                        </select>
                        @error('order_type') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- ROW 2: Mach Number + Mach Type --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Mach Number searchable dropdown --}}
                    @php $selMachine = old('machine_id', ''); @endphp
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Mach Number <span class="text-red-500">*</span>
                        </label>
                        <input type="hidden" name="machine_id" id="machine_id_value" value="{{ $selMachine }}">
                        <div class="relative" id="machineDropdown">
                            <button type="button" onclick="machineToggle()"
                                    class="w-full px-4 py-3 rounded-xl border {{ $errors->has('machine_id') ? 'border-red-500' : 'border-slate-200' }} bg-white text-left flex items-center justify-between hover:border-blue-400 transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none">
                                <span id="machineLabel" class="text-slate-400">Pilih Mach Number...</span>
                                <i class="fas fa-chevron-down text-slate-400 text-xs transition-transform duration-200" id="machineChevron"></i>
                            </button>
                            <div id="machinePanel" class="hidden absolute z-30 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden">
                                <div class="p-2 border-b border-slate-100">
                                    <input type="text" id="machineSearch" placeholder="Search mach number..."
                                           oninput="machineFilter(this.value)"
                                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 outline-none focus:border-blue-500">
                                </div>
                                <ul id="machineList" class="max-h-52 overflow-y-auto py-1">
                                    @foreach($machines as $m)
                                    <li class="machine-item">
                                        <button type="button"
                                                onclick="machineSelect({{ $m->id }}, '{{ $m->mach_number }}', '{{ $m->mach_type }}')"
                                                class="w-full text-left px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors flex items-center justify-between">
                                            <span class="font-medium">{{ $m->mach_number }}</span>
                                            <span class="text-xs text-slate-400">{{ $m->mach_type }}</span>
                                        </button>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @error('machine_id') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Mach Type</label>
                        <input type="text" id="mach_type_display" readonly
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-600 outline-none"
                               placeholder="Auto-fill dari Mach Number">
                    </div>
                </div>

                {{-- ROW 3: Pos --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Pos</label>
                        <input type="text" name="pos" value="{{ old('pos') }}"
                               placeholder="e.g. 1, 2, 3..."
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
                    </div>
                </div>

                {{-- ROW 4: Part ID lookup + auto-fill fields --}}
                <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50 space-y-4">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Informasi Part (opsional)</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Part ID AJAX search --}}
                        <div class="relative">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Part ID</label>
                            <input type="text" name="part_id" id="part_id_input"
                                   value="{{ old('part_id') }}"
                                   placeholder="Ketik Part ID untuk search..."
                                   autocomplete="off"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all uppercase">
                            <div id="partDropdown" class="hidden absolute z-30 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden">
                                <ul id="partList" class="max-h-52 overflow-y-auto py-1"></ul>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Category</label>
                            <input type="text" name="category" id="category_input" value="{{ old('category') }}"
                                   placeholder="Auto-fill atau manual"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Part Name</label>
                        <input type="text" name="part_name" id="part_name_input" value="{{ old('part_name') }}"
                               placeholder="Auto-fill atau manual"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Part Detail</label>
                        <input type="text" name="part_detail" id="part_detail_input" value="{{ old('part_detail') }}"
                               placeholder="Auto-fill atau manual"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
                    </div>
                </div>

                {{-- ROW 5: Kerusakan + Remark --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Kerusakan</label>
                        <input type="text" name="kerusakan" value="{{ old('kerusakan') }}"
                               placeholder="Deskripsi kerusakan"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Remark Pembongkaran</label>
                        <input type="text" name="remark" value="{{ old('remark') }}"
                               placeholder="Catatan tambahan"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
                    </div>
                </div>

                {{-- ROW 6: PIC Bongkar --}}
                @php $selPic = old('pic_bongkar', ''); $selPicName = ''; @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            PIC Bongkar <span class="text-red-500">*</span>
                        </label>
                        <input type="hidden" name="pic_bongkar" id="pic_value" value="{{ $selPic }}">
                        <div class="relative" id="picDropdown">
                            <button type="button" onclick="picToggle()"
                                    class="w-full px-4 py-3 rounded-xl border {{ $errors->has('pic_bongkar') ? 'border-red-500' : 'border-slate-200' }} bg-white text-left flex items-center justify-between hover:border-blue-400 transition-all focus:border-blue-500 outline-none">
                                <span id="picLabel" class="text-slate-400">Pilih PIC Bongkar...</span>
                                <i class="fas fa-chevron-down text-slate-400 text-xs transition-transform duration-200" id="picChevron"></i>
                            </button>
                            <div id="picPanel" class="hidden absolute z-30 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden">
                                <div class="p-2 border-b border-slate-100">
                                    <input type="text" id="picSearch" placeholder="Search nama..."
                                           oninput="picFilter(this.value)"
                                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 outline-none focus:border-blue-500">
                                </div>
                                <ul id="picList" class="max-h-52 overflow-y-auto py-1">
                                    @foreach($users as $user)
                                    <li class="pic-item">
                                        <button type="button"
                                                onclick="picSelect({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                                class="w-full text-left px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                                            {{ $user->name }}
                                        </button>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @error('pic_bongkar') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                    <a href="{{ route('work-orders.index') }}"
                       class="px-5 py-2.5 text-slate-600 bg-slate-100 rounded-xl font-medium hover:bg-slate-200 transition-all">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-medium hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg shadow-blue-500/25">
                        <i class="fas fa-save mr-2"></i> Simpan Work Order
                    </button>
                </div>
            </form>
        </div>
    </div>

<script>
// ===================== MACHINE DROPDOWN =====================
function machineToggle() {
    const panel = document.getElementById('machinePanel');
    const chevron = document.getElementById('machineChevron');
    panel.classList.toggle('hidden');
    chevron.classList.toggle('rotate-180');
    if (!panel.classList.contains('hidden')) {
        const s = document.getElementById('machineSearch');
        s.value = ''; machineFilter(''); s.focus();
    }
}
function machineSelect(id, num, type) {
    document.getElementById('machine_id_value').value = id;
    const lbl = document.getElementById('machineLabel');
    lbl.textContent = num + ' (' + type + ')';
    lbl.className = 'text-slate-700 font-medium';
    document.getElementById('mach_type_display').value = type;
    document.getElementById('machinePanel').classList.add('hidden');
    document.getElementById('machineChevron').classList.remove('rotate-180');
}
function machineFilter(q) {
    document.querySelectorAll('.machine-item').forEach(item => {
        item.style.display = item.textContent.toLowerCase().includes(q.toLowerCase()) ? '' : 'none';
    });
}
document.addEventListener('click', function(e) {
    const dd = document.getElementById('machineDropdown');
    if (dd && !dd.contains(e.target)) {
        document.getElementById('machinePanel')?.classList.add('hidden');
        document.getElementById('machineChevron')?.classList.remove('rotate-180');
    }
});

// ===================== PIC DROPDOWN =====================
function picToggle() {
    const panel = document.getElementById('picPanel');
    const chevron = document.getElementById('picChevron');
    panel.classList.toggle('hidden');
    chevron.classList.toggle('rotate-180');
    if (!panel.classList.contains('hidden')) {
        const s = document.getElementById('picSearch');
        s.value = ''; picFilter(''); s.focus();
    }
}
function picSelect(id, name) {
    document.getElementById('pic_value').value = id;
    const lbl = document.getElementById('picLabel');
    lbl.textContent = name;
    lbl.className = 'text-slate-700 font-medium';
    document.getElementById('picPanel').classList.add('hidden');
    document.getElementById('picChevron').classList.remove('rotate-180');
}
function picFilter(q) {
    document.querySelectorAll('.pic-item').forEach(item => {
        item.style.display = item.textContent.toLowerCase().includes(q.toLowerCase()) ? '' : 'none';
    });
}
document.addEventListener('click', function(e) {
    const dd = document.getElementById('picDropdown');
    if (dd && !dd.contains(e.target)) {
        document.getElementById('picPanel')?.classList.add('hidden');
        document.getElementById('picChevron')?.classList.remove('rotate-180');
    }
});

// ===================== PART ID AJAX LOOKUP =====================
let partTimer;
const partInput = document.getElementById('part_id_input');
const partDropdown = document.getElementById('partDropdown');
const partList = document.getElementById('partList');

partInput.addEventListener('input', function () {
    clearTimeout(partTimer);
    const q = this.value.trim();
    if (q.length < 1) { partDropdown.classList.add('hidden'); return; }
    partTimer = setTimeout(() => {
        fetch(`{{ route('api.part-lookup') }}?q=${encodeURIComponent(q)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(parts => {
            if (!parts.length) { partDropdown.classList.add('hidden'); return; }
            partList.innerHTML = parts.map(p => `
                <li>
                    <button type="button" onclick="selectPart('${p.part_id.replace(/'/g,"\\'")}','${(p.part_name||'').replace(/'/g,"\\'")}','${(p.category||'').replace(/'/g,"\\'")}','${(p.part_detail||'').replace(/'/g,"\\'")}' )"
                            class="w-full text-left px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors flex items-center justify-between">
                        <span class="font-mono font-semibold text-blue-700">${p.part_id}</span>
                        <span class="text-xs text-slate-500 truncate max-w-[180px]">${p.part_name||''}</span>
                    </button>
                </li>`).join('');
            partDropdown.classList.remove('hidden');
        });
    }, 300);
});

function selectPart(partId, partName, category, partDetail) {
    partInput.value = partId;
    document.getElementById('part_name_input').value = partName;
    document.getElementById('category_input').value = category;
    document.getElementById('part_detail_input').value = partDetail;
    partDropdown.classList.add('hidden');
}

document.addEventListener('click', function(e) {
    if (!partInput.contains(e.target) && !partDropdown.contains(e.target)) {
        partDropdown.classList.add('hidden');
    }
});
</script>

</x-layouts.app>
