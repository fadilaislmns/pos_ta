@extends('layouts.app')

@section('content')

    <div class="container-fluid">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <h3 class="font-weight-bold">
                Detail Penjualan
            </h3>

            <button class="btn btn-light border" data-toggle="modal" data-target="#modalInvoice">
                <i class="fas fa-print"></i>
                Cetak Invoice
            </button>

        </div>

        <!-- INFO -->
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="row">
                    <!-- INVOICE -->
                    <div class="col-md-3 border-right">
                        <div class="d-flex">
                            <div class="mr-3">
                                <i class="fas fa-file-invoice fa-3x text-primary"></i>
                            </div>

                            <div>
                                <h5 class="mb-1">
                                    {{ $sale->invoice_no ?? 'INV-' . $sale->id }}
                                </h5>
                                <small>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</small>

                                <div class="mt-2">
                                    Kasir:{{ $sale->user->name ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CUSTOMER -->
                    <div class="col-md-3 border-right">
                        <small>Pelanggan</small>
                        <h6 class="mt-2">
                            {{ $sale->customer->customer_name ?? '-' }}
                        </h6>
                    </div>

                    <!-- PAYMENT -->
                    <div class="col-md-3 border-right">
                        <small>Metode Pembayaran</small>
                        <h6 class="mt-2 text-uppercase">
                            {{ $sale->payment_type }}
                        </h6>
                    </div>

                    <!-- STATUS -->
                    <div class="col-md-3">
                        <small>Status</small>
                        <h6 class="mt-2">
                            @if ($sale->payment_type == 'credit' && optional($sale->debt)->amount > 0)
                                @if (optional($sale->debt)->status == 'paid')
                                    <span class="badge badge-success">
                                        Lunas
                                    </span>
                                @else
                                    <span class="badge badge-danger">
                                        Belum Lunas
                                    </span>
                                @endif
                            @else
                                <span class="badge badge-success">
                                    Lunas
                                </span>
                            @endif
                        </h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLE ITEM -->
        <div class="card shadow-sm mb-3">
            <div class="card-header">
                <b>Daftar Item</b>
            </div>

            <div class="card-body table-responsive p-0">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($sale->details as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->product->product_name ?? '-' }}</td>
                                <td>Rp {{ number_format($item->selling_price, 0, ',', '.') }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SUMMARY -->
        <div class="row">

            <!-- PAYMENT -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <b>Ringkasan Pembayaran</b>
                    </div>

                    <div class="card-body">

                        <!-- TOTAL -->
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total</span>
                            <b>Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</b>
                        </div>

                        <!-- DIBAYAR -->
                        <div class="d-flex justify-content-between mb-2">
                            <span>Dibayar</span>
                            <b class="text-primary">Rp {{ number_format($sale->paid_amount ?? 0, 0, ',', '.') }}</b>
                        </div>

                        @if ($sale->payment_type == 'cash')
                            <!-- KEMBALIAN -->
                            <div class="d-flex justify-content-between">
                                <span>Kembalian</span>
                                <b class="text-success">Rp {{ number_format($sale->change_amount ?? 0, 0, ',', '.') }}</b>
                            </div>
                        @endif

                        @if ($sale->payment_type == 'credit')
                            <!-- SISA HUTANG -->
                            <div class="d-flex justify-content-between">
                                <span>Sisa Hutang</span>
                                <b class="text-danger">Rp {{ number_format(optional($sale->debt)->amount ?? 0, 0, ',', '.') }}</b>
                            </div>
                        @endif

                    </div>
                </div>
            </div>

            <!-- INFO -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <b>Informasi Tambahan</b>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td>No. Transaksi</td>
                                <td>:</td>
                                <td>{{ $sale->id }}</td>
                            </tr>

                            <tr>
                                <td>Tanggal</td>
                                <td>:</td>
                                <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</td>
                            </tr>

                            <tr>
                                <td>Kasir</td>
                                <td>:</td>
                                <td>{{ $sale->user->name ?? '-' }}</td>
                            </tr>

                            <tr>
                                <td>Pelanggan</td>
                                <td>:</td>
                                <td>{{ $sale->customer->customer_name ?? '-' }}</td>
                            </tr>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL INVOICE -->
    <div class="modal fade" id="modalInvoice" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- HEADER -->
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-print"></i>
                        Cetak Invoice
                    </h5>

                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- BODY -->
                <div class="modal-body">
                    <div class="row">
                        <!-- INVOICE -->
                        <div class="col-md-7">
                            <div class="invoice-print-area" id="printArea">
                                <!-- LOGO -->
                                <div class="text-center mb-3">
                                    <i class="fas fa-shopping-cart fa-2x"></i>
                                    <h4 class="mb-0">
                                        AMILA POS
                                    </h4>
                                    <small>
                                        Point of Sales System
                                    </small>
                                    <div>
                                        Jl. Raya by pass Simpang Lubuk Minturun
                                    </div>
                                </div>

                                <hr>
                                <!-- INVOICE -->
                                <div class="text-center mb-3">
                                    <b>INVOICE</b>
                                    <div>
                                        {{ $sale->invoice_no ?? 'INV-' . $sale->id }}
                                    </div>
                                </div>

                                <!-- INFO -->
                                <table class="table table-sm table-borderless mb-2">
                                    <tr>
                                        <td width="90">Tanggal</td>
                                        <td>:</td>
                                        <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</td>
                                    </tr>

                                    <tr>
                                        <td>Kasir</td>
                                        <td>:</td>
                                        <td>{{ $sale->user->name ?? '-' }}</td>
                                    </tr>

                                    <tr>
                                        <td>Pelanggan</td>
                                        <td>:</td>
                                        <td>{{ $sale->customer->customer_name ?? '-' }}</td>
                                    </tr>

                                    <tr>
                                        <td>Metode</td>
                                        <td>:</td>
                                        <td class="text-uppercase">{{ $sale->payment_type }}</td>
                                    </tr>
                                </table>

                                <hr>
                                <!-- ITEM -->
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th>Qty</th>
                                            <th>Harga</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($sale->details as $item)
                                            <tr>
                                                <td>{{ $item->product->product_name }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>{{ number_format($item->selling_price, 0, ',', '.') }}</td>
                                                <td>{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <hr>
                                <!-- TOTAL -->
                                <div class="d-flex justify-content-between">
                                    <span>Total</span>
                                    <b>Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</b>
                                </div>

                                @if ($sale->payment_type == 'credit')
                                    <div class="d-flex justify-content-between">
                                        <span>Sisa Hutang</span>
                                        <b>Rp {{ number_format(optional($sale->debt)->amount ?? 0, 0, ',', '.') }}</b>
                                    </div>
                                @endif

                                <hr>
                                <!-- FOOTER -->
                                <div class="text-center mt-3">
                                    <b>TERIMA KASIH</b>
                                    <div>
                                        Barang yang sudah dibeli
                                        tidak dapat dikembalikan
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- BUTTON -->
                        <div class="col-md-5 d-flex align-items-center">
                            <div class="w-100">
                                <button class="btn btn-primary btn-block mb-3" onclick="printInvoice()">
                                    <i class="fas fa-print"></i>
                                    Cetak Invoice
                                </button>

                                <button class="btn btn-light border btn-block" data-dismiss="modal">
                                    Batal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function printInvoice() {
                window.print();
            }
        </script>
    @endpush
@endsection
