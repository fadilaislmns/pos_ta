@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="mb-4">
            <h3 class="font-weight-bold">Laporan Pembelian</h3>
        </div>

        <div class="card border-0 shadow-sm mb-4" style="border-radius:20px;">
            <div class="card-body p-4">
                <form method="GET">
                    <div class="row align-items-end">
                        <!-- DARI -->
                        <div class="col-lg-2 col-md-6 mb-3">
                            <label class="filter-label">Dari Tanggal</label>
                            <input type="date" name="start_date" class="form-control custom-input"
                                value="{{ request('start_date') }}">
                        </div>
                        <!-- SAMPAI -->
                        <div class="col-lg-2 col-md-6 mb-3">
                            <label class="filter-label">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="form-control custom-input"
                                value="{{ request('end_date') }}">
                        </div>
                        <!-- SUPPLIER -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="filter-label">Supplier</label>
                            <select name="supplier_id" class="form-control custom-input">
                                <option value="">Semua Supplier</option>
                                @foreach ($suppliers as $s)
                                    <option value="{{ $s->id }}"
                                        {{ request('supplier_id') == $s->id ? 'selected' : '' }}>
                                        {{ $s->supplier_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!-- STATUS -->
                        <div class="col-lg-2 col-md-6 mb-3">
                            <label class="filter-label">Status</label>
                            <select name="status" class="form-control custom-input">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed
                                </option>
                            </select>
                        </div>

                        <!-- BUTTON -->
                        <div class="col-lg-3 mb-3">
                            <div class="d-flex align-items-center" style="gap:10px;">
                                <button type="submit" class="btn btn-primary custom-btn flex-fill">
                                    <i class="fas fa-filter mr-2"></i>
                                    Filter
                                </button>
                                <a href="{{ route('reports.purchases') }}"
                                    class="btn btn-light border custom-btn flex-fill">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-4">
            @foreach ([['val' => 'Rp ' . number_format($totalPurchase, 0, ',', '.'), 'label' => 'Total Pembelian', 'icon' => 'fa-file-invoice', 'bg' => 'bg-info'], ['val' => $totalPO, 'label' => 'Jumlah PO', 'icon' => 'fa-tags', 'bg' => 'bg-success'], ['val' => $totalItems, 'label' => 'Item Dibeli', 'icon' => 'fa-box', 'bg' => 'bg-warning'], ['val' => 'Rp ' . number_format($averagePurchase, 0, ',', '.'), 'label' => 'Rata-rata', 'icon' => 'fa-wallet', 'bg' => 'bg-purple']] as $item)
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm p-3 d-flex flex-row align-items-center" style="border-radius:15px;">
                        <div class="{{ $item['bg'] }} text-white d-flex align-items-center justify-content-center mr-3"
                            style="width:45px; height:45px; border-radius:10px;">
                            <i class="fas {{ $item['icon'] }}"></i>
                        </div>
                        <div>
                            <div class="text-muted small" style="font-size: 11px;">{{ $item['label'] }}</div>
                            <div class="font-weight-bold" style="font-size: 16px;">{{ $item['val'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card border-0 shadow-sm" style="border-radius:20px;">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <h5 class="font-weight-bold">Daftar Pembelian</h5>
                    <div>
                        <a href="{{ route('reports.purchases.pdf', request()->query()) }}" class="btn btn-outline-danger">
                            <i class="fas fa-file-pdf"></i>
                            Export PDF
                        </a>
                        <a href="{{ route('reports.purchases.excel', request()->query()) }}"
                            class="btn btn-outline-success">
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
                                <th>No Purchase</th>
                                <th>Supplier</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>User</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($purchases as $purchase)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}</td>
                                    <td class="font-weight-bold">{{ $purchase->invoice_no }}</td>
                                    <td>{{ $purchase->supplier->supplier_name ?? '-' }}</td>
                                    <td>Rp {{ number_format($purchase->total, 0, ',', '.') }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $purchase->status == 'completed' ? 'badge-success' : 'badge-warning' }}">
                                            {{ ucfirst($purchase->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $purchase->user->name ?? '-' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('purchases.show', $purchase->id) }}"
                                            class="btn btn-sm btn-light">
                                            <i class="fas fa-eye text-info"></i>

                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Data pembelian kosong</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="row align-items-center mt-4">
                    <div class="col-md-6 text-muted small">
                        Menampilkan <strong>{{ $purchases->firstItem() ?? 0 }}</strong> sampai
                        <strong>{{ $purchases->lastItem() ?? 0 }}</strong> dari
                        <strong>{{ $purchases->total() }}</strong> data
                    </div>

                    <div class="col-md-6 d-flex justify-content-end">
                        {{ $purchases->withQueryString()->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
