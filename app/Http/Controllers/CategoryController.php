<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];
        if (auth()->user()->is_admin == '1') {
            $categories = Category::latest()->get();
            $branchs = Warehouse::all();
        } else {
            $categories = Category::whereIn('branch', $warehousePermission)->latest()->get();
            $branchs = Warehouse::whereIn('id', $warehousePermission)->get();
        }
        return view('brand.category', [
            "categories" => $categories,
            "branchs" => $branchs,
            // "branches" => $branches
        ]);
    }

    public function store(Request $request)
    {
        Category::create($request->all());
        return redirect(url('brand_category'))->with('success', 'Category Created Successfully!');
    }

    public function edit(Category $category)
    {

        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];
        if (auth()->user()->is_admin == '1') {
            $branchs = Warehouse::all();
        } else {
            $branchs = Warehouse::whereIn('branch', $warehousePermission)->get();
        }

        return view('brand.category_edit', [
            "category" => $category,
            "branchs" => $branchs
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $category->update($request->all());
        return redirect(url('brand_category'))->with('success', 'Category Updated Successfully!');
    }

    public function delete(Category $category)
    {
        $category->delete();
        return redirect()->back()->with('delete', 'Category Deleted Successfully!');
    }
}
