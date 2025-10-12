<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\ItemQuantity;
use App\Models\ItemVariation;
use App\Models\ProductList;
use Illuminate\Support\Facades\Cache;
use App\Models\Warehouse;

class ProductListController extends Controller
{
    public function index()
    {
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        $query = ProductList::with('warehouse')->latest();
        if (auth()->user()->is_admin != '1') {
            $query->whereIn('warehouse_id', $warehousePermission);
        }

        $product_lists = $query->get();

        return view('product_list.product_list_manage', compact('product_lists'));
    }


    public function register()
    {
        $branches = Warehouse::all();
        return view('product_list.product_list_register', compact('branches'));
    }

    public function store(Request $request)
    {
        $item = Item::find($request->item_id);

        $product = ProductList::where('item_id', $item->id)->where('variation_id', $request->variation_id)
            ->where('warehouse_id', $request->warehouse_id)
            ->first();

        if ($product) {
            return redirect(url('product_list_manage'))->with('error', 'Item Already Exist In The Product List!');
        }

        $variation  = ItemVariation::where('id', $request->variation_id)->where('item_id', $item->id)->first();
        $variation->buy_price = $request->unit_price;
        //update cost price of item
        $variation->cost_price = $request->cost_price;
        $variation->status = $request->status;
        $variation->update();

        $product_list = new ProductList();
        $product_list->item_id = $request->item_id;
        $product_list->variation_desc = $request->variation_desc;
        $product_list->variation_id = $request->variation_id;
        $product_list->brand_id = $request->brand_id;
        $product_list->warehouse_id = $request->warehouse_id;
        $product_list->unit_price = $request->unit_price;
        $product_list->cost_price = $request->cost_price;
        $product_list->selling_price = $request->selling_price;
        $product_list->unit = $request->unit;
        $product_list->moq = $request->moq;
        $product_list->unit_m3 = $request->unit_m3;
        $product_list->unit_kg = $request->unit_kg;
        $product_list->model = $request->model;
        $product_list->colour = $request->colour;
        $product_list->size = $request->size;
        $product_list->seater = $request->seater;
        $product_list->product_code = $request->product_code;

        $product_list->save();

        return redirect(url('product_list_manage'))->with('success', 'Product List Created Successfully!');
    }

    public function edit(ProductList $product_list)
    {
        $branches = Warehouse::all();
        $variation = ItemVariation::where('item_id', $product_list->item_id)->where('id', $product_list->variation_id)->first();
        $item_qty = ItemQuantity::where('item_id', $product_list->item_id)->where('variation_id', $product_list->variation_id)->first();
        return view('product_list.product_list_edit', compact('product_list', 'branches', 'variation', 'item_qty'));
    }

    public function update(Request $request, ProductList $product_list)
    {
        $item = Item::find($request->item_id);

        $product = ProductList::where('item_id', $item->id)->where('variation_id', $request->variation_id)
            ->where('warehouse_id', $request->warehouse_id)
            ->first();

        if ($product && ($product->id != $product_list->id)) {
            return redirect(url('product_list_manage'))->with('error', 'Item Already Exists In The Product List!');
        }

        $variation  = ItemVariation::where('id', $request->variation_id)->where('item_id', $item->id)->first();
        $variation->buy_price = $request->unit_price;
        //update cost price of item
        $variation->cost_price = $request->cost_price;
        $variation->update();

        $product_list->update($request->except('item'));
        return redirect(url('product_list_manage'))->with('success', 'Product List Updated Successfully!');
    }


    public function delete(ProductList $product_list)
    {
        $product_list->delete();
        return redirect()->back()->with('delete', 'Product List Deleted Successfully!');
    }

    public function item_search(Request $request)
    {
        $query = $request->get('query');
        $warehouse = $request->get('location');

        $cacheKey = "item_search_{$query}_{$warehouse}";

        // $data = Cache::remember($cacheKey, 60, function () use ($query,$warehouse) {
        //     return Item::select('item_name')
        //         ->where('warehouse_id',$warehouse)
        //         ->where('item_name', 'LIKE', '%' . $query . '%')
        //         ->where('parent_id', 0)
        //         ->pluck('id','item_name');
        // });

        $items = Cache::remember($cacheKey, 60, function () use ($query, $warehouse) {
            $itemIds = Item::where('warehouse_id', $warehouse)
                ->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('item_name', 'like', '%' . $query . '%')
                        ->orWhereHas('variations', function ($q) use ($query) {
                            $q->where('model', 'like', '%' . $query . '%');
                        });
                })
                ->pluck('id');

            return ItemVariation::whereIn('item_id', $itemIds)
                ->with('item:id,item_name') // Fetch related item data
                ->get(['item_id', 'product_code', 'id', 'model', 'colour', 'size', 'seater', 'variation_desc'])
                ->map(function ($variation) {
                    return [
                        'item_name' => $variation->item ? $variation->item->item_name : 'Unknown Item',
                        'product_code' => $variation->product_code,
                        'variation_desc' => $variation->variation_desc,
                        'model' => $variation->model,
                        'colour' => $variation->colour,
                        'size' => $variation->size,
                        'seater' => $variation->seater,
                        'id' => $variation->id,
                        'item_id' => $variation->item_id,
                    ];
                });
        });
        return response()->json($items);
    }

    public function itemDataFill(Request $request)
    {
        $item_id = $request->item_id;
        $variation_id = $request->variation_id;
        $location = $request->location;

        $cacheKey = "item_data_fill_{$item_id}_{$variation_id}_{$location}";

        $responseData = Cache::remember($cacheKey, 60, function () use ($item_id, $variation_id) {
            $product = Item::find($item_id);

            if (!$product) {
                return ['error' => 'Item not found'];
            }

            $warehouse = Warehouse::find($product->warehouse_id);
            $variation = ItemVariation::with('item_quantity')->where('id', $variation_id)->where('item_id', $product->id)->first();
            $item_qty = ItemQuantity::where('item_id', $variation->item_id)->where('variation_id', $variation->id)->first();

            return [
                'item' => $product,
                'warehouse' => $warehouse,
                'variation' => $variation,
                'item_qty' => $item_qty,
            ];
        });

        if (isset($responseData['error'])) {
            return response()->json($responseData, 404);
        }

        return response()->json($responseData);
    }
}
