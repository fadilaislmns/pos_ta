@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- TITLE -->
        <div class="mb-4">
            <h3 class="font-weight-bold">
                Dashboard
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

            <!-- USER -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">
                                Total Pengguna
                            </div>
                            <h4 class="font-weight-bold mb-1">
                                {{ $totalUsers }} Pengguna
                            </h4>

                            <small class="text-primary">
                                Lihat detail
                            </small>
                        </div>

                        <div class="dashboard-icon bg-dark">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CHART -->
        <div class="row mt-4">
            <!-- LINE CHART -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 font-weight-bold">
                                Grafik Penjualan
                            </h6>
                            <!-- FILTER -->
                            <form method="GET" class="ml-auto">
                                <select name="months" class="form-control form-control-sm" style="width:130px;" onchange="this.form.submit()">
                                    <option value="1" {{ $months == 1 ? 'selected' : '' }}> 1 Bulan</option>
                                    <option value="3" {{ $months == 3 ? 'selected' : '' }}>3 Bulan</option>
                                    <option value="6" {{ $months == 6 ? 'selected' : '' }}>6 Bulan</option>
                                    <option value="9" {{ $months == 9 ? 'selected' : '' }}>9 Bulan</option>
                                    <option value="12" {{ $months == 12 ? 'selected' : '' }}>12 Bulan</option>
                                </select>
                            </form>
                    </div>

                    <div class="card-body">
                        <canvas id="salesChart" height="120"></canvas>
                    </div>
                </div>
            </div>

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

            <!-- TRANSAKSI -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0 d-flex align-items-center">
                        <h6 class="mb-0 font-weight-bold">
                            Transaksi Terbaru
                        </h6>
                        <a href="/sales" class="small text-primary ml-auto">
                            Lihat Semua
                        </a>
                    </div>

                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <tbody>
                                @foreach ($latestSales as $sale)
                                    <tr>
                                        <td>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}
                                            </small>
                                            <div>
                                                {{ $sale->customer->customer_name ?? 'Umum' }}
                                            </div>
                                        </td>

                                        <td class="text-right font-weight-bold">
                                            Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
