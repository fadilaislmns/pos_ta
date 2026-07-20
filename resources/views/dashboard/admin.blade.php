@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- TITLE -->
        <div class="mb-4">
            <h3 class="font-weight-bold">
                Dashboard Admin
            </h3>
        </div>

        <!-- SUMMARY -->
        <div class="row">
            <!-- TOTAL SALES -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">
                                Total Penjualan
                            </div>
                            <h4 class="font-weight-bold mb-1">
                                Rp {{ number_format($totalSales, 0, ',', '.') }}
                            </h4>

                            <small class="{{ $salesGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="fas fa-arrow-up"></i>
                                {{ number_format($salesGrowth, 1) }} % dari bulan lalu
                            </small>
                        </div>

                        <div class="dashboard-icon bg-primary">
                            <i class="fas fa-cash-register"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TOTAL PURCHASE -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">
                                Total Pembelian
                            </div>
                            <h4 class="font-weight-bold mb-1">
                                Rp {{ number_format($totalPurchases, 0, ',', '.') }}
                            </h4>

                            <small class="{{ $purchaseGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="fas fa-arrow-up"></i>
                                {{ number_format($purchaseGrowth, 1) }}% dari bulan lalu
                            </small>
                        </div>

                        <div class="dashboard-icon bg-warning">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PROFIT -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">
                                Total Profit
                            </div>

                            <h4 class="font-weight-bold mb-1">
                                Rp {{ number_format($profit, 0, ',', '.') }}
                            </h4>

                            <small class="{{ $profitGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="fas fa-arrow-up"></i>
                                {{ number_format($profitGrowth, 1) }}% dari bulan lalu
                            </small>
                        </div>

                        <div class="dashboard-icon bg-success">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TOTAL PRODUK -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">

                        <div>
                            <div class="text-muted small">
                                Total Produk
                            </div>

                            <h4 class="font-weight-bold mb-1">
                                {{ $totalProducts }} Produk
                            </h4>

                            <a href="{{ route('products.index') }}"
                                class="text-primary small font-weight-medium text-decoration-none">
                                Produk aktif di sistem
                            </a>
                        </div>

                        <div class="dashboard-icon bg-dark">
                            <i class="fas fa-box"></i>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <!-- CHART -->
        <div class="row mt-2">

            <!-- LINE CHART -->
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 font-weight-bold">
                            Grafik Penjualan
                        </h6>

                        <!-- FILTER -->
                        <form method="GET" class="ml-auto">
                            <select name="months" class="form-control form-control-sm" style="width:130px;"
                                onchange="this.form.submit()">

                                <option value="1" {{ $months == 1 ? 'selected' : '' }}>
                                    1 Bulan
                                </option>

                                <option value="3" {{ $months == 3 ? 'selected' : '' }}>
                                    3 Bulan
                                </option>

                                <option value="6" {{ $months == 6 ? 'selected' : '' }}>
                                    6 Bulan
                                </option>

                                <option value="9" {{ $months == 9 ? 'selected' : '' }}>
                                    9 Bulan
                                </option>

                                <option value="12" {{ $months == 12 ? 'selected' : '' }}>
                                    12 Bulan
                                </option>

                            </select>
                        </form>
                    </div>

                    <div class="card-body">
                        <canvas id="salesChart" height="120"></canvas>
                    </div>
                </div>
            </div>

            <!-- CATEGORY -->
            <div class="col-lg-3 col-md-6 mb-3">

                <div class="card shadow-sm border-0 h-100">

                    <div class="card-header bg-white border-0">
                        <h6 class="mb-0 font-weight-bold">
                            Penjualan per Kategori
                        </h6>
                    </div>

                    <div class="card-body">
                        <canvas id="categoryChart"></canvas>
                    </div>

                </div>

            </div>

            <!-- STOCK ALERT -->
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-0 d-flex align-items-center">
                        <h6 class="mb-0 font-weight-bold text-danger">
                            Stok Produk Terendah
                        </h6>
                        <a href="{{ route('stocks.index') }}" class="small text-primary ml-auto">
                            Lihat Semua
                        </a>
                    </div>

                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-center">Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lowStocks as $stock)
                                    <tr>
                                        <td>{{ $stock->product->product_name }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-danger px-3 py-2">
                                                {{ $stock->current_stock }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-4">
                                            Semua stok aman
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <!-- FORECAST -->
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            Ringkasan Rekomendasi Pembelian
                        </h5>
                        <a href="{{ route('forecast.recommendation') }}" class="small text-primary ml-auto">
                            Lihat Detail Rekomendasi
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Prediksi 14 Hari</th>
                                    <th>Rekomendasi Pembelian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($forecastAlerts->take(5) as $item)
                                    <tr>
                                        <td>{{ $item->product_name }}</td>
                                        <td>{{ number_format($item->forecast) }}</td>
                                        <td>{{ number_format($item->recommendation) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TRANSAKSI TERBARU -->
            <div class="col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0 d-flex align-items-center">
                        <h6 class="mb-0 font-weight-bold">
                            Transaksi Terbaru
                        </h6>
                        <a href="{{ route('sales.index') }}" class="small text-primary ml-auto">
                            Lihat Semua
                        </a>
                    </div>

                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kasir</th>
                                    <th>Pelanggan</th>
                                    <th>Total</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($latestSales as $sale)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</td>
                                        <td>{{ $sale->user->name ?? '-' }}</td>
                                        <td>{{ $sale->customer->customer_name ?? 'Umum' }}</td>
                                        <td>Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            const ctx = document.getElementById('salesChart');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),

                    datasets: [{
                        label: 'Penjualan',
                        data: @json($chartSales),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59,130,246,.1)',
                        fill: true,
                        tension: .4
                    }]
                },

                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // PIE CHART
            const categoryCtx = document.getElementById('categoryChart');
            new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($categoryLabels),

                    datasets: [{
                        data: @json($categoryTotals),
                        backgroundColor: [
                            '#3b82f6',
                            '#22c55e',
                            '#f59e0b',
                            '#e11d48',
                            '#8b5cf6',
                            '#ec4899',
                            '#14b8a6',
                        ],
                        borderWidth: 0
                    }]
                },

                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        </script>
    @endpush
