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

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

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

            </div>

            {{-- PAGINATION DI DALAM CARD --}}
            <div class="card-footer d-flex justify-content-between align-items-center">

                <div class="d-flex align-items-center mt-3">

                    <span class="mr-2">Show</span>

                    <select class="form-control mr-2" style="width: 80px;" onchange="changeLimit(this.value)">
                        <option value="10" {{ request('limit') == 5 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('limit') == 10 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('limit') == 25 ? 'selected' : '' }}>50</option>
                    </select>

                    <span>
                        from {{ $purchases->total() }}
                    </span>

                </div>
            </div>
        </div>

    </div>
    </div>

    </div>

    <!-- FILTER PANEL -->
    <div id="filterPanel" class="filter-panel">

        <div class="filter-header">
            <h5 class="mb-0">Filter</h5>
            <button type="button" id="closeFilter">&times;</button>
        </div>

        <form method="GET" action="{{ route('purchases.index') }}">

            <div class="filter-body">

                <!-- SUPPLIER -->
                <h6 class="mb-2">Nama Supplier</h6>
                @foreach ($suppliers as $s)
                    <div class="mb-1">
                        <input type="radio" name="supplier_id" value="{{ $s->id }}"
                            {{ request('supplier_id') == $s->id ? 'checked' : '' }}>
                        {{ $s->supplier_name }}
                    </div>
                @endforeach

                <hr>

                <!-- DATE -->
                <h6 class="mb-2">Rentang Tanggal</h6>

                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control mb-2">

                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">

            </div>

            <div class="filter-footer">
                <a href="{{ route('purchases.index') }}" class="btn btn-light">Reset</a>
                <button class="btn btn-success">Terapkan</button>
            </div>
        </form>
    </div>

    <!-- ================= MODAL ADD ================= -->
    <div class="modal fade" id="modalAdd">
        <div class="modal-dialog modal-lg">

            <form action="{{ route('purchases.store') }}" method="POST" id="formPurchase">
                @csrf

                <div class="modal-content">

                    <div class="modal-header">
                        <h5>Tambah Pesanan Pembelian</h5>
                    </div>

                    <div class="modal-body">

                        {{-- ERROR VALIDATION --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- SUPPLIER -->
                        <label>Supplier</label>
                        <select name="supplier_id" class="form-control mb-2">
                            @foreach ($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->supplier_name }}</option>
                            @endforeach
                        </select>

                        <label>Tanggal</label>
                        <input type="date" name="purchase_date" class="form-control mb-3">

                        <!-- TABLE PRODUK -->
                        <table class="table" id="tableProduct">
                            <thead>
                                <tr>
                                    <th>Nama Produk</th>
                                    <th width="100">Jumlah</th>
                                    <th width="150">Harga Satuan</th>
                                    <th width="150">Subtotal</th>
                                    <th width="50"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                        <button type="button" class="btn btn-primary btn-sm" id="addRow">
                            + Tambah Pesanan
                        </button>
                        <h4 class="mt-3">Total: Rp <span id="total">0</span></h4>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    @push('scripts')
        <script>
            let products = @json($products);

            function rupiah(num) {
                return new Intl.NumberFormat('id-ID').format(num);
            }

            // ADD ROW
            $('#addRow').click(function() {

                let index = $('#tableProduct tbody tr').length;

                let row = `
<tr>
    <td>
        <select name="products[${index}][product_id]" class="form-control">
            ${products.map(p => `<option value="${p.id}">${p.product_name}</option>`).join('')}
        </select>
    </td>

    <td>
        <input type="number" name="products[${index}][qty]" class="form-control qty" value="1" min="1">
    </td>

    <td>
        <input type="number" name="products[${index}][price]" class="form-control price" value="0" min="0">
    </td>

    <td class="subtotal">0</td>

    <td>
        <button type="button" class="btn btn-danger btn-sm remove">x</button>
    </td>
</tr>
`;
                $('#tableProduct tbody').append(row);
            });

            // REMOVE
            $(document).on('click', '.remove', function() {
                $(this).closest('tr').remove();
                total();
            });

            // HITUNG
            $(document).on('keyup change', '.qty, .price', function() {
                let row = $(this).closest('tr');
                let qty = parseInt(row.find('.qty').val()) || 0;
                let price = parseInt(row.find('.price').val()) || 0;
                let subtotal = qty * price;
                row.find('.subtotal').text(rupiah(subtotal));
                total();
            });

            // TOTAL
            function total() {
                let total = 0;
                $('.subtotal').each(function() {
                    total += parseInt($(this).text().replace(/\./g, '')) || 0;
                });
                $('#total').text(rupiah(total));
            }

            // RESET MODAL
            $('#modalAdd').on('shown.bs.modal', function() {
                $('#tableProduct tbody').html('');
                $('#addRow').click();
            });

            // VALIDASI SUBMIT (FIX)
            $('#formPurchase').submit(function() {
                if ($('#tableProduct tbody tr').length === 0) {
                    alert('Tambahkan minimal 1 produk!');
                    return false;
                }
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
        </script>
        @if ($errors->any())
            $('#modalAdd').modal('show');
        @endif
    @endpush
@endsection
