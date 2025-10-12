<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\BrandVariations;
use Illuminate\Http\Request;

class BrandVariationsController extends Controller
{
    public function index(Brand $brand){
        $variations = BrandVariations::with('brand')->where('brand_id',$brand->id)->latest()->get();
        return view('brand.brand_variation',compact('brand','variations'));
    }

    public function store(Request $request){
        $brandvariation = new BrandVariations();
        $brandvariation->brand_id = $request->brand_id;
        $brandvariation->model = $request->model ?? 'No Model';
        $brandvariation->colour = $request->colour ?? 'No Color';
        $brandvariation->size = $request->size ?? '0';
        $brandvariation->seater = $request->seater ?? '0';
        $brandvariation->save();
        // BrandVariations::create($request->all());
        return redirect()->back()->with('success','Variation Addded Successfully!');
    }

    public function edit(BrandVariations $variation)
    {
        return view('brand.brand_variation_edit', compact('variation'));
    }

    public function update(Request $request, BrandVariations $variation)
    {
        $variation->update($request->all());
        return redirect()->route('brand_variation',$variation->brand_id)->with('success', 'Variation Updated Successfully!');
    }

    public function delete(BrandVariations $variation)
    {
        $variation->delete();
        return redirect()->back()->with('delete', 'Variation Deleted Successfully!');
    }

    public function getModel(Request $request){

        $variations = BrandVariations::select('model')->where('brand_id', $request->brand)->distinct()->get();
        if (!$variations) {
            return response()->json(['error' => 'Model not found'], 404);
        }
        return response()->json($variations);
    }

    public function getColour(Request $request){

        $variations = BrandVariations::select('colour')->where('brand_id',$request->brand)->where('model', $request->model)->distinct()->get();
        if (!$variations) {
            return response()->json(['error' => 'Colour not found'], 404);
        }
        return response()->json($variations);
    }

    public function getSize(Request $request){

        $variations = BrandVariations::select('size')->where('brand_id',$request->brand)->where('model', $request->model)->where('colour', $request->colour)->distinct()->get();
        if (!$variations) {
            return response()->json(['error' => 'Size not found'], 404);
        }
        return response()->json($variations);
    }

    public function getSeater(Request $request){

        $variations = BrandVariations::select('seater')->where('brand_id',$request->brand)->where('model', $request->model)->where('colour', $request->colour)->where('size', $request->size)->distinct()->get();
        if (!$variations) {
            return response()->json(['error' => 'Seater not found'], 404);
        }
        return response()->json($variations);
    }

    public function getBrandVariation(Request $request){

        $variation = BrandVariations::select('id')->where('brand_id',$request->brand)->where('model', $request->model)->where('colour', $request->colour)
                        ->where('size', $request->size)->where('seater',$request->seater)->first();
        if (!$variation) {
            return response()->json(['error' => 'Seater not found'], 404);
        }
        return response()->json($variation);
    }
}
