@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- HEADER -->
        <div class="mb-3">
            <h3 class="font-weight-bold mb-2">Stock Inventory</h3>

            {{-- <div class="d-flex justify-content-between">

                <!-- SEARCH -->
                <input type="text" id="searchStock" class="form-control" placeholder="Search product..."
                    style="width: 300px;">

                <!-- RIGHT -->
                <a href="{{ route('stocks.movements') }}" class="btn btn-dark">
                    <i class="fas fa-exchange-alt"></i> Stock Movements
                </a>

            </div> --}}
            <div class="d-flex justify-content-between align-items-center">
                <input type="text" id="searchStock" class="form-control" placeholder="Search product..."
                    style="width: 300px;">
                <div class="d-flex">
                    <button type="button" class="btn btn-outline-dark mr-2" id="btnFilter"> <i class="fas fa-filter"></i>
                        Filter
                    </button>

                    <a href="{{ route('stocks.movements') }}" class="btn btn-dark">
                        <i class="fas fa-exchange-alt"></i> Stock Movements
                    </a>
                </div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="card shadow-sm">
            <div class="card-body p-0">

                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Stok Saat ini</th>
                            <th>Safety Stok</th>
                            <th>ROP</th>
                            <th>Status</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="stockTable">
                        @foreach ($stocks as $i => $s)
                            <tr data-status="{{ $s->current_stock <= $s->reorder_point ? 'peringatan' : 'aman' }}">
                                <td>{{ $stocks->firstItem() + $i }}</td>
                                <td>{{ $s->product->product_name }}</td>
                                <td>{{ $s->current_stock }}</td>
                                <td>{{ $s->safety_stock }}</td>
                                <td>{{ $s->reorder_point }}</td>

                                <td>
                                    @if ($s->current_stock <= $s->reorder_point)
                                        <span class="badge badge-danger">Peringatan</span>
                                    @else
                                        <span class="badge badge-success">Aman</span>
                                    @endif
                                </td>

                                <td>
                                    <!-- EDIT -->
                                    <i class="fas fa-pen text-warning mr-3 btnEdit" style="cursor:pointer;"
                                        data-id="{{ $s->id }}" data-name="{{ $s->product->product_name }}"
                                        data-stock="{{ $s->current_stock }}" data-safety="{{ $s->safety_stock }}"
                                        data-rop="{{ $s->reorder_point }}">
                                    </i>

                                    <!-- DETAIL -->
                                    <i class="fas fa-eye text-info btnDetail" style="cursor:pointer;"
                                        data-name="{{ $s->product->product_name }}" data-stock="{{ $s->current_stock }}"
                                        data-safety="{{ $s->safety_stock }}" data-rop="{{ $s->reorder_point }}">
                                    </i>
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
                        from {{ $stocks->total() }}
                    </span>

                </div>
            </div>

            <div class="card-footer">
                {{ $stocks->links() }}
            </div>
        </div>

    </div>

    {{-- MODAL EDIT --}}
    <div class="modal fade" id="modalEdit">
        <div class="modal-dialog">
            <form method="POST" id="formEdit">
                @csrf
                @method('PUT')

                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Edit Stock</h5>
                    </div>

                    <div class="modal-body">

                        <!-- NAMA PRODUK -->
                        <div class="form-group">
                            <label>Nama Produk</label>
                            <input type="text" id="editName" class="form-control" readonly>
                        </div>

                        <!-- STOCK SAAT INI -->
                        <div class="form-group">
                            <label>Stok Saat Ini</label>
                            <input type="number" id="editStock" class="form-control" readonly>
                        </div>

                        <!-- SAFETY -->
                        <div class="form-group">
                            <label>Safety Stock</label>
                            <input type="number" name="safety_stock" id="editSafety" class="form-control">
                        </div>

                        <!-- ROP -->
                        <div class="form-group">
                            <label>Reorder Point</label>
                            <input type="number" name="reorder_point" id="editROP" class="form-control">
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-success">Update</button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    {{-- MODAL DETAIL --}}
    <div class="modal fade" id="modalDetail">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>Detail Stock</h5>
                </div>

                <div class="modal-body">

                    <p><b>Nama Produk:</b> <span id="dName"></span></p>
                    <p><b>Jumlah Stok saat ini:</b> <span id="dStock"></span></p>
                    <p><b>Safety Stock:</b> <span id="dSafety"></span></p>
                    <p><b>ROP:</b> <span id="dROP"></span></p>

                </div>

            </div>
        </div>
    </div>

    <div id="filterPanel" class="filter-panel">

        <div class="filter-header">
            <h5>Filter</h5>
            <button type="button" id="closeFilter">&times;</button>
        </div>

        <form method="GET">

            <div class="filter-body">

                <h6>Status Stock</h6>

                <div>
                    <input type="radio" name="status" value="" {{ request('status') == '' ? 'checked' : '' }}>
                    Semua
                </div>

                <div>
                    <input type="radio" name="status" value="aman" {{ request('status') == 'aman' ? 'checked' : '' }}>
                    Aman
                </div>

                <div>
                    <input type="radio" name="status" value="peringatan"
                        {{ request('status') == 'peringatan' ? 'checked' : '' }}> Peringatan
                </div>

            </div>

            <div class="filter-footer">
                <a href="{{ route('stocks.index') }}" class="btn btn-light">Reset</a>
                <button class="btn btn-success">Terapkan</button>
            </div>

        </form>

    </div>

    @push('scripts')
        <script>
            $('.btnEdit').click(function() {
                let id = $(this).data('id');
                let name = $(this).data('name');
                let stock = $(this).data('stock');
                let safety = $(this).data('safety');
                let rop = $(this).data('rop');

                $('#editName').val(name);
                $('#editStock').val(stock);
                $('#editSafety').val(safety);
                $('#editROP').val(rop);

                $('#formEdit').attr('action', '/stocks/' + id);

                $('#modalEdit').modal('show');
            });

            $('#searchStock').on('keyup', function() {
                let value = $(this).val().toLowerCase();

                $('#stockTable tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // buka panel
            $(document).ready(function() {

                // OPEN FILTER
                $('#btnFilter').click(function() {
                    $('#filterPanel').addClass('active');
                });

                // CLOSE FILTER
                $('#closeFilter').click(function() {
                    $('#filterPanel').removeClass('active');
                });

            });

            $('.btnDetail').click(function() {
                $('#dName').text($(this).data('name'));
                $('#dStock').text($(this).data('stock'));
                $('#dSafety').text($(this).data('safety'));
                $('#dROP').text($(this).data('rop'));

                $('#modalDetail').modal('show');
            });
        </script>
    @endpush
@endsection
