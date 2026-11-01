@extends('layouts.app', ['title' => 'Edit Data Unit'])

@section('content')
@php
    $ramOptions = [4, 8, 12, 16, 24, 32, 64];
    $selectedRam = old('ram_gb', $product->ram_gb);
    $capacityOptions = ['128GB', '256GB', '512GB', '1TB', '2TB'];
    $selectedCapacity = old('storage_capacity', $storageCapacity);
    $storageTypeOptions = ['NVMe M.2 SSD', 'SATA 2.5" SSD', 'HDD SATA', 'eMMC'];
    $selectedStorageType = old('storage_type', $storageType);
@endphp

<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Ubah Data Unit: {{ $product->name }}</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-7 mb-3">
                            <label for="name" class="form-label fw-semibold">Nama Seri Laptop</label>
                            <input type="text" id="name" name="name" maxlength="255" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-5 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="category_select" class="form-label fw-semibold mb-0">Kategori Brand</label>
                                <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none fw-semibold" data-bs-toggle="modal" data-bs-target="#modalAddCategory">
                                    <i class="bi bi-plus-circle me-1"></i>Brand Baru
                                </button>
                            </div>
                            <select name="category_id" id="category_select" class="form-select @error('category_id') is-invalid @enderror" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="processor" class="form-label fw-semibold">Tipe Prosesor</label>
                            <input type="text" id="processor" name="processor" maxlength="255" class="form-control @error('processor') is-invalid @enderror" value="{{ old('processor', $product->processor) }}" required>
                            @error('processor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="ram_gb" class="form-label fw-semibold">Kapasitas RAM</label>
                            <select id="ram_gb" name="ram_gb" class="form-select @error('ram_gb') is-invalid @enderror" required>
                                @if(!in_array((int) $selectedRam, $ramOptions, true))
                                    <option value="{{ $selectedRam }}" selected>{{ $selectedRam }} GB</option>
                                @endif
                                @foreach($ramOptions as $ram)
                                    <option value="{{ $ram }}" @selected((int) $selectedRam === $ram)>{{ $ram }} GB</option>
                                @endforeach
                            </select>
                            @error('ram_gb') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="storage_capacity" class="form-label fw-semibold">Kapasitas Penyimpanan</label>
                            <select id="storage_capacity" name="storage_capacity" class="form-select @error('storage_capacity') is-invalid @enderror" required>
                                @if($selectedCapacity && !in_array($selectedCapacity, $capacityOptions, true))
                                    <option value="{{ $selectedCapacity }}" selected>{{ $selectedCapacity }}</option>
                                @endif
                                @foreach($capacityOptions as $capacity)
                                    <option value="{{ $capacity }}" @selected($selectedCapacity === $capacity)>{{ $capacity }}</option>
                                @endforeach
                            </select>
                            @error('storage_capacity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="storage_type" class="form-label fw-semibold">Tipe Storage</label>
                            <select id="storage_type" name="storage_type" class="form-select @error('storage_type') is-invalid @enderror" required>
                                @if($selectedStorageType && !in_array($selectedStorageType, $storageTypeOptions, true))
                                    <option value="{{ $selectedStorageType }}" selected>{{ $selectedStorageType }}</option>
                                @endif
                                @foreach($storageTypeOptions as $type)
                                    <option value="{{ $type }}" @selected($selectedStorageType === $type)>{{ $type }}</option>
                                @endforeach
                            </select>
                            @error('storage_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="battery_condition" class="form-label fw-semibold">Kondisi Baterai</label>
                            <input type="text" id="battery_condition" name="battery_condition" maxlength="255" class="form-control @error('battery_condition') is-invalid @enderror" value="{{ old('battery_condition', $product->battery_condition) }}" required>
                            @error('battery_condition') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="physical_condition" class="form-label fw-semibold">Kondisi Fisik Unit</label>
                            <input type="text" id="physical_condition" name="physical_condition" maxlength="255" class="form-control @error('physical_condition') is-invalid @enderror" value="{{ old('physical_condition', $product->physical_condition) }}" required>
                            @error('physical_condition') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price_display" class="form-label fw-semibold">Harga Jual (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold">Rp</span>
                                <input type="text" id="price_display" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') ? number_format(old('price'), 0, ',', '.') : number_format($product->price, 0, ',', '.') }}" placeholder="Contoh: 7.500.000" required>
                                <input type="hidden" name="price" id="price_raw" value="{{ old('price', (int) $product->price) }}">
                            </div>
                            @error('price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="stock" class="form-label fw-semibold">Jumlah Stok</label>
                            <input type="number" id="stock" name="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', $product->stock) }}" min="0" max="2147483647" step="1" required>
                            @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="inputImage" class="form-label fw-semibold">Foto Unit Laptop</label>
                        <div class="mb-2">
                            <p class="small text-muted mb-1">Foto Saat Ini:</p>
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="Foto {{ $product->name }}" class="rounded border p-1" style="max-height: 120px; object-fit: contain;">
                            @else
                                <span class="text-muted small">Belum ada foto</span>
                            @endif
                        </div>

                        <input type="file" name="image" id="inputImage" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/webp,image/jpg" onchange="previewFile(event)">
                        <div class="form-text">Pilih file baru jika ingin mengganti foto unit (JPG, JPEG, PNG, WEBP; maks. 2 MB).</div>
                        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="mt-3 d-none" id="previewContainer">
                            <p class="small text-primary fw-semibold mb-1">Pratinjau Foto Pengganti:</p>
                            <img id="imagePreview" src="#" alt="Pratinjau foto baru" class="rounded border border-primary p-1" style="max-height: 140px; object-fit: contain;">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-semibold">Deskripsi Kelengkapan</label>
                        <textarea id="description" name="description" rows="3" maxlength="10000" class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-light border">Batal</a>
                        <button type="submit" class="btn btn-primary fw-semibold px-4">Perbarui Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAddCategory" tabindex="-1" aria-labelledby="modalAddCategoryLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalAddCategoryLabel">Tambah Kategori Brand Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="new_category_name" class="form-label fw-semibold">Nama Brand / Seri</label>
                    <input type="text" id="new_category_name" maxlength="255" class="form-control" placeholder="Contoh: ASUS ROG, Acer Swift">
                </div>
                <div class="mb-3">
                    <label for="new_category_desc" class="form-label">Deskripsi Singkat (Opsional)</label>
                    <textarea id="new_category_desc" class="form-control" rows="2" maxlength="10000" placeholder="Penjelasan singkat brand..."></textarea>
                </div>
                <div id="modal_error_msg" class="text-danger small mb-2 d-none" role="alert"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary btn-sm fw-semibold" id="btnSaveCategory">Simpan Brand</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalFileAlert" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="text-danger mb-3">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 2.5rem;"></i>
                </div>
                <h6 class="fw-bold mb-2 text-dark" id="fileAlertTitle">Pemberitahuan</h6>
                <p class="text-muted small mb-3" id="fileAlertMessage">Pesan error...</p>
                <button type="button" class="btn btn-dark btn-sm w-100 fw-semibold" data-bs-dismiss="modal">
                    Mengerti
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const priceDisplay = document.getElementById('price_display');
const priceRaw = document.getElementById('price_raw');

if (priceDisplay && priceRaw) {
    priceDisplay.addEventListener('input', function () {
        const cleanVal = this.value.replace(/\D/g, '');

        if (cleanVal) {
            this.value = parseInt(cleanVal, 10).toLocaleString('id-ID');
            priceRaw.value = cleanVal;
        } else {
            this.value = '';
            priceRaw.value = '';
        }
    });
}

function showFileModal(title, message) {
    document.getElementById('fileAlertTitle').innerText = title;
    document.getElementById('fileAlertMessage').innerText = message;

    const modalEl = document.getElementById('modalFileAlert');
    const alertModal = bootstrap.Modal.getOrCreateInstance(modalEl);
    alertModal.show();
}

function previewFile(event) {
    const input = event.target;
    const file = input.files ? input.files[0] : null;
    const previewContainer = document.getElementById('previewContainer');
    const preview = document.getElementById('imagePreview');
    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];

    if (file) {
        if (!allowedTypes.includes(file.type)) {
            showFileModal(
                'Format File Tidak Didukung',
                'File yang Anda pilih bukan gambar yang valid. Sistem hanya menerima file berekstensi JPG, JPEG, PNG, atau WEBP.'
            );
            input.value = '';
            if (previewContainer) previewContainer.classList.add('d-none');
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            showFileModal(
                'Ukuran File Terlalu Besar',
                'Ukuran file melebihi batas maksimal 2MB. Silakan pilih atau kompres file gambar Anda terlebih dahulu.'
            );
            input.value = '';
            if (previewContainer) previewContainer.classList.add('d-none');
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            if (previewContainer) previewContainer.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }
}

document.getElementById('btnSaveCategory').addEventListener('click', async () => {
    const nameInput = document.getElementById('new_category_name');
    const descInput = document.getElementById('new_category_desc');
    const errorMsg = document.getElementById('modal_error_msg');
    const btnSave = document.getElementById('btnSaveCategory');
    const name = nameInput.value.trim();

    errorMsg.classList.add('d-none');
    errorMsg.textContent = '';

    if (!name) {
        errorMsg.textContent = 'Nama brand tidak boleh kosong.';
        errorMsg.classList.remove('d-none');
        nameInput.focus();
        return;
    }

    btnSave.disabled = true;
    btnSave.textContent = 'Menyimpan...';

    try {
        const response = await fetch(@json(route('admin.categories.quick-store')), {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': @json(csrf_token()),
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ name, description: descInput.value.trim() }),
        });
        const result = await response.json();

        if (!response.ok) {
            const validationMessage = Object.values(result.errors || {}).flat()[0];
            throw new Error(validationMessage || result.message || 'Gagal menyimpan kategori.');
        }

        if (result.status !== 'success' || !result.data) {
            throw new Error(result.message || 'Respons server tidak valid.');
        }

        const select = document.getElementById('category_select');
        select.add(new Option(result.data.name, result.data.id, true, true));
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalAddCategory')).hide();
        nameInput.value = '';
        descInput.value = '';
    } catch (error) {
        errorMsg.textContent = error.message || 'Terjadi kesalahan jaringan atau server.';
        errorMsg.classList.remove('d-none');
    } finally {
        btnSave.disabled = false;
        btnSave.textContent = 'Simpan Brand';
    }
});
</script>
@endsection
