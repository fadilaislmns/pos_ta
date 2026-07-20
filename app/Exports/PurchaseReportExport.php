<?php

namespace App\Exports;

use App\Models\Purchase;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PurchaseReportExport implements FromArray, ShouldAutoSize, WithStyles
{
    protected $request;
    protected $totalRows;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function array(): array
    {
        $query = Purchase::with(['supplier', 'user', 'details.product']);
        if ($this->request->start_date) $query->whereDate('purchase_date', '>=', $this->request->start_date);
        if ($this->request->end_date) $query->whereDate('purchase_date', '<=', $this->request->end_date);
        if ($this->request->supplier_id) $query->where('supplier_id', $this->request->supplier_id);
        if ($this->request->status) $query->where('status', $this->request->status);

        $purchases = $query->latest()->get();

        $rows = [];
        $rows[] = ['AMILA POS', '', '', '', '', '', ''];
        $rows[] = ['LAPORAN PEMBELIAN', '', '', '', '', '', ''];
        $rows[] = ['Periode: ' . ($this->request->start_date ?: '-') . ' s/d ' . ($this->request->end_date ?: '-'), '', '', '', '', '', ''];
        $rows[] = ['']; // Baris 4

        $rows[] = ['No', 'Tanggal', 'No Purchase', 'Supplier', 'Produk', 'Qty', 'Harga Satuan', 'Subtotal', 'Status'];

        foreach ($purchases as $index => $p) {
            foreach ($p->details as $dIndex => $d) {
                $rows[] = [
                    $dIndex == 0 ? $index + 1 : '', // No hanya muncul di baris pertama transaksi
                    $dIndex == 0 ? Carbon::parse($p->purchase_date)->format('d/m/Y') : '',
                    $dIndex == 0 ? $p->invoice_no : '',
                    $dIndex == 0 ? ($p->supplier->supplier_name ?? '-') : '',
                    $d->product->product_name ?? 'Produk Dihapus',
                    $d->qty,
                    $d->price,
                    $d->qty * $d->price,
                    $dIndex == 0 ? ucfirst($p->status) : ''
                ];
            }
            // Baris total per transaksi (opsional, jika ingin ada total per invoice)
        }

        $rows[] = ['', '', '', '', '', '', '', 'TOTAL PEMBELIAN', $purchases->sum('total')];

        $this->totalRows = count($rows);
        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->totalRows;

        // Merge Judul (A sampai I)
        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');
        $sheet->mergeCells('A3:I3');

        // Header Tabel (Baris 5)
        $sheet->getStyle("A5:I5")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '274472']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Border dari baris 5 sampai akhir
        $sheet->getStyle("A5:I{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Format Rupiah untuk kolom Harga Satuan (G), Subtotal (H), dan Total (I)
        $sheet->getStyle("G6:I{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');

        // Bold Baris Total
        $sheet->getStyle("H{$lastRow}:I{$lastRow}")->getFont()->setBold(true);

        return [
            'A1:I3' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            1 => ['font' => ['bold' => true, 'size' => 16]],
            5 => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]
        ];
    }
}
