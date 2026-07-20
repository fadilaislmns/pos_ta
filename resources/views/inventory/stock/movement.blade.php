@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="mb-3">

            <h3 class="font-weight-bold mb-2">Stock Movements</h3>

            <div class="d-flex justify-content-between align-items-center">

                <!-- SEARCH -->
                <form method="GET" class="d-flex">
                    <input type="text" name="search" class="form-control mr-2" placeholder="Search product / source..."
                        value="{{ request('search') }}" style="width: 300px;">
                </form>

                <!-- FILTER BUTTON -->
                <button type="button" class="btn btn-outline-dark" id="btnFilter">
                    <i class="fas fa-filter"></i> Filter
                </button>

            </div>

        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">

                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Product</th>
                            <th>Type</th>
                            <th>Qty</th>
                            <th>Source</th>
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($movements as $i => $m)
                            <tr>
                                <td>{{ $movements->firstItem() + $i }}</td>
                                <td>{{ $m->product->product_name }}</td>

                                <td>
                                    @if ($m->movement_type == 'IN')
                                        <span class="badge badge-success">IN</span>
                                    @else
                                        <span class="badge badge-danger">OUT</span>
                                    @endif
                                </td>

                                <td>{{ $m->quantity }}</td>
                                <td>{{ $m->source }}</td>
                                <td>{{ $m->created_at->format('d-m-Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>

            </div>
            <div class="card-footer d-flex justify-content-between align-items-center flex-wrap">
                <!-- LEFT -->
                <div class="d-flex align-items-center">
                    <span class="mr-2">Show</span>
                    <select class="form-control form-control-sm mr-2" style="width:80px;" onchange="changeLimit(this.value)">
                        <option value="10" {{ request('limit') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('limit') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('limit') == 50 ? 'selected' : '' }}>50</option>
                    </select>
                    <span>dari {{ $movements->total() }} data
                    </span>
                </div>
                <!-- RIGHT -->
                <div>
                    {{ $movements->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>

    </div>
    <div id="filterPanel" class="filter-panel">

        <div class="filter-header d-flex justify-content-between align-items-center"">
            <h5 class="mb-0">Filter</h5>
            <button class="btn btn-sm btn-light" id="closeFilter">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="GET">
            <div class="filter-body">
                <!-- PRODUCT -->
                <h6>Nama Produk</h6>
                <select name="product_id" class="form-control mb-3">
                    <option value="">Semua</option>
                    @foreach ($products as $p)
                        <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->product_name }}
                        </option>
                    @endforeach
                </select>

                <!-- TYPE -->
                <h6>Tipe</h6>
                <select name="type" class="form-control mb-3">
                    <option value="">Semua</option>
                    <option value="IN" {{ request('type') == 'IN' ? 'selected' : '' }}>IN</option>
                    <option value="OUT" {{ request('type') == 'OUT' ? 'selected' : '' }}>OUT</option>
                </select>

                <!-- DATE -->
                <h6>Rentang Tanggal</h6>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control mb-2">

                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">

            </div>

            <div class="filter-footer">
                <a href="{{ route('stocks.movements') }}" class="btn btn-light">Reset</a>
                <button class="btn btn-success">Terapkan</button>
            </div>

        </form>

    </div>
    @push('styles')
        <style>
            .pagination {
                margin-bottom: 0 !important;
            }
            .page-item.active .page-link {
                background-color: #007bff;
                border-color: #007bff;
            }
            .page-link {
                color: #6c757d;
            }
        </style>
    @endpush
    @push('scripts')
        <script>
            $(document).ready(function() {

                $('#btnFilter').click(function() {
                    $('#filterPanel').addClass('active');
                });

                $('#closeFilter').click(function() {
                    $('#filterPanel').removeClass('active');
                });

            });
            let delay;
            $('input[name="search"]').on('keyup', function() {
                clearTimeout(delay);
                delay = setTimeout(() => {
                    $(this).closest('form').submit();
                }, 500);
            });

            function changeLimit(limit) {

                let url =
                    new URL(window.location.href);

                url.searchParams.set('limit', limit);

                window.location.href = url;

            }
        </script>
    @endpush
@endsection
