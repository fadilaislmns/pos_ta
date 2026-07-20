@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        {{-- Header --}}
        <div class="mb-3">
            <h3 class="font-weight-bold mb-2">Manajemen Diskon Produk</h3>

            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex">
                    <input type="text" id="searchProduct" class="form-control mr-2" placeholder="Cari produk">
                </div>
                <div class="d-flex">
                    <button type="button" class="btn btn-outline-dark mr-2" id="btnFilter">
                        <i class="fas fa-filter"></i>
                        Filter
                    </button>
                    <button type="button" class="btn btn-dark" data-toggle="modal" data-target="#addModal">
                        + Tambah Diskon
                    </button>
                </div>
            </div>
        </div>

        {{-- Tabel --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <div style="overflow-x:auto; width:100%;">
                <table id="productTable" class="table table-hover" style="min-width:1000px;">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Produk</th>
                            <th>Harga Normal</th>
                            <th>Diskon</th>
                            <th>Harga Setelah Diskon</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($discounts as $discount)
                            @php
                                $aktif =
                                    $discount->is_active && now()->between($discount->start_date, $discount->end_date);
                            @endphp
                            @php
                                $hargaNormal = $discount->product->selling_price;
                                if ($discount->discount_type == 'percent') {
                                    $hargaDiskon = $hargaNormal - ($hargaNormal * $discount->discount_value) / 100;
                                } else {
                                    $hargaDiskon = $hargaNormal - $discount->discount_value;
                                }
                            @endphp
                            <tr data-status="{{ $aktif ? '1' : '0' }}">
                                <td>{{ $discounts->firstItem() + $loop->index }}</td>
                                <td>{{ $discount->product->product_name }}</td>
                                <td>Rp {{ number_format($hargaNormal, 0, ',', '.') }}</td>
                                <td>
                                    @if ($discount->discount_type == 'percent')
                                        {{ $discount->discount_value }}%
                                    @else
                                        Rp {{ number_format($discount->discount_value, 0, ',', '.') }}
                                    @endif
                                </td>
                                <td>Rp {{ number_format(max(0, $hargaDiskon), 0, ',', '.') }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($discount->start_date)->format('d/m/Y') }}
                                    -
                                    {{ \Carbon\Carbon::parse($discount->end_date)->format('d/m/Y') }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center" style="gap:12px;">
                                        <div class="custom-switch-btn toggle-discount-btn {{ $discount->is_active ? 'active' : '' }}"
                                            data-id="{{ $discount->id }}" id="switch-{{ $discount->id }}"
                                            style="cursor:pointer;">
                                            <div class="custom-switch-thumb"></div>
                                        </div>
                                        <span class="status-text" id="label-status-{{ $discount->id }}">
                                            {{ $discount->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-sm btnEdit text-warning" data-id="{{ $discount->id }}">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button class="btn btn-sm btnDelete text-danger" data-id="{{ $discount->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    Tidak ada data diskon
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $discounts->links() }}
            </div>
            </div>
        </div>
    </div>
    {{-- MODAL ADD --}}
    <div class="modal fade" id="addModal">
        <div class="modal-dialog">
            <form id="formAdd">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Tambah Diskon</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Produk</label>
                            <select name="product_id" class="form-control" required>
                                <option value="">
                                    Pilih Produk
                                </option>
                                @foreach ($availableProducts as $product)
                                    <option value="{{ $product->id }}">
                                        {{ $product->product_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Jenis Diskon</label>
                            <select name="discount_type" class="form-control">
                                <option value="percent">Persen (%)</option>
                                <option value="nominal">Nominal (Rp)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Nilai Diskon</label>
                            <input type="number" name="discount_value" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Tanggal Berakhir</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="font-weight-bold text-dark d-block">Status</label>
                            <div class="d-flex align-items-center" style="gap: 12px;">
                                <div class="custom-switch-btn active" id="addStatusSwitch" style="cursor: pointer;">
                                    <div class="custom-switch-thumb"></div>
                                </div>
                                <span class="status-text" id="addStatusLabel">Active</span>
                                <input type="hidden" name="is_active" id="addStatusInput" value="1">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div class="modal fade" id="editModal">
        <div class="modal-dialog">
            <form id="formEdit">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Edit Diskon</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Produk</label>
                            <select name="product_id" id="edit_product_id" class="form-control">
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">
                                        {{ $product->product_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Jenis Diskon</label>
                            <select name="discount_type" id="edit_discount_type" class="form-control">
                                <option value="percent">
                                    Persen (%)
                                </option>
                                <option value="nominal">
                                    Nominal (Rp)
                                </option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Nilai Diskon</label>
                            <input type="number" id="edit_discount_value" name="discount_value" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Tanggal Mulai</label>
                            <input type="date" id="edit_start_date" name="start_date" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Tanggal Berakhir</label>
                            <input type="date" id="edit_end_date" name="end_date" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="font-weight-bold text-dark d-block">Status</label>
                            <div class="d-flex align-items-center" style="gap: 12px;">
                                <div class="custom-switch-btn active" id="editStatusSwitch" style="cursor: pointer;">
                                    <div class="custom-switch-thumb"></div>
                                </div>
                                <span class="status-text" id="editStatusLabel">Aktif</span>
                                <input type="hidden" name="is_active" id="editStatusInput" value="1">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-warning">
                            Update
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- FILTER PANEL -->
    <div class="filter-panel" id="filterPanel">
        <!-- HEADER -->
        <div class="filter-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Filter</h5>
            <button class="btn btn-sm btn-light" id="closeFilter">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <!-- BODY -->
        <div class="filter-body">
            <!-- STATUS -->
            <div class="form-group">
                <label class="filter-label">Status</label>
                <div class="filter-radio-group">
                    <!-- SEMUA -->
                    <label class="filter-radio">
                        <input type="radio" name="status" value="" checked>
                        <span>Semua</span>
                    </label>
                    <!-- ACTIVE -->
                    <label class="filter-radio">
                        <input type="radio" name="status" value="1">
                        <span>Aktif</span>
                    </label>
                    <!-- INACTIVE -->
                    <label class="filter-radio">
                        <input type="radio" name="status" value="0">
                        <span>Tidak Aktif</span>
                    </label>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="filter-footer d-flex justify-content-between">
                <button class="btn btn-light" id="resetFilter">
                    Reset
                </button>
                <button class="btn btn-success" id="applyFilter">
                    Terapkan
                </button>
            </div>
        </div>

        @push('scripts')
            <script>
                $('#searchProduct').on('keyup', function() {
                    let value = $(this).val().toLowerCase();
                    $('#productTable tr').filter(function() {
                        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                    });
                });
                $('#formAdd').submit(function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: "{{ route('discounts.store') }}",
                        method: "POST",
                        data: $(this).serialize(),
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            setTimeout(() => {
                                location.reload();
                            }, 1500);

                        },

                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: xhr.responseJSON.message
                            });
                        }
                    });
                });

                $(document).on('click', '.btnEdit', function() {
                    let id = $(this).data('id');
                    $.get('/discounts/' + id + '/edit', function(res) {
                        $('#edit_id').val(res.id);
                        $('#edit_product_id').val(res.product_id);
                        $('#edit_discount_type').val(res.discount_type);
                        $('#edit_discount_value').val(res.discount_value);
                        $('#edit_start_date').val(res.start_date);
                        $('#edit_end_date').val(res.end_date);
                        if (res.is_active == 1) {
                            $('#editStatusSwitch').addClass('active');
                            $('#editStatusLabel').text('Aktif');
                            $('#editStatusInput').val(1);
                        } else {
                            $('#editStatusSwitch').removeClass('active');
                            $('#editStatusLabel').text('Tidak Aktif');
                            $('#editStatusInput').val(0);
                        }
                        let modal =
                            new bootstrap.Modal(
                                document.getElementById('editModal')
                            );
                        modal.show();
                    });
                });

                $('#formEdit').submit(function(e) {
                    e.preventDefault();
                    let id = $('#edit_id').val();
                    $.ajax({
                        url: '/discounts/' + id,
                        method: 'POST',
                        data: $(this).serialize(),
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: xhr.responseJSON.message
                            });
                        }
                    });
                });

                // FILTER PANEL
                $('#btnFilter').click(function() {
                    $('#filterPanel').addClass('active');
                });
                $('#closeFilter').click(function() {
                    $('#filterPanel').removeClass('active');
                });

                // APPLY FILTER
                $('#applyFilter').click(function() {
                    let status = $('input[name="status"]:checked').val();

                    $('#productTable tbody tr').each(function() {
                        let rowStatus = $(this).data('status');
                        if (status === '') {
                            $(this).show();
                        } else {
                            $(this).toggle(rowStatus == status);
                        }
                    });

                    $('#filterPanel').removeClass('active');
                });

                $('#resetFilter').click(function() {
                    window.location = "{{ route('discounts.index') }}";
                });

                $(document).on('click', '.btnDelete', function() {
                    let id = $(this).data('id');
                    Swal.fire({
                        title: 'Hapus diskon?',
                        text: 'Data tidak dapat dikembalikan',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya Hapus'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '/discounts/' + id,
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    _method: 'DELETE'
                                },
                                success: function(res) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: res.message
                                    });
                                    setTimeout(() => {
                                        location.reload();
                                    }, 1000);
                                }
                            });
                        }
                    });
                });

                document.querySelectorAll('.toggle-discount-btn')
                    .forEach(btn => {

                        btn.addEventListener('click', function() {

                            let discountId = this.dataset.id;
                            let label = document.getElementById(
                                `label-status-${discountId}`
                            );

                            let isActive =
                                this.classList.contains('active');

                            Swal.fire({
                                    title: isActive ?
                                        'Nonaktifkan diskon?' : 'Aktifkan diskon?',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonText: 'Ya',
                                    cancelButtonText: 'Batal'
                                })
                                .then((result) => {

                                    if (result.isConfirmed) {

                                        $.ajax({
                                            url: `/discounts/${discountId}/toggle`,
                                            method: 'POST',
                                            data: {
                                                _token: '{{ csrf_token() }}',
                                                _method: 'PATCH'
                                            },

                                            success: function(res) {

                                                btn.classList.toggle('active');

                                                if (btn.classList.contains('active')) {
                                                    label.innerText = 'Aktif';
                                                } else {
                                                    label.innerText = 'Tidak Aktif';
                                                }

                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Berhasil',
                                                    text: res.message,
                                                    timer: 1200,
                                                    showConfirmButton: false
                                                });

                                            }

                                        });

                                    }

                                });

                        });

                    });

                const addStatusSwitch = document.getElementById('addStatusSwitch');
                const addStatusInput = document.getElementById('addStatusInput');
                const addStatusLabel = document.getElementById('addStatusLabel');

                const editStatusSwitch = document.getElementById('editStatusSwitch');
                const editStatusInput = document.getElementById('editStatusInput');
                const editStatusLabel = document.getElementById('editStatusLabel');

                addStatusSwitch.addEventListener('click', function() {
                    this.classList.toggle('active');
                    if (this.classList.contains('active')) {
                        addStatusInput.value = 1;
                        addStatusLabel.innerText = 'Aktif';
                    } else {
                        addStatusInput.value = 0;
                        addStatusLabel.innerText = 'Tidak Aktif';
                    }
                });
                editStatusSwitch.addEventListener('click', function() {
                    this.classList.toggle('active');
                    if (this.classList.contains('active')) {
                        editStatusInput.value = 1;
                        editStatusLabel.innerText = 'Aktif';
                    } else {
                        editStatusInput.value = 0;
                        editStatusLabel.innerText = 'Tidak Aktif';
                    }
                });
            </script>
        @endpush
    @endsection
