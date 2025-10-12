<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Warehouse;

use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if (auth()->user()->is_admin == '1') {
            $brands = Brand::with('category')->latest()->get();          
        } else {
            $brands = Brand::with('category')->whereIn('warehouse_id', $warehousePermission)->latest()->get();
        }
        $categories = Category::latest()->get();
        $branches = Warehouse::all();
        
        return view('brand.brand', [
            "brands" => $brands,
            "categories" => $categories,
            "branches" => $branches
        ]);
    }

    public function store(Request $request)
    {
        // dd($request);
        Brand::create($request->all());
        return redirect(url('brand'))->with('success', 'Brand Created Successfully!');
    }

    public function edit(Brand $brand)
    {
        $categories = Category::latest()->get();
        $branches = Warehouse::all();

        return view('brand.brand_edit', compact('brand','categories','branches'));
    }

    public function update(Request $request, Brand $brand)
    {
        $brand->update($request->all());
        return redirect(url('brand'))->with('success', 'Brand Updated Successfully!');
    }

    public function delete(Brand $brand)
    {
        $brand->delete();
        return redirect()->back()->with('delete', 'Brand Deleted Successfully!');
    }

    public function getBrand(Request $request){
        $category = Category::where('name',$request->category)->where('branch',$request->warehouse)->first();

        $brands = Brand::select('id','name')->where('category_id', $category->id)->where('warehouse_id', $request->warehouse)->get();
        if (!$brands) {
            return response()->json(['error' => 'Brands not found'], 404);
        }
        return response()->json($brands);
    }
}
