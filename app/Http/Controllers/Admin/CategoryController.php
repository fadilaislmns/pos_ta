<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);

        $categories = Category::latest()
            ->paginate($perPage)
            ->withQueryString();
        return view('master.category.index', compact('categories'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'category_name' => 'required|max:100|unique:categories,category_name',
                'description' => 'nullable'
            ], [
                'category_name.unique' => 'Nama kategori sudah digunakan!'
            ]);

            Category::create($request->all());

            return redirect()->back()
                ->with('success', 'Berhasil tambah');
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
                'category_name' => 'required|max:100|unique:categories,category_name,' . $id,
                'description' => 'nullable'
            ], [
                'category_name.unique' => 'Nama kategori sudah digunakan!'
            ]);

            $category = Category::findOrFail($id);
            $category->update($request->all());

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
        Category::findOrFail($id)->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Berhasil hapus');
    }
}
