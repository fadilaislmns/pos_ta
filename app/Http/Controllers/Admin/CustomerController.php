<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomerController extends Controller
{
    // 🔹 GET ALL
    public function index()
    {
        try {
            $customers = Customer::all();

            return response()->json([
                'success' => true,
                'message' => 'Data customer berhasil diambil',
                'data' => $customers
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 🔹 STORE
    public function store(Request $request)
    {
        try {
            $request->validate([
                'customer_name' => 'required|string|max:100',
                'phone' => 'nullable|string|max:20'
            ]);

            $customer = Customer::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Customer berhasil ditambahkan',
                'data' => $customer
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 🔹 SHOW
    public function show($id)
    {
        try {
            $customer = Customer::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail customer berhasil diambil',
                'data' => $customer
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Customer tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    // 🔹 UPDATE (STRICT)
    public function update(Request $request, $id)
    {
        try {
            $customer = Customer::findOrFail($id);

            $request->validate([
                'customer_name' => 'required|string|max:100',
                'phone' => 'nullable|string|max:20'
            ]);

            $customer->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Customer berhasil diupdate',
                'data' => $customer
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 🔹 DELETE
    public function destroy($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Customer berhasil dihapus'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
