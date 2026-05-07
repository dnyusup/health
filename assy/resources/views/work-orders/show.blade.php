<x-layouts.app>
    <x-slot:title>Detail Work Order</x-slot:title>

    @php
        function statusBadgeClass($s) {
            return match($s) {
                'Open'        => 'bg-emerald-100 text-emerald-700',
                'On Progress' => 'bg-amber-100 text-amber-700',
                'Closed'      => 'bg-slate-100 text-slate-600',
                default       => 'bg-slate-100 text-slate-500',
            };
        }
        $picAsmIds   = $work_order->pic_assembling ?? [];
        $picAsmNames = $users->whereIn('id', $picAsmIds)->pluck('name');
    @endphp

    <div class="max-w-4xl mx-auto space-y-6">

        @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 flex items-center gap-3">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif

        <!-- Header -->
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-4">
                <a href="{{ route('work-orders.index') }}"
                   class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">
                        {{ $work_order->order_number ?: 'Work Order #'.$work_order->id }}
                    </h1>
                    <p class="text-slate-500 mt-1">Detail Work Order Pembongkaran</p>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold {{ statusBadgeClass($work_order->status) }}">
                    {{ $work_order->status }}
                </span>
                <button onclick="openRepairModal()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 text-amber-700 border border-amber-200 rounded-xl font-medium hover:bg-amber-100 transition-all">
                    <i class="fas fa-tools"></i> Repair
                </button>
                <a href="{{ route('work-orders.edit', $work_order) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 border border-blue-200 rounded-xl font-medium hover:bg-blue-100 transition-all">
                    <i class="fas fa-edit"></i> Edit
                </a>
                @if(auth()->user()->isAdmin())
                <form action="{{ route('work-orders.destroy', $work_order) }}" method="POST"
                      onsubmit="return confirm('Yakin ingin menghapus work order ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-700 border border-red-200 rounded-xl font-medium hover:bg-red-100 transition-all">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
                @endif
            </div>
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Card 1: Order Info -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Informasi Order</h2>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Tanggal Bongkar</span>
                        <span class="font-semibold text-slate-800">{{ $work_order->tanggal_bongkar?->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Order Number</span>
                        <span class="font-semibold text-slate-800">{{ $work_order->order_number ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Order Type</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold
                            {{ $work_order->order_type === 'ZSPM' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                            {{ $work_order->order_type }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Status</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ statusBadgeClass($work_order->status) }}">
                            {{ $work_order->status }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">PIC Bongkar</span>
                        <span class="font-semibold text-slate-800">{{ $work_order->pic->name ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Card 2: Machine Info -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 space-y-4">
                <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Informasi Mesin</h2>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Mach Number</span>
                        <span class="font-semibold text-slate-800">{{ $work_order->mach_number }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Mach Type</span>
                        <span class="text-slate-700">{{ $work_order->mach_type }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Pos</span>
                        <span class="text-slate-700">{{ $work_order->pos ?: '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Part Info -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 space-y-4">
            <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Informasi Part</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex justify-between items-start">
                    <span class="text-sm text-slate-500">Part ID</span>
                    <span class="font-mono font-semibold text-slate-800">{{ $work_order->part_id ?: '-' }}</span>
                </div>
                <div class="flex justify-between items-start">
                    <span class="text-sm text-slate-500">Category</span>
                    <span class="text-slate-700">{{ $work_order->category ?: '-' }}</span>
                </div>
                <div class="flex justify-between items-start md:col-span-2">
                    <span class="text-sm text-slate-500">Part Name</span>
                    <span class="text-slate-700 text-right">{{ $work_order->part_name ?: '-' }}</span>
                </div>
                <div class="flex justify-between items-start md:col-span-2">
                    <span class="text-sm text-slate-500">Part Detail</span>
                    <span class="text-slate-700 text-right">{{ $work_order->part_detail ?: '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Card 4: Kerusakan + Remark -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 space-y-4">
            <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Catatan</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Kerusakan</p>
                    <p class="text-slate-800">{{ $work_order->kerusakan ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500 mb-1">Remark Pembongkaran</p>
                    <p class="text-slate-800">{{ $work_order->remark ?: '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Card 5: Assembling / Repair Info (only if filled) -->
        @if($work_order->tanggal_assembling)
        <div class="bg-white rounded-2xl shadow-sm border border-amber-200 p-5 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-amber-600 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-tools"></i> Informasi Assembling / Repair
                </h2>
                <button onclick="openRepairModal()"
                        class="text-xs text-amber-600 hover:text-amber-800 font-medium underline underline-offset-2">
                    Edit
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex justify-between items-start">
                    <span class="text-sm text-slate-500">Tanggal Assembling</span>
                    <span class="font-semibold text-slate-800">{{ $work_order->tanggal_assembling->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between items-start">
                    <span class="text-sm text-slate-500">PIC Assembling</span>
                    <span class="text-slate-800 text-right">
                        @if($picAsmNames->isNotEmpty())
                            {{ $picAsmNames->implode(', ') }}
                        @else
                            -
                        @endif
                    </span>
                </div>
                <div class="flex justify-between items-start md:col-span-2">
                    <span class="text-sm text-slate-500">Action</span>
                    <span class="text-slate-800 text-right">{{ $work_order->action_assembling ?: '-' }}</span>
                </div>
                <div class="flex justify-between items-start md:col-span-2">
                    <span class="text-sm text-slate-500">Remark Assembling</span>
                    <span class="text-slate-800 text-right">{{ $work_order->remark_assembling ?: '-' }}</span>
                </div>
            </div>
            @if($work_order->foto_kerusakan)
            <div>
                <p class="text-sm text-slate-500 mb-2">Foto Kerusakan</p>
                <a href="{{ asset('storage/'.$work_order->foto_kerusakan) }}" target="_blank">
                    <img src="{{ asset('storage/'.$work_order->foto_kerusakan) }}"
                         alt="Foto Kerusakan"
                         class="max-h-60 rounded-xl border border-slate-200 object-contain hover:opacity-90 transition-opacity">
                </a>
            </div>
            @endif
            <div class="border-t border-slate-100 pt-3 flex gap-6 text-xs text-slate-400">
                <span>Diisi oleh: {{ $work_order->repairedBy->name ?? '-' }}</span>
                <span>Pada: {{ $work_order->repaired_at?->format('d/m/Y H:i') }}</span>
            </div>
        </div>
        @endif

        <!-- Meta -->
        <div class="text-xs text-slate-400 flex gap-6 flex-wrap">
            <span>Dibuat oleh: {{ $work_order->creator->name ?? '-' }}</span>
            <span>Dibuat pada: {{ $work_order->created_at?->format('d/m/Y H:i') }}</span>
            <span>Diupdate: {{ $work_order->updated_at?->format('d/m/Y H:i') }}</span>
        </div>
    </div>

    <!-- ===================== REPAIR MODAL ===================== -->
    <div id="repairModalOverlay"
         class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
         onclick="handleOverlayClick(event)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
             id="repairModalBox">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 sticky top-0 bg-white rounded-t-2xl z-10">
                <div>
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-tools text-amber-500"></i> Form Repair / Assembling
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $work_order->order_number ?: 'WO #'.$work_order->id }}</p>
                </div>
                <button onclick="closeRepairModal()" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-all">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Form -->
            <form action="{{ route('work-orders.repair', $work_order) }}" method="POST"
                  enctype="multipart/form-data" class="px-6 py-5 space-y-5">
                @csrf

                <!-- Row 1: Tanggal + Status -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Tanggal Assembling <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_assembling"
                               value="{{ old('tanggal_assembling', $work_order->tanggal_assembling?->format('Y-m-d')) }}"
                               required
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition-all @error('tanggal_assembling') border-red-500 @enderror">
                        @error('tanggal_assembling') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="repairStatus" required onchange="handleStatusChange(this)"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition-all bg-white">
                            <option value="Open"        {{ old('status', $work_order->status) === 'Open'        ? 'selected' : '' }}>Open</option>
                            <option value="On Progress" {{ old('status', $work_order->status) === 'On Progress' ? 'selected' : '' }}>On Progress</option>
                            <option value="Closed"      {{ old('status', $work_order->status) === 'Closed'      ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                </div>

                <!-- Action -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Action</label>
                    <textarea name="action_assembling" rows="2"
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition-all resize-none"
                              placeholder="Deskripsikan tindakan yang dilakukan...">{{ old('action_assembling', $work_order->action_assembling) }}</textarea>
                </div>

                <!-- PIC Assembling Multi-Select -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        PIC Assembling <span class="text-red-500">*</span>
                    </label>
                    @error('pic_assembling') <p class="mb-1 text-xs text-red-500">{{ $message }}</p> @enderror

                    <!-- Selected Tags + Trigger -->
                    <div id="picAsmTagsBox"
                         onclick="picAsmToggle(event)"
                         class="min-h-[46px] px-3 py-2 rounded-xl border border-slate-200 bg-white flex flex-wrap gap-2 items-start cursor-pointer hover:border-amber-400 transition-all @error('pic_assembling') border-red-500 @enderror">
                        <span id="picAsmPlaceholder" class="text-slate-400 text-sm self-center {{ count($picAsmIds) > 0 ? 'hidden' : '' }}">
                            Pilih PIC Assembling...
                        </span>
                    </div>

                    <!-- Dropdown Panel -->
                    <div id="picAsmPanel" class="hidden relative z-40 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-2 border-b border-slate-100">
                            <input type="text" id="picAsmSearch" placeholder="Search nama..."
                                   oninput="picAsmFilter(this.value)"
                                   class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 outline-none focus:border-amber-500">
                        </div>
                        <ul id="picAsmList" class="max-h-48 overflow-y-auto py-1">
                            @foreach($users as $u)
                            <li class="pic-asm-item" data-name="{{ strtolower($u->name) }}">
                                <button type="button"
                                        onclick="picAsmToggleUser({{ $u->id }}, '{{ addslashes($u->name) }}')"
                                        id="picAsmBtn_{{ $u->id }}"
                                        class="w-full text-left px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors flex items-center justify-between">
                                    <span>{{ $u->name }}</span>
                                    <i id="picAsmCheck_{{ $u->id }}" class="fas fa-check text-amber-500 {{ in_array($u->id, $picAsmIds) ? '' : 'hidden' }}"></i>
                                </button>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <!-- Hidden inputs will be injected here by JS -->
                    <div id="picAsmHidden"></div>
                </div>

                <!-- Remark Assembling -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Remark Assembling</label>
                    <textarea name="remark_assembling" rows="2"
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition-all resize-none"
                              placeholder="Catatan tambahan...">{{ old('remark_assembling', $work_order->remark_assembling) }}</textarea>
                </div>

                <!-- Foto Kerusakan -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Foto Kerusakan
                        <span class="text-slate-400 font-normal">(opsional, max 5MB)</span>
                    </label>
                    @if($work_order->foto_kerusakan)
                    <div class="mb-3 flex items-center gap-3">
                        <img src="{{ asset('storage/'.$work_order->foto_kerusakan) }}"
                             class="h-16 w-16 object-cover rounded-lg border border-slate-200">
                        <span class="text-xs text-slate-500">Foto saat ini. Upload baru untuk mengganti.</span>
                    </div>
                    @endif
                    <label class="flex items-center gap-3 px-4 py-3 rounded-xl border-2 border-dashed border-slate-200 hover:border-amber-400 cursor-pointer transition-all">
                        <i class="fas fa-camera text-slate-400 text-lg"></i>
                        <span id="fotoLabel" class="text-sm text-slate-500">Pilih foto...</span>
                        <input type="file" name="foto_kerusakan" accept="image/*" class="hidden"
                               onchange="updateFotoLabel(this)">
                    </label>
                    @error('foto_kerusakan') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeRepairModal()"
                            class="px-5 py-2.5 text-slate-600 bg-slate-100 rounded-xl font-medium hover:bg-slate-200 transition-all">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 text-white rounded-xl font-medium hover:from-amber-600 hover:to-amber-700 transition-all shadow-lg shadow-amber-500/25">
                        <i class="fas fa-save mr-2"></i> Simpan Repair
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Closed confirmation dialog (hidden) -->
    <div id="closedConfirmOverlay"
         class="fixed inset-0 z-[60] hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-500"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-slate-800">Konfirmasi Close</h4>
                    <p class="text-sm text-slate-500">Work order akan ditutup permanen.</p>
                </div>
            </div>
            <p class="text-sm text-slate-600">Yakin ingin mengubah status menjadi <strong>Closed</strong>? Pastikan semua pekerjaan sudah selesai.</p>
            <div class="flex gap-3">
                <button onclick="cancelClose()"
                        class="flex-1 px-4 py-2.5 text-slate-600 bg-slate-100 rounded-xl font-medium hover:bg-slate-200 transition-all">
                    Batal
                </button>
                <button onclick="confirmClose()"
                        class="flex-1 px-4 py-2.5 bg-red-500 text-white rounded-xl font-medium hover:bg-red-600 transition-all">
                    Ya, Close
                </button>
            </div>
        </div>
    </div>

<script>
// ===================== MODAL OPEN/CLOSE =====================
function openRepairModal() {
    document.getElementById('repairModalOverlay').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeRepairModal() {
    document.getElementById('repairModalOverlay').classList.add('hidden');
    document.getElementById('picAsmPanel').classList.add('hidden');
    document.body.style.overflow = '';
}
function handleOverlayClick(e) {
    if (e.target === document.getElementById('repairModalOverlay')) {
        closeRepairModal();
    }
}
// Auto-open if validation errors
@if($errors->any())
document.addEventListener('DOMContentLoaded', openRepairModal);
@endif

// ===================== STATUS CLOSED CONFIRMATION =====================
let pendingClose = false;
function handleStatusChange(sel) {
    if (sel.value === 'Closed') {
        // revert temporarily while user confirms
        sel.value = '{{ $work_order->status }}';
        document.getElementById('closedConfirmOverlay').classList.remove('hidden');
    }
}
function cancelClose() {
    document.getElementById('closedConfirmOverlay').classList.add('hidden');
}
function confirmClose() {
    document.getElementById('closedConfirmOverlay').classList.add('hidden');
    document.getElementById('repairStatus').value = 'Closed';
}

// ===================== PIC ASSEMBLING MULTI-SELECT =====================
// Pre-populate from existing saved values
const picAsmSelected = new Map(); // id -> name
@foreach($users as $u)
@if(in_array($u->id, $picAsmIds))
picAsmSelected.set({{ $u->id }}, '{{ addslashes($u->name) }}');
@endif
@endforeach

function picAsmRender() {
    const tagsBox = document.getElementById('picAsmTagsBox');
    const placeholder = document.getElementById('picAsmPlaceholder');
    const hiddenBox = document.getElementById('picAsmHidden');

    // Remove old tags (keep placeholder)
    tagsBox.querySelectorAll('.pic-asm-tag').forEach(t => t.remove());

    // Render tags
    picAsmSelected.forEach((name, id) => {
        const tag = document.createElement('span');
        tag.className = 'pic-asm-tag inline-flex items-center gap-1 bg-amber-100 text-amber-800 text-xs font-medium px-2.5 py-1 rounded-full';
        tag.innerHTML = `${name} <button type="button" onclick="picAsmRemove(${id})" class="ml-0.5 text-amber-600 hover:text-amber-900 leading-none">&times;</button>`;
        tagsBox.appendChild(tag);
    });

    // Placeholder
    placeholder.classList.toggle('hidden', picAsmSelected.size > 0);

    // Hidden inputs
    hiddenBox.innerHTML = '';
    picAsmSelected.forEach((name, id) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'pic_assembling[]';
        input.value = id;
        hiddenBox.appendChild(input);
    });
}

function picAsmToggleUser(id, name) {
    if (picAsmSelected.has(id)) {
        picAsmSelected.delete(id);
        document.getElementById('picAsmCheck_' + id)?.classList.add('hidden');
    } else {
        picAsmSelected.set(id, name);
        document.getElementById('picAsmCheck_' + id)?.classList.remove('hidden');
    }
    picAsmRender();
}

function picAsmRemove(id) {
    picAsmSelected.delete(id);
    document.getElementById('picAsmCheck_' + id)?.classList.add('hidden');
    picAsmRender();
}

function picAsmToggle(e) {
    if (e && e.target.closest('.pic-asm-tag')) return; // don't open when clicking tag/X
    const panel = document.getElementById('picAsmPanel');
    panel.classList.toggle('hidden');
    if (!panel.classList.contains('hidden')) {
        const s = document.getElementById('picAsmSearch');
        s.value = ''; picAsmFilter(''); s.focus();
    }
}

function picAsmFilter(q) {
    document.querySelectorAll('.pic-asm-item').forEach(item => {
        item.style.display = item.dataset.name.includes(q.toLowerCase()) ? '' : 'none';
    });
}

// Close pic dropdown on click outside (but not when clicking inside modal)
document.addEventListener('click', function(e) {
    const panel = document.getElementById('picAsmPanel');
    const tagsBox = document.getElementById('picAsmTagsBox');
    if (!panel.contains(e.target) && !tagsBox.contains(e.target)) {
        panel.classList.add('hidden');
    }
});

// Init render on load
document.addEventListener('DOMContentLoaded', picAsmRender);

// ===================== FOTO LABEL =====================
function updateFotoLabel(input) {
    const lbl = document.getElementById('fotoLabel');
    lbl.textContent = input.files[0]?.name || 'Pilih foto...';
}
</script>

</x-layouts.app>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Mach Number</span>
                        <span class="font-semibold text-slate-800">{{ $work_order->mach_number }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Mach Type</span>
                        <span class="text-slate-700">{{ $work_order->mach_type }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Pos</span>
                        <span class="text-slate-700">{{ $work_order->pos ?: '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Part Info -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 space-y-4">
            <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Informasi Part</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex justify-between items-start">
                    <span class="text-sm text-slate-500">Part ID</span>
                    <span class="font-mono font-semibold text-slate-800">{{ $work_order->part_id ?: '-' }}</span>
                </div>
                <div class="flex justify-between items-start">
                    <span class="text-sm text-slate-500">Category</span>
                    <span class="text-slate-700">{{ $work_order->category ?: '-' }}</span>
                </div>
                <div class="flex justify-between items-start md:col-span-2">
                    <span class="text-sm text-slate-500">Part Name</span>
                    <span class="text-slate-700 text-right">{{ $work_order->part_name ?: '-' }}</span>
                </div>
                <div class="flex justify-between items-start md:col-span-2">
                    <span class="text-sm text-slate-500">Part Detail</span>
                    <span class="text-slate-700 text-right">{{ $work_order->part_detail ?: '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Card 4: Kerusakan + Remark -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 space-y-4">
            <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Catatan</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Kerusakan</p>
                    <p class="text-slate-800">{{ $work_order->kerusakan ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500 mb-1">Remark Pembongkaran</p>
                    <p class="text-slate-800">{{ $work_order->remark ?: '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Meta -->
        <div class="text-xs text-slate-400 flex gap-6">
            <span>Dibuat oleh: {{ $work_order->creator->name ?? '-' }}</span>
            <span>Dibuat pada: {{ $work_order->created_at?->format('d/m/Y H:i') }}</span>
            <span>Diupdate: {{ $work_order->updated_at?->format('d/m/Y H:i') }}</span>
        </div>
    </div>

</x-layouts.app>
