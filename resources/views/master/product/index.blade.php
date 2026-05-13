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

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif


        <!-- TABLE -->
        <div class="card">
            <div class="card-body">

                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Category</th>
                            <th>Satuan</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="productTable">
                        @foreach ($products as $i => $p)
                            <tr>
                                <td>{{ $products->firstItem() + $i }}</td>
                                <td>{{ $p->product_code }}</td>
                                <td>{{ $p->product_name }}</td>
                                <td>{{ $p->category->category_name }}</td>
                                <td>{{ $p->unit }}</td>
                                <td>Rp {{ number_format($p->purchase_price, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($p->selling_price, 0, ',', '.') }}</td>

                                <td>
                                    <!-- DETAIL -->
                                    <a href="#" class="action-icon text-info btn-detail"
                                        data-code="{{ $p->product_code }}" data-name="{{ $p->product_name }}"
                                        data-category="{{ $p->category->category_name }}" data-unit="{{ $p->unit }}"
                                        data-buy="{{ $p->purchase_price }}" data-sell="{{ $p->selling_price }}"
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

            </div>
            <div class="card-footer d-flex justify-content-between align-items-center">

                <div class="d-flex align-items-center mt-3">

                    <span class="mr-2">Show</span>

                    <select class="form-control mr-2" style="width: 80px;" onchange="changeLimit(this.value)">
                        <option value="10" {{ request('limit') == 5 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('limit') == 10 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('limit') == 25 ? 'selected' : '' }}>50</option>
                    </select>

                    <span>
                        from {{ $products->total() }}
                    </span>

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

                    <div class="modal-body">
                        {{-- <label>Kode Produk</label>
                        <input type="text" name="product_code" class="form-control mb-2" placeholder="Kode produk">
                        <label>Nama Produk</label>
                        <input type="text" name="product_name" class="form-control mb-2" placeholder="Nama produk"> --}}
                        <div class="form-group">
    <label>Kode Produk</label>
    <input type="text" name="product_code" class="form-control" placeholder="Kode Produk">
</div>

<div class="form-group">
    <label>Nama Produk</label>
    <input type="text" name="product_name" class="form-control" placeholder="Nama Produk">
</div>
                        <label>Kategori</label>
                        <select name="category_id" class="form-control mb-2">
                            <option value="">-- Kategori --</option>
                            @foreach ($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->category_name }}</option>
                            @endforeach
                        </select>
                        <label>Satuan</label>
                        <input type="text" name="unit" class="form-control mb-2" placeholder="Satuan">
                        <label>Harga Beli</label>
                        <input type="number" name="purchase_price" class="form-control mb-2" placeholder="Harga beli">
                        <label>Harga Jual</label>
                        <input type="number" name="selling_price" class="form-control mb-2" placeholder="Harga jual">

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

                    <div class="modal-body">
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        <label>Kode Produk</label>
                        <input type="text" id="editCode" name="product_code" class="form-control mb-2">
                        <label>Nama Produk</label>
                        <input type="text" id="editName" name="product_name" class="form-control mb-2">
                        <label>Kategori</label>
                        <select id="editCategory" name="category_id" class="form-control mb-2">
                            @foreach ($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->category_name }}</option>
                            @endforeach
                        </select>
                        <label>Satuan</label>
                        <input type="text" id="editUnit" name="unit" class="form-control mb-2">
                        <label>Harga Beli</label>
                        <input type="number" id="editBuy" name="purchase_price" class="form-control mb-2">
                        <label>Harga Jual</label>
                        <input type="number" id="editSell" name="selling_price" class="form-control mb-2">

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

                <div class="modal-body">

                    <div class="form-group">
                        <label>Kode Produk</label>
                        <input type="text" id="detailCode" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label>Nama Produk</label>
                        <input type="text" id="detailName" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label>Kategori</label>
                        <input type="text" id="detailCategory" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label>Satuan</label>
                        <input type="text" id="detailUnit" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label>Harga Beli</label>
                        <input type="text" id="detailBuy" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label>Harga Jual</label>
                        <input type="text" id="detailSell" class="form-control" readonly>
                    </div>

                </div>

            </div>
        </div>
    </div>

@push('scripts')
<script>
$(document).ready(function() {

    // =========================
    // GLOBAL CSRF (WAJIB)
    // =========================
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    });

    // =========================
    // SEARCH
    // =========================
    $('#searchProduct').on('keyup', function() {
        let value = $(this).val().toLowerCase();
        $('#productTable tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // =========================
    // RESET MODAL ADD
    // =========================
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

    // =========================
    // ADD PRODUCT
    // =========================
    $(document).on('submit', '#formAdd', function(e) {
        e.preventDefault();

        let form = $(this);
        let modal = $('#modalAdd');

        $.post("{{ route('products.store') }}", form.serialize())
        .done(function() {
            modal.modal('hide');
            location.reload();
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

                modal.find('.modal-body').prepend(`
                    <div class="alert alert-danger">
                        Session habis, refresh halaman sekali saja!
                    </div>
                `);

            } else {

                modal.find('.modal-body').prepend(`
                    <div class="alert alert-danger">
                        Server error!
                    </div>
                `);
            }
        });
    });

    // =========================
    // EDIT BUTTON
    // =========================
    $(document).on('click', '.btn-edit', function() {

        let data = $(this).data('product');

        $('#editCode').val(data.product_code);
        $('#editName').val(data.product_name);
        $('#editCategory').val(data.category_id);
        $('#editUnit').val(data.unit);
        $('#editBuy').val(data.purchase_price);
        $('#editSell').val(data.selling_price);

        $('#formEdit').attr('action', "/products/" + data.id);

        $('#modalEdit .alert').remove();
        $('#modalEdit .text-danger').remove();
        $('#modalEdit .is-invalid').removeClass('is-invalid');

        $('#modalEdit').modal('show');
    });

    // =========================
    // RESET MODAL EDIT
    // =========================
    $('#modalEdit').on('hidden.bs.modal', function () {
        let modal = $(this);

        modal.find('.alert').remove();
        modal.find('.text-danger').remove();
        modal.find('.is-invalid').removeClass('is-invalid');
    });

    // =========================
    // UPDATE PRODUCT
    // =========================
    $('#formEdit').on('submit', function(e) {
        e.preventDefault();

        let url = $(this).attr('action');
        let modal = $('#modalEdit');

        $.post(url, $(this).serialize() + '&_method=PUT')
        .done(function(res) {

            modal.modal('hide');

            $('body').prepend(`
                <div class="alert alert-success" style="
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 9999;
                ">
                    ${res.message}
                </div>
            `);

            setTimeout(() => $('.alert-success').fadeOut(), 2000);

            location.reload();
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

                modal.find('.modal-body').prepend(`
                    <div class="alert alert-danger">
                        Server error!
                    </div>
                `);
            }
        });
    });

});
</script>
@endpush
@endsection
