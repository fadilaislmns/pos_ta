<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;

class SalePageController extends Controller
{
    // INDEX (LIST PENJUALAN)
    public function index()
    {
        return view('transaksi.sale.index');
    }

    // CREATE (POS TRANSAKSI)
    public function create()
    {
        $products = Product::select('id', 'category_id', 'product_name', 'selling_price')->get();
        $customers = Customer::select('id', 'customer_name')->get();
        $users = User::select('id', 'name')->get();
        $categories = Category::select('id', 'category_name')->get();

        return view('transaksi.sale.create', compact('products', 'customers', 'users', 'categories'));
    }

    // DETAIL PENJUALAN
    public function show($id)
    {
        $sale = Sale::with([
            'details.product',
            'customer',
            'user',
            'debt'
        ])->findOrFail($id);
        
        return view('transaksi.sale.detail', compact('sale'));
    }
}
