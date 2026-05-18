<x-layouts.app>
    <x-slot:title>Detail Vendor</x-slot:title>

    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-4">
            <a href="{{ route('vendors.index') }}"
               class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Detail Vendor</h1>
                <p class="text-slate-500 mt-1">{{ $vendor->vendor_id }}</p>
            </div>
            <div class="ml-auto flex gap-2">
                <a href="{{ route('vendors.edit', $vendor) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 border border-blue-200 rounded-xl font-medium hover:bg-blue-100 transition-all">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
                <div>
                    <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Vendor ID</dt>
                    <dd class="text-slate-800 font-semibold">{{ $vendor->vendor_id }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Nama Vendor</dt>
                    <dd class="text-slate-800">{{ $vendor->vendor_name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">PIC Vendor</dt>
                    <dd class="text-slate-700">{{ $vendor->pic_vendor ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Telepon</dt>
                    <dd class="text-slate-700">
                        @if($vendor->telp)
                            <a href="tel:{{ $vendor->telp }}" class="text-blue-600 hover:underline">{{ $vendor->telp }}</a>
                        @else
                            -
                        @endif
                    </dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Email</dt>
                    <dd class="text-slate-700">
                        @if($vendor->email)
                            <a href="mailto:{{ $vendor->email }}" class="text-blue-600 hover:underline">{{ $vendor->email }}</a>
                        @else
                            -
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Created By</dt>
                    <dd class="text-slate-700">{{ $vendor->creator->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Created At</dt>
                    <dd class="text-slate-700">{{ $vendor->created_at?->format('d/m/Y H:i') }}</dd>
                </div>
            </dl>
        </div>
    </div>

</x-layouts.app>
