<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Receipt</title>

    <style>
        body {
            font-family: monospace;
            width: 80mm;
            margin: 0 auto;
            padding: 5px;
            font-size: 14px;
        }

        .text-center {
            text-align: center;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 2px 0;
        }

        .right {
            text-align: right;
        }

        @page {
            size: 80mm auto;
            margin: 0;
        }

        @media print {
            body {
                width: 80mm;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="text-center">
        <h3>AMILA MART</h3>
        <br>
        <small>Jl. Raya By Pass Simpang Lubuk Minturun</small>
    </div>

    <div class="line"></div>

    <div class="text-center">
        <strong>{{ $sale->invoice_no }}</strong>
    </div>

    <table>
        <tr>
            <td>Transaksi</td>
            <td>:</td>
            <td>#{{ $sale->id }}</td>
        </tr>
    </table>

    <table>
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
            <td>Customer</td>
            <td>:</td>
            <td>{{ $sale->customer->customer_name ?? 'Umum' }}</td>
        </tr>
        <tr>
            <td>Metode</td>
            <td>:</td>
            <td>{{ strtoupper($sale->payment_type) }}</td>
        </tr>
    </table>

    @php
        $productDiscount = $sale->details->sum(function ($d) {
            return ($d->discount_amount ?? 0) * $d->quantity;
        });
        $subtotalProduk = $sale->details->sum(function ($d) {
            return ($d->selling_price + ($d->discount_amount ?? 0)) * $d->quantity;
        });
    @endphp

    <div class="line"></div>

    @foreach ($sale->details as $item)
        @php
            $hargaNormal = $item->selling_price + ($item->discount_amount ?? 0);
            $gross = $hargaNormal * $item->quantity;
            $discTotal = ($item->discount_amount ?? 0) * $item->quantity;
        @endphp

        <div style="margin-bottom:8px;">
            <strong>{{ $item->product->product_name }}</strong>
            <table>
                <tr>
                    <td>
                        {{ $item->quantity }} x
                        {{ number_format($hargaNormal, 0, ',', '.') }}
                    </td>
                    <td class="right">
                        {{ number_format($gross, 0, ',', '.') }}
                    </td>
                </tr>

                @if ($discTotal > 0)
                    <tr>
                        <td>Diskon</td>
                        <td class="right">
                            - {{ number_format($discTotal, 0, ',', '.') }}
                        </td>
                    </tr>
                @endif

                <tr>
                    <td>Subtotal</td>
                    <td class="right">
                        {{ number_format($item->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </div>
    @endforeach

    <div class="line"></div>

    <table>

        <tr>
            <td>Item</td>
            <td class="right">{{ $sale->details->count() }}</td>
        </tr>
        <tr>
            <td>Qty</td>
            <td class="right">{{ $sale->details->sum('quantity') }}</td>
        </tr>
        <tr>
            <td>Subtotal Belanja</td>
            <td class="right">Rp {{ number_format($subtotalProduk, 0, ',', '.') }}</td>
        </tr>

        @if ($productDiscount > 0)
            <tr>
                <td>Diskon Produk</td>
                <td class="right">
                    - Rp {{ number_format($productDiscount, 0, ',', '.') }}
                </td>
            </tr>
        @endif

        @if (($sale->discount ?? 0) > 0)
            <tr>
                <td>Diskon Transaksi</td>
                <td class="right">
                    - Rp {{ number_format($sale->discount, 0, ',', '.') }}
                </td>
            </tr>
        @endif

    </table>

    <div style="margin:6px 0;">
        ========================================
    </div>

    <table>

        <tr>
            <td style="font-size:16px;font-weight:bold;">
                TOTAL
            </td>

            <td class="right" style="font-size:16px;font-weight:bold;">
                Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
            </td>
        </tr>

    </table>

    <div style="margin:6px 0;">
        ========================================
    </div>

    <table>

        <tr>
            <td>BAYAR</td>
            <td class="right">
                Rp {{ number_format($sale->paid_amount ?? 0, 0, ',', '.') }}
            </td>
        </tr>

        @if ($sale->payment_type == 'cash')
            <tr>
                <td>KEMBALIAN</td>
                <td class="right">
                    Rp {{ number_format($sale->change_amount ?? 0, 0, ',', '.') }}
                </td>
            </tr>
        @endif

    </table>

    @if ($sale->payment_type == 'credit')
        <table>
            <tr>
                <td>Sisa Hutang</td>
                <td class="right">
                    Rp {{ number_format(optional($sale->debt)->amount ?? 0, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    @endif

    <div class="line"></div>

    <div class="text-center">
        <strong>TERIMA KASIH</strong>
        <br><br>
        Simpan struk ini sebagai
        <br>
        bukti transaksi
        <br><br>
        Barang yang telah dibeli
        <br>
        tidak dapat ditukar
        <br>
        atau dikembalikan
    </div>

    <div class="text-center no-print" style="margin-top:10px">
        <button onclick="window.print()">
            Cetak Struk
        </button>
    </div>

</body>

</html>
