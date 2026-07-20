@extends('layouts.app')
@section('content')
    <div class="container-fluid">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h3 class="font-weight-bold">Master Product</h3>

                <input type="text" id="searchProduct" class="form-control" placeholder="Search product..."
                    style="width: 300px;">
            </div>

            <button class="btn btn-dark" data-toggle="modal" data-target="#modalAdd">
                <i class="fas fa-plus"></i> Add
            </button>
        </div>

        <!-- TABLE -->
        <div class="card">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="min-width:1400px; white-space:nowrap;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Category</th>
                                <th>Supplier</th>
                                <th>Satuan</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <th>Diskon</th>
                                <th>Harga Aktif</th>
                                <th>Status Diskon</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody id="productTable">
                            @foreach ($products as $i => $p)
                                @php
                                    $discount = $p->discount;

                                    $discountActive = false;
                                    $finalPrice = $p->selling_price;

                                    if (
                                        $discount &&
                                        $discount->is_active &&
                                        now()->between($discount->start_date, $discount->end_date)
                                    ) {
                                        $discountActive = true;

                                        if ($discount->discount_type == 'percent') {
                                            $finalPrice =
                                                $p->selling_price -
                                                ($p->selling_price * $discount->discount_value) / 100;
                                        } else {
                                            $finalPrice = $p->selling_price - $discount->discount_value;
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $products->firstItem() + $i }}</td>
                                    <td>{{ $p->product_code }}</td>
                                    <td>{{ $p->product_name }}</td>
                                    <td>{{ $p->category->category_name }}</td>
                                    <td>{{ $p->suppliers->first()->supplier_name ?? '-' }}</td>
                                    <td>{{ $p->unit }}</td>
                                    <td>Rp {{ number_format($p->purchase_price, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($p->selling_price, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($discountActive)
                                            @if ($discount->discount_type == 'percent')
                                                <span>
                                                    {{ $discount->discount_value }}%
                                                </span>
                                            @else
                                                <span>
                                                    Rp {{ number_format($discount->discount_value, 0, ',', '.') }}
                                                </span>
                                            @endif
                                        @else
                                            <span> - </span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($discountActive)
                                            <span class="font-weight-bold text-success">
                                                Rp {{ number_format($finalPrice, 0, ',', '.') }}
                                            </span>
                                        @else
                                            Rp {{ number_format($p->selling_price, 0, ',', '.') }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($discountActive)
                                            <span class="badge badge-success">Aktif</span>
                                        @elseif($discount)
                                            <span class="badge badge-warning">Expired</span>
                                        @else
                                            <span class="badge badge-secondary">Tidak Ada</span>
                                        @endif
                                    </td>
                                    <td>
                                        <!-- DETAIL -->
                                        <a href="#" class="action-icon text-info btn-detail"
                                            data-code="{{ $p->product_code }}" data-name="{{ $p->product_name }}"
                                            data-category="{{ $p->category->category_name }}"
                                            data-unit="{{ $p->unit }}" data-buy="{{ $p->purchase_price }}"
                                            data-sell="{{ $p->selling_price }}"
                                            data-supplier="{{ $p->suppliers->first()->supplier_name ?? '-' }}"
                                            data-toggle="modal" data-target="#modalDetail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <!-- EDIT -->
                                        <a href="#" class="action-icon text-warning btn-edit"
                                            data-product='@json($p)'>
                                            <i class="fas fa-pen"></i>
                                        </a>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                    <div class="row align-items-center mt-4">
                        <div class="col-md-6 text-muted small">
                            Menampilkan <strong>{{ $products->firstItem() }}</strong> sampai
                            <strong>{{ $products->lastItem() }}</strong> dari
                            <strong>{{ $products->total() }}</strong> data
                        </div>

                        <div class="col-md-6 d-flex justify-content-end align-items-center">

                            <div class="mr-3">
                                <select id="perPageSelect" class="form-control form-control-sm"
                                    style="width: 70px; border-radius: 8px;">
                                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                </select>
                            </div>

                            <div>
                                {{ $products->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!--  MODAL ADD  -->
    <div class="modal fade" id="modalAdd">
        <div class="modal-dialog">
            <form id="formAdd">
                @csrf

                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Tambah Produk</h5>
                    </div>

                    <div class="modal-body px-4 py-4">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">
                                Kode Produk <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="product_code" class="form-control"
                                placeholder="Masukkan kode produk" required style="border-radius: 10px; height: 45px;">
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold ">
                                Nama Produk <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="product_name" class="form-control"
                                placeholder="Masukkan nama produk" required style="border-radius: 10px; height: 45px;">
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">
                                Kategori <span class="text-danger">*</span>
                            </label>
                            <select name="category_id" class="form-control" required
                                style="border-radius: 10px; height: 45px;">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->category_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">
                                Supplier <span class="text-danger">*</span>
                            </label>
                            <select name="supplier_id" class="form-control" required>
                                <option value="">-- Pilih Supplier --</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">
                                Satuan <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="unit" class="form-control" placeholder="Contoh: Pcs / Kg / Box"
                                required style="border-radius: 10px; height: 45px;">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold">
                                        Harga Beli <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="purchase_price" class="form-control" placeholder="0"
                                        required style="border-radius: 10px; height: 45px;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold ">
                                        Harga Jual <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="selling_price" class="form-control" placeholder="0"
                                        required style="border-radius: 10px; height: 45px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-success">Save</button>
                    </div>
                </div>

            </form>
        </div>
    </div>


    <!--  MODAL EDIT  -->
    <div class="modal fade" id="modalEdit">
        <div class="modal-dialog">
            <form id="formEdit">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Edit Produk</h5>
                    </div>
                    <div class="modal-body px-4 py-4">
                        @if (session('error'))
                            <div class="alert alert-danger" style="border-radius: 10px;">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">
                                Kode Produk <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="editCode" name="product_code" class="form-control" required
                                style="border-radius: 10px; height: 45px;">
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">
                                Nama Produk <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="editName" name="product_name" class="form-control" required
                                style="border-radius: 10px; height: 45px;">
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">
                                Kategori <span class="text-danger">*</span>
                            </label>
                            <select id="editCategory" name="category_id" class="form-control" required
                                style="border-radius: 10px; height: 45px;">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->category_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">
                                Supplier
                            </label>
                            <select id="editSupplier" name="supplier_id" class="form-control">
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">
                                Satuan <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="editUnit" name="unit" class="form-control" required
                                style="border-radius: 10px; height: 45px;">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold">
                                        Harga Beli <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="editBuy" name="purchase_price" class="form-control"
                                        required style="border-radius: 10px; height: 45px;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold">
                                        Harga Jual <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="editSell" name="selling_price" class="form-control"
                                        required style="border-radius: 10px; height: 45px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary">Update</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- MODAL DETAIL  -->
    <div class="modal fade" id="modalDetail">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>Detail Product</h5>
                </div>

                <div class="modal-body px-4 py-4">
                    <div class="form-group mb-4">
                        <label class="font-weight-bold">Kode Produk</label>
                        <input type="text" id="detailCode" class="form-control" readonly
                            style="border-radius: 10px; height: 45px; background-color: #f8f9fa;">
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold">Nama Produk</label>
                        <input type="text" id="detailName" class="form-control" readonly
                            style="border-radius: 10px; height: 45px; background-color: #f8f9fa;">
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold">Kategori</label>
                        <input type="text" id="detailCategory" class="form-control" readonly
                            style="border-radius: 10px; height: 45px; background-color: #f8f9fa;">
                    </div>
                    <div class="form-group mb-4">
                        <label class="font-weight-bold">Supplier</label>
                        <input type="text" id="detailSupplier" class="form-control" readonly
                            style="border-radius: 10px; height: 45px; background-color: #f8f9fa;">
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold">Satuan</label>
                        <input type="text" id="detailUnit" class="form-control" readonly
                            style="border-radius: 10px; height: 45px; background-color: #f8f9fa;">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold">Harga Beli</label>
                                <input type="text" id="detailBuy" class="form-control" readonly
                                    style="border-radius: 10px; height: 45px; background-color: #f8f9fa;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold">Harga Jual</label>
                                <input type="text" id="detailSell" class="form-control" readonly
                                    style="border-radius: 10px; height: 45px; background-color: #f8f9fa;">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {

                // GLOBAL CSRF
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    }
                });

                // FORMAT ANGKA
                function formatRupiah(angka) {
                    let number_string = angka.replace(/[^,\d]/g, '').toString();
                    let split = number_string.split(',');
                    let sisa = split[0].length % 3;
                    let rupiah = split[0].substr(0, sisa);
                    let ribuan = split[0]
                        .substr(sisa)
                        .match(/\d{3}/gi);
                    if (ribuan) {
                        let separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }

                    return split[1] != undefined ?
                        rupiah + ',' + split[1] :
                        rupiah;
                }

                // FORMAT RUPIAH REALTIME
                $(document).on('keyup',
                    'input[name="purchase_price"], input[name="selling_price"], #editBuy, #editSell',
                    function() {
                        $(this).val(
                            formatRupiah($(this).val())
                        );
                    }
                );

                // SEARCH
                $('#searchProduct').on('keyup', function() {
                    let value = $(this).val().toLowerCase();
                    $('#productTable tr').filter(function() {
                        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                    });
                });

                // RESET MODAL ADD
                function resetAddModal() {
                    let modal = $('#modalAdd');

                    modal.find('input, select').val('');
                    modal.find('select').prop('selectedIndex', 0);

                    modal.find('.alert').remove();
                    modal.find('.text-danger').remove();
                    modal.find('.is-invalid').removeClass('is-invalid');
                }

                $('button[data-target="#modalAdd"]').on('click', resetAddModal);
                $('#modalAdd').on('hidden.bs.modal', resetAddModal);

                // ADD PRODUCT
                $(document).on('submit', '#formAdd', function(e) {
                    e.preventDefault();
                    let form = $(this);
                    let modal = $('#modalAdd');

                    // Ambil data form
                    let formData = form.serializeArray();

                    // BERSIHKAN DATA HARGA (Hapus "Rp " dan titik)
                    formData.forEach(item => {
                        if (item.name == 'purchase_price' || item.name == 'selling_price') {
                            item.value = item.value.replace(/[^0-9]/g, '');
                        }
                    });

                    $.post("{{ route('products.store') }}", $.param(formData))
                        .done(function(res) {
                            modal.modal('hide');
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Produk berhasil ditambahkan',
                                showConfirmButton: false,
                                timer: 2000
                            });

                            // Refresh tabel agar data baru muncul
                            setTimeout(() => location.reload(), 1000);
                        })
                        .fail(function(xhr) {

                            modal.find('.alert').remove();
                            modal.find('.text-danger').remove();
                            modal.find('.is-invalid').removeClass('is-invalid');

                            if (xhr.status === 422) {

                                let errors = xhr.responseJSON.errors;

                                $.each(errors, function(field, messages) {
                                    let input = modal.find('[name="' + field + '"]');

                                    input.addClass('is-invalid');

                                    input.after(`
                        <small class="text-danger">${messages[0]}</small>
                    `);
                                });

                            } else if (xhr.status === 419) {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'warning',
                                    title: 'Session habis, refresh halaman!',
                                    showConfirmButton: false,
                                    timer: 3000
                                });

                            } else {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'error',
                                    title: 'Server error!',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            }
                        });
                });

                // EDIT BUTTON
                $(document).on('click', '.btn-edit', function() {

                    let data = $(this).data('product');

                    $('#editCode').val(data.product_code);
                    $('#editName').val(data.product_name);
                    $('#editCategory').val(data.category_id);
                    let supplierId = null;
                    if (data.suppliers.length > 0) {
                        supplierId = data.suppliers[0].id;
                    }
                    $('#editSupplier').val(supplierId);
                    $('#editUnit').val(data.unit);
                    $('#editBuy').val(Number(data.purchase_price).toLocaleString('id-ID'));
                    $('#editSell').val(Number(data.selling_price).toLocaleString('id-ID'));

                    $('#formEdit').attr('action', "/products/" + data.id);

                    $('#modalEdit .alert').remove();
                    $('#modalEdit .text-danger').remove();
                    $('#modalEdit .is-invalid').removeClass('is-invalid');

                    $('#modalEdit').modal('show');
                });

                // RESET MODAL EDIT
                $('#modalEdit').on('hidden.bs.modal', function() {
                    let modal = $(this);

                    modal.find('.alert').remove();
                    modal.find('.text-danger').remove();
                    modal.find('.is-invalid').removeClass('is-invalid');
                });

                // DETAIL PRODUCT
                $(document).on('click', '.btn-detail', function() {
                    $('#detailCode').val($(this).data('code'));
                    $('#detailName').val($(this).data('name'));
                    $('#detailCategory').val($(this).data('category'));
                    $('#detailSupplier').val($(this).data('supplier'));
                    $('#detailUnit').val($(this).data('unit'));
                    $('#detailBuy').val(
                        'Rp ' + Number($(this).data('buy'))
                        .toLocaleString('id-ID')
                    );
                    $('#detailSell').val(
                        'Rp ' + Number($(this).data('sell'))
                        .toLocaleString('id-ID')
                    );

                });

                // UPDATE PRODUCT
                $('#formEdit').on('submit', function(e) {
                    e.preventDefault();

                    let url = $(this).attr('action');
                    let modal = $('#modalEdit');

                    let formData = $(this).serializeArray();
                    formData.forEach(item => {
                        if (item.name == 'purchase_price' ||
                            item.name == 'selling_price') {
                            item.value = item.value.replace(/\./g, '');
                        }
                    });
                    formData.push({
                        name: '_method',
                        value: 'PUT'
                    });
                    $.post(
                            url,
                            $.param(formData)
                        )
                        .done(function(res) {
                            modal.modal('hide');

                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: res.message,
                                showConfirmButton: false,
                                timer: 4000,
                                timerProgressBar: true
                            });
                            setTimeout(() => {
                                location.reload();
                            }, 1200);
                        })
                        .fail(function(xhr) {

                            modal.find('.alert').remove();
                            modal.find('.text-danger').remove();
                            modal.find('.is-invalid').removeClass('is-invalid');

                            if (xhr.status === 422) {

                                let errors = xhr.responseJSON.errors;

                                $.each(errors, function(field, messages) {
                                    let input = modal.find('[name="' + field + '"]');

                                    input.addClass('is-invalid');

                                    input.after(`
                        <small class="text-danger">${messages[0]}</small>
                    `);
                                });

                            } else {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'error',
                                    title: 'Server error!',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            }
                        });
                });

            });
            document.getElementById('perPageSelect').addEventListener('change', function() {
                const url = new URL(window.location.href);
                url.searchParams.set('per_page', this.value);
                url.searchParams.set('page', 1);
                window.location.href = url.toString();
            });
        </script>
        @if (session('success'))
            <script>
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true
                });
            </script>
        @endif

        @if (session('error'))
            <script>
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            </script>
        @endif
    @endpush
@endsection
