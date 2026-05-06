<x-layouts.app>
    <x-slot:title>New Transaction</x-slot:title>
    <x-slot:header>New Stock Transaction</x-slot:header>

    <!-- Include html5-qrcode library for barcode scanning -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    
    <style>
        /* Custom select dropdown styles */
        .custom-select-wrapper {
            position: relative;
        }
        .custom-select-search {
            position: relative;
        }
        .custom-select-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #d1d5db;
            border-top: none;
            border-radius: 0 0 0.375rem 0.375rem;
            max-height: 200px;
            overflow-y: auto;
            z-index: 50;
            display: none;
        }
        .custom-select-dropdown.show {
            display: block;
        }
        .custom-select-option {
            padding: 0.5rem 0.75rem;
            cursor: pointer;
            font-size: 0.875rem;
        }
        .custom-select-option:hover {
            background-color: #f3f4f6;
        }
        .custom-select-option.selected {
            background-color: #e0e7ff;
        }
        /* Quantity stepper buttons */
        .qty-stepper {
            display: flex;
            align-items: center;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            overflow: hidden;
        }
        .qty-stepper input {
            border: none;
            text-align: center;
            flex: 1;
            padding: 0.5rem;
            font-size: 0.875rem;
        }
        .qty-stepper input:focus {
            outline: none;
        }
        .qty-stepper button {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f9fafb;
            border: none;
            cursor: pointer;
            font-size: 1.25rem;
            color: #6b7280;
            transition: background 0.2s;
        }
        .qty-stepper button:hover {
            background: #e5e7eb;
        }
    </style>

    <div class="max-w-xl mx-auto">
        <div class="bg-white shadow rounded-lg">
            <form action="{{ route('transactions.store') }}" method="POST" class="px-4 py-5 sm:p-6">
                @csrf
                
                <div class="grid grid-cols-1 gap-6">
                    <!-- Product Barcode with Scanner -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Product Barcode <span class="text-red-500">*</span></label>
                        <div class="flex gap-2">
                            <div class="custom-select-wrapper flex-1">
                                <div class="custom-select-search">
                                    <input type="text" id="barcode_search" 
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-3 py-2 border @error('sparepart_id') border-red-500 @enderror"
                                           placeholder="Scan or search barcode..."
                                           autocomplete="off">
                                    <input type="hidden" name="sparepart_id" id="sparepart_id" value="{{ old('sparepart_id', $sparepart->id ?? '') }}" required>
                                </div>
                                <div class="custom-select-dropdown" id="barcode_dropdown">
                                    @foreach($spareparts as $sp)
                                        <div class="custom-select-option" 
                                             data-value="{{ $sp->id }}" 
                                             data-code="{{ $sp->material_code }}"
                                             data-description="{{ $sp->description }}"
                                             data-stock="{{ $sp->stock }}"
                                             data-unit="{{ $sp->unit }}">
                                            <strong>{{ $sp->material_code }}</strong> - {{ Str::limit($sp->description, 35) }}
                                            <span class="text-xs text-gray-500">({{ number_format($sp->stock, 0) }} {{ $sp->unit }})</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <button type="button" id="scan_btn" 
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md bg-gray-50 hover:bg-gray-100 text-gray-700 transition">
                                <i class="fas fa-camera text-lg"></i>
                            </button>
                        </div>
                        @error('sparepart_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Camera Scanner Modal -->
                    <div id="scanner_modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                            <div class="flex justify-between items-center px-4 py-3 border-b">
                                <h3 class="text-lg font-medium">Scan Barcode / QR Code</h3>
                                <button type="button" id="close_scanner" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times text-xl"></i>
                                </button>
                            </div>
                            <div class="p-4">
                                <div id="scanner_reader" style="width: 100%;"></div>
                                <p class="text-sm text-gray-500 mt-3 text-center">
                                    <i class="fas fa-info-circle mr-1"></i> Arahkan kamera ke barcode atau QR code
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Material Description (Auto-fill) -->
                    <div>
                        <label class="block text-sm font-medium text-amber-600 mb-1">Material Description <span class="text-red-500">*</span></label>
                        <input type="text" id="material_description" readonly
                               class="block w-full rounded-md border-gray-200 bg-gray-50 shadow-sm sm:text-sm px-3 py-2 border text-gray-600"
                               value="{{ $sparepart->description ?? '' }}"
                               placeholder="Auto-fill dari Product Barcode">
                    </div>

                    <!-- Existing Stock (Auto-fill) -->
                    <div>
                        <label class="block text-sm font-medium text-blue-600 mb-1">Existing Stock</label>
                        <div class="flex items-center gap-2">
                            <input type="text" id="existing_stock" readonly
                                   class="block w-full rounded-md border-gray-200 bg-blue-50 shadow-sm sm:text-sm px-3 py-2 border text-blue-700 font-semibold"
                                   value="{{ isset($sparepart) ? number_format($sparepart->stock, 0) . ' ' . $sparepart->unit : '' }}"
                                   placeholder="Auto-fill dari Product Barcode">
                        </div>
                    </div>

                    <!-- Quantity with Stepper (default blank) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantity <span class="text-red-500">*</span></label>
                        <div class="qty-stepper @error('quantity') border-red-500 @enderror">
                            <input type="number" name="quantity" id="quantity" value="{{ old('quantity', '') }}" required min="0" step="0.1"
                                   placeholder="Masukkan jumlah">
                            <button type="button" onclick="adjustQty(-1)">−</button>
                            <button type="button" onclick="adjustQty(1)">+</button>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">For adjustment, enter the new stock quantity</p>
                        @error('quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Satuan (Unit - Auto-fill) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                        <input type="text" id="satuan" readonly
                               class="block w-full rounded-md border-gray-200 bg-gray-50 shadow-sm sm:text-sm px-3 py-2 border text-gray-600"
                               value="{{ $sparepart->unit ?? '' }}"
                               placeholder="PC">
                    </div>

                    <!-- Order Number (Reference No) - Numeric, 8-10 digits -->
                    <div>
                        <label for="reference_no" class="block text-sm font-medium text-gray-700 mb-1">Order Number <span class="text-red-500">*</span></label>
                           <input type="number" name="reference_no" id="reference_no" value="{{ old('reference_no') }}"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-3 py-2 border @error('reference_no') border-red-500 @enderror"
                               placeholder="Masukkan nomor order (8-10 digit angka)" required min="10000000" max="9999999999">
                        <p class="text-xs text-gray-500">Order number harus angka 8-10 digit.</p>
                        @error('reference_no')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Transaction Type (role-based) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Transaction Type <span class="text-red-500">*</span></label>
                        @if(auth()->user()->role === 'admin')
                        <div class="grid grid-cols-3 gap-3">
                            <label class="flex items-center justify-center p-4 border rounded-lg cursor-pointer transition hover:border-green-400 
                                          {{ old('type') == 'in' ? 'border-green-500 bg-green-50' : 'border-gray-300' }}">
                                <input type="radio" name="type" value="in" class="sr-only" {{ old('type') == 'in' ? 'checked' : '' }} required>
                                <div class="text-center">
                                    <i class="fas fa-arrow-down text-green-500 text-xl mb-1"></i>
                                    <p class="text-sm font-medium">In</p>
                                </div>
                            </label>
                            <label class="flex items-center justify-center p-4 border rounded-lg cursor-pointer transition hover:border-red-400
                                          {{ old('type', 'out') == 'out' ? 'border-red-500 bg-red-50' : 'border-gray-300' }}">
                                <input type="radio" name="type" value="out" class="sr-only" {{ old('type', 'out') == 'out' ? 'checked' : '' }}>
                                <div class="text-center">
                                    <i class="fas fa-arrow-up text-red-500 text-xl mb-1"></i>
                                    <p class="text-sm font-medium">Out</p>
                                </div>
                            </label>
                            <label class="flex items-center justify-center p-4 border rounded-lg cursor-pointer transition hover:border-yellow-400
                                          {{ old('type') == 'adjustment' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-300' }}">
                                <input type="radio" name="type" value="adjustment" class="sr-only" {{ old('type') == 'adjustment' ? 'checked' : '' }}>
                                <div class="text-center">
                                    <i class="fas fa-edit text-yellow-500 text-xl mb-1"></i>
                                    <p class="text-sm font-medium">Adjust</p>
                                </div>
                            </label>
                        </div>
                        @else
                        <div class="grid grid-cols-1">
                            <label class="flex items-center justify-center p-4 border rounded-lg bg-red-50 border-red-500">
                                <input type="radio" name="type" value="out" class="sr-only" checked readonly>
                                <div class="text-center">
                                    <i class="fas fa-arrow-up text-red-500 text-xl mb-1"></i>
                                    <p class="text-sm font-medium">Out</p>
                                </div>
                            </label>
                        </div>
                        @endif
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Name (Dropdown dari data user, searchable) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                        <select name="user_id" id="user_id_select" class="tom-select-user block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-3 py-2 border @error('user_id') border-red-500 @enderror" required>
                            <option value="">Pilih user...</option>
                            @php
                                $currentUserId = auth()->id();
                                $currentUser = $users->firstWhere('id', $currentUserId);
                                $otherUsers = $users->filter(fn($u) => $u->id !== $currentUserId);
                            @endphp
                            @if($currentUser)
                                <option value="{{ $currentUser->id }}" {{ old('user_id') == $currentUser->id ? 'selected' : '' }}>{{ $currentUser->name }}</option>
                            @endif
                            @foreach($otherUsers as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Pilih nama user yang melakukan transaksi</p>
                        @error('user_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
</div>

<!-- TomSelect for user dropdown (bundled via Vite) -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.TomSelect) {
            new TomSelect('#user_id_select', {
                create: false,
                sortField: 'text',
                placeholder: 'Cari nama user...'
            });
        }
    });
</script>

                    <!-- Remark (Notes) -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">REMARK</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-3 py-2 border"
                                  placeholder="Additional notes...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="mt-6 flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-check mr-2"></i>Save Transaction
                    </button>
                    <a href="{{ route('transactions.index') }}" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ========== Searchable Dropdown ==========
        const barcodeSearch = document.getElementById('barcode_search');
        const barcodeDropdown = document.getElementById('barcode_dropdown');
        const sparepartIdInput = document.getElementById('sparepart_id');
        const options = document.querySelectorAll('.custom-select-option');
        
        // Initialize if there's a pre-selected value
        @if($sparepart)
            barcodeSearch.value = '{{ $sparepart->material_code }}';
        @endif

        barcodeSearch.addEventListener('focus', () => {
            barcodeDropdown.classList.add('show');
        });

        barcodeSearch.addEventListener('input', (e) => {
            const searchVal = e.target.value.toLowerCase();
            options.forEach(opt => {
                const code = opt.dataset.code.toLowerCase();
                const desc = opt.dataset.description.toLowerCase();
                if (code.includes(searchVal) || desc.includes(searchVal)) {
                    opt.style.display = '';
                } else {
                    opt.style.display = 'none';
                }
            });
            barcodeDropdown.classList.add('show');
        });

        options.forEach(opt => {
            opt.addEventListener('click', () => {
                selectSparepart(opt);
            });
        });

        function selectSparepart(opt) {
            sparepartIdInput.value = opt.dataset.value;
            barcodeSearch.value = opt.dataset.code;
            document.getElementById('material_description').value = opt.dataset.description;
            document.getElementById('satuan').value = opt.dataset.unit;
            document.getElementById('existing_stock').value = Number(opt.dataset.stock).toLocaleString('id-ID') + ' ' + opt.dataset.unit;
            barcodeDropdown.classList.remove('show');
            
            // Update all options visibility
            options.forEach(o => {
                o.style.display = '';
                o.classList.remove('selected');
            });
            opt.classList.add('selected');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.custom-select-wrapper')) {
                barcodeDropdown.classList.remove('show');
            }
        });

        // ========== Barcode Scanner ==========
        const scannerModal = document.getElementById('scanner_modal');
        const scanBtn = document.getElementById('scan_btn');
        const closeScanner = document.getElementById('close_scanner');
        let html5QrCode = null;

        scanBtn.addEventListener('click', () => {
            scannerModal.classList.remove('hidden');
            startScanner();
        });

        closeScanner.addEventListener('click', () => {
            stopScanner();
            scannerModal.classList.add('hidden');
        });

        scannerModal.addEventListener('click', (e) => {
            if (e.target === scannerModal) {
                stopScanner();
                scannerModal.classList.add('hidden');
            }
        });

        function startScanner() {
            html5QrCode = new Html5Qrcode("scanner_reader");
            html5QrCode.start(
                { facingMode: "environment" },
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                },
                (decodedText) => {
                    // Find matching sparepart by material_code
                    const matchingOpt = Array.from(options).find(opt => opt.dataset.code === decodedText);
                    if (matchingOpt) {
                        selectSparepart(matchingOpt);
                        stopScanner();
                        scannerModal.classList.add('hidden');
                    } else {
                        // Try partial match
                        const partialMatch = Array.from(options).find(opt => 
                            opt.dataset.code.includes(decodedText) || decodedText.includes(opt.dataset.code)
                        );
                        if (partialMatch) {
                            selectSparepart(partialMatch);
                            stopScanner();
                            scannerModal.classList.add('hidden');
                        } else {
                            alert('Barcode tidak ditemukan: ' + decodedText);
                        }
                    }
                },
                (errorMessage) => {
                    // Ignore errors (no QR code found in frame)
                }
            ).catch((err) => {
                console.error('Scanner error:', err);
                alert('Tidak dapat mengakses kamera. Pastikan izin kamera diberikan.');
            });
        }

        function stopScanner() {
            if (html5QrCode) {
                html5QrCode.stop().catch(err => console.log(err));
            }
        }

        // ========== Quantity Stepper ==========
        function adjustQty(delta) {
            const input = document.getElementById('quantity');
            let val = parseFloat(input.value) || 0;
            val = Math.max(0, val + delta);
            input.value = val.toFixed(1);
        }

        // ========== Radio Button Visual Selection ==========
        document.querySelectorAll('input[name="type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('input[name="type"]').forEach(r => {
                    r.closest('label').classList.remove('border-green-500', 'bg-green-50', 'border-red-500', 'bg-red-50', 'border-yellow-500', 'bg-yellow-50');
                    r.closest('label').classList.add('border-gray-300');
                });
                
                const label = this.closest('label');
                label.classList.remove('border-gray-300');
                
                if (this.value === 'in') {
                    label.classList.add('border-green-500', 'bg-green-50');
                } else if (this.value === 'out') {
                    label.classList.add('border-red-500', 'bg-red-50');
                } else {
                    label.classList.add('border-yellow-500', 'bg-yellow-50');
                }
            });
        });
    </script>
</x-layouts.app>
