<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Profit</title>

    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
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
        }

        hr {
            border: 1px solid #111827;
            margin: 20px 0;
        }

        .title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .summary {
            width: 100%;
            margin-bottom: 25px;
        }

        .summary td {
            padding: 8px;
            border: 1px solid #d1d5db;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            background: #1e293b;
            color: white;
            padding: 10px;
            border: 1px solid #d1d5db;
        }

        table td {
            padding: 8px;
            border: 1px solid #d1d5db;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 40px;
        }

        .signature {
            width: 250px;
            float: right;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>AMILA POS</h1>
        <p>Jl. By Pass No.18, Koto Panjang Ikua Koto, kec. Koto Tangah, Kota Padang, Sumatera Barat</p>
        <p>Telp: 0812-1234-9999</p>
    </div>
    <hr>
    <div class="title">
        LAPORAN ANALISIS PROFIT
    </div>
    <table class="summary">
        <tr>
            <td width="20%">Tahun</td>
            <td width="30%">{{ $year }}</td>
            <td width="20%">Periode Bulan</td>
            <td width="30%">
                {{ \Carbon\Carbon::create(null, $startMonth)->translatedFormat('F') }}
                -
                {{ \Carbon\Carbon::create(null, $endMonth)->translatedFormat('F') }}
            </td>
        </tr>
    </table>

    <table style="margin-bottom:25px;">
        <tr>
            <th>Total Pendapatan</th>
            <th>Total Biaya</th>
            <th>Total Profit</th>
            <th>Margin Profit</th>
        </tr>

        <tr>
            <td class="text-right">
                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
            </td>
            <td class="text-right">
                Rp {{ number_format($totalExpense, 0, ',', '.') }}
            </td>
            <td class="text-right">
                Rp {{ number_format($totalProfit, 0, ',', '.') }}
            </td>
            <td class="text-right">
                {{ number_format($marginProfit, 2) }}%
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Bulan</th>
                <th>Pendapatan</th>
                <th>Biaya</th>
                <th>Profit</th>
                <th>Margin</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($monthlyData as $data)
                <tr>
                    <td>{{ $data['month'] }}</td>
                    <td class="text-right">Rp {{ number_format($data['revenue'], 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($data['expense'], 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($data['profit'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($data['margin'], 2) }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div style="float:left;">
            Dicetak pada:
            {{ now()->translatedFormat('d F Y H:i') }}
        </div>
        <div class="signature">
            <p>
                Padang,
                {{ now()->translatedFormat('d F Y') }}
            </p>
            <br><br><br>
            <strong>
                {{ auth()->user()->name }}
            </strong>
        </div>
    </div>
</body>

</html>
