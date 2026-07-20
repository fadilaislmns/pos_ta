<?php

namespace App\Exports;

use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SalesReportExport implements FromArray, ShouldAutoSize, WithStyles
{
    protected $request;
    protected $totalRows;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function array(): array
    {
        $query = Sale::with(['customer', 'user', 'details.product']);
        if (Auth::user()->role->role_name == 'cashier') {
            $query->where('user_id', Auth::id());
        }

        if ($this->request->start_date) $query->whereDate('sale_date', '>=', $this->request->start_date);
        if ($this->request->end_date) $query->whereDate('sale_date', '<=', $this->request->end_date);
        if ($this->request->user_id) $query->where('user_id', $this->request->user_id);
        if ($this->request->payment_type) $query->where('payment_type', $this->request->payment_type);

        $sales = $query->latest()->get();

        $rows = [];
        $rows[] = ['AMILA POS', '', '', '', '', '', '', '', '', '', '', ''];
        $rows[] = ['LAPORAN PENJUALAN', '', '', '', '', '', '', '', '', '', '', ''];
        $rows[] = ['Periode : ' . ($this->request->start_date ?: '-') . ' s/d ' . ($this->request->end_date ?: '-'), '', '', '', '', '', '', '', '', '', '', ''];
        $rows[] = ['']; // Baris 4 (Jarak)

        $rows[] = [
            'No',
            'Tanggal',
            'Invoice',
            'Pelanggan',
            'Kasir',
            'Produk',
            'Qty',
            'Harga Normal',
            'Diskon',
            'Harga Jual',
            'Subtotal',
            'Status'
        ];

        $grandDiscount = 0;

        foreach ($sales as $index => $sale) {
            $productDiscount = $sale->details->sum(function ($d) {
                return ($d->discount_amount ?? 0) * $d->quantity;
            });
            $grandDiscount += $productDiscount;
            foreach ($sale->details as $dIndex => $detail) {
                $hargaJual = $detail->selling_price;
                $diskon = $detail->discount_amount ?? 0;
                $hargaNormal = $hargaJual + $diskon;
                $rows[] = [
                    $dIndex == 0 ? $index + 1 : '',
                    $dIndex == 0 ? Carbon::parse($sale->sale_date)->format('d/m/Y') : '',
                    $dIndex == 0 ? $sale->invoice_no : '',
                    $dIndex == 0 ? ($sale->customer->customer_name ?? 'Umum') : '',
                    $dIndex == 0 ? ($sale->user->name ?? '-') : '',
                    $detail->product->product_name ?? 'Produk',
                    $detail->quantity,
                    $hargaNormal,
                    $diskon,
                    $hargaJual,
                    $detail->subtotal,
                    $dIndex == 0
                        ? ($sale->payment_type == 'credit'
                            ? 'Kredit'
                            : 'Lunas')
                        : ''
                ];
            }
        }

        $rows[] = [
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'TOTAL DISKON',
            $grandDiscount,
            ''
        ];

        $rows[] = [
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'TOTAL PENJUALAN',
            $sales->sum('total_amount'),
            ''
        ];
        $this->totalRows = count($rows);
        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->totalRows;

        $sheet->mergeCells('A1:L1');
        $sheet->mergeCells('A2:L2');
        $sheet->mergeCells('A3:L3');
        $sheet->getStyle('A1:L3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header (Baris 5)
        $sheet->getStyle("A5:L5")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Border dari baris 5 ke bawah
        $sheet->getStyle("A5:L{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Catatan: Jika total ada di kolom I, sesuaikan range-nya
        $sheet->getStyle("H6:K{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');

        // Bold Baris Total
        $sheet->getStyle("J" . ($lastRow - 1) . ":K{$lastRow}")
            ->getFont()
            ->setBold(true);
        return [];
    }
}
