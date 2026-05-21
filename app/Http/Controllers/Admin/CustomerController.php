<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    // INDEX
    public function index()
    {
        $customers = Customer::latest()->get();

        return view('master.customer.index', compact('customers'));
    }

    // STORE
    public function store(Request $request)
    {
        try {

            $request->validate([
                'customer_name' => 'required|max:100',
                'phone' => 'nullable|numeric|digits_between:10,15|unique:customers,phone'
            ], [
                'phone.unique' => 'No HP sudah digunakan',
                'phone.numeric' => 'No HP harus angka',
                'phone.digits_between' => 'No HP harus 10-15 digit'
            ]);

            $customer = Customer::create([
                'customer_name' => $request->customer_name,
                'phone' => $request->phone
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Customer berhasil ditambahkan',
                'data' => $customer
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'errors' => $e->validator->errors()
            ], 422);
        } catch (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|max:100',
            'phone' =>
            'nullable|numeric|digits_between:10,15|unique:customers,phone,' . $id
        ], [
            'phone.unique' => 'No HP sudah digunakan',
            'phone.numeric' => 'No HP harus angka',
            'phone.digits_between' => 'No HP harus 10-15 digit'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $customer = Customer::findOrFail($id);
        $customer->update([
            'customer_name' => $request->customer_name,
            'phone' => $request->phone
        ]);
        return response()->json([
            'status' => 'success',
            'message' => 'Customer berhasil diupdate'
        ]);
    }

    // DELETE
    public function destroy($id)
    {
        Customer::findOrFail($id)->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Customer berhasil dihapus'
        ]);
    }
}
