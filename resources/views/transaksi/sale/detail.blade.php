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
                                    Kasir : {{ $sale->user->name ?? '-' }}
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
                            <th>Harga Normal</th>
                            <th>Diskon</th>
                            <th>Harga Jual</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($sale->details as $item)
                            @php
                                $hargaNormal = $item->selling_price + ($item->discount_amount ?? 0);
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->product->product_name }}</td>
                                <td>Rp {{ number_format($hargaNormal, 0, ',', '.') }}</td>
                                <td>
                                    @if (($item->discount_amount ?? 0) > 0)
                                        <span class="text-success">
                                            Rp {{ number_format($item->discount_amount, 0, ',', '.') }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>Rp {{ number_format($item->selling_price, 0, ',', '.') }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>
                                    <strong>
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </strong>
                                </td>
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
                @php
                    $productDiscount = $sale->details->sum(function ($d) {
                        return ($d->discount_amount ?? 0) * $d->quantity;
                    });
                    $subtotalProduk = $sale->details->sum(function ($d) {
                        return ($d->selling_price + ($d->discount_amount ?? 0)) * $d->quantity;
                    });
                @endphp
                <div class="card shadow-sm">
                    <div class="card-header">
                        <b>Ringkasan Pembayaran</b>
                    </div>

                    <div class="card-body">

                        <!-- TOTAL -->
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal Produk</span>
                            <b>
                                Rp {{ number_format($subtotalProduk, 0, ',', '.') }}
                            </b>
                        </div>
                        @if ($productDiscount > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span>Diskon Produk</span>
                                <b class="text-success">
                                    - Rp {{ number_format($productDiscount, 0, ',', '.') }}
                                </b>
                            </div>
                        @endif

                        @if (($sale->discount ?? 0) > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span>Diskon</span>
                                <b class="text-danger">
                                    - Rp {{ number_format($sale->discount, 0, ',', '.') }}
                                </b>
                            </div>
                        @endif

                        <hr>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Bayar</span>
                            <b class="text-primary">
                                Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                            </b>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Uang Dibayar</span>
                            <b>
                                Rp {{ number_format($sale->paid_amount ?? 0, 0, ',', '.') }}
                            </b>
                        </div>

                        @if ($sale->payment_type == 'cash')
                            <div class="d-flex justify-content-between mb-2">
                                <span>Kembalian</span>
                                <b class="text-success">
                                    Rp {{ number_format($sale->change_amount ?? 0, 0, ',', '.') }}
                                </b>
                            </div>
                        @endif

                        @if ($sale->payment_type == 'credit')
                            <div class="d-flex justify-content-between">
                                <span>Sisa Hutang</span>
                                <b class="text-danger">
                                    Rp {{ number_format(optional($sale->debt)->amount ?? 0, 0, ',', '.') }}
                                </b>
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
                                        AMILA MART
                                    </h4>
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
                                <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                                    <thead>
                                        <tr style="border-bottom: 1px dashed #000;">
                                            <th style="text-align: left;">Produk</th>
                                            <th style="text-align: center;">Qty</th>
                                            <th style="text-align: right;">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sale->details as $item)
                                            @php
                                                $hargaNormal = $item->selling_price + ($item->discount_amount ?? 0);
                                            @endphp
                                            <tr>
                                                <td style="padding: 5px 0;">
                                                    {{ $item->product->product_name }}<br>
                                                    <small style="color: #666;">
                                                        Rp{{ number_format($hargaNormal, 0, ',', '.') }}
                                                        @if ($item->discount_amount > 0)
                                                            <span
                                                                style="color:red;">(-Rp{{ number_format($item->discount_amount, 0, ',', '.') }})</span>
                                                        @endif
                                                    </small>
                                                </td>
                                                <td style="text-align: center;">{{ $item->quantity }}</td>
                                                <td style="text-align: right;">
                                                    {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <hr>
                                <!-- TOTAL -->
                                <div class="d-flex justify-content-between">
                                    <span>Total</span>
                                    <b>
                                        Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                                    </b>
                                </div>

                                @if (($sale->discount ?? 0) > 0)
                                    <div class="d-flex justify-content-between">
                                        <span>Diskon</span>
                                        <b class="text-danger">
                                            - Rp {{ number_format($sale->discount, 0, ',', '.') }}
                                        </b>
                                    </div>
                                @endif

                                <div class="d-flex justify-content-between">
                                    <span>Dibayar</span>
                                    <b>
                                        Rp {{ number_format($sale->paid_amount ?? 0, 0, ',', '.') }}
                                    </b>
                                </div>

                                @if ($sale->payment_type == 'cash')
                                    <div class="d-flex justify-content-between">
                                        <span>Kembalian</span>
                                        <b>
                                            Rp {{ number_format($sale->change_amount ?? 0, 0, ',', '.') }}
                                        </b>
                                    </div>
                                @endif

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
        <style>
            /* Sembunyikan elemen lain saat mencetak */
            @media print {
                body * {
                    visibility: hidden;
                }

                #printArea,
                #printArea * {
                    visibility: visible;
                }

                #printArea {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                }

                /* Menghilangkan tombol cetak/batal saat print */
                .modal-header,
                .modal-footer,
                .btn {
                    display: none !important;
                }
            }
        </style>

        <script>
            function printInvoice() {
                // Fokuskan cetakan ke area printArea
                var printContents = document.getElementById('printArea').innerHTML;
                var originalContents = document.body.innerHTML;

                document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;
                // Refresh agar modal tidak hilang setelah print
                location.reload();
            }
        </script>
    @endpush
@endsection
