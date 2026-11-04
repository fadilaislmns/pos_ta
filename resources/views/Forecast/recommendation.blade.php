@extends('layouts.app')

@section('content')
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <form method="GET" action="{{ route('forecast.recommendation') }}">
                        <label>Pilih Produk</label>
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
                    <label>Periode</label>
                    <input type="text" class="form-control" value="14 Hari" readonly>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                <div class="col-md-1">
                    <a href="{{ route('forecast.recommendation') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
                </form>
                <div class="col-md-3">
                    <form id="formForecast" action="{{ route('forecast.generate') }}" method="POST">
                        @csrf
                        <input type="hidden" name="source" value="recommendation">
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
                    @if ($data->count() == 0)
                        -
                    @else
                        {{ \Carbon\Carbon::parse($data->first()->forecast_period)->translatedFormat('d F Y') }}
                    @endif
                </strong>
                s/d
                <strong>
                    @if ($data->count() == 0)
                        -
                    @else
                        {{ \Carbon\Carbon::parse($data->first()->forecast_period)->addDays(13)->translatedFormat('d F Y') }}
                    @endif
                </strong>
            </small>

        </div>

        {{-- Card Ringkasan --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <small>Total Forecast</small>
                        <h3 class="text-primary">{{ number_format($totalForecast) }}</h3>
                        <small>Unit</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <small>Rata-rata Forecast</small>
                        <h3 class="text-primary">{{ number_format($avgForecast) }}</h3>
                        <small>Unit</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <small>Total Rekomendasi Pembelian</small>
                        <h3 class="text-primary">{{ number_format($totalRecommendationQty) }}</h3>
                        <small>Unit</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <small>Produk Direkomendasikan</small>
                        <h3 class="text-danger">{{ $totalRecommendedProducts }}</h3>
                        <small>Produk</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" style="min-width:1400px; white-space:nowrap;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Produk</th>
                                <th>Supplier</th>
                                <th>Stok Saat Ini</th>
                                <th>Forecast 14 Hari</th>
                                <th>Lead Time</th>
                                <th>MOQ</th>
                                <th>Safety Stock</th>
                                <th>ROP</th>
                                <th>Rekomendasi Pembelian</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($data->isEmpty())
                                <tr>
                                    <td colspan="12" class="text-center">Tidak ada data</td>
                                </tr>
                            @endif
                            @foreach ($data as $item)
                                @php
                                    $supplier = $item->product->suppliers->first();
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->product->product_name }}</td>
                                    <td>{{ $supplier->supplier_name ?? '-' }}</td>
                                    <td>{{ $item->product->stock->current_stock ?? 0 }}</td>
                                    <td>{{ number_format($item->predicted_qty) }}</td>
                                    <td>{{ $supplier->lead_time ?? '-' }}</td>
                                    <td>{{ $supplier->MOQ ?? '-' }}</td>
                                    <td>{{ $item->product->stock->safety_stock ?? 0 }}</td>

                                    <td>{{ $item->product->stock->reorder_point ?? 0 }}</td>
                                    <td>{{ $item->product->purchaseRecommendation->recommendation_qty ?? 0 }}</td>
                                    <td>
                                        @php
                                            $recommendation =
                                                $item->product->purchaseRecommendation->recommendation_qty ?? 0;
                                        @endphp
                                        @if ($recommendation > 0)
                                            <span class="badge bg-warning text-dark">Direkomendasikan</span>
                                        @else
                                            <span class="badge bg-success">Stok Mencukupi</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $supplier = $item->product->suppliers->first();
                                        @endphp
                                        <a
                                            href="{{ route('purchases.index', [
                                                'auto_po' => 1,
                                                'product_id' => $item->product->id,
                                                'supplier_id' => $supplier->id ?? null,
                                                'qty' => $item->product->purchaseRecommendation->recommendation_qty,
                                            ]) }}">
                                            <button class="btn btn-sm" title="Buat Draft Purchase Order">
                                                <i class="fas fa-shopping-cart text-primary"></i>
                                            </button>
                                        </a>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#28a745'
                });
            </script>
        @endif
        @if (session('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Forecast Gagal',
                    text: @json(session('error')),
                    confirmButtonColor: '#dc3545'
                });
            </script>
        @endif
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
