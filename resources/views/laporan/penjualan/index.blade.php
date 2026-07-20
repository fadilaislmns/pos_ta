@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="mb-4">
            <h3 class="font-weight-bold">Laporan Penjualan</h3>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET">
                    <div class="row align-items-end">
                        <!-- DARI TANGGAL -->
                        <div class="col-lg-2 col-md-6 mb-3">
                            <label class="font-weight-bold">
                                Dari Tanggal
                            </label>
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>

                        <!-- SAMPAI -->
                        <div class="col-lg-2 col-md-6 mb-3">
                            <label class="font-weight-bold">
                                Sampai Tanggal
                            </label>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>

                        <!-- CUSTOMER -->
                        @if (auth()->user()->role->role_name == 'admin')
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label class="font-weight-bold">
                                    Kasir
                                </label>
                                <select name="user_id" class="form-control">
                                    <option value="">Semua Kasir</option>
                                    @foreach ($cashiers as $cashier)
                                        <option value="{{ $cashier->id }}"
                                            {{ request('user_id') == $cashier->id ? 'selected' : '' }}>
                                            {{ $cashier->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- METODE -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="font-weight-bold">Metode Pembayaran</label>

                            <select name="payment_type" class="form-control">
                                <option value="">Semua Metode</option>
                                <option value="cash">Cash</option>
                                <option value="qris">QRIS</option>
                                <option value="card">Card</option>
                                <option value="credit">Credit</option>
                            </select>
                        </div>

                        <!-- BUTTON -->
                        <div class="col-lg-2 mb-3">
                            <div class="d-flex" style="gap:10px;">
                                <button class="btn btn-primary flex-fill">
                                    Filter
                                </button>

                                <a href="{{ route('reports.sales') }}" class="btn btn-light border flex-fill">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-4">
            @php
                $summaries = [
                    [
                        'label' => 'Total Penjualan',
                        'value' => 'Rp ' . number_format($totalSales, 0, ',', '.'),
                        'icon' => 'fa-shopping-cart',
                        'color' => 'text-primary',
                    ],
                    [
                        'label' => 'Total Transaksi',
                        'value' => $totalTransactions,
                        'icon' => 'fa-briefcase',
                        'color' => 'text-success',
                    ],
                    [
                        'label' => 'Rata-rata Transaksi',
                        'value' => 'Rp ' . number_format($averageTransaction, 0, ',', '.'),
                        'icon' => 'fa-wallet',
                        'color' => 'text-warning',
                    ],
                    [
                        'label' => 'Produk Terjual',
                        'value' => $totalProducts,
                        'icon' => 'fa-calendar-alt',
                        'color' => 'text-info',
                    ],
                ];
            @endphp
            @foreach ($summaries as $s)
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm p-3">
                        <div class="d-flex align-items-center">
                            <div class="mr-3 p-3 rounded bg-light"><i
                                    class="fas {{ $s['icon'] }} {{ $s['color'] }}"></i></div>
                            <div>
                                <small class="text-muted">{{ $s['label'] }}</small>
                                <h5 class="font-weight-bold mb-0">{{ $s['value'] }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <h5 class="font-weight-bold">Daftar Penjualan</h5>
                    <div>
                        <a href="{{ route('reports.sales.pdf', request()->query()) }}"
                            class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-file-pdf"></i>
                            Export PDF
                        </a>
                        <a href="{{ route('reports.sales.excel', request()->query()) }}"
                            class="btn btn-sm btn-outline-success">
                            <i class="fas fa-file-excel"></i>
                            Export Excel
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>No.Invoice</th>
                                <th>Pelanggan</th>
                                <th>Total</th>
                                <th>Metode</th>
                                @if (auth()->user()->role->role_name == 'admin')
                                    <th>Kasir</th>
                                @endif
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sales as $sale)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</td>
                                    <td class="font-weight-bold">{{ $sale->invoice_no }}</td>
                                    <td>{{ $sale->customer->customer_name ?? 'Umum' }}</td>
                                    <td>Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                                    <td>{{ ucfirst($sale->payment_type) }}</td>
                                    @if (auth()->user()->role->role_name == 'admin')
                                        <td>{{ $sale->user->name ?? '-' }}</td>
                                    @endif
                                    <td>
                                        <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-sm btn-light">
                                            <i class="fas fa-eye text-info"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    @if (auth()->user()->role->role_name == 'admin')
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            Data laporan kosong
                                        </td>
                                    @else
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            Data laporan kosong
                                        </td>
                                    @endif
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="row align-items-center mt-4">
                    <div class="col-md-6 text-muted small">
                        Menampilkan <strong>{{ $sales->firstItem() ?? 0 }}</strong> sampai
                        <strong>{{ $sales->lastItem() ?? 0 }}</strong> dari
                        <strong>{{ $sales->total() }}</strong> data
                    </div>

                    <div class="col-md-6 d-flex justify-content-end">
                        {{ $sales->withQueryString()->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
