@extends('layouts.app')

@section('content')
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-end">
                <!-- Grup 1: Filter Produk (Lebih lebar) -->
                <div class="col-md-4">
                    <label class="fw-bold mb-1">Produk</label>
                    <form method="GET" action="{{ route('forecast.index') }}" class="d-flex">
                        <select name="product_id" class="form-control mr-2">
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
                    <label>Periode</label>
                    <input type="text" class="form-control" value="14 Hari" readonly>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                <div class="col-md-1">
                    <a href="{{ route('forecast.index') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
                </form>
                <div class="col-md-3">
                    <form id="formForecast" action="{{ route('forecast.generate') }}" method="POST">
                        @csrf
                        <input type="hidden" name="source" value="index">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-play"></i>
                            Generate Forecast
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-3">
            <small class="text-muted">
                <i class="fas fa-calendar-alt"></i>
                Periode Forecast :
                <strong>
                    {{ \Carbon\Carbon::parse($data->first()->forecast_period)->translatedFormat('d F Y') }}
                </strong>
                s/d
                <strong>
                    {{ \Carbon\Carbon::parse($data->first()->forecast_period)->addDays(13)->translatedFormat('d F Y') }}
                </strong>
            </small>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <small class="text-muted">
                            Total Forecast
                        </small>
                        <h2 class="text-primary mt-2">{{ number_format($totalForecast) }}</h2>
                        <small>Unit / 14 Hari</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <small class="text-muted">
                            Forecast Rata-rata
                        </small>
                        <h2 class="text-success mt-2">
                            {{ number_format($avgForecast) }}
                        </h2>
                        <small>Unit</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <small class="text-muted">
                            Produk Forecast
                        </small>
                        <h2 class="text-warning mt-2">{{ $totalProduk }}</h2>
                        <small>Produk</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <small class="text-muted">Produk Dominan</small>
                        <h5 class="text-danger mt-2">
                            {{ $topProduct->product->product_name }}
                        </h5>
                        <small>
                            {{ round(($topProduct->predicted_qty / $totalForecast) * 100, 1) }}%
                            kontribusi
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header fw-bold">
                {{ $chartTitle }}
            </div>
            <div class="card-body">
                <canvas id="forecastChart" height="110"></canvas>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header fw-bold">
                Detail Forecast Produk
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Rank</th>
                            <th>Produk</th>
                            <th>Forecast 14 Hari</th>
                            <th>Forecast / Hari</th>
                            <th>Kontribusi</th>
                            <th>Periode Forecast</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data->sortByDesc('predicted_qty') as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->product->product_name }}</td>
                                <td>
                                    <span class="badge bg-primary">
                                        {{ number_format($item->predicted_qty) }}
                                    </span>
                                </td>
                                <td>{{ round($item->predicted_qty / 14) }}</td>
                                <td>{{ round(($item->predicted_qty / $totalForecast) * 100, 1) }}%</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($item->forecast_period)->format('d/m/Y') }}
                                    -
                                    {{ \Carbon\Carbon::parse($item->forecast_period)->addDays(13)->format('d/m/Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById(
                'forecastChart'
            );
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: @json($chartDatasets)
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },

                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
        <script>
            document
                .getElementById('formForecast')
                .addEventListener('submit', function() {

                    Swal.fire({
                        title: 'Sedang Menghitung Forecast',
                        text: 'Mohon tunggu beberapa saat...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                });
        </script>
    @endsection
