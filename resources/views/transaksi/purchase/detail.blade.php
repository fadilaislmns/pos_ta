@extends('layouts.app')

@section('content')

<div class="container">

    <h3>Detail Pesanan Pembelian Barang</h3>

    <div class="card">
        <div class="card-body">

            <p><b>Nama Supplier:</b> {{ $purchase->supplier->supplier_name }}</p>
            <p><b>Tanggal:</b> {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d-m-Y') }}</p>

            <table class="table mt-3">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Jumlah</th>
                        <th>Harga Satuan</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($purchase->details as $d)
                    <tr>
                        <td>{{ $d->product->product_name }}</td>
                        <td>{{ $d->qty }}</td>
                        <td>{{ number_format($d->price) }}</td>
                        <td>{{ number_format($d->qty * $d->price) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <h4>Total: Rp {{ number_format($purchase->total) }}</h4>

        </div>
    </div>

</div>

@endsection
