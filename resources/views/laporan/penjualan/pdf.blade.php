<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>

    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
            color: #111827;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
        }

        .header p {
            margin: 3px 0;
            font-size: 12px;
        }

        .line {
            border-top: 3px solid #111827;
            margin-top: 15px;
            margin-bottom: 20px;
        }

        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .filter-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .filter-table td {
            border: 1px solid #d1d5db;
            padding: 8px;
            font-size: 11px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background: #1e293b;
            color: white;
            border: 1px solid #cbd5e1;
            padding: 10px;
            text-align: center;
            font-size: 11px;
        }

        .table td {
            border: 1px solid #cbd5e1;
            padding: 8px;
            vertical-align: top;
            font-size: 10px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .item-detail {
            line-height: 1.6;
        }

        .footer-wrapper {
            margin-top: 25px;
            width: 100%;
        }

        .footer-total {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
        }

        .printed {
            margin-top: 8px;
            font-size: 11px;
            color: #6b7280;
        }

        .signature {
            width: 100%;
            margin-top: 20px;
        }

        .signature td {
            text-align: right;
            font-size: 12px;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <div class="header">
        <h1>AMILA POS</h1>

        <p>
            Jl. By Pass No.18, Koto Panjang Ikua Koto,
            Kec. Koto Tangah, Kota Padang, Sumatera Barat
        </p>

        <p>
            Telp: 0812-1234-9999
        </p>
    </div>

    <div class="line"></div>

    <!-- TITLE -->
    <div class="title">
        LAPORAN PENJUALAN
    </div>

    <!-- FILTER -->
    <table class="filter-table">
        <tr>
            <td width="20%">
                Dari Tanggal
            </td>

            <td width="30%">
                {{ $request->start_date ?? '-' }}
            </td>

            <td width="20%">
                Sampai Tanggal
            </td>

            <td width="30%">
                {{ $request->end_date ?? '-' }}
            </td>
        </tr>
    </table>

    <!-- TABLE -->
    @php
        $grandDiscount = 0;
    @endphp
    <table class="table">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="8%">Tanggal</th>
                <th width="12%">Invoice</th>
                <th width="10%">Pelanggan</th>
                <th width="8%">Kasir</th>
                <th width="8%">Metode</th>
                <th width="25%">Rincian Barang</th>
                <th width="10%">Diskon Produk</th>
                <th width="10%">Diskon Transaksi</th>
                <th width="10%">Total</th>
                <th width="5%">Status</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($sales as $index => $sale)
                @php
                    $productDiscount = $sale->details->sum(function ($d) {
                        return ($d->discount_amount ?? 0) * $d->quantity;
                    });
                    $grandDiscount += $productDiscount;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}
                    </td>
                    <td>{{ $sale->invoice_no }}</td>
                    <td>{{ $sale->customer->customer_name ?? 'Umum' }}</td>
                    <td>{{ $sale->user->name ?? '-' }}</td>
                    <td class="text-center">{{ strtoupper($sale->payment_type) }}</td>

                    <!-- RINCIAN -->
                    <td class="item-detail">
                        @foreach ($sale->details as $detail)
                            @php
                                $hargaJual = $detail->selling_price;
                                $diskon = $detail->discount_amount ?? 0;
                                $hargaNormal = $hargaJual + $diskon;
                                $subtotalNormal = $hargaNormal * $detail->quantity;
                                $subtotalDiskon = $diskon * $detail->quantity;
                            @endphp

                            • <strong>
                                {{ $detail->product->product_name }}
                            </strong>
                            <br>
                            Qty :
                            {{ $detail->quantity }}
                            ×
                            Rp {{ number_format($hargaNormal, 0, ',', '.') }}
                            =
                            Rp {{ number_format($subtotalNormal, 0, ',', '.') }}
                            @if ($diskon > 0)
                                <br>
                                Diskon :
                                - Rp {{ number_format($subtotalDiskon, 0, ',', '.') }}
                            @endif
                            <br>
                            Subtotal :
                            <strong>
                                Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                            </strong>
                            @if (!$loop->last)
                                <hr style="border:none;border-top:1px dashed #d1d5db;margin:8px 0;">
                            @endif
                        @endforeach

                    </td>
                    @php
                        $productDiscount = $sale->details->sum(function ($d) {
                            return ($d->discount_amount ?? 0) * $d->quantity;
                        });
                    @endphp
                    <td class="text-right">
                        Rp {{ number_format($productDiscount, 0, ',', '.') }}
                    </td>
                    <td class="text-right">
                        Rp {{ number_format($sale->discount ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="text-right">
                        Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                    </td>
                    <td class="text-center">
                        {{ ucfirst($sale->payment_type ?? 'Lunas') }}
                    </td>

                </tr>
            @endforeach

        </tbody>

    </table>

    <!-- FOOTER -->
    <div class="footer-wrapper">
        @if ($grandDiscount > 0)
            <div style="margin-top:10px;">
                Total Diskon Produk :
                Rp {{ number_format($grandDiscount, 0, ',', '.') }}
            </div>
        @endif
        
        <div class="footer-total">
            Total Penjualan :
            Rp {{ number_format($totalSales, 0, ',', '.') }}
        </div>

        <div class="printed">
            Dicetak pada :
            {{ \Carbon\Carbon::now()->format('d M Y H:i') }}
        </div>

    </div>

    <!-- TTD -->
    <table class="signature">

        <tr>
            <td>

                Padang,
                {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}

                <br><br><br><br>

                <strong>
                    {{ auth()->user()->name }}
                </strong>

            </td>
        </tr>

    </table>

</body>

</html>
