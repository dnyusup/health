<x-layouts.app>
    <x-slot:title>Detail Work Order</x-slot:title>

    @php
        function statusBadgeClass($s) {
            return match($s) {
                'Open'        => 'bg-emerald-100 text-emerald-700',
                'On Progress' => 'bg-amber-100 text-amber-700',
                'Closed'      => 'bg-slate-100 text-slate-600',
                'Installed'   => 'bg-blue-100 text-blue-700',
                'Scrap'       => 'bg-red-100 text-red-700',
                default       => 'bg-slate-100 text-slate-500',
            };
        }
        $picAsmIds   = $work_order->pic_assembling ?? [];
        $picAsmNames = $users->whereIn('id', $picAsmIds)->pluck('name');
        $picPasangIds   = $work_order->pic_pasang ?? [];
        $picPasangNames = $users->whereIn('id', $picPasangIds)->pluck('name');
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
                @php $u = auth()->user(); @endphp
                @if($u->isAdmin() || ($u->isWorkshop() && $work_order->status !== 'Closed'))
                <button onclick="openRepairModal()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 text-amber-700 border border-amber-200 rounded-xl font-medium hover:bg-amber-100 transition-all">
                    <i class="fas fa-tools"></i> Repair
                </button>
                @endif
                @if($u->isAdmin() || $u->isShopfloor())
                <a href="{{ route('work-orders.edit', $work_order) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 border border-blue-200 rounded-xl font-medium hover:bg-blue-100 transition-all">
                    <i class="fas fa-edit"></i> Edit
                </a>
                @endif
                @if($u->isAdmin() || ($u->isShopfloor() && $work_order->status === 'Closed'))
                <button onclick="openInstallModal()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl font-medium hover:bg-emerald-100 transition-all">
                    <i class="fas fa-wrench"></i> Install
                </button>
                @endif
                @if($u->isAdmin())
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
                @if(auth()->user()->isAdmin() || auth()->user()->isWorkshop())
                <button onclick="openRepairModal()"
                        class="text-xs text-amber-600 hover:text-amber-800 font-medium underline underline-offset-2">
                    Edit
                </button>
                @endif
            </div>
            {{-- Row 1: Tanggal + PIC / Vendor --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Tanggal Assembling</p>
                    <p class="text-sm font-semibold text-slate-800">{{ $work_order->tanggal_assembling->format('d/m/Y') }}</p>
                </div>
                <div>
                    @if($work_order->repair_by_vendor)
                    <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Vendor</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-truck mr-1"></i>
                        {{ $work_order->repairVendor?->vendor_name ?? '-' }}
                    </span>
                    @else
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
                    @endif
                </div>
            </div>

            {{-- PO Number (if repair by vendor) --}}
            @if($work_order->repair_by_vendor && $work_order->po_number)
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">PO Number</p>
                <p class="text-sm font-semibold text-slate-800">{{ $work_order->po_number }}</p>
            </div>
            @endif

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

        <!-- Card 6: Informasi Pemasangan (only if filled) -->
        @if($work_order->tanggal_pasang)
        <div class="bg-white rounded-2xl shadow-sm border border-emerald-200 p-5 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-emerald-600 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-wrench"></i> Informasi Pemasangan
                </h2>
                @if(auth()->user()->isAdmin() || auth()->user()->isShopfloor())
                <button onclick="openInstallModal()"
                        class="text-xs text-emerald-600 hover:text-emerald-800 font-medium underline underline-offset-2">
                    Edit
                </button>
                @endif
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Tanggal Pasang</p>
                    <p class="text-sm font-semibold text-slate-800">{{ $work_order->tanggal_pasang->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">PIC Pasang</p>
                    @if($picPasangNames->isNotEmpty())
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($picPasangNames as $name)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                            {{ $name }}
                        </span>
                        @endforeach
                    </div>
                    @else
                    <p class="text-sm text-slate-500">-</p>
                    @endif
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-8 gap-y-4">
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Dipasang di Mesin</p>
                    <p class="text-sm font-semibold text-slate-800">{{ $work_order->install_mach_number ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Mach Type</p>
                    <p class="text-sm text-slate-700">{{ $work_order->install_mach_type ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Pos</p>
                    <p class="text-sm text-slate-700">{{ $work_order->install_pos ?: '-' }}</p>
                </div>
            </div>
            @if($work_order->remark_pemasangan)
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Remark Pemasangan</p>
                <p class="text-sm text-slate-800 whitespace-pre-wrap">{{ $work_order->remark_pemasangan }}</p>
            </div>
            @endif
            <div class="border-t border-slate-100 pt-3 flex flex-wrap gap-4 text-xs text-slate-400">
                <span><i class="fas fa-user mr-1"></i>Dipasang oleh: <strong class="text-slate-600">{{ $work_order->installedBy->name ?? '-' }}</strong></span>
                <span><i class="fas fa-clock mr-1"></i>Pada: {{ $work_order->installed_at?->format('d/m/Y H:i') }}</span>
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

                <!-- PIC Assembling / Vendor toggle -->
                <div id="picAsmWrapper">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-slate-700">
                            <span id="picAsmLabel">PIC Assembling</span> <span class="text-red-500" id="picAsmRequired">*</span>
                        </label>
                        <!-- Repair by Vendor toggle -->
                        <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                            <span class="text-xs text-slate-500 font-medium">Repair by Vendor</span>
                            <div class="relative">
                                <input type="checkbox" name="repair_by_vendor" id="repairByVendorToggle" value="1"
                                       class="sr-only peer"
                                       {{ old('repair_by_vendor', $work_order->repair_by_vendor) ? 'checked' : '' }}
                                       onchange="handleRepairByVendorToggle(this.checked)">
                                <div class="w-10 h-5 bg-slate-200 peer-checked:bg-amber-500 rounded-full transition-colors duration-200"></div>
                                <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-5"></div>
                            </div>
                        </label>
                    </div>

                    @error('pic_assembling') <p class="mb-1 text-xs text-red-500">{{ $message }}</p> @enderror

                    <!-- User multi-select (default) -->
                    <div id="picAsmUserSection">
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

                    <!-- Vendor select (shown when Repair by Vendor = true) -->
                    <div id="picAsmVendorSection" class="hidden">
                        @error('repair_vendor_id') <p class="mb-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        <select name="repair_vendor_id" id="repairVendorSelect"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition-all bg-white @error('repair_vendor_id') border-red-500 @enderror">
                            <option value="">-- Pilih Vendor --</option>
                            @foreach($vendors as $v)
                            <option value="{{ $v->id }}"
                                {{ old('repair_vendor_id', $work_order->repair_vendor_id) == $v->id ? 'selected' : '' }}>
                                {{ $v->vendor_id }} - {{ $v->vendor_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- PO Number (shown when Repair by Vendor = true) -->
                <div id="poNumberSection" class="hidden">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        PO Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="po_number" id="poNumberInput"
                           value="{{ old('po_number', $work_order->po_number) }}"
                           placeholder="Nomor PO dari vendor..."
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition-all @error('po_number') border-red-500 @enderror">
                    @error('po_number') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
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
                        <option value="">-- Pilih Status --</option>
                        <option value="On Progress" {{ old('status') === 'On Progress' ? 'selected' : '' }}>On Progress</option>
                        <option value="Closed"      {{ old('status') === 'Closed'      ? 'selected' : '' }}>Closed</option>
                        <option value="Scrap"        {{ old('status') === 'Scrap'        ? 'selected' : '' }}>Scrap</option>
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
                        <h4 class="font-semibold text-slate-800" id="closedConfirmTitle">Konfirmasi</h4>
                        <p class="text-sm text-slate-500" id="closedConfirmSubtitle">Perubahan status permanen.</p>
                    </div>
                </div>
                <p class="text-sm text-slate-600" id="closedConfirmMsg">Yakin ingin mengubah status menjadi <strong>Closed</strong>? Pastikan semua pekerjaan sudah selesai.</p>
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
    if (sel.value === 'Closed' || sel.value === 'Scrap') {
        // revert temporarily while user confirms
        const chosen = sel.value;
        sel.value = '{{ $work_order->status }}';
        document.getElementById('closedConfirmOverlay').classList.remove('hidden');
        // store chosen for confirmClose
        document.getElementById('closedConfirmOverlay').dataset.chosen = chosen;
        // update confirm message based on choice
        const msgEl = document.getElementById('closedConfirmMsg');
        const titleEl = document.getElementById('closedConfirmTitle');
        const subtitleEl = document.getElementById('closedConfirmSubtitle');
        if (chosen === 'Scrap') {
            if (titleEl) titleEl.textContent = 'Konfirmasi Scrap';
            if (subtitleEl) subtitleEl.textContent = 'Part akan ditandai tidak dapat digunakan.';
            if (msgEl) msgEl.innerHTML = 'Yakin tandai part ini sebagai <strong>Scrap</strong>? Part sudah tidak bisa direpair dan digunakan kembali.';
        } else {
            if (titleEl) titleEl.textContent = 'Konfirmasi Close';
            if (subtitleEl) subtitleEl.textContent = 'Work order akan ditutup permanen.';
            if (msgEl) msgEl.innerHTML = 'Yakin ingin mengubah status menjadi <strong>Closed</strong>? Pastikan semua pekerjaan sudah selesai.';
        }
    }
}
function cancelClose() {
    document.getElementById('closedConfirmOverlay').classList.add('hidden');
}
function confirmClose() {
    const overlay = document.getElementById('closedConfirmOverlay');
    const chosen = overlay.dataset.chosen || 'Closed';
    overlay.classList.add('hidden');
    document.getElementById('repairStatus').value = chosen;
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

document.addEventListener('DOMContentLoaded', function () {
    picAsmRender();
    // Init toggle state (restore after validation errors or existing data)
    const toggle = document.getElementById('repairByVendorToggle');
    if (toggle) handleRepairByVendorToggle(toggle.checked, true);
});

// ===================== REPAIR BY VENDOR TOGGLE =====================
function handleRepairByVendorToggle(isVendor, silent) {
    const userSection   = document.getElementById('picAsmUserSection');
    const vendorSection = document.getElementById('picAsmVendorSection');
    const poSection     = document.getElementById('poNumberSection');
    const vendorSelect  = document.getElementById('repairVendorSelect');
    const poInput       = document.getElementById('poNumberInput');
    const picLabel      = document.getElementById('picAsmLabel');

    if (isVendor) {
        userSection.classList.add('hidden');
        vendorSection.classList.remove('hidden');
        poSection.classList.remove('hidden');
        if (picLabel) picLabel.textContent = 'Vendor';
        if (vendorSelect) vendorSelect.required = true;
        if (poInput) poInput.required = true;
        // Close user dropdown if open
        document.getElementById('picAsmDropdown')?.classList.add('hidden');
        document.getElementById('picAsmChevron')?.classList.remove('rotate-180');
    } else {
        userSection.classList.remove('hidden');
        vendorSection.classList.add('hidden');
        poSection.classList.add('hidden');
        if (picLabel) picLabel.textContent = 'PIC Assembling';
        if (vendorSelect) vendorSelect.required = false;
        if (poInput) poInput.required = false;
    }
}

// ===================== FOTO LABEL =====================
function updateFotoLabel(input) {
    const lbl = document.getElementById('fotoLabel');
    if (input.files[0]) {
        lbl.textContent = input.files[0].name;
        const other = input.id === 'fotoInput' ? document.getElementById('fotoCamera') : document.getElementById('fotoInput');
        other.disabled = true;
        input.disabled = false;
    } else {
        lbl.textContent = '';
    }
}
</script>

    <!-- ===================== INSTALL MODAL ===================== -->
    @php $picPasangIdsOld = old('pic_pasang', $work_order->pic_pasang ?? []); @endphp
    <div id="installModalOverlay"
         class="fixed inset-0 z-50 hidden flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 overflow-hidden">
        <div class="relative bg-white w-full max-w-2xl rounded-t-2xl sm:rounded-2xl shadow-2xl
                    flex flex-col max-h-[80vh] sm:max-h-[90vh]"
             id="installModalBox"
             onclick="event.stopPropagation()">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 flex-shrink-0">
                <div>
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-wrench text-emerald-500"></i> Form Pemasangan
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $work_order->order_number ?: 'WO #'.$work_order->id }}</p>
                </div>
                <button onclick="closeInstallModal()" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-all">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <!-- Form -->
            <form id="installForm" action="{{ route('work-orders.install', $work_order) }}" method="POST"
                  class="px-6 py-5 space-y-5 overflow-y-auto flex-1 min-h-0">
                @csrf

                <!-- Tanggal Pasang -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Tanggal Pasang <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_pasang"
                           value="{{ old('tanggal_pasang', $work_order->tanggal_pasang?->format('Y-m-d')) }}"
                           required
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all @error('tanggal_pasang') border-red-500 @enderror">
                    @error('tanggal_pasang') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Dipasang di Mesin -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Dipasang di Mesin <span class="text-red-500">*</span>
                    </label>
                    <div class="relative" id="machineSearchWrapper">
                        <input type="text" id="machineSearchInput"
                               value="{{ $work_order->install_mach_number ?? '' }}"
                               placeholder="Ketik untuk mencari mesin..."
                               autocomplete="off"
                               oninput="machineFilter(this.value)"
                               onfocus="machineDropdownShow()"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all @error('install_machine_id') border-red-500 @enderror">
                        <input type="hidden" name="install_machine_id" id="installMachineId"
                               value="{{ old('install_machine_id', $work_order->install_machine_id) }}">
                        <div id="machineDropdown"
                             class="hidden absolute z-10 mt-1 w-full bg-white border border-slate-200 rounded-xl shadow-lg max-h-48 overflow-y-auto">
                            @foreach($machines as $m)
                            <button type="button"
                                    onclick="selectMachine({{ $m->id }}, '{{ addslashes($m->mach_number) }}', '{{ addslashes($m->mach_type) }}')"
                                    data-mach="{{ strtolower($m->mach_number) }} {{ strtolower($m->mach_type) }}"
                                    class="machine-option w-full text-left px-4 py-2.5 text-sm text-slate-700 hover:bg-emerald-50 flex items-center justify-between">
                                <span class="font-medium">{{ $m->mach_number }}</span>
                                <span class="text-slate-400 text-xs">{{ $m->mach_type }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @error('install_machine_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Pos -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Dipasang di Pos</label>
                    <input type="text" name="install_pos"
                           value="{{ old('install_pos', $work_order->install_pos) }}"
                           placeholder="Contoh: 1, 2A, ..."
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all">
                </div>

                <!-- PIC Pasang -->
                <div id="picPasangWrapper">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        PIC Pasang <span class="text-red-500">*</span>
                    </label>
                    @error('pic_pasang') <p class="mb-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    <div id="picPasangTrigger"
                         onclick="picPasangToggleDropdown(event)"
                         class="min-h-[46px] w-full px-3 py-2 rounded-xl border border-slate-200 bg-white flex flex-wrap gap-1.5 items-center cursor-pointer hover:border-emerald-400 transition-all select-none @error('pic_pasang') border-red-400 @enderror">
                        <div id="picPasangTags" class="flex flex-wrap gap-1.5 flex-1 items-center pointer-events-none">
                            <span id="picPasangPlaceholder" class="text-slate-400 text-sm">Pilih PIC Pasang...</span>
                        </div>
                        <i id="picPasangChevron" class="fas fa-chevron-down text-slate-400 text-xs flex-shrink-0 transition-transform duration-200 pointer-events-none"></i>
                    </div>
                    <div id="picPasangDropdown" class="hidden mt-1 border border-slate-200 rounded-xl overflow-hidden bg-white shadow-md">
                        <div class="p-2 bg-slate-50 border-b border-slate-200">
                            <input type="text" id="picPasangSearch" placeholder="Search nama..."
                                   oninput="picPasangFilter(this.value)"
                                   class="w-full px-3 py-1.5 text-sm rounded-lg border border-slate-200 outline-none focus:border-emerald-500 bg-white">
                        </div>
                        <ul id="picPasangList" class="max-h-36 overflow-y-auto divide-y divide-slate-50">
                            @foreach($users as $u)
                            <li class="pic-pasang-item" data-name="{{ strtolower($u->name) }}">
                                <button type="button"
                                        onclick="picPasangToggleUser({{ $u->id }}, '{{ addslashes($u->name) }}')"
                                        id="picPasangBtn_{{ $u->id }}"
                                        class="w-full text-left px-4 py-2.5 text-sm transition-colors flex items-center justify-between
                                               {{ in_array($u->id, $picPasangIdsOld) ? 'bg-emerald-50 text-emerald-800 font-medium' : 'text-slate-700 hover:bg-slate-50' }}">
                                    <span>{{ $u->name }}</span>
                                    <i id="picPasangCheck_{{ $u->id }}" class="fas fa-check text-emerald-500 {{ in_array($u->id, $picPasangIdsOld) ? '' : 'invisible' }}"></i>
                                </button>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div id="picPasangHidden"></div>
                </div>

                <!-- Remark Pemasangan -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Remark Pemasangan</label>
                    <textarea name="remark_pemasangan" rows="2"
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all resize-none"
                              placeholder="Catatan tambahan...">{{ old('remark_pemasangan', $work_order->remark_pemasangan) }}</textarea>
                </div>

            </form>
            <!-- Footer Buttons -->
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 flex-shrink-0">
                <button type="button" onclick="closeInstallModal()"
                        class="px-5 py-2.5 text-slate-600 bg-slate-100 rounded-xl font-medium hover:bg-slate-200 transition-all">
                    Cancel
                </button>
                <button type="button" onclick="showInstallConfirm()"
                        class="px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl font-medium hover:from-emerald-600 hover:to-emerald-700 transition-all shadow-lg shadow-emerald-500/25">
                    <i class="fas fa-save mr-2"></i> Simpan Pemasangan
                </button>
            </div>

            <!-- Install Confirmation Overlay -->
            <div id="installConfirmOverlay"
                 class="hidden absolute inset-0 z-10 bg-black/60 rounded-t-2xl sm:rounded-2xl flex items-center justify-center px-6 py-4">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xs sm:max-w-sm p-5 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <i class="fas fa-check-circle text-emerald-500"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-slate-800">Konfirmasi Pemasangan</h4>
                            <p class="text-sm text-slate-500">Tindakan ini tidak dapat dibatalkan.</p>
                        </div>
                    </div>
                    <p class="text-sm text-slate-600">Setelah disimpan, status work order akan berubah menjadi <strong>Installed</strong> dan tidak dapat diubah lagi. Part ini juga akan <strong>hilang dari daftar ready stock</strong>.</p>
                    <p class="text-sm text-slate-600">Pastikan semua data pemasangan sudah benar sebelum melanjutkan.</p>
                    <div class="flex gap-3">
                        <button type="button" onclick="cancelInstallConfirm()"
                                class="flex-1 px-4 py-2.5 text-slate-600 bg-slate-100 rounded-xl font-medium hover:bg-slate-200 transition-all">
                            Batal
                        </button>
                        <button type="button" onclick="confirmInstall()"
                                class="flex-1 px-4 py-2.5 bg-emerald-500 text-white rounded-xl font-medium hover:bg-emerald-600 transition-all">
                            Ya, Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
// ===================== INSTALL MODAL =====================
function openInstallModal() {
    const overlay = document.getElementById('installModalOverlay');
    const box = document.getElementById('installModalBox');
    box.style.maxHeight = Math.floor(window.innerHeight * 0.88) + 'px';
    overlay.classList.remove('hidden');
    overlay.onclick = function(e) {
        if (e.target === overlay) closeInstallModal();
    };
    document.body.style.overflow = 'hidden';
}
function closeInstallModal() {
    document.getElementById('installModalOverlay').classList.add('hidden');
    document.getElementById('picPasangDropdown')?.classList.add('hidden');
    document.getElementById('picPasangChevron')?.classList.remove('rotate-180');
    document.getElementById('machineDropdown')?.classList.add('hidden');
    document.body.style.overflow = '';
}
@if($errors->any() && old('_token') && request()->is('*install*'))
document.addEventListener('DOMContentLoaded', openInstallModal);
@endif

// ===================== INSTALL CONFIRMATION =====================
function showInstallConfirm() {
    const form = document.getElementById('installForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    document.getElementById('installConfirmOverlay').classList.remove('hidden');
}
function cancelInstallConfirm() {
    document.getElementById('installConfirmOverlay').classList.add('hidden');
}
function confirmInstall() {
    document.getElementById('installConfirmOverlay').classList.add('hidden');
    document.getElementById('installForm').submit();
}

// ===================== MACHINE SEARCH =====================
function machineFilter(q) {
    const lq = q.toLowerCase();
    document.querySelectorAll('.machine-option').forEach(opt => {
        opt.style.display = opt.dataset.mach.includes(lq) ? '' : 'none';
    });
}
function machineDropdownShow() {
    document.getElementById('machineDropdown')?.classList.remove('hidden');
}
function selectMachine(id, machNumber, machType) {
    document.getElementById('installMachineId').value = id;
    document.getElementById('machineSearchInput').value = machNumber + ' — ' + machType;
    document.getElementById('machineDropdown').classList.add('hidden');
}
document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('machineSearchWrapper');
    const dd = document.getElementById('machineDropdown');
    if (dd && !dd.classList.contains('hidden') && wrapper && !wrapper.contains(e.target)) {
        dd.classList.add('hidden');
    }
});

// ===================== PIC PASANG MULTI-SELECT =====================
const picPasangSelected = new Map();
@foreach($users as $u)
@if(in_array($u->id, $picPasangIdsOld))
picPasangSelected.set({{ $u->id }}, '{{ addslashes($u->name) }}');
@endif
@endforeach

function picPasangRender() {
    const tagsBox   = document.getElementById('picPasangTags');
    const placeholder = document.getElementById('picPasangPlaceholder');
    const hiddenBox = document.getElementById('picPasangHidden');
    tagsBox.querySelectorAll('.pic-pasang-tag').forEach(t => t.remove());
    if (picPasangSelected.size > 0) {
        placeholder?.classList.add('hidden');
        picPasangSelected.forEach((name, id) => {
            const tag = document.createElement('span');
            tag.className = 'pic-pasang-tag inline-flex items-center gap-1 bg-emerald-100 text-emerald-800 text-xs font-medium px-2.5 py-1 rounded-full';
            tag.innerHTML = `${name} <button type="button" onclick="event.stopPropagation();picPasangRemove(${id})" class="ml-0.5 text-emerald-600 hover:text-emerald-900 leading-none font-bold">&times;</button>`;
            tagsBox.appendChild(tag);
        });
    } else {
        placeholder?.classList.remove('hidden');
    }
    hiddenBox.innerHTML = '';
    picPasangSelected.forEach((name, id) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'pic_pasang[]';
        input.value = id;
        hiddenBox.appendChild(input);
    });
}
function picPasangToggleDropdown(e) {
    e.stopPropagation();
    const dropdown = document.getElementById('picPasangDropdown');
    const chevron  = document.getElementById('picPasangChevron');
    const isHidden = dropdown.classList.contains('hidden');
    if (isHidden) {
        dropdown.classList.remove('hidden');
        chevron?.classList.add('rotate-180');
        const search = document.getElementById('picPasangSearch');
        if (search) { search.value = ''; picPasangFilter(''); setTimeout(() => search.focus(), 50); }
    } else {
        dropdown.classList.add('hidden');
        chevron?.classList.remove('rotate-180');
    }
}
document.addEventListener('click', function(e) {
    const wrapper  = document.getElementById('picPasangWrapper');
    const dropdown = document.getElementById('picPasangDropdown');
    if (!dropdown || dropdown.classList.contains('hidden')) return;
    if (!wrapper || !wrapper.contains(e.target)) {
        dropdown.classList.add('hidden');
        document.getElementById('picPasangChevron')?.classList.remove('rotate-180');
    }
});
function picPasangToggleUser(id, name) {
    const btn   = document.getElementById('picPasangBtn_' + id);
    const check = document.getElementById('picPasangCheck_' + id);
    if (picPasangSelected.has(id)) {
        picPasangSelected.delete(id);
        check?.classList.add('invisible');
        btn?.classList.remove('bg-emerald-50', 'text-emerald-800', 'font-medium');
        btn?.classList.add('text-slate-700');
    } else {
        picPasangSelected.set(id, name);
        check?.classList.remove('invisible');
        btn?.classList.add('bg-emerald-50', 'text-emerald-800', 'font-medium');
        btn?.classList.remove('text-slate-700');
    }
    picPasangRender();
}
function picPasangRemove(id) {
    picPasangSelected.delete(id);
    document.getElementById('picPasangCheck_' + id)?.classList.add('invisible');
    const btn = document.getElementById('picPasangBtn_' + id);
    btn?.classList.remove('bg-emerald-50', 'text-emerald-800', 'font-medium');
    btn?.classList.add('text-slate-700');
    picPasangRender();
}
function picPasangFilter(q) {
    document.querySelectorAll('.pic-pasang-item').forEach(item => {
        item.style.display = item.dataset.name.includes(q.toLowerCase()) ? '' : 'none';
    });
}
document.addEventListener('DOMContentLoaded', picPasangRender);
</script>

</x-layouts.app>
