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
                @if($work_order->status !== 'Closed' || auth()->user()->isAdmin())
                <button onclick="openRepairModal()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 text-amber-700 border border-amber-200 rounded-xl font-medium hover:bg-amber-100 transition-all">
                    <i class="fas fa-tools"></i> Repair
                </button>
                <a href="{{ route('work-orders.edit', $work_order) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 border border-blue-200 rounded-xl font-medium hover:bg-blue-100 transition-all">
                    <i class="fas fa-edit"></i> Edit
                </a>
                @endif
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
                @if($work_order->status !== 'Closed' || auth()->user()->isAdmin())
                <button onclick="openRepairModal()"
                        class="text-xs text-amber-600 hover:text-amber-800 font-medium underline underline-offset-2">
                    Edit
                </button>
                @endif
            </div>
            {{-- Row 1: Tanggal + PIC --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Tanggal Assembling</p>
                    <p class="text-sm font-semibold text-slate-800">{{ $work_order->tanggal_assembling->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">PIC Assembling</p>
                    @if($picAsmNames->isNotEmpty())
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($picAsmNames as $name)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                            {{ $name }}
                        </span>
                        @endforeach
                    </div>
                    @else
                    <p class="text-sm text-slate-500">-</p>
                    @endif
                </div>
            </div>

            {{-- Row 2: Action --}}
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Action</p>
                <p class="text-sm text-slate-800 whitespace-pre-wrap">{{ $work_order->action_assembling ?: '-' }}</p>
            </div>

            {{-- Row 3: Remark --}}
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Remark Assembling</p>
                <p class="text-sm text-slate-800 whitespace-pre-wrap">{{ $work_order->remark_assembling ?: '-' }}</p>
            </div>

            {{-- Row 4: Foto --}}
            @if($work_order->foto_kerusakan)
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide mb-2">Foto Kerusakan</p>
                <a href="{{ route('storage.serve', $work_order->foto_kerusakan) }}" target="_blank"
                   class="inline-block">
                    <img src="{{ route('storage.serve', $work_order->foto_kerusakan) }}"
                         alt="Foto Kerusakan"
                         class="max-h-64 w-auto rounded-xl border border-slate-200 object-contain hover:opacity-90 transition-opacity shadow-sm">
                </a>
            </div>
            @endif

            {{-- Footer --}}
            <div class="border-t border-slate-100 pt-3 flex flex-wrap gap-4 text-xs text-slate-400">
                <span><i class="fas fa-user mr-1"></i>Diisi oleh: <strong class="text-slate-600">{{ $work_order->repairedBy->name ?? '-' }}</strong></span>
                <span><i class="fas fa-clock mr-1"></i>Pada: {{ $work_order->repaired_at?->format('d/m/Y H:i') }}</span>
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
         class="fixed inset-0 z-50 hidden flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 overflow-hidden">
        <!-- Inner: stops propagation so clicking modal doesn't close -->
        <div class="relative bg-white w-full max-w-2xl rounded-t-2xl sm:rounded-2xl shadow-2xl
                    flex flex-col max-h-[80vh] sm:max-h-[90vh]"
             id="repairModalBox"
             onclick="event.stopPropagation()">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 flex-shrink-0">
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

            <!-- Form (scrollable) -->
            <form action="{{ route('work-orders.repair', $work_order) }}" method="POST"
                  enctype="multipart/form-data" class="px-6 py-5 space-y-5 overflow-y-auto flex-1 min-h-0">
                @csrf

                <!-- Tanggal Assembling -->
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

                <!-- Action -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Action <span class="text-red-500">*</span>
                    </label>
                    <textarea name="action_assembling" rows="2" required
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition-all resize-none @error('action_assembling') border-red-500 @enderror"
                              placeholder="Deskripsikan tindakan yang dilakukan...">{{ old('action_assembling', $work_order->action_assembling) }}</textarea>
                    @error('action_assembling') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- PIC Assembling: inline collapsible dropdown -->
                <div id="picAsmWrapper">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        PIC Assembling <span class="text-red-500">*</span>
                    </label>
                    @error('pic_assembling') <p class="mb-1 text-xs text-red-500">{{ $message }}</p> @enderror

                    <!-- Trigger -->
                    <div id="picAsmTrigger"
                         onclick="picAsmToggleDropdown(event)"
                         class="min-h-[46px] w-full px-3 py-2 rounded-xl border border-slate-200 bg-white flex flex-wrap gap-1.5 items-center cursor-pointer hover:border-amber-400 transition-all select-none @error('pic_assembling') border-red-400 @enderror">
                        <div id="picAsmTags" class="flex flex-wrap gap-1.5 flex-1 items-center pointer-events-none">
                            <span id="picAsmPlaceholder" class="text-slate-400 text-sm">Pilih PIC Assembling...</span>
                        </div>
                        <i id="picAsmChevron" class="fas fa-chevron-down text-slate-400 text-xs flex-shrink-0 transition-transform duration-200 pointer-events-none"></i>
                    </div>

                    <!-- Inline collapsible panel (in normal flow, modal scrolls) -->
                    <div id="picAsmDropdown" class="hidden mt-1 border border-slate-200 rounded-xl overflow-hidden bg-white shadow-md">
                        <div class="p-2 bg-slate-50 border-b border-slate-200">
                            <input type="text" id="picAsmSearch" placeholder="Search nama..."
                                   oninput="picAsmFilter(this.value)"
                                   class="w-full px-3 py-1.5 text-sm rounded-lg border border-slate-200 outline-none focus:border-amber-500 bg-white">
                        </div>
                        <ul id="picAsmList" class="max-h-36 overflow-y-auto divide-y divide-slate-50">
                            @foreach($users as $u)
                            <li class="pic-asm-item" data-name="{{ strtolower($u->name) }}">
                                <button type="button"
                                        onclick="picAsmToggleUser({{ $u->id }}, '{{ addslashes($u->name) }}')"
                                        id="picAsmBtn_{{ $u->id }}"
                                        class="w-full text-left px-4 py-2.5 text-sm transition-colors flex items-center justify-between
                                               {{ in_array($u->id, $picAsmIds) ? 'bg-amber-50 text-amber-800 font-medium' : 'text-slate-700 hover:bg-slate-50' }}">
                                    <span>{{ $u->name }}</span>
                                    <i id="picAsmCheck_{{ $u->id }}" class="fas fa-check text-amber-500 {{ in_array($u->id, $picAsmIds) ? '' : 'invisible' }}"></i>
                                </button>
                            </li>
                            @endforeach
                        </ul>
                    </div>

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
                        <img src="{{ route('storage.serve', $work_order->foto_kerusakan) }}"
                             class="h-16 w-16 object-cover rounded-lg border border-slate-200">
                        <span class="text-xs text-slate-500">Foto saat ini. Upload baru untuk mengganti.</span>
                    </div>
                    @endif

                    <!-- Hidden file input (used by both buttons) -->
                    <input type="file" id="fotoInput" name="foto_kerusakan" accept="image/*" class="hidden"
                           onchange="updateFotoLabel(this)">
                    <!-- Hidden camera input -->
                    <input type="file" id="fotoCamera" name="foto_kerusakan" accept="image/*" capture="environment" class="hidden"
                           onchange="updateFotoLabel(this)">

                    <div class="grid grid-cols-2 gap-2">
                        <label for="fotoInput"
                               class="flex flex-col items-center gap-1.5 px-3 py-3 rounded-xl border-2 border-dashed border-slate-200 hover:border-amber-400 cursor-pointer transition-all text-center">
                            <i class="fas fa-image text-slate-400 text-xl"></i>
                            <span class="text-xs text-slate-500 font-medium">Upload File</span>
                        </label>
                        <label for="fotoCamera"
                               class="flex flex-col items-center gap-1.5 px-3 py-3 rounded-xl border-2 border-dashed border-slate-200 hover:border-amber-400 cursor-pointer transition-all text-center">
                            <i class="fas fa-camera text-slate-400 text-xl"></i>
                            <span class="text-xs text-slate-500 font-medium">Kamera</span>
                        </label>
                    </div>
                    <p id="fotoLabel" class="mt-1.5 text-xs text-slate-400 truncate"></p>
                    @error('foto_kerusakan') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Status — di paling bawah -->
                <div class="pt-1">
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

        <!-- Closed confirmation — absolute over modal box, no stacking context issues -->
        <div id="closedConfirmOverlay"
             class="hidden absolute inset-0 z-10 bg-black/60 rounded-t-2xl sm:rounded-2xl flex items-center justify-center px-6 py-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xs sm:max-w-sm p-5 space-y-4">
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
                    <button type="button" onclick="cancelClose()"
                            class="flex-1 px-4 py-2.5 text-slate-600 bg-slate-100 rounded-xl font-medium hover:bg-slate-200 transition-all">
                        Batal
                    </button>
                    <button type="button" onclick="confirmClose()"
                            class="flex-1 px-4 py-2.5 bg-red-500 text-white rounded-xl font-medium hover:bg-red-600 transition-all">
                        Ya, Close
                    </button>
                </div>
            </div>
        </div>
    </div>

<script>
// ===================== MODAL OPEN/CLOSE =====================
function openRepairModal() {
    const overlay = document.getElementById('repairModalOverlay');
    const box = document.getElementById('repairModalBox');
    // Use window.innerHeight (actual visible height, excludes browser chrome on mobile)
    box.style.maxHeight = Math.floor(window.innerHeight * 0.88) + 'px';
    overlay.classList.remove('hidden');
    overlay.onclick = function(e) {
        if (e.target === overlay) closeRepairModal();
    };
    document.body.style.overflow = 'hidden';
}
function closeRepairModal() {
    document.getElementById('repairModalOverlay').classList.add('hidden');
    document.getElementById('picAsmDropdown')?.classList.add('hidden');
    document.getElementById('picAsmChevron')?.classList.remove('rotate-180');
    document.body.style.overflow = '';
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
const picAsmSelected = new Map(); // id -> name
@foreach($users as $u)
@if(in_array($u->id, $picAsmIds))
picAsmSelected.set({{ $u->id }}, '{{ addslashes($u->name) }}');
@endif
@endforeach

function picAsmRender() {
    const tagsBox   = document.getElementById('picAsmTags');
    const placeholder = document.getElementById('picAsmPlaceholder');
    const hiddenBox = document.getElementById('picAsmHidden');

    // Remove old tags but keep placeholder
    tagsBox.querySelectorAll('.pic-asm-tag').forEach(t => t.remove());

    if (picAsmSelected.size > 0) {
        placeholder?.classList.add('hidden');
        picAsmSelected.forEach((name, id) => {
            const tag = document.createElement('span');
            tag.className = 'pic-asm-tag inline-flex items-center gap-1 bg-amber-100 text-amber-800 text-xs font-medium px-2.5 py-1 rounded-full';
            tag.innerHTML = `${name} <button type="button" onclick="event.stopPropagation();picAsmRemove(${id})" class="ml-0.5 text-amber-600 hover:text-amber-900 leading-none font-bold">&times;</button>`;
            tagsBox.appendChild(tag);
        });
    } else {
        placeholder?.classList.remove('hidden');
    }

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

function picAsmToggleDropdown(e) {
    e.stopPropagation();
    const dropdown = document.getElementById('picAsmDropdown');
    const chevron  = document.getElementById('picAsmChevron');
    const isHidden = dropdown.classList.contains('hidden');

    if (isHidden) {
        dropdown.classList.remove('hidden');
        chevron?.classList.add('rotate-180');
        const search = document.getElementById('picAsmSearch');
        if (search) { search.value = ''; picAsmFilter(''); setTimeout(() => search.focus(), 50); }
    } else {
        dropdown.classList.add('hidden');
        chevron?.classList.remove('rotate-180');
    }
}

// Close dropdown when clicking outside the wrapper
document.addEventListener('click', function(e) {
    const wrapper  = document.getElementById('picAsmWrapper');
    const dropdown = document.getElementById('picAsmDropdown');
    if (!dropdown || dropdown.classList.contains('hidden')) return;
    if (!wrapper || !wrapper.contains(e.target)) {
        dropdown.classList.add('hidden');
        document.getElementById('picAsmChevron')?.classList.remove('rotate-180');
    }
});

function picAsmToggleUser(id, name) {
    const btn   = document.getElementById('picAsmBtn_' + id);
    const check = document.getElementById('picAsmCheck_' + id);
    if (picAsmSelected.has(id)) {
        picAsmSelected.delete(id);
        check?.classList.add('invisible');
        btn?.classList.remove('bg-amber-50', 'text-amber-800', 'font-medium');
        btn?.classList.add('text-slate-700');
    } else {
        picAsmSelected.set(id, name);
        check?.classList.remove('invisible');
        btn?.classList.add('bg-amber-50', 'text-amber-800', 'font-medium');
        btn?.classList.remove('text-slate-700');
    }
    picAsmRender();
}

function picAsmRemove(id) {
    picAsmSelected.delete(id);
    document.getElementById('picAsmCheck_' + id)?.classList.add('invisible');
    const btn = document.getElementById('picAsmBtn_' + id);
    btn?.classList.remove('bg-amber-50', 'text-amber-800', 'font-medium');
    btn?.classList.add('text-slate-700');
    picAsmRender();
}

function picAsmFilter(q) {
    document.querySelectorAll('.pic-asm-item').forEach(item => {
        item.style.display = item.dataset.name.includes(q.toLowerCase()) ? '' : 'none';
    });
}

document.addEventListener('DOMContentLoaded', picAsmRender);

// ===================== FOTO LABEL =====================
function updateFotoLabel(input) {
    const lbl = document.getElementById('fotoLabel');
    if (input.files[0]) {
        lbl.textContent = input.files[0].name;
        // Sync: copy the chosen file to the other input so only 1 named input submits
        // We rename both to 'foto_kerusakan' but only the last-changed matters;
        // simpler: disable the other input so form doesn't send an empty file field
        const other = input.id === 'fotoInput' ? document.getElementById('fotoCamera') : document.getElementById('fotoInput');
        other.disabled = true;
        input.disabled = false;
    } else {
        lbl.textContent = '';
    }
}
</script>

</x-layouts.app>
