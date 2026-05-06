<x-layouts.app>
    <x-slot:title>Detail Work Order</x-slot:title>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
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
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold
                    {{ $work_order->status === 'Open' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                    {{ $work_order->status }}
                </span>
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
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                            {{ $work_order->status === 'Open' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
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

        <!-- Meta -->
        <div class="text-xs text-slate-400 flex gap-6">
            <span>Dibuat oleh: {{ $work_order->creator->name ?? '-' }}</span>
            <span>Dibuat pada: {{ $work_order->created_at?->format('d/m/Y H:i') }}</span>
            <span>Diupdate: {{ $work_order->updated_at?->format('d/m/Y H:i') }}</span>
        </div>
    </div>

</x-layouts.app>
