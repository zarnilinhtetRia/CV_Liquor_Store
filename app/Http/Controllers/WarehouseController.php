<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemQuantity;
use App\Models\ItemVariation;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\TransferHistory;
use App\Models\Transfer_Historyy;
use Illuminate\Support\Facades\DB;

use Exception;
use Illuminate\Support\Facades\Cache;


class WarehouseController extends Controller
{
    public function index()
    {
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if (auth()->user()->is_admin == '1') {
            $warehouses = Warehouse::all();
        } else {
            $warehouses = Warehouse::whereIn('id', $warehousePermission)->get();
        }
        $warehouse = Warehouse::all()->count();
        // dd($warehouse);

        return view('warehouse.warehouse', compact('warehouses', 'warehouse'));
    }
    public function warehouse_register(Request $request, Warehouse $warehouse)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'phone_number' => 'required',
                'address' => 'required',
            ], [
                'phone_number.required' => 'Phone Number is required',
                'name.required' => 'Name is required',
                'address.required' => 'Address is required',
            ]);

            $warehouse->create($validated);

            return back()->with('success', 'Register Warehouse Successful');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . 'Something Wrong!');
        }
    }
    public function warehouse_delete($id)
    {
        $itemsCount = Item::where('warehouse_id', $id)->count();
        $transferCount = TransferHistory::where(function ($query) use ($id) {
            $query->where('from_location', $id)
                ->orWhere('to_location', $id);
        })->count();

        if ($itemsCount > 0) {
            return back()->with('error', 'Cannot delete warehouse because items are associated with it.');
        } elseif ($transferCount > 0) {
            return back()->with('error', 'Cannot delete warehouse because transfer histories are associated with it.');
        } else {
            Warehouse::destroy($id);
            return back()->with('delete', 'Warehouse deleted successfully.');
        }
    }


    public function warehouse_edit($id)
    {
        $warehouse = Warehouse::find($id);
        return view('warehouse.warehouse_edit', compact('warehouse'));
    }
    public function warehouse_Update($id, Request $request)
    {

        $validated = $request->validate([
            'name' => 'required',
            'phone_number' => 'required',
            'address' => 'required',
        ], [
            'phone_number.required' => 'Phone Number is required',
            'name.required' => 'Name is required',
            'address.required' => 'Address is required',
        ]);
        $warehouse = Warehouse::find($id);
        $warehouse->update($validated);
        return redirect(url('warehouse'))->with('success', 'Warehouse Updated Successful');
    }


    public function getBranchTransferNo(Request $request)
    {
        // Get the highest transfer_no that starts with 'Transfer-'
        $latestTransferNo = TransferHistory::whereNotNull('transfer_no')
            ->where('transfer_no', 'LIKE', 'Transfer-%')
            ->max('transfer_no');

        if ($latestTransferNo) {
            // Extract the numeric part and increment
            $latestNumber = (int) str_replace('Transfer-', '', $latestTransferNo);
            $nextTransferNo = 'Transfer-' . ($latestNumber + 1);
        } else {
            // Start from Transfer-1 if no previous records exist
            $nextTransferNo = 'Transfer-1';
        }

        return response()->json(['no' => $nextTransferNo]);
    }




    public function transfer_item()
    {
        $warehouses = WareHouse::all();

        return view('warehouse.transfer_item', compact('warehouses'));
    }


    public function store_transfer_item(Request $request)
    {
        // DB::beginTransaction();

        try {
            $from = Warehouse::find($request->from_location);
            $to = Warehouse::find($request->to_location);
            // dd($request->to_location);



            // dd($latest_transferno);
            $count = count($request->part_number);
            // info($count);
            for ($i = 0; $i < $count; $i++) {

                $item_to = Item::where('item_name', $request->result_item_name[$i])
                    ->where('item_descriptions', $request->desc[$i])
                    ->where('stock_type', $request->stock_type[$i])
                    ->where('warehouse_id', $request->to_location)->first();

                info($item_to);
                $item_from = Item::where('id', $request->item_id[$i])->where('warehouse_id', $request->from_location)->first();

                $item_from_variation = ItemVariation::where('item_id', $item_from->id)
                    ->where('id', $request->result_id[$i])
                    ->first();

                $item_from_quantity = ItemQuantity::where('item_id', $item_from->id)
                    ->where('variation_id', $item_from_variation->id)
                    ->first();

                if ($item_to) {

                    $item_to_variation = ItemVariation::where('item_id', $item_to->id)
                        ->where('product_code', $request->product_code[$i])
                        ->where('model', $request->model[$i])
                        ->where('colour', $request->colour[$i])
                        ->first();

                    if ($item_to_variation) {

                        $item_to_quantity = ItemQuantity::where('item_id', $item_to->id)
                            ->where('variation_id', $item_to_variation->id)
                            ->first();
                        $item_to_quantity->warehouse_qty += $request->product_qty[$i];
                        $item_from_quantity->warehouse_qty = $item_from_quantity->warehouse_qty - $request->product_qty[$i];

                        $item_to_quantity->available_qty += $request->product_qty[$i]; //fix
                        $item_from_quantity->available_qty = $item_from_quantity->available_qty - $request->product_qty[$i]; //fix

                        $item_to_quantity->save();
                        $item_from_quantity->save();
                    } else {
                        $variation = new ItemVariation();
                        $variation->item_id = $item_to->id;
                        // $variation->brand_variation_id = $item_from_variation->brand_variation_id;
                        $variation->unit = $item_from_variation->unit;
                        $variation->variation_desc = $item_from_variation->variation_desc;
                        $variation->expired_date = $item_from_variation->expired_date;
                        $variation->product_code = $item_from_variation->product_code;
                        $variation->barcode = $item_from_variation->barcode;
                        $variation->stock_agent_date = $item_from_variation->stock_agent_date;
                        $variation->model = $item_from_variation->model;
                        $variation->colour = $item_from_variation->colour;
                        $variation->buy_price = $item_from_variation->buy_price;
                        $variation->wholesale_price = $item_from_variation->wholesale_price;
                        $variation->retail_price = $item_from_variation->retail_price;
                        $variation->retail_set_price = $item_from_variation->retail_set_price;
                        $variation->promotion_retail_unit = $item_from->promotion_retail_unit;
                        $variation->promotion_retail_set = $item_from_variation->promotion_retail_set;
                        $variation->save();
                        $item_from_variation->save();

                        $quantity = new ItemQuantity();
                        // $quantity->item_id = $item->id;
                        $quantity->item_id = $item_to->id; //fix
                        $quantity->variation_id = $variation->id;
                        $quantity->warehouse_qty = $request->product_qty[$i];
                        $product_qty = $request->product_qty[$i];
                        $item_from_quantity->warehouse_qty = $item_from_quantity->warehouse_qty - $product_qty;
                        $item_from_quantity->available_qty = $item_from_quantity->available_qty - $product_qty;
                        $quantity->available_qty = $request->product_qty[$i]; //fix
                        $quantity->deliver_qty = '0';
                        $quantity->alert_qty = $item_from_quantity->alert_qty;
                        $quantity->save();
                        $item_from_quantity->save();
                    }



                    $transfer_history = new TransferHistory();
                    $transfer_history->from_location = $request->from_location;
                    $transfer_history->to_location = $request->to_location;
                    $transfer_history->remark = $request->remark;
                    // $transfer_history->transfer_no = (int) $latest_transferno + 1;
                    $transfer_history->transfer_no = $request->transfer_number;
                    $transfer_history->item_name = $request->part_number[$i];
                    $transfer_history->product_name = $request->result_item_name[$i];
                    $transfer_history->item_id = $request->item_id[$i];
                    $transfer_history->variation_id = $request->result_id[$i];
                    $transfer_history->quantity = $request->product_qty[$i];
                    $transfer_history->date = $request->date;
                    if ($item_to_variation) {
                        $transfer_history->to_variation_id = $item_to_variation->id;
                        $transfer_history->to_item_id = $item_to->id;
                    } else {
                        $transfer_history->to_variation_id = $variation->id;
                        $transfer_history->to_item_id = $item_to->id;
                    }
                    $transfer_history->save();
                } else {
                    $item = new Item();
                    $item->item_name = $request->result_item_name[$i];
                    $item->brand_id = $item_from->brand_id;
                    $item->brand = $item_from->brand;
                    $item->item_descriptions = $item_from->item_descriptions;
                    $item->image = $item_from->image;
                    $item->stock_type = $item_from->stock_type;
                    $item->item_type = $item_from->item_type;
                    $item->parent_id = $item_from->id;
                    $item->warehouse_id = $request->to_location;
                    $item->save();

                    if ($request->result_id[$i] == $item_from_variation->id) {
                        $variation = new ItemVariation();
                        $variation->item_id = $item->id;
                        // $variation->brand_variation_id = $item_from_variation->brand_variation_id;
                        $variation->unit = $item_from_variation->unit;
                        $variation->variation_desc = $item_from_variation->variation_desc;
                        $variation->expired_date = $item_from_variation->expired_date;
                        $variation->product_code = $item_from_variation->product_code;
                        $variation->barcode = $item_from_variation->barcode;
                        $variation->stock_agent_date = $item_from_variation->stock_agent_date;
                        $variation->model = $item_from_variation->model;
                        $variation->colour = $item_from_variation->colour;
                        $variation->buy_price = $item_from_variation->buy_price;
                        $variation->wholesale_price = $item_from_variation->wholesale_price;
                        $variation->retail_price = $item_from_variation->retail_price;
                        $variation->retail_set_price = $item_from_variation->retail_set_price;
                        $variation->promotion_retail_unit = $item_from->promotion_retail_unit;
                        $variation->promotion_retail_set = $item_from_variation->promotion_retail_set;
                        $variation->save();
                        $item_from_variation->save();


                        $quantity = new ItemQuantity();
                        $quantity->item_id = $item->id;
                        $quantity->variation_id = $variation->id;
                        $quantity->warehouse_qty = $request->product_qty[$i];
                        $product_qty = $request->product_qty[$i];
                        $item_from_quantity->warehouse_qty = $item_from_quantity->warehouse_qty - $product_qty;
                        $quantity->alert_qty = $item_from_quantity->alert_qty;
                        $item_from_quantity->available_qty = $item_from_quantity->available_qty - $product_qty;
                        $quantity->available_qty = $request->product_qty[$i];
                        $quantity->deliver_qty = '0';
                        $quantity->save();
                        $item_from_quantity->save();
                    }

                    // Store the transfer history
                    $transfer_history = new TransferHistory();
                    $transfer_history->from_location = $request->from_location;
                    $transfer_history->to_location = $request->to_location;
                    $transfer_history->remark = $request->remark;
                    // $transfer_history->transfer_no = (int) $latest_transferno + 1;
                    $transfer_history->transfer_no = $request->transfer_number;
                    $transfer_history->item_name = $request->part_number[$i];
                    $transfer_history->product_name = $request->result_item_name[$i];
                    $transfer_history->item_id = $request->item_id[$i];
                    // $transfer_history->variation_id = $variation->id;
                    $transfer_history->variation_id = $request->result_id[$i];
                    $transfer_history->quantity = $request->product_qty[$i];
                    $transfer_history->date = $request->date;
                    $transfer_history->to_item_id = $variation->item_id;
                    $transfer_history->to_variation_id = $variation->id;
                    $transfer_history->save();
                }
            }

            // DB::commit();
            return redirect('show_transfer_history')->with('success', 'Successfully Transfer Item from ' . $from->name . ' to ' . $to->name . ' ');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect('show_transfer_history')->with('error', 'Error Transfer Item from ' . $e->getMessage());
        }
    }

    public function autocompletePartCode(Request $request)
    {
        $query = $request->get('query');
        $location = $request->get('location');

        $cacheKey = "autocomplete_part_code_{$query}_{$location}";
        $items = Cache::remember($cacheKey, 60, function () use ($query, $location) {

            $itemIds = Item::where('warehouse_id', $location)
                ->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('item_name', 'like', '%' . $query . '%')
                        ->orWhereHas('variations', function ($q) use ($query) {
                            $q->where('model', 'like', '%' . $query . '%');
                        });
                })
                ->pluck('id');
            info($itemIds);
            return ItemVariation::whereIn('item_id', $itemIds)
                ->with(['item:id,item_name,item_descriptions,brand,stock_type'])
                ->get(['item_id', 'product_code', 'id', 'model', 'colour', 'size', 'seater', 'variation_desc'])
                ->map(function ($variation) {
                    return [
                        'item_name' => $variation->item ? $variation->item->item_name : 'Unknown Item',
                        'description' => $variation->item ? $variation->item->item_descriptions : '',
                        'stock_type' => $variation->item ? $variation->item->stock_type : '',
                        'brand' => $variation->item ? $variation->item->brand : 'Unknown Brand',
                        'item_category' => $variation->item ? $variation->item->item_category : 'Unknown Category',
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



    public function getPartData(Request $request)
    {
        try {
            $item_id = $request->item_id;
            $variation_id = $request->variation_id;
            $location = $request->location;

            $cacheKey = "get_part_data_{$item_id}_{$variation_id}_{$location}";

            $result = Cache::remember($cacheKey, 60, function () use ($item_id, $variation_id) {
                $item = Item::find($item_id);
                $variation = ItemVariation::find($variation_id);

                if (!$item || !$variation) {
                    return null;
                }

                $item_qty = ItemQuantity::where('item_id', $item->id)
                    ->where('variation_id', $variation->id)
                    ->first();

                return [
                    'warehouse_id' => $item->warehouse_id,
                    'retail_price' => $item->retail_price,
                    'quantity' => optional($item_qty)->warehouse_qty ?? 0,
                    'reorder_level_stock' => optional($item_qty)->alert_qty ?? 0,
                ];
            });

            if (!$result) {
                return response()->json(['error' => 'Product not found'], 404);
            }

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function show_history()
    {

        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if (auth()->user()->is_admin == '1') {
            $location_names = [];
            // $histories = TransferHistory::all();

            $latestTransferIds = TransferHistory::select(DB::raw('MAX(id) as id'))->groupBy('transfer_no')
                ->pluck('id'); // Get only the IDs

            // Retrieve the full records for those IDs
            $histories = TransferHistory::whereIn('id', $latestTransferIds)
                ->orderBy('id', 'desc') // Optional: Sort by the latest ID
                ->get();


            // foreach ($histories as $history) {
            //     $from = Warehouse::find($history->from_location);
            //     $to = Warehouse::find($history->to_location);

            //     // Check if $from and $to are not null before accessing their properties
            //     if ($from && $to) {
            //         // Store location names along with history ID in the associative array
            //         $location_names[$history->id] = [
            //             'from' => $from->name,
            //             'to' => $to->name
            //         ];
            //     }
            // }
        } else {
            $location_names = [];

            // $histories = TransferHistory::whereIn('from_location', $warehousePermission)->get();

            $latestTransferIds = TransferHistory::whereIn('from_location', $warehousePermission)->select(DB::raw('MAX(id) as id'))
                ->groupBy('transfer_no')
                ->pluck('id'); // Get only the IDs

            // Retrieve the full records for those IDs
            $histories = TransferHistory::whereIn('id', $latestTransferIds)
                ->orderBy('id', 'desc') // Optional: Sort by the latest ID
                ->get();


            // foreach ($histories as $history) {
            //     $from = Warehouse::find($history->from_location);
            //     $to = Warehouse::find($history->to_location);
            //     if ($from && $to) {
            //         $location_names[$history->id] = [
            //             'from' => $from->name,
            //             'to' => $to->name
            //         ];
            //     }
            // }
        }
        // dd($histories);

        return view('warehouse.transfer_history', compact('histories', 'location_names'));
    }

    public function transferHistoryDetail($transfer_no)
    {
        $histories = TransferHistory::where('transfer_no', $transfer_no)
            ->latest()->get();

        return view('warehouse.transfer_history_detail', compact('histories'));
    }


    public function transfer_history_delete($transfer_no)
    {
        $transfer_histories = TransferHistory::where('transfer_no', $transfer_no)->get();

        if ($transfer_histories->isEmpty()) {
            return back()->with('error', 'Transfer history not found.');
        }

        foreach ($transfer_histories as $transfer_history) {
            $from_location_item = Item::where('id', $transfer_history->item_id)
                ->where('warehouse_id', $transfer_history->from_location)
                ->first();

            if ($from_location_item) {
                $from_location_item_variation = ItemVariation::where('id', $transfer_history->variation_id)->first();

                if ($from_location_item_variation) {
                    $from_location_item_quantity = ItemQuantity::where('item_id', $from_location_item_variation->item_id)
                        ->where('variation_id', $from_location_item_variation->id)
                        ->first();

                    if ($from_location_item_quantity) {
                        $from_location_item_quantity->warehouse_qty += $transfer_history->quantity;
                        $from_location_item_quantity->available_qty += $transfer_history->quantity;
                        $from_location_item_quantity->save();
                    }
                }
            }

            $to_location_item = Item::where('item_name', $transfer_history->product_name)
                ->where('warehouse_id', $transfer_history->to_location)
                ->where('id', $transfer_history->to_item_id)
                ->first();

            if ($to_location_item) {
                $to_location_item_variation = ItemVariation::where('item_id', $to_location_item->id)
                    ->where('id', $transfer_history->to_variation_id ?? null)
                    ->first();

                if ($to_location_item_variation) {
                    $to_location_item_quantity = ItemQuantity::where('item_id', $to_location_item->id)
                        ->where('variation_id', $to_location_item_variation->id)
                        ->first();

                    if ($to_location_item_quantity) {
                        $to_location_item_quantity->warehouse_qty -= $transfer_history->quantity;
                        $to_location_item_quantity->available_qty -= $transfer_history->quantity;
                        $to_location_item_quantity->save();
                    }
                }
            }

            $transfer_history->delete();
        }

        return back()->with('success', 'All transfer histories deleted successfully.');
    }


    public function transfer_history_edit($transfer_no)
    {

        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if (auth()->user()->is_admin == '1') {
            $warehouses = Warehouse::all();
        } else {
            $warehouses = Warehouse::whereIn('id', $warehousePermission)->get();
        }

        $historys = TransferHistory::where('transfer_no', $transfer_no)->get();

        return view('warehouse.transfer_history_edit', compact('historys', 'warehouses'));
    }




    public function transfer_history_update(Request $request, $transfer_no)
    {
        $transfer_histories = TransferHistory::where('transfer_no', $transfer_no)->get();

        foreach ($transfer_histories as $transfer_history) {

            $old_quantity = $transfer_history->quantity;
            $transfer_history->delete();

            $count = count($request->part_number);

            for ($i = 0; $i < $count; $i++) {

                $item_to = Item::where('item_name', $request->result_item_name[$i])
                    ->where('brand', $request->brand[$i])
                    ->where('item_category', $request->category[$i])
                    ->where('warehouse_id', $request->to_location)->first();
                $item_from = Item::where('id', $request->item_id[$i])->where('warehouse_id', $request->from_location)->first();

                $item_from_variation = ItemVariation::where('item_id', $item_from->id)
                    ->where('id', $request->result_id[$i])
                    ->first();

                $item_from_quantity = ItemQuantity::where('item_id', $item_from->id)
                    ->where('variation_id', $item_from_variation->id)
                    ->first();

                if ($item_to) {

                    $item_to_variation = ItemVariation::where('item_id', $item_to->id)
                        ->where('product_code', $request->product_code[$i])
                        ->where('model', $request->model[$i])
                        ->where('size', $request->size[$i])
                        ->where('colour', $request->colour[$i])
                        ->where('seater', $request->seater[$i])
                        ->first();

                    if ($item_to_variation) {
                        $item_to_quantity = ItemQuantity::where('item_id', $item_to->id)
                            ->where('variation_id', $item_to_variation->id)
                            ->first();
                        $item_to_quantity->warehouse_qty += $request->product_qty[$i] - $old_quantity;
                        $item_from_quantity->warehouse_qty = $item_from_quantity->warehouse_qty - $request->product_qty[$i] + $old_quantity;

                        $item_to_quantity->available_qty += $request->product_qty[$i] - $old_quantity;
                        $item_from_quantity->available_qty = $item_from_quantity->available_qty - $request->product_qty[$i] + $old_quantity;

                        $item_to_quantity->save();
                        $item_from_quantity->save();
                    } else {
                        $variation = new ItemVariation();
                        $variation->item_id = $item_to->id;
                        $variation->unit = $item_from_variation->unit;
                        $variation->variation_desc = $item_from_variation->variation_desc;
                        $variation->status = '1';
                        $variation->expired_date = $item_from_variation->expired_date;
                        $variation->product_code = $item_from_variation->product_code;
                        $variation->barcode = $item_from_variation->barcode;
                        $variation->stock_agent_date = $item_from_variation->stock_agent_date;
                        $variation->model = $item_from_variation->model;
                        $variation->colour = $item_from_variation->colour;
                        $variation->size = $item_from_variation->size;
                        $variation->seater = $item_from_variation->seater;
                        $variation->buy_price = $item_from_variation->buy_price;
                        $variation->wholesale_price = $item_from_variation->wholesale_price;
                        $variation->retail_price = $item_from_variation->retail_price;
                        $variation->retail_set_price = $item_from_variation->retail_set_price;
                        $variation->promotion_retail_unit = $item_from->promotion_retail_unit;
                        $variation->promotion_retail_set = $item_from_variation->promotion_retail_set;
                        $variation->save();
                        $item_from_variation->save();

                        $quantity = new ItemQuantity();
                        $quantity->item_id = $item_to->id;
                        $quantity->variation_id = $variation->id;
                        $quantity->warehouse_qty = $request->product_qty[$i];
                        $product_qty = $request->product_qty[$i];
                        $item_from_quantity->warehouse_qty = $item_from_quantity->warehouse_qty - $product_qty;
                        $item_from_quantity->available_qty = $item_from_quantity->available_qty - $product_qty;
                        $quantity->available_qty = $request->product_qty[$i];
                        $quantity->deliver_qty = '0';
                        $quantity->alert_qty = $item_from_quantity->alert_qty;
                        $quantity->save();
                        $item_from_quantity->save();
                    }

                    $transfer_history = new TransferHistory();
                    $transfer_history->from_location = $request->from_location;
                    $transfer_history->to_location = $request->to_location;
                    $transfer_history->transfer_no = $request->transfer_number;
                    $transfer_history->item_name = $request->part_number[$i];
                    $transfer_history->product_name = $request->result_item_name[$i];
                    $transfer_history->item_id = $request->item_id[$i];
                    $transfer_history->variation_id = $request->result_id[$i];
                    $transfer_history->quantity = $request->product_qty[$i];
                    $transfer_history->date = $request->date;
                    $transfer_history->save();
                } else {
                    $item = new Item();
                    $item->item_name = $request->result_item_name[$i];
                    $item->brand_id = $item_from->brand_id;
                    $item->brand = $item_from->brand;
                    $item->item_descriptions = $item_from->item_descriptions;
                    $item->item_category = $item_from->item_category;
                    $item->type = $item_from->type;
                    $item->stock_type = $item_from->stock_type;
                    $item->item_type = $item_from->item_type;
                    $item->parent_id = $item_from->id;
                    $item->warehouse_id = $request->to_location;
                    $item->save();

                    if ($request->result_id[$i] == $item_from_variation->id) {
                        $variation = new ItemVariation();
                        $variation->item_id = $item->id;
                        $variation->unit = $item_from_variation->unit;
                        $variation->variation_desc = $item_from_variation->variation_desc;
                        $variation->status = '1';
                        $variation->expired_date = $item_from_variation->expired_date;
                        $variation->product_code = $item_from_variation->product_code;
                        $variation->barcode = $item_from_variation->barcode;
                        $variation->stock_agent_date = $item_from_variation->stock_agent_date;
                        $variation->model = $item_from_variation->model;
                        $variation->colour = $item_from_variation->colour;
                        $variation->size = $item_from_variation->size;
                        $variation->seater = $item_from_variation->seater;
                        $variation->buy_price = $item_from_variation->buy_price;
                        $variation->wholesale_price = $item_from_variation->wholesale_price;
                        $variation->retail_price = $item_from_variation->retail_price;
                        $variation->retail_set_price = $item_from_variation->retail_set_price;
                        $variation->promotion_retail_unit = $item_from->promotion_retail_unit;
                        $variation->promotion_retail_set = $item_from_variation->promotion_retail_set;
                        $variation->save();
                        $item_from_variation->save();

                        $quantity = new ItemQuantity();
                        $quantity->item_id = $item->id;
                        $quantity->variation_id = $variation->id;
                        $quantity->warehouse_qty = $request->product_qty[$i];
                        $product_qty = $request->product_qty[$i];
                        $item_from_quantity->warehouse_qty = $item_from_quantity->warehouse_qty - $product_qty;
                        $item_from_quantity->available_qty = $item_from_quantity->available_qty - $product_qty;
                        $quantity->available_qty = $request->product_qty[$i];
                        $quantity->deliver_qty = '0';
                        $quantity->alert_qty = $item_from_quantity->alert_qty;
                        $quantity->save();
                        $item_from_quantity->save();
                    }

                    $transfer_history = new TransferHistory();
                    $transfer_history->from_location = $request->from_location;
                    $transfer_history->to_location = $request->to_location;
                    $transfer_history->transfer_no = $request->transfer_number;
                    $transfer_history->item_name = $request->part_number[$i];
                    $transfer_history->product_name = $request->result_item_name[$i];
                    $transfer_history->item_id = $request->item_id[$i];
                    $transfer_history->variation_id = $variation->id;
                    $transfer_history->quantity = $request->product_qty[$i];
                    $transfer_history->date = $request->date;
                    $transfer_history->save();
                }
            }
        }

        return back()->with('success', 'Transfer history updated successfully.');
    }

    public function good_received($transfer_no)
    {


        $transfer_history = TransferHistory::where('transfer_no', $transfer_no)
            ->latest()->get();

        return view('warehouse.good_received', compact('transfer_history'));
    }
}
