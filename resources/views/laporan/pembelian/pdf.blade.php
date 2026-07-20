<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #111;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            font-size: 22px;
        }

        .header p {
            margin: 2px 0;
            font-size: 12px;
        }

        .title {
            text-align: center;
            margin-bottom: 20px;
        }

        .title h3 {
            margin: 0;
            font-size: 18px;
        }

        .filter-info {
            margin-bottom: 20px;
        }

        .filter-info table td {
            padding: 4px 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table thead th {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            padding: 10px;
            font-size: 12px;
        }

        table tbody td {
            border: 1px solid #d1d5db;
            padding: 8px;
            font-size: 11px;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .footer-total {
            margin-top: 15px;
            text-align: right;
            font-size: 14px;
            font-weight: bold;
        }

        .printed {
            margin-top: 15px;
            font-size: 11px;
            text-align: left;
            color: #555;
        }
    </style>
</head>

<body>

    <!-- KOP -->
    <div class="header">
        <h2>AMILA POS</h2>
        <p>Jl. By Pass No.18, Koto Panjang Ikua Koto, kec. Koto Tangah, Kota Padang, Sumatera Barat</p>
        <p>Telp: 0812-1234-9999</p>
    </div>

    <!-- TITLE -->
    <div class="title">
        <h3>LAPORAN PEMBELIAN</h3>
    </div>

    <!-- FILTER -->
    <div class="filter-info">

        <table>
            <tr>
                <td width="120">Dari Tanggal</td>
                <td width="10">:</td>
                <td>{{ $request->start_date ? \Carbon\Carbon::parse($request->start_date)->translatedFormat('d F Y') : '-' }}
                </td>
                <td width="120">Sampai Tanggal</td>
                <td width="10">:</td>
                <td>{{ $request->end_date ? \Carbon\Carbon::parse($request->end_date)->translatedFormat('d F Y') : '-' }}
                </td>
            </tr>
        </table>

    </div>

    <!-- TABLE -->
    <table>
    <thead>
        <tr>
            <th width="30">No</th>
            <th>Tanggal</th>
            <th>No. Pembelian</th>
            <th>Supplier</th>
            <th>Produk</th>
            <th>Qty</th>
            <th>Harga Satuan</th>
            <th>Total</th>
            <th>Status</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($purchases as $purchase)
            @foreach ($purchase->details as $index => $detail)
            <tr>
                {{-- Kolom yang hanya muncul di baris pertama tiap pembelian --}}
                @if ($index == 0)
                    <td rowspan="{{ count($purchase->details) }}">{{ $loop->parent->iteration }}</td>
                    <td rowspan="{{ count($purchase->details) }}">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}</td>
                    <td rowspan="{{ count($purchase->details) }}">{{ $purchase->invoice_no }}</td>
                    <td rowspan="{{ count($purchase->details) }}">{{ $purchase->supplier->supplier_name ?? '-' }}</td>
                @endif

                {{-- Kolom detail produk --}}
                <td>{{ $detail->product->product_name ?? 'Produk Dihapus' }}</td>
                <td class="text-center">{{ $detail->qty }}</td>
                <td class="text-right">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>

                {{-- Kolom Total & Status di baris pertama saja --}}
                @if ($index == 0)
                    <td rowspan="{{ count($purchase->details) }}" class="text-right">
                        Rp {{ number_format($purchase->total, 0, ',', '.') }}
                    </td>
                    <td rowspan="{{ count($purchase->details) }}">
                        {{ ucfirst($purchase->status ?? 'completed') }}
                    </td>
                @endif
            </tr>
            @endforeach
        @endforeach
    </tbody>
</table>

    <!-- TOTAL -->
    <div style="margin-top:30px; width:100%;">
        <table width="100%" style="border:none;">
            <tr>
                <td style="border:none; text-align:left; font-size:13px; color:#555;">
                    Dicetak pada :
                    {{ \Carbon\Carbon::now()->format('d M Y H:i') }}
                </td>
                <td style="border:none; text-align:right; font-size:16px; font-weight:bold;">
                    Total Pembelian : Rp {{ number_format($totalPurchase, 0, ',', '.') }}
                </td>
            </tr>
        </table>

        <div style="margin-top:20px; text-align:right;">
            <p style="margin-bottom:70px;">
                Padang,
                {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
            </p>
            <p><b>{{ auth()->user()->name }}</b></p>
        </div>

    </div>
</body>

</html>
