@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- TITLE -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="font-weight-bold">
            Detail Pesanan Pembelian
        </h2>

    </div>

    <!-- INFO -->
    <div class="card border-0 shadow-sm mb-4"
        style="border-radius:20px;">
        <div class="card-body p-4">
            <div class="row">
                <!-- PURCHASE -->
                <div class="col-md-3 border-right">
                    <small class="text-muted">No Purchase</small>
                    <h4 class="font-weight-bold mt-2">
                        {{ $purchase->purchase_no ?? 'PO-' . str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}
                    </h4>
                    <div class="text-muted mt-2">
                        {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d F Y') }}
                    </div>
                </div>

                <!-- SUPPLIER -->
                <div class="col-md-3 border-right">
                    <small class="text-muted">Supplier</small>
                    <h4 class="font-weight-bold mt-2">
                        {{ $purchase->supplier->supplier_name }}
                    </h4>
                </div>

                <!-- USER -->
                <div class="col-md-3 border-right">
                    <small class="text-muted">Dibuat Oleh</small>
                    <h4 class="font-weight-bold mt-2">
                        {{ $purchase->user->name ?? '-' }}
                    </h4>
                </div>

                <!-- STATUS -->
                <div class="col-md-3">
                    <small class="text-muted">Status</small>
                    <div class="mt-2">
                        <span class="badge badge-success px-3 py-2">
                            Completed
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="card border-0 shadow-sm"
        style="border-radius:20px;">
        <div class="card-header bg-white border-0 pt-4">
            <h4 class="font-weight-bold">Daftar Item</h4>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Produk</th>
                            <th>Qty</th>
                            <th>Harga</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->details as $d)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $d->product->product_name }}</td>
                            <td>{{ $d->qty }}</td>
                            <td>Rp {{ number_format($d->price,0,',','.') }}</td>
                            <td class="font-weight-bold">Rp {{ number_format($d->qty * $d->price,0,',','.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- TOTAL -->
            <div class="d-flex justify-content-end mt-4">
                <div style="width:320px;">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Item</span>
                        <b>{{ $purchase->details->sum('qty') }}</b>
                    </div>
                    <div class="d-flex justify-content-between">
                        <h4 class="font-weight-bold">
                            Total
                        </h4>

                        <h4 class="font-weight-bold text-primary">
                            Rp {{ number_format($purchase->total,0,',','.') }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
