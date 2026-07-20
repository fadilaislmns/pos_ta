<?php

namespace App\Helpers;

class BreadcrumbHelper
{
    public static function generate()
    {
        $segments = request()->segments();

        $breadcrumb = [];

        // DASHBOARD
        $breadcrumb[] = [
            'name' => 'Dashboard',
            'url' => route('dashboard'),
            'clickable' => true
        ];

        $path = '';

        // CUSTOM NAMA
        $customNames = [

            // REPORT
            'sales' => 'Penjualan',
            'purchases' => 'Pembelian',
            'profit' => 'Analisis Profit',

            // SALES
            'create' => 'Tambah',
            'edit' => 'Edit',
            'detail' => 'Detail',

            // MASTER
            'products' => 'Produk',
            'categories' => 'Kategori',
            'customers' => 'Pelanggan',
            'suppliers' => 'Supplier',
            'users' => 'Pengguna',

            // INVENTORY
            'stocks' => 'Stok',

            // PIUTANG
            'customer-debts' => 'Piutang',

        ];

        // KHUSUS REPORT
        if (isset($segments[0]) && $segments[0] == 'reports') {

            $breadcrumb[] = [
                'name' => 'Laporan',
                'url' => null,
                'clickable' => false
            ];

            // ambil segment terakhir
            $lastSegment = end($segments);

            $breadcrumb[] = [
                'name' => $customNames[$lastSegment]
                    ?? ucfirst($lastSegment),

                'url' => url()->current(),
                'clickable' => true
            ];

            return $breadcrumb;
        }

        // NORMAL PAGE
        foreach ($segments as $segment) {
            $path .= '/' . $segment;
            if ($segment == 'dashboard') {
                continue;
            }
            // Skip reports
            if ($segment == 'reports') {
                continue;
            }
            // ANGKA = DETAIL
            if (is_numeric($segment)) {
                $breadcrumb[] = [
                    'name' => 'Detail',
                    'url' => url($path),
                    'clickable' => true
                ];
                continue;
            }

            $name = $customNames[$segment]
                ?? ucfirst(str_replace('-', ' ', $segment));

            $breadcrumb[] = [
                'name' => $name,
                'url' => url($path),
                'clickable' => true
            ];
        }

        return $breadcrumb;
    }
}
