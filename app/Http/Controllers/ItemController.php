<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemQuantity;
use App\Models\PO_sells;
use App\Models\Unit;
use App\Models\InOut;
use App\Models\Invoice;
use App\Models\Warehouse;
use App\Exports\ItemsExport;
use App\Imports\ItemsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\ItemsImportTemplate;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ItemsUpdateImport;
use App\Models\ItemVariation;
use App\Models\Category;
use App\Models\Brand;
use App\Models\BrandVariations;
use App\Models\Expense;
use App\Models\ProductList;
//use File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{

    public function index(Request $request)
    {
        // Get the selected branch (warehouse) from the request
        $branch = $request->branch;

        // Fetch branch names
        $branches = Warehouse::pluck('name', 'id');
        $currentBranchName = $branch ? $branches[$branch] : 'All Locations';

        // Initialize the query
        if (auth()->user()->is_admin == '1') {
            $query = Item::with(['warehouse', 'variations', 'item_quantity'])->latest();
        } else {
            $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];
            $query = Item::with(['warehouse', 'variations', 'item_quantity'])
                ->whereIn('warehouse_id', $warehousePermission)
                ->latest();
        }

        // Search functionality
        $searchTerm = $request->input('search');
        if ($searchTerm) {
            $query->where(function ($subQuery) use ($searchTerm) {
                $subQuery->where('item_name', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('item_category', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('item_type', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('brand', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('item_descriptions', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhereHas('warehouse', function ($warehouseQuery) use ($searchTerm) {
                        $warehouseQuery->where('name', 'LIKE', '%' . $searchTerm . '%');
                    });
            });
        }

        // Apply branch filter only if a specific branch is selected (not null or empty)
        if ($branch) {
            $query->whereHas('warehouse', function ($warehouseQuery) use ($branch) {
                $warehouseQuery->where('id', $branch);
            });
        }

        // Fetch the warehouses and items
        $warehouses = Warehouse::select('name', 'id')->get();
        $items = $query->paginate(1000);

        return view('item.item', compact('items', 'warehouses', 'currentBranchName'));
    }


    public function item_delete_record()
    {
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if (auth()->user()->is_admin == '1') {
            $items = Item::with(['warehouse', 'variations', 'item_quantity'])
                ->onlyTrashed()
                ->latest()
                ->get();
        } else {
            $items = Item::with(['warehouse', 'variations', 'item_quantity'])
                ->whereIn('warehouse_id', $warehousePermission)
                ->onlyTrashed()
                ->latest()
                ->get();
        }

        return view('item.item_delete_record', compact('items'));
    }


    public function restore_items($id)
    {
        $item = Item::withTrashed()->find($id); // Retrieve soft-deleted item

        if ($item) {
            // Restore the item
            $item->restore();

            // Restore related variations and quantities
            $item_variations = ItemVariation::where('item_id', $item->id)->onlyTrashed()->get();
            foreach ($item_variations as $variation) {

                // Get the related item quantities
                $item_quantities = ItemQuantity::where('item_id', $item->id)
                    ->where('variation_id', $variation->id)
                    ->onlyTrashed()
                    ->get();

                // Loop through each item quantity and restore it
                foreach ($item_quantities as $item_quantity) {
                    $item_quantity->restore(); // Restore the item quantity
                }

                $variation->restore(); // Restore the variation
            }

            return redirect()->back()->with('success', 'Product restored successfully');
        }

        return redirect()->back()->with('error', 'Product not found');
    }

    public function select_restore_items(Request $request)
    {
        // dd($request->all());
        $ids = $request->input('ids');

        if ($ids) {
            $user = auth()->user();

            $isAdmin = $user->is_admin == 1;

            $warehousePermission = $user->level ? json_decode($user->level) : [];

            $items = Item::with('variations.item_quantity')
                ->whereIn('id', $ids)
                ->onlyTrashed()
                ->get();


            foreach ($items as $item) {

                if ($isAdmin || in_array($item->warehouse_id, $warehousePermission)) {
                    foreach ($item->variations as $variation) {
                        if ($variation->item_quantity) {
                            $variation->item_quantity->restore();
                        }
                        $variation->restore();
                    }
                    $item->restore();
                }
            }

            return redirect()->back()->with('success', 'Selected Products Restore Successfully!');
        }

        return redirect()->back()->with('error', 'No Product selected for Restore.');
    }


    public function register()
    {
        $units = Unit::all();
        $branchs = Warehouse::select('name', 'id')->get();
        $brand_categories = Category::all();
        return view('item.itemRegister', compact('branchs', 'units', 'brand_categories'));
    }
    public function store(Request $request)
    {
        try {
            // Start a database transaction
            DB::beginTransaction();

            $item = new Item();
            $item->warehouse_id = $request->warehouse_id;
            //
            $item->brand_id = $request->brand_id;
            //
            $item->item_name = $request->item_name;
            $item->brand = $request->brand;
            $item->item_descriptions = $request->item_descriptions;
            $item->item_category = $request->item_category;
            $item->parent_id = $request->parent_id;
            //
            $item->type = $request->type;
            $item->stock_type = $request->stock_type;
            $item->item_type = $request->item_type;
            if ($request->hasFile('image')) {
                $image = $request->file('image');

                // Check file size (limit: 10MB)
                if ($image->getSize() > 10 * 1024 * 1024) {
                    return back()->with('error', 'Image size should not exceed 10MB');
                }

                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $imagePath = public_path('item_images/' . $imageName);

                // Resize and Save Image
                $this->resizeAndSave($image, $imagePath);

                // Save the image filename in the database
                $item->image = $imageName;
            }




            //
            $item->save();

            $itemVariationIds = [];
            if ($request->has('model')) {
                foreach ($request->model as $key => $model) {
                    $itemVariation = new ItemVariation();
                    $itemVariation->item_id = $item->id;
                    $itemVariation->brand_variation_id = $request->brand_variation_id[$key] ?? null;
                    $itemVariation->unit = $request->unit[$key] ?? null;
                    $itemVariation->variation_desc = $request->variation_desc[$key] ?? null;
                    $itemVariation->expired_date = $request->expired_date[$key] ?? null;
                    $itemVariation->product_code = $request->product_code[$key] ?? null;
                    $itemVariation->barcode = $request->barcode[$key] ?? $this->generateBarcode();
                    $itemVariation->model = $model;
                    // $itemVariation->status = $request->status[$key] ?? null;
                    $itemVariation->status = '1';
                    $itemVariation->colour = $request->colour[$key];
                    $itemVariation->warranty = $request->warranty[$key];
                    // $itemVariation->size = $request->size[$key] ?? null;
                    // $itemVariation->seater = $request->seater[$key] ?? null;
                    $itemVariation->stock_agent_date = $request->stock_agent_date[$key] ?? null;
                    $itemVariation->buy_price = $request->buy_price[$key] ?? 0;
                    $itemVariation->wholesale_price = $request->wholesale_price[$key] ?? 0;
                    $itemVariation->retail_price = $request->retail_price[$key] ?? 0;
                    $itemVariation->retail_set_price = $request->retail_set_price[$key] ?? 0;
                    $itemVariation->promotion_retail_unit = $request->promotion_retail_unit[$key] ?? 0;
                    $itemVariation->promotion_retail_set = $request->promotion_retail_set[$key] ?? 0;
                    //
                    $itemVariation->cost_price = $request->cost_price[$key] ?? 0;
                    $itemVariation->save();

                    $itemVariationIds[] = $itemVariation->id;
                }
            }

            // if ($request->has('warehouse_qty')) {
            //     foreach ($request->warehouse_qty as $key => $warehouse_qty) {
            //         $itemqty = new ItemQuantity();
            //         $itemqty->item_id = $item->id;
            //         $itemqty->variation_id = $itemVariationIds[$key] ?? null;
            //         $itemqty->warehouse_qty = $warehouse_qty;
            //         // $itemqty->available_qty = $warehouse_qty;
            //         // $itemqty->deliver_qty = '0';
            //         $itemqty->alert_qty = $request->alert_qty[$key] ?? null;
            //         $itemqty->save();
            //     }
            // }

            if ($request->has('warehouse_qty')) {
                foreach ($request->warehouse_qty as $key => $warehouse_qty) {
                    $itemqty = new ItemQuantity();
                    $itemqty->item_id = $item->id;
                    $itemqty->variation_id = $itemVariationIds[$key] ?? null;
                    $itemqty->warehouse_qty = $warehouse_qty;

                    if (empty($request->available_qty)) {
                        $itemqty->available_qty = $warehouse_qty;
                    } else {
                        $itemqty->available_qty = $request->available_qty[$key];
                    }

                    if (empty($request->deliver_qty)) {
                        $itemqty->deliver_qty = '0';
                    } else {
                        $itemqty->deliver_qty = $request->deliver_qty[$key];
                    }

                    $itemqty->alert_qty = $request->alert_qty[$key] ?? null;
                    $itemqty->save();
                }
            }

            DB::commit();

            return redirect(url('items'))->with('success', 'Product registered successfully');
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollBack();

            // Log the error or handle it as needed
            Log::error('Error during database operation: ' . $e->getMessage());

            // return response()->json(['message' => 'An error occurred.', 'error' => $e->getMessage()], 500);
            return redirect(url('items'))->with('error', $e->getMessage());
        }
    }

    private function generateBarcode()
    {
        return str_pad(mt_rand(0, 9999999999999), 13, '0', STR_PAD_LEFT);
    }
    public function details($id)
    {
        $items = Item::find($id);
        $item_variations = ItemVariation::where('item_id', $items->id)->get();
        $item_quantity = ItemQuantity::where('item_id', $items->id)->get();
        return view('item.item_details', compact('items', 'item_variations', 'item_quantity'));
    }
    public function edit($id)
    {
        $item = Item::find($id);
        $item_variations = ItemVariation::where('item_id', $item->id)->get();
        $units = Unit::all();
        $brand_categories = Category::all();
        $selected_category = Category::where('name', $item->item_category)->first();
        $brand = Brand::where('name', $item->brand)->first();
        $brand_variations = BrandVariations::all();

        $selected_models = [];
        $selected_colours = [];
        // $selected_sizes = [];
        // $selected_seaters = [];

        foreach ($item_variations as $key => $variation) {
            $selected_models[$key] = BrandVariations::select('model')
                ->where('brand_id', $item->brand_id)
                ->distinct()
                ->get();

            $selected_colours[$key] = BrandVariations::select('colour')
                ->where('brand_id', $item->brand_id)
                ->where('model', $variation->model)
                ->distinct()
                ->get();

            // $selected_sizes[$key] = BrandVariations::select('size')
            //     ->where('brand_id', $item->brand_id)
            //     ->where('model', $variation->model)
            //     ->where('colour', $variation->colour)
            //     ->distinct()
            //     ->get();

            // $selected_seaters[$key] = BrandVariations::select('seater')
            //     ->where('brand_id', $item->brand_id)
            //     ->where('model', $variation->model)
            //     ->where('colour', $variation->colour)
            //     ->where('size', $variation->size)
            //     ->distinct()
            //     ->get();
        }

        $branchs = Warehouse::select('name', 'id')->get();

        return view('item.item_edit', compact(
            'item',
            'branchs',
            'units',
            'item_variations',
            'brand_categories',
            'selected_category',
            'brand',
            'brand_variations',
            'selected_models',
            'selected_colours',
        ));
    }


    public function update(Request $request, $id)
    {

        // dd($request->all());
        $item = Item::findOrFail($id);
        $item->warehouse_id = $request->warehouse_id;
        //
        $item->brand_id = $request->brand_id;
        //
        $item->item_name = $request->item_name;
        $item->brand = $request->brand;
        $item->item_descriptions = $request->item_descriptions;
        $item->item_category = $request->item_category;
        $item->parent_id = $request->parent_id;
        //
        $item->type = $request->type;
        $item->stock_type = $request->stock_type;
        $item->item_type = $request->item_type;
        // Check if a new image is uploaded
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if (!empty($item->image) && file_exists(public_path('item_images/' . $item->image))) {
                unlink(public_path('item_images/' . $item->image));
            }

            // Store the new image
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = public_path('item_images/' . $imageName);

            // Resize and Save Image
            $this->resizeAndSave($image, $imagePath);

            // Update the item's image field
            $item->image = $imageName;
        }
        //
        $item->save();


        if ($request->has('model')) {
            foreach ($request->model as $key => $model) {
                $variationId = $request->variation_id[$key] ?? null;
                $itemVariation = $variationId ? ItemVariation::find($variationId) : new ItemVariation();
                $itemVariation->item_id = $item->id;
                $itemVariation->status = $request->status[$key] ?? 1;
                $itemVariation->variation_desc = $request->variation_desc[$key] ?? 1;
                $itemVariation->brand_variation_id = $request->brand_variation_id[$key] ?? null;
                $itemVariation->unit = $request->unit[$key] ?? null;
                $itemVariation->expired_date = $request->expired_date[$key] ?? null;
                $itemVariation->product_code = $request->product_code[$key] ?? null;
                $itemVariation->barcode = $request->barcode[$key] ?? ($itemVariation->exists ? $itemVariation->barcode : $this->generateBarcode());
                $itemVariation->model = $model;
                $itemVariation->colour = $request->colour[$key] ?? null;
                $itemVariation->warranty = $request->warranty[$key] ?? null;

                // $itemVariation->size = $request->size[$key] ?? null;
                // $itemVariation->seater = $request->seater[$key] ?? null;
                $itemVariation->stock_agent_date = $request->stock_agent_date[$key] ?? null;
                $itemVariation->buy_price = $request->buy_price[$key] ?? 0;
                $itemVariation->wholesale_price = $request->wholesale_price[$key] ?? 0;
                $itemVariation->retail_price = $request->retail_price[$key] ?? 0;
                $itemVariation->retail_set_price = $request->retail_set_price[$key] ?? 0;
                $itemVariation->promotion_retail_unit = $request->promotion_retail_unit[$key] ?? 0;
                $itemVariation->promotion_retail_set = $request->promotion_retail_set[$key] ?? 0;
                //
                $itemVariation->cost_price = $request->cost_price[$key] ?? 0;
                $itemVariation->save();
            }
        }



        // if ($request->has('warehouse_qty')) {
        //     foreach ($request->warehouse_qty as $key => $warehouse_qty) {
        //         $quantityId = $request->quantity_id[$key] ?? null;
        //         $itemqty = $quantityId ? ItemQuantity::find($quantityId) : new ItemQuantity();
        //         $itemqty->item_id = $item->id;
        //         $itemqty->variation_id = $request->variation_id[$key] ?? $itemVariation->id;
        //         // $oldWarehouseQty = $itemqty->warehouse_qty;
        //         // $newWarehouseQty = $warehouse_qty;
        //         // $itemqty->available_qty += $newWarehouseQty - $oldWarehouseQty;
        //         $itemqty->warehouse_qty = $warehouse_qty;
        //         // $itemqty->deliver_qty = $request->deliver_qty[$key] ?? '0';
        //         $itemqty->alert_qty = $request->alert_qty[$key] ?? null;
        //         $itemqty->save();
        //     }
        // }
        if ($request->has('warehouse_qty')) {
            // Get all existing quantities for this item
            $existingQuantities = ItemQuantity::where('item_id', $item->id)->get();

            foreach ($request->warehouse_qty as $key => $warehouse_qty) {
                $quantityId = $request->quantity_id[$key] ?? null;
                $itemqty = $quantityId ? ItemQuantity::find($quantityId) : new ItemQuantity();
                $itemqty->item_id = $item->id;
                $itemqty->variation_id = $request->variation_id[$key] ?? $itemVariation->id;
                $oldWarehouseQty = $itemqty->warehouse_qty;
                $newWarehouseQty = $warehouse_qty;
                if (empty($request->available_qty)) {
                    $itemqty->available_qty += $newWarehouseQty - $oldWarehouseQty;
                } else {
                    $itemqty->available_qty = $request->available_qty[$key];
                }

                if (empty($request->deliver_qty)) {
                } else {
                    $itemqty->deliver_qty = $request->deliver_qty[$key];
                }

                $itemqty->warehouse_qty = $warehouse_qty;
                $itemqty->alert_qty = $request->alert_qty[$key] ?? null;
                $itemqty->save();
            }

            // Delete quantities that are not in the current request
            $quantityIdsInRequest = $request->quantity_id ?? [];
            foreach ($existingQuantities as $existingQuantity) {
                if (!in_array($existingQuantity->id, $quantityIdsInRequest)) {
                    $existingQuantity->delete();
                }
            }
        }

        return redirect(url('items'))->with('success', 'Product updated successfully');
    }

    public function delete($id)
    {
        $item = Item::find($id);

        if ($item) {
            $item_variations = ItemVariation::with('item_quantity')->where('item_id', $item->id)->get();

            foreach ($item_variations as $variation) {

                if ($variation->item_quantity) {
                    //item quantity delete
                    $variation->item_quantity->delete();
                }

                $variation->delete();
            }

            $item->delete();


            return redirect(url('items'))->with('delete', 'Product Deleted Successfully');
        }

        return redirect(url('items'))->with('error', 'Product Not Found');
    }

    public function deleteItems(Request $request)
    {

        $ids = $request->input('ids');

        if ($ids) {
            $user = auth()->user();

            $isAdmin = $user->is_admin == 1;

            $warehousePermission = $user->level ? json_decode($user->level) : [];

            $items = Item::with('variations.item_quantity')->whereIn('id', $ids)->get();

            foreach ($items as $item) {

                if ($isAdmin || in_array($item->warehouse_id, $warehousePermission)) {
                    foreach ($item->variations as $variation) {
                        if ($variation->item_quantity) {
                            $variation->item_quantity->delete();
                        }
                        $variation->delete();
                    }
                    $item->delete();
                }
            }

            return redirect()->back()->with('delete', 'Selected Items Deleted Successfully!');
        }

        return redirect()->back()->with('error', 'No items selected for deletion.');
    }

    public function inout($id)
    {
        $item_variations = ItemVariation::find($id);
        $items = Item::find($item_variations->item_id);
        $inout = Inout::where('item_variation_id', $id)->where('in_out', 'first_in')->first();

        $po = PO_sells::with('branch')->where('variation_id', $item_variations->id)->latest()->get();
        $branchs = Warehouse::all();
        $invoices = Invoice::latest()->get();

        return view('inout.inout', compact('id', 'items', 'inout', 'branchs', 'invoices', 'item_variations', 'po'));
    }
    public function item_search(Request $request)
    {
        $items = Item::all();
        return response()->json($items);
    }
    //Excel
    public function fileImport(Request $request)
    {
        try {
            $request->validate([
                'warehouse_id' => 'required',
                'file' => 'required_if:warehouse_id,import|file|mimes:xlsx,xls,csv',
            ], [
                'file.required_if' => 'Please upload a file for import.',
                'file.file' => 'The uploaded file must be valid.',
                'file.mimes' => 'The file must be an Excel or CSV format.',
            ]);

            $file = $request->file('file');
            $warehouseId = $request->warehouse_id;

            $import = new ItemsImport($warehouseId);
            Excel::import($import, $file->store('temp'));

            return response()->json(['status' => 'success', 'message' => 'File Import Successful!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function fileExport(Request $request)
    {
        try {
            $warehouseId = $request->warehouse_id;

            if ($warehouseId == "All Location" || is_null($warehouseId)) {
                return Excel::download(new ItemsExport(), 'items.xlsx');
            } else {
                return Excel::download(new ItemsExport($warehouseId), 'items.xlsx');
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred during export: ' . $e->getMessage()], 500);
        }
    }




    public function fileUpdateImport(Request $request)
    {
        try {
            $request->validate([
                'warehouse_id' => 'required|exists:warehouses,id',
                'file' => 'required|file|mimes:xlsx,xls,csv',
            ], [
                'file.required' => 'Please upload a file.',
                'file.file' => 'The uploaded file is invalid.',
                'file.mimes' => 'The file must be a valid Excel or CSV file.',
            ]);

            $warehouseId = $request->warehouse_id;
            $file = $request->file('file');
            $import = new ItemsUpdateImport($warehouseId);
            Excel::import($import, $file->store('temp'));

            return back()->with('success', 'File Import Is Successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while importing the file: ' . $e->getMessage());
        }
    }



    public function fileImportTemplate()
    {
        return Excel::download(new ItemsImportTemplate, 'items.xlsx');
    }
    public function barcode($item_id, $id)
    {
        try {
            $item = Item::findOrFail($item_id);
            $productCode = ItemVariation::findOrFail($id);
            return view('item.barcode', compact('item', 'productCode'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Product or Product variation not found.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    public function drop_table(Request $request)
    {
        DB::table('items')->truncate();
        return redirect()->back()->with('success', 'Table dropped successfully!');
    }

    //items qty
    public function itemsQty(Request $request)
    {
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        // $query = ItemQuantity::with(['item', 'variations'])->where('available_qty', '<', 0)->latest();
        $query = ItemQuantity::with(['item', 'variations'])->latest();

        $searchTerm = $request->input('search');

        if ($searchTerm) {
            $query->where('warehouse_qty', 'like', '%' . $searchTerm . '%')
                ->orWhere('available_qty', 'like', '%' . $searchTerm . '%')
                ->orWhere('deliver_qty', 'like', '%' . $searchTerm . '%')
                ->orWhereHas('item', function ($query) use ($searchTerm) {
                    $query->where('item_name', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('item_category', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('brand', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('item_type', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhereHas('warehouse', function ($query) use ($searchTerm) {
                            $query->where('name', 'LIKE', '%' . $searchTerm . '%');
                        });
                })
                ->orWhereHas('variations', function ($query) use ($searchTerm) {
                    $query->where('model', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('colour', 'LIKE', '%' . $searchTerm . '%');
                });
        }

        if (auth()->user()->is_admin != '1') {
            // $query->whereIn('warehouse_id', $warehousePermission);
            $query->whereHas('item', function ($query) use ($warehousePermission) {
                $query->whereIn('warehouse_id', $warehousePermission);
            });
        }

        $items = $query->paginate(1000);

        return view('item.items_qty', compact('items'));
    }

    public function newOrderItem(Request $request)
    {
        $query = Item::with(['variations', 'item_quantity'])
            ->where('item_type', 'New Order Product')
            ->latest();

        // Get the selected branch (warehouse) from the request
        $branch = $request->branch ?? null;

        // Fetch branch names
        $branches = Warehouse::pluck('name', 'id');
        $currentBranchName = $branch ? $branches[$branch] : 'All';

        // Search functionality
        $searchTerm = $request->input('search');
        if ($searchTerm) {
            $query->where(function ($subQuery) use ($searchTerm) {
                $subQuery->where('item_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('item_category', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('item_type', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('brand', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('item_descriptions', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhereHas('warehouse', function ($warehouseQuery) use ($searchTerm) {
                        $warehouseQuery->where('name', 'LIKE', '%' . $searchTerm . '%');
                    });
            });
        }

        // Filter by branch (warehouse) if a branch is selected
        if ($branch) {
            $query->whereHas('warehouse', function ($warehouseQuery) use ($branch) {
                $warehouseQuery->where('id', $branch);
            });
        }

        // Fetch the warehouses and items
        $warehouses = Warehouse::select('name', 'id')->get();
        $items = $query->paginate(1000);

        return view('item.new_order_items', compact('items', 'warehouses', 'currentBranchName'));
    }

    private function resizeAndSave($image, $destination)
    {
        list($width, $height) = getimagesize($image);
        $newWidth = $width * 0.5; // Reduce to 50% size
        $newHeight = $height * 0.5;

        $src = imagecreatefromstring(file_get_contents($image));
        $dst = imagecreatetruecolor($newWidth, $newHeight);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        imagejpeg($dst, $destination, 80); // Save with 80% quality

        imagedestroy($src);
        imagedestroy($dst);
    }
}
