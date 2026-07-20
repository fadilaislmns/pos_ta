@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- HEADER -->
        <div class="mb-3">
            <h3 class="font-weight-bold mb-2">Pesanan Pembelian</h3>
            <div class="d-flex justify-content-between align-items-center">
                <input type="text" id="searchPurchase" class="form-control" placeholder="Search purchase..."
                    style="width: 300px;">
                <div class="d-flex">
                    <button class="btn btn-outline-dark mr-2" id="btnFilter">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    <button class="btn btn-dark" data-toggle="modal" data-target="#modalAdd">
                        <i class="fas fa-plus mr-1"></i> Add Purchase
                    </button>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">

                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Tanggal</th>
                            <th>Nama Supplier</th>
                            <th>Total</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="purchaseTable">
                        @foreach ($purchases as $i => $p)
                            <tr>
                                {{-- NOMOR TIDAK RESET --}}
                                <td>{{ $purchases->firstItem() + $i }}</td>

                                <td>{{ \Carbon\Carbon::parse($p->purchase_date)->format('d-m-Y') }}</td>
                                <td>{{ $p->supplier->supplier_name }}</td>
                                <td>Rp {{ number_format($p->total, 0, ',', '.') }}</td>

                                <td>
                                    <a href="{{ route('purchases.show', $p->id) }}" class="text-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div
                    class="card-footer bg-white border-top-0 d-flex flex-wrap justify-content-between align-items-center py-3">
                    <div class="text-muted small">
                        Menampilkan <strong>{{ $purchases->firstItem() }}</strong> sampai
                        <strong>{{ $purchases->lastItem() }}</strong> dari
                        <strong>{{ $purchases->total() }}</strong> data
                    </div>

                    <div class="d-flex align-items-center">
                        <!-- Select Per Page -->
                        <select id="perPageSelect" class="form-control form-control-sm mr-3" style="width: 70px;">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        </select>

                        <!-- Pagination Links -->
                        <div class="pagination-wrapper">
                            {{ $purchases->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER PANEL -->
    <div id="filterPanel" class="filter-panel">
        <div class="filter-header d-flex justify-content-between align-items-center"">
            <h5 class="mb-0">Filter</h5>
            <button class="btn btn-sm btn-light" id="closeFilter">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="GET" action="{{ route('purchases.index') }}">
            <div class="filter-body">
                <!-- SUPPLIER -->
                <div class="form-group">
                    <label class="font-weight-bold">
                        Supplier
                    </label>

                    <select name="supplier_id" class="form-control">
                        <option value="">
                            Semua Supplier
                        </option>
                        @foreach ($suppliers as $s)
                            <option value="{{ $s->id }}" {{ request('supplier_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->supplier_name }}
                            </option>
                        @endforeach

                    </select>
                </div>
                <hr>
                <!-- DATE -->
                <h6 class="mb-2">Rentang Tanggal</h6>
                <div class="form-group mt-3">
                    <label class="font-weight-bold">
                        Dari Tanggal
                    </label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">
                        Sampai Tanggal
                    </label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                </div>

                <div class="filter-footer d-flex justify-content-between">
                    <a href="{{ route('purchases.index') }}" class="btn btn-light">
                        Reset
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Terapkan
                    </button>
                </div>
        </form>
    </div>

    <!-- ================= MODAL ADD ================= -->
    <div class="modal fade" id="modalAdd">
        <div class="modal-dialog modal-xl">
            <form action="{{ route('purchases.store') }}" method="POST" id="formPurchase">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Tambah Pembelian</h5>
                    </div>
                    <div class="modal-body">
                        <label>Supplier</label>
                        <select id="supplierSelect" name="supplier_id" class="form-control mb-3">
                            @foreach ($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->supplier_name }}</option>
                            @endforeach
                        </select>

                        <label>Tanggal</label>
                        <input type="date" name="purchase_date" class="form-control mb-3" required>

                        <table class="table" id="tableProduct">
                            <thead>
                                <tr>
                                    <th width="250">Nama Produk</th>
                                    <th width="150">Jumlah</th>
                                    <th>Harga Satuan</th>
                                    <th>Harga Jual</th>
                                    <th width="200">Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <button type="button" class="btn btn-primary btn-sm" id="addRow">+ Tambah Pesanan</button>
                        <h4 class="mt-3">Total: <span id="totalDisplay">Rp 0</span></h4>
                    </div>
                    <div class="modal-footer"><button type="submit" class="btn btn-success">Simpan</button></div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            let autoPO = @json($autoPO);
            let currentProducts = [];

            function rupiah(angka) {
                return 'Rp ' + Number(angka).toLocaleString('id-ID');
            }

            // FUNGSI UBAH KE ANGKA MURNI (Untuk Kalkulasi)
            function angka(value) {
                if (!value) return 0;
                return parseInt(value.toString().replace(/[^0-9]/g, '')) || 0;
            }

            function total() {
                let grandTotal = 0;
                $('.subtotal').each(function() {
                    grandTotal += angka($(this).text());
                });
                $('#totalDisplay').text(rupiah(grandTotal));
            }

            // ADD ROW
            $('#addRow').click(function() {
                let index = $('#tableProduct tbody tr').length;
                let row = `
            <tr>
                <td>
                    <select name="products[${index}][product_id]" class="form-control product-select" required>
                        <option value="">Pilih Produk</option>
                        ${currentProducts.map(p => `<option value="${p.id}">${p.product_name}</option>`).join('')}
                    </select>
                </td>
                <td><input type="number" name="products[${index}][qty]" class="form-control qty" value="1" min="1" required></td>
                <td><input type="text" name="products[${index}][price]" class="form-control price" value="0" required></td>
                <td><input type="text" name="products[${index}][selling_price]" class="form-control selling_price" value="0" required></td>
                <td class="subtotal">0</td>
                <td><button type="button" class="btn btn-danger btn-sm remove">x</button></td>
            </tr>`;
                $('#tableProduct tbody').append(row);
            });

            // EVENT: HITUNG SUB-TOTAL (Keyup pada price)
            $(document).on('keyup change', '.qty, .price', function() {
                let row = $(this).closest('tr');
                let qty = parseInt(row.find('.qty').val()) || 0;
                let price = angka(row.find('.price').val());

                row.find('.subtotal').text(rupiah(qty * price));
                total();
            });

            // EVENT: FORMAT RUPIAH SAAT KETIK
            $(document).on('keyup', '.price, .selling_price', function() {
                let val = angka($(this).val());
                $(this).val(rupiah(val));
            });

            // REMOVE
            $(document).on('click', '.remove', function() {
                $(this).closest('tr').remove();
                total();
            });

            // CHANGE PRODUK
            $(document).on('change', '.product-select', function() {
                let row = $(this).closest('tr');
                let product = currentProducts.find(p => p.id == $(this).val());
                if (product) {
                    row.find('.price').val(rupiah(product.purchase_price));
                    row.find('.selling_price').val(rupiah(product.selling_price));
                    row.find('.price').trigger('change'); // Ini memicu event hitung subtotal
                    total(); // Tambahkan ini agar total langsung terupdate
                }
            });

            // SUPPLIER CHANGE
            $('#supplierSelect').change(function() {
                let supplierId = $(this).val();
                if (!supplierId) return;
                $.get('/supplier-products/' + supplierId, function(products) {
                    currentProducts = products;
                    // Kalau sudah ada row, update dropdownnya
                    $('.product-select').each(function() {
                        let selected = $(this).val();

                        $(this).html(
                            '<option value="">Pilih Produk</option>' +
                            products.map(p =>
                                `<option value="${p.id}">${p.product_name}</option>`
                            ).join('')
                        );

                        $(this).val(selected);
                    });

                    // ===== AUTO PO =====
                    if (autoPO) {
                        $('#tableProduct tbody').empty();
                        $('#addRow').trigger('click');
                        let row = $('#tableProduct tbody tr:last');
                        row.find('.product-select')
                            .val(String(autoPO.product_id))
                            .trigger('change');
                        row.find('.qty')
                            .val(autoPO.qty)
                            .trigger('change');
                        autoPO = null;
                    }
                });
            });

            $('#modalAdd').on('shown.bs.modal', function() {
                if (autoPO) {
                    $('#supplierSelect')
                        .val(autoPO.supplier_id)
                        .trigger('change');
                }
            });

            // VALIDASI SUBMIT (KONVERSI SEMUA HARGA KE ANGKA MURNI SEBELUM KIRIM)
            $('#formPurchase').submit(function() {
                if ($('#tableProduct tbody tr').length === 0) {
                    alert('Tambahkan minimal 1 produk!');
                    return false;
                }
                // Konversi kembali ke angka sebelum dikirim ke server
                $('.price, .selling_price').each(function() {
                    $(this).val(angka($(this).val()));
                });
            });

            // FILTER PANEL
            $('#btnFilter').click(function() {
                $('#filterPanel').addClass('active');
            });
            $('#closeFilter').click(function() {
                $('#filterPanel').removeClass('active');
            });

            // SEARCH
            $('#searchPurchase').on('keyup', function() {
                let value = $(this).val().toLowerCase();
                $('#purchaseTable tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });

            });
            $('#perPageSelect').on('change', function() {
                let perPage = $(this).val();
                let url = new URL(window.location.href);
                url.searchParams.set('per_page', perPage);
                window.location.href = url.toString();
            });
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if (request('auto_po'))
                $(document).ready(function() {
                    $('#modalAdd').modal('show');
                });
            @endif
        </script>
        @if ($errors->any())
            $('#modalAdd').modal('show');
        @endif
    @endpush
@endsection
