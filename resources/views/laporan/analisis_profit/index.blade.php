@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- TITLE -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="font-weight-bold mb-1">
                    Laporan Analisis Profit
                </h2>
            </div>

            <div class="d-flex" style="gap:10px;">
                <a href="#" class="btn btn-light border">
                    Reset
                </a>

                <a href="{{ route('reports.profit.pdf', request()->query()) }}" class="btn btn-outline-danger">
                    <i class="fas fa-file-pdf"></i>
                    Export PDF
                </a>
            </div>
        </div>

        <!-- FILTER -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius:20px;">
            <div class="card-body">
                <form method="GET">
                    <div class="row align-items-end">
                        <!-- TAHUN -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="filter-label">
                                Tahun
                            </label>

                            <select name="year" class="form-control custom-input">
                                @for ($y = now()->year; $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <!-- DARI BULAN -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="filter-label">
                                Dari Bulan
                            </label>
                            <select name="from_month" class="form-control custom-input">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $fromMonth == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <!-- SAMPAI BULAN -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="filter-label">
                                Sampai Bulan
                            </label>
                            <select name="to_month" class="form-control custom-input">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $toMonth == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <!-- BUTTON -->
                        <div class="col-lg-3 mb-3">
                            <button class="btn btn-primary custom-btn w-100">
                                <i class="fas fa-filter mr-2"></i>
                                Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- SUMMARY -->
        <div class="row">
            <!-- PENDAPATAN -->
            <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius:18px;">
                    <div class="card-body d-flex align-items-center">
                        <div class="summary-icon bg-primary-light text-primary">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div class="ml-3">
                            <div class="summary-label">
                                Total Pendapatan
                            </div>
                            <h4 class="font-weight-bold mb-1 summary-value">
                                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                            </h4>
                            <small class="text-muted">
                                dari penjualan
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BIAYA -->
            <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius:18px;">
                    <div class="card-body d-flex align-items-center">
                        <div class="summary-icon bg-success-light text-success">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div class="ml-3">
                            <div class="summary-label">
                                Total Biaya
                            </div>
                            <h4 class="font-weight-bold mb-1 summary-value">
                                Rp {{ number_format($totalCost, 0, ',', '.') }}
                            </h4>
                            <small class="text-muted">
                                dari pembelian
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PROFIT -->
            <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius:18px;">
                    <div class="card-body d-flex align-items-center">
                        <div class="summary-icon bg-warning-light text-warning">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        <div class="ml-3">
                            <div class="summary-label">
                                Total Profit
                            </div>
                            <h4 class="font-weight-bold mb-1 summary-value">
                                Rp {{ number_format($totalProfit, 0, ',', '.') }}
                            </h4>
                            <small class="text-muted">
                                laba bersih
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MARGIN -->
            <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius:18px;">
                    <div class="card-body d-flex align-items-center">
                        <div class="summary-icon bg-purple-light text-purple">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="ml-3">
                            <div class="summary-label">
                                Margin Profit
                            </div>
                            <h4 class="font-weight-bold mb-1 summary-value">
                                {{ number_format($profitMargin, 1) }}%
                            </h4>
                            <small class="text-muted">
                                profit margin
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CHART -->
        <div class="row">
            <!-- PROFIT -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius:18px;">
                    <div class="card-header bg-white border-0">
                        <h6 class="font-weight-bold mb-0">
                            Profit per Bulan
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="profitChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            <!-- REVENUE VS COST -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius:18px;">
                    <div class="card-header bg-white border-0">
                        <h6 class="font-weight-bold mb-0">
                            Pendapatan vs Biaya
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            <!-- TABLE -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius:18px;">
                    <div class="card-header bg-white border-0">
                        <h6 class="font-weight-bold mb-0">
                            Ringkasan per Bulan
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>Bulan</th>
                                        <th>Pendapatan</th>
                                        <th>Biaya</th>
                                        <th>Profit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($monthlySummary as $item)
                                        <tr>
                                            <td>{{ $item['month'] }}</td>
                                            <td>
                                                Rp {{ number_format($item['revenue'], 0, ',', '.') }}
                                            </td>
                                            <td>
                                                Rp {{ number_format($item['cost'], 0, ',', '.') }}
                                            </td>
                                            <td class="font-weight-bold">
                                                Rp {{ number_format($item['profit'], 0, ',', '.') }}
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
    </div>
@endsection

@push('scripts')
    <script>
        // PROFIT CHART
        new Chart(document.getElementById('profitChart'), {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Profit',
                    data: @json($chartProfit),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,.1)',
                    fill: true,
                    tension: .4
                }]
            }
        });

        // REVENUE VS COST
        new Chart(document.getElementById('revenueChart'), {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                        label: 'Pendapatan',
                        data: @json($chartRevenue),
                        backgroundColor: '#3b82f6'
                    },
                    {
                        label: 'Biaya',
                        data: @json($chartCost),
                        backgroundColor: '#f59e0b'
                    }
                ]
            }
        });
    </script>
@endpush
