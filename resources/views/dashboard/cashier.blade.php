@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h3 class="font-weight-bold">Dashboard Kasir</h3>
    </div>

    <div class="row">
        @foreach ([
            ['icon' => 'fa-receipt', 'color' => 'text-dark', 'label' => 'Total Transaksi', 'value' => $todaySales, 'unit' => 'transaksi'],
            ['icon' => 'fa-wallet', 'color' => 'text-success', 'label' => 'Total Penjualan', 'value' => 'Rp ' . number_format($todayRevenue, 0, ',', '.'), 'unit' => 'Hari Ini'],
            ['icon' => 'fa-user', 'color' => 'text-primary', 'label' => 'Status Kasir', 'value' => 'Online', 'unit' => 'Aktif'],
            ['icon' => 'fa-box', 'color' => 'text-warning', 'label' => 'Stok Kritis', 'value' => count($lowStocks), 'unit' => 'produk']
        ] as $item)
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card dashboard-card dashboard-summary-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="summary-icon bg-light"><i class="fas {{ $item['icon'] }} {{ $item['color'] }}"></i></div>
                        <div class="ml-3">
                            <small class="text-muted">{{ $item['label'] }}</small>
                            <h4 class="font-weight-bold mb-0 {{ $item['label'] == 'Status Kasir' ? 'text-success' : '' }}">{{ $item['value'] }}</h4>
                            <small class="text-muted">{{ $item['unit'] }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm dashboard-card quick-action-card mb-4">
                <div class="card-body">
                    <h5 class="font-weight-bold mb-4">Quick Action</h5>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <a href="{{ route('sales.create') }}" class="quick-btn quick-primary">
                                <i class="fas fa-cart-plus mr-2"></i> Mulai Transaksi (POS)
                            </a>
                        </div>
                        <div class="col-md-6 mb-2">
                            <a href="{{ route('sales.index') }}" class="quick-btn quick-outline">
                                <i class="fas fa-file-alt mr-2"></i> Riwayat Transaksi
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm dashboard-card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="font-weight-bold">Transaksi Terakhir</h5>
                        <a href="{{ route('sales.index') }}" class="text-primary font-weight-bold">Lihat Semua</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead><tr><th>No. Invoice</th><th>Waktu</th><th>Total</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse ($latestSales as $sale)
                                <tr>
                                    <td class="font-weight-bold">{{ $sale->invoice_no }}</td>
                                    <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('H:i') }}</td>
                                    <td>Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge {{ $sale->payment_type == 'credit' ? 'badge-danger' : 'badge-success' }}">
                                            {{ $sale->payment_type == 'credit' ? 'Piutang' : 'Lunas' }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada transaksi</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm dashboard-card stock-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="font-weight-bold mb-0">Stok Hampir Habis</h5>
                        <a href="{{ route('stocks.index') }}" class="text-primary font-weight-bold" style="font-size: 0.8rem;">Lihat</a>
                    </div>
                    <div style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-sm">
                            <thead><tr><th>No</th><th>Produk</th><th>Qty</th></tr></thead>
                            <tbody>
                                @forelse ($lowStocks as $stock)
                                <tr><td>{{ $loop->iteration }}</td><td>{{ $stock->product->product_name }}</td><td>{{ $stock->current_stock }}</td></tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted">Stok aman</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-warning mt-3 mb-0" style="font-size: 0.85rem; border-radius:12px;">
                        Produk dengan stok rendah sebaiknya segera direstock.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
