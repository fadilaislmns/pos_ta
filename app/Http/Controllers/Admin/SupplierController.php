<?php

namespace App\Http\Controllers\Admin;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);

        $suppliers = Supplier::latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('master.supplier.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'supplier_name' => 'required|max:100|unique:suppliers,supplier_name',
                'lead_time' => 'required|numeric',
                'MOQ' => 'required|numeric'
            ], [
                'supplier_name.unique' => 'Nama pemasok sudah digunakan!'
            ]);

            Supplier::create($request->all());

            return redirect()->back()->with('success', 'Supplier berhasil ditambahkan');
        } catch (\Illuminate\Validation\ValidationException $e) {

            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('modal', 'add');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'supplier_name' => 'required|max:100|unique:suppliers,supplier_name,' . $id,
                'lead_time' => 'required|numeric',
                'MOQ' => 'required|numeric'
            ], [
                'supplier_name.unique' => 'Nama pemasok sudah digunakan!'
            ]);

            $supplier = Supplier::findOrFail($id);
            $supplier->update($request->all());

            return redirect()->back()->with('success', 'Berhasil update');
        } catch (\Illuminate\Validation\ValidationException $e) {

            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('modal', 'edit');
        }
    }

    public function destroy($id)
    {
        Supplier::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Supplier berhasil dihapus');
    }
}
