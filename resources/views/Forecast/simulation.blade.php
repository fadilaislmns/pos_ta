@extends('layouts.app')

@section('content')
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET">
                <div class="row align-items-end">
                    <div class="col-md-5">
                        <label class="fw-bold">
                            Produk
                        </label>

                        <select name="product_id" class="form-control">
                            <option value="">Semua Produk</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}"
                                    {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->product_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="fw-bold">Periode Forecast</label>
                        <input type="text" class="form-control" value="14 Hari" readonly>
                    </div>

                    <div class="col-md-4">
                        <button class="btn btn-primary w-100">
                            Tampilkan Analisis
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <small>Total Prediksi</small>
                    <h3 class="text-primary">{{ number_format($totalForecast) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <small>Total Pendapatan</small>
                    <h3 class="text-success">
                        Rp {{ number_format($totalRevenue) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <small>Total Modal Penjualan</small>
                    <h3 class="text-warning">
                        Rp {{ number_format($totalCost) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <small>Estimasi Keuntungan</small>
                    <h3 class="text-danger">
                        Rp {{ number_format($totalProfit) }}
                    </h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik Profitabilitas --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold">
            Analisis Profitabilitas Produk
        </div>
        <div class="card-body">
            <canvas id="profitChart"></canvas>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card shadow-sm">
        <div class="card-header fw-bold">
            Detail Profitabilitas
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Prediksi Penjualan</th>
                        <th>Pendapatan</th>
                        <th>Modal Penjualan</th>
                        <th>Estimasi Keuntungan</th>
                        <th>Persentase Keuntungan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($profitData as $item)
                        <tr>
                            <td>{{ $item['product_name'] }}</td>
                            <td>{{ number_format($item['forecast_qty']) }}</td>
                            <td>Rp {{ number_format($item['revenue']) }}</td>
                            <td>Rp {{ number_format($item['cost']) }}</td>
                            <td>Rp {{ number_format($item['profit']) }}</td>
                            <td>{{ $item['margin'] }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        new Chart(
            document.getElementById(
                'profitChart'
            ),

            {
                type: 'bar',
                data: {
                    labels: @json($chartLabels),
                    datasets: [
                        {
                            label: 'Revenue',
                            data: @json($revenueChart),
                            backgroundColor: '#198754'
                        },
                        {
                            label: 'Cost',
                            data: @json($costChart),
                            backgroundColor: '#ffc107'
                        },
                        {
                            label: 'Profit',
                            data: @json($profitChart),
                            backgroundColor: '#0d6efd'
                        }
                    ]
                }
            });
    </script>
@endsection
