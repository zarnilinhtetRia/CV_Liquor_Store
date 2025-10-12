<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Item;
use App\Models\Unit;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Exchange;
use App\Models\PO_sells;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\ProductList;
use App\Models\Transaction;
use App\Models\UserProfile;
use Illuminate\Support\Str;
use App\Models\ItemQuantity;
use Illuminate\Http\Request;
use App\Models\ItemVariation;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\PurchaseOrderMakePayment;
use App\Models\PurchaseOrderPaymentMethod;
use Illuminate\Support\Facades\Log;

class PurchaseOrderController extends Controller
{

    public function po_get_transaction(Request $request)
    {
        $mode = $request->balance_due;
        $location = $request->locationId;

        if ($mode == "PO") {
            $settings = Setting::where('location', $location)
                ->where('category', 'purchase order')
                ->get();
        } else {
            $settings = Setting::where('location', $location)
                ->where('category', 'invoice return')
                ->get();
        }

        if ($settings->isNotEmpty()) {
            $transaction = collect();

            foreach ($settings as $setting) {
                $transactionsForSetting = Transaction::where('location', $location)
                    ->where('account_id', $setting->transaction_id)
                    ->get();

                $transaction = $transaction->merge($transactionsForSetting);
            }

            if ($transaction->isNotEmpty()) {
                return response()->json($transaction);
            } else {
                return response()->json(['error' => 'Transactions not found'], 404);
            }
        } else {
            return response()->json(['error' => 'Settings not found'], 404);
        }
    }

    public function index($branch = null)
    {
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if (auth()->user()->is_admin == '1') {
            if ($branch) {
                $po = PurchaseOrder::where('po_status', 'PO')->where('branch', $branch)->latest()->get();
                $branchs = Warehouse::all();
            } else {
                $po = PurchaseOrder::where('po_status', 'PO')->latest()->get();
                $branchs = Warehouse::all();
            }
        } else {

            $po = PurchaseOrder::where('po_status', 'PO')->whereIn('branch', $warehousePermission)->latest()->get();
            $branchs = Warehouse::all();
        }
        $branchs = Warehouse::all();
        $branchNames = $branchs->pluck('name', 'id');

        $currentBranchName = $branch ? $branchNames[$branch] : 'All Purchase Orders';

        return view('purchase_order.purchase_order_manage', compact('po', 'branchs', 'currentBranchName', 'branchNames'));
    }
    public function purchase_order_register()
    {
        $suppliers = Supplier::all();
        $po_number = PurchaseOrder::where('quote_no', 'like', 'PO%')->latest()->get();
        $units = Unit::all();
        $po_no = 'PO-' . (count($po_number) + 1);
        $warehouses = Warehouse::all();
        // $rate = Exchange::first()->rate;
        return view('purchase_order.purchase_order', compact('po_no', 'suppliers', 'units', 'warehouses'));
    }

    public function po_no_updates(Request $request)
    {
        try {
            $locationId = $request->query('location_id'); // Get location ID

            if (!$locationId) {
                return response()->json(['error' => 'Location ID is missing'], 400);
            }
            // Fetch the maximum numeric part of invoice numbers
            $latestNumber = PurchaseOrder::where('status', 'invoice')
                ->where('branch',  $locationId)
                ->selectRaw("MAX(CAST(SUBSTRING_INDEX(quote_no, '-', -1) AS UNSIGNED)) as max_invoice_no")
                ->value('max_invoice_no');



            $nextNumber = $latestNumber ? $latestNumber + 1 : 1;


            $invoice_no = "PO-" . $nextNumber;

            return response()->json(['invoice_no' => $invoice_no]);
        } catch (\Exception $e) {
            Log::error("Error fetching invoice: " . $e->getMessage()); // Log error
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    //Customer Fill
    public function po_search(Request $request)
    {
        $query = $request->get('query');

        $data = Supplier::select('name', 'phno')
            ->where('name', 'LIKE', '%' . $query . '%')
            ->orWhere('phno', 'LIKE', '%' . $query . '%')
            ->get();

        return response()->json($data);
    }

    public function po_search_fill(Request $request)
    {
        // $userBranchId = auth()->user()->branch_id;
        $supplier = Supplier::where('name', $request->model)
            ->orWhere('phno', $request->model)
            ->orderBy('created_at', 'desc')
            ->first();
        if (!$supplier) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        $responseData = [
            'product' => $supplier,

        ];

        return response()->json($responseData);
    }

    public function purchase_order_store(Request $request)
    {

        $count = count($request->part_number);
        $invoice = new PurchaseOrder();




        $invoice->supplier_id = $request->supplier_id;
        $invoice->supplier_name = $request->supplier_name;
        $invoice->invoice_category = $request->quote_category;
        $invoice->phno  = $request->phno;
        $invoice->status  = $request->status;
        $invoice->type  = $request->type;
        $invoice->address  = $request->address;
        $invoice->invoice_no  = $request->invoice_no;
        $invoice->overdue_date = $request->overdue_date;
        $invoice->po_date = $request->po_date;
        $invoice->branch = $request->location;
        $invoice->quote_no  = $request->po_number;
        $invoice->sub_total  = $request->sub_total;
        $invoice->total  = $request->total;
        $invoice->balance_due  = $request->balance_due;
        $invoice->discount_total  = $request->total_discount;
        $invoice->deposit  = $request->paid;
        $invoice->remain_balance  = $request->balance;
        $invoice->remark = $request->remark;
        // $invoice->payment_method   = $request->payment_method;
        //new column
        $invoice->currency_method = $request->currency_method;
        $invoice->exchange_rate = $request->exchange_rate;
        $invoice->overall_discount_mmk = $request->overall_discount_mmk;
        $invoice->po_status = 'PO';
        $invoice->transit_status = '0';
        $invoice->internal_register_number = $request->internal_register_number;
        $invoice->supplier_register_number = $request->supplier_register_number;
        $invoice->container_register_number = $request->container_register_number;

        $invoice->save();
        $last_id = $invoice->id;
        for ($i = 0; $i < $count; $i++) {
            $result = new PO_sells();
            $result->invoiceid = $last_id;
            $result->supplier_id = $request->supplier_id;
            $result->description = $request->part_description[$i];
            $result->part_number = $request->part_number[$i];
            $result->variation_id = $request->result_id[$i];
            $result->item_id = $request->item_id[$i];
            $result->unit = $request->item_unit[$i];
            $result->exp_date = $request->exp_date[$i];
            $result->product_qty = $request->product_qty[$i];
            $result->product_price = $request->product_price[$i];
            $result->warehouse = $request->warehouse[$i];
            $result->discount = $request->discount[$i];
            $result->product_name = $request->result_item_name[$i];
            //new column
            $result->discount_category = $request->discount_category[$i];
            $result->discount_amt = $request->discount_amt[$i];
            // $result->foc = $request->foc[$i];

            $result->product_code = $request->result_product_code[$i];
            $result->model  =  $request->model[$i];
            $result->colour  =  $request->colour[$i];
            $result->save();
        }
        $po_payemt = 0;
        $count2 = count($request->payment_method);
        for ($i = 0; $i < $count2; $i++) {
            //save to PurchaseOrderMakePayment
            $payment = new PurchaseOrderMakePayment();
            $payment->payment_method =
                $request->payment_method[$i];
            $payment->amount =
                $request->payment_amount[$i];
            $payment->note = $request->note;
            $payment->po_no = $request->po_number;
            $payment->po_id = $last_id;
            $payment->payment_date = $request->po_date;
            $payment->save();

            //end save to PurchaseOrderMakePayment
            $payment_method = new PurchaseOrderPaymentMethod();
            $payment_method->po_id = $last_id;
            $payment_method->payment_method = $request->payment_method[$i];
            $payment_method->payment_amount = $request->payment_amount[$i];
            $payment_method->save();
            $po_payemt += $request->payment_amount[$i];
        }
        $setting_receive = Setting::where('location', $request->location)->where('category', 'payable')->first();

        $setting_buy = Setting::where('location', $request->location)->where('category', 'buyaccount')->first();
        info($setting_receive);
        info($setting_buy);
        if ($setting_receive) {
            $transaction = Transaction::where('location', $setting_receive->location)->where('account_id', $setting_receive->transaction_id)->first();

            if (
                $po_payemt < $request->total && $transaction->id
            ) {
                $payment_method
                    = new PurchaseOrderPaymentMethod();

                $payment_method->payment_method = $transaction->id;
                $payment_method->payment_amount = $request->total - $po_payemt;
                $payment_method->status = 'payable';
                $payment_method->po_id = $last_id;
                $payment_method->save();
            }
        }

        if ($setting_buy) {
            $transaction = Transaction::where('location', $setting_buy->location)->where('account_id', $setting_buy->transaction_id)->first();


            $payment_method
                = new PurchaseOrderPaymentMethod();
            $payment_method->payment_method = $transaction->id;
            $payment_method->payment_amount = $request->total;
            $payment_method->status = 'buyaccount';
            $payment_method->po_id = $last_id;
            $payment_method->save();
        }


        if ($invoice->balance_due == 'Sale Return Invoice') {
            foreach ($invoice->po_sells as $key => $po_sell) {
                $item = Item::where('id', $po_sell->item_id)
                    ->where('warehouse_id', $po_sell->warehouse)
                    ->first();

                if ($item) {
                    $item_variation = ItemVariation::where('item_id', $item->id)
                        ->where('id', $po_sell->variation_id)
                        ->first();

                    if ($item_variation) {

                        $item_quantity = ItemQuantity::where('item_id', $item_variation->item_id)->where('variation_id', $item_variation->id)->first();

                        // $newWarehoueQuantity = $item_quantity->warehouse_qty + $request->input('product_qty')[$key];
                        // $newAvailableQuantity = $item_quantity->available_qty + $request->input('product_qty')[$key];

                        // $item_quantity->warehouse_qty = $newWarehoueQuantity;
                        // $item_quantity->available_qty = $newAvailableQuantity;

                        $newWarehoueQuantity = $item_quantity->warehouse_qty + $request->input('product_qty')[$key];
                        $newAvailableQuantity = $item_quantity->available_qty + $request->input('product_qty')[$key];

                        $item_quantity->warehouse_qty = $newWarehoueQuantity;
                        $item_quantity->available_qty = $newAvailableQuantity;

                        $item_quantity->save();
                    } else {
                    }
                } else {
                    continue;
                }
            }
        } else {
            foreach ($invoice->po_sells as $key => $po_sell) {
                $item = Item::where('id', $po_sell->item_id)
                    ->where('warehouse_id', $po_sell->warehouse)
                    ->first();

                if ($item) {
                    $item_variation = ItemVariation::where('item_id', $item->id)
                        ->where('id', $po_sell->variation_id)
                        ->first();

                    if ($item_variation) {
                        $item_variation->buy_price = $po_sell->product_price;
                        $item_variation->save();

                        $item_quantity = ItemQuantity::where('item_id', $item_variation->item_id)->where('variation_id', $item_variation->id)->first();

                        $item_quantity->warehouse_qty += $po_sell->product_qty;
                        $item_quantity->available_qty += $po_sell->product_qty;

                        $item_quantity->save();
                    }
                } else {
                    continue;
                }
            }
        }



        if ($invoice->balance_due == 'PO') {
            return redirect('/purchase_order_manage')->with('success', 'Purchase Order Added Successful!');
        } elseif ($invoice->balance_due == 'Sale Return Invoice') {
            return redirect('/report_sale_return')->with('success', 'Sale Return Added Successful!');
        } else {
            return redirect('/sale_return')->with('success', 'Sale Return Added Successful!');
        }
    }
    public function edit($id)
    {
        $suppliers = Supplier::all();
        $purchase_orders = PurchaseOrder::find($id);
        $purchase_sells = PO_sells::where('invoiceid', $id)->get();
        $warehouses = Warehouse::all();
        $payment_method = PurchaseOrderPaymentMethod::where('po_id', $id)->whereNull('status')->get();

        $setting = Setting::where('category', 'purchase order')->where('location', $purchase_orders->branch)->get();

        $transactions = [];
        if ($setting) {
            foreach ($setting as $singleSetting) {
                $transactions[] = Transaction::where('account_id', $singleSetting->transaction_id)
                    ->where('location', $purchase_orders->branch)
                    ->get();
            }
        }

        return view('purchase_order.purchase_order_edit', compact('purchase_orders', 'suppliers', 'purchase_sells', 'warehouses', 'payment_method', 'transactions'));
    }

    public function purchase_order_update(Request $request, $id)
    {
        $count = count($request->part_number);
        $invoice = PurchaseOrder::find($id);

        // try {
        //     DB::beginTransaction();

        if ($request->balance_due == 'PO') {
            if ($invoice) {
                $oldtotal = $invoice->deposit;
                $currenttotal = $request->paid;

                if ($invoice->transaction_id) {
                    $payment = Payment::where('transaction_id', $invoice->transaction_id)->skip(2)->first();

                    if ($payment) {
                        $payment->amount = $payment->amount + ($currenttotal - $oldtotal);
                        $payment->save();
                    } else {
                    }
                } else {
                }
            } else {
            }
        } else if ($request->balance_due == 'Sale Return Invoice') {
            if ($invoice) {
                $oldtotal = $invoice->deposit;
                $currenttotal = $request->paid;

                if ($invoice->transaction_id) {
                    $payment = Payment::where('transaction_id', $invoice->transaction_id)->skip(4)->first();

                    if ($payment) {
                        $payment->amount = $payment->amount + ($currenttotal - $oldtotal);
                        $payment->save();
                    } else {
                    }
                } else {
                }
            } else {
            }
        } else if ($request->balance_due == 'Sale Return') {
            if ($invoice) {
                $oldtotal = $invoice->deposit;
                $currenttotal = $request->paid;

                if ($invoice->transaction_id) {
                    $payment = Payment::where('transaction_id', $invoice->transaction_id)->skip(5)->first();

                    if ($payment) {
                        $payment->amount = $payment->amount + ($currenttotal - $oldtotal);
                        $payment->save();
                    } else {
                    }
                } else {
                }
            } else {
            }
        }


        $invoice->supplier_id = $request->supplier_id;
        $invoice->invoice_category = $request->quote_category;
        $invoice->phno  = $request->phno;
        $invoice->status  = $request->status;
        $invoice->type  = $request->type;
        $invoice->address  = $request->address;
        $invoice->invoice_no  = $request->invoice_no;
        $invoice->overdue_date = $request->overdue_date;
        $invoice->po_date = $request->po_date;
        $invoice->branch = $request->location;
        $invoice->quote_no  = $request->po_number;
        $invoice->sub_total  = $request->sub_total;
        $invoice->total  = $request->total;
        $invoice->balance_due  = $request->balance_due;
        $invoice->discount_total  = $request->total_discount;
        $invoice->deposit  = $request->paid;
        $invoice->remain_balance  = $request->balance;
        $invoice->remark = $request->remark;
        // $invoice->payment_method   = $request->payment_method;
        //new column
        $invoice->currency_method = $request->currency_method;
        $invoice->exchange_rate = $request->exchange_rate;
        $invoice->overall_discount_mmk = $request->overall_discount_mmk;
        // $invoice->po_status =  $invoice->po_status;
        $invoice->internal_register_number = $request->internal_register_number;
        $invoice->supplier_register_number = $request->supplier_register_number;
        $invoice->container_register_number = $request->container_register_number;
        $invoice->save();
        $last_id = $invoice->id;


        $po = [];
        for ($i = 0; $i < $count; $i++) {
            $po[] = [
                'invoiceid' => $last_id,
                'supplier_id' => $request->supplier_id,
                'description' => $request->part_description[$i],
                'part_number' => $request->part_number[$i],
                'unit' => $request->item_unit[$i],
                'product_qty' => $request->product_qty[$i],
                'exp_date' => $request->exp_date[$i],
                'product_price' => $request->product_price[$i],
                'discount' => $request->discount[$i],
                'warehouse' => $request->warehouse[$i],
                'variation_id' => $request->result_id[$i],
                'item_id' => $request->item_id[$i],
                'product_name' => $request->result_item_name[$i],
                //new column
                'discount_category' => $request->discount_category[$i],
                'discount_amt' => $request->discount_amt[$i],

                'product_code' => $request->input('result_product_code')[$i],
                'model' => $request->input('model')[$i] ?? '',
                'colour' => $request->input('colour')[$i],

                'created_at' => now(),
            ];
        }
        // $po_payemt = 0;

        PurchaseOrderPaymentMethod::where('po_id', $id)->delete();
        if ($request->input('payment_method')) {
            foreach ($request->input('payment_method') as $key => $paymentMethod) {
                $payment_amount = $request->input('payment_amount')[$key] ?? 0;
                $payment_method = new PurchaseOrderPaymentMethod();
                $payment_method->po_id = $id;
                $payment_method->payment_method = $paymentMethod;
                $payment_method->payment_amount = $payment_amount;
                $payment_method->created_at = now();
                $payment_method->updated_at = now();
                $payment_method->save();
                // $po_payemt += $request->payment_amount[$i];
            }
        }

        $setting_buy = Setting::where('location', $request->location)->where('category', 'buyaccount')->first();

        $setting_receive = Setting::where('location', $request->location)->where('category', 'payable')->first();


        if ($setting_receive) {
            $transaction = Transaction::where('location', $setting_receive->location)->where('account_id', $setting_receive->transaction_id)->first();
            if (
                $payment_amount < $request->total && $transaction->id
            ) {
                $payment_method
                    = new PurchaseOrderPaymentMethod();
                $payment_method->payment_method = $transaction->id;
                $payment_method->payment_amount = $request->total - $payment_amount;
                $payment_method->status = 'payable';
                $payment_method->po_id = $last_id;
                $payment_method->save();
            }
        }


        if ($setting_buy) {
            $transaction = Transaction::where('location', $setting_buy->location)->where('account_id', $setting_buy->transaction_id)->first();

            if (
                $payment_amount < $request->total && $transaction->id
            ) {
                $payment_method
                    = new PurchaseOrderPaymentMethod();
                $payment_method->payment_method = $transaction->id;
                $payment_method->payment_amount = $request->total;
                $payment_method->status = 'buyaccount';
                $payment_method->po_id = $last_id;
                $payment_method->save();
            }
        }



        $oldQuantities = [];
        $oldItems = [];
        $newItems = [];
        foreach ($invoice->po_sells as $key => $po_sell) {
            $oldQuantities[$po_sell->item_id][$po_sell->variation_id] = $po_sell->product_qty;
            $oldItems[$po_sell->variation_id] = $po_sell->item_id . '-' . $po_sell->variation_id;
        }

        foreach ($request->input('item_id') as $key => $itemID) {

            $item = Item::where('id', $itemID)
                ->where('warehouse_id', $request->warehouse[$key])
                ->first();
            // info($item);
            if ($item) {
                $item_variation = ItemVariation::where('item_id', $item->id)
                    ->where('id', $request->result_id[$key])
                    ->first();

                if ($invoice->balance_due == 'PO') {
                    $item_variation->buy_price = $request->product_price[$key];
                    $item_variation->save();
                }

                if ($item_variation) {

                    $newItems[$itemID . '-' . $item_variation->id] = $item_variation->id;

                    $item_quantity = ItemQuantity::where('item_id', $item_variation->item_id)->where('variation_id', $item_variation->id)->first();

                    if (!$item_quantity) {
                        continue;
                    }

                    $currentWarehouseQuantity = $item_quantity->warehouse_qty;
                    $currentAvailableQuantity = $item_quantity->available_qty;

                    $newWarehoueQuantity = ($currentWarehouseQuantity - ($oldQuantities[$itemID][$item_variation->id] ?? 0)) + $request->input('product_qty')[$key];
                    $newAvailableQuantity = ($currentAvailableQuantity - ($oldQuantities[$itemID][$item_variation->id] ?? 0)) + $request->input('product_qty')[$key];

                    //check whether

                    $item_quantity->warehouse_qty = $newWarehoueQuantity;
                    $item_quantity->available_qty = $newAvailableQuantity;

                    $item_quantity->save();
                }
            } else {
                continue;
            }
        }

        //recalculate qty for remove item
        foreach ($oldItems as $key => $oldItem) {
            if (!array_key_exists($oldItem, $newItems)) {

                $item = Item::where('id', $oldItem)->first();

                $item_variation = ItemVariation::with('item_quantity')->where('item_id', $item->id)
                    ->where('id', $key)
                    ->first();


                $item_quantity = ItemQuantity::where('item_id', $item_variation->item_id)->where('variation_id', $item_variation->id)->first();

                $item_quantity->available_qty -= $oldQuantities[$item_variation->item_id][$item_variation->id] ?? 0;
                $item_quantity->warehouse_qty -= $oldQuantities[$item_variation->item_id][$item_variation->id] ?? 0;
                $item_quantity->save();
            }
        }



        PO_sells::where('invoiceid', $id)->delete();
        PO_sells::insert($po);

        // DB::commit();

        if (Str::contains($invoice->quote_no, 'PO') && $invoice->balance_due == 'PO') {
            return redirect('/purchase_order_manage')->with('success', 'Purchase Order Added Successful!');
        } elseif ($invoice->balance_due == 'Sale Return Invoice') {
            return redirect('/report_sale_return')->with('success', 'Sale Return Added Successful!');
        } else {
            return redirect('/sale_return')->with('success', 'Sale Return Added Successful!');
        }
    }

    public function po_delete($id)
    {

        DB::beginTransaction();

        try {

            $invoice = PurchaseOrder::findOrFail($id);
            if ($invoice->balance_due == 'PO') {
                if ($invoice) {
                    $oldtotal = $invoice->deposit;

                    if ($invoice->transaction_id) {
                        $payment = Payment::where('transaction_id', $invoice->transaction_id)->skip(2)->first();

                        if ($payment) {
                            $payment->amount = $payment->amount - $oldtotal;
                            $payment->save();
                        } else {
                        }
                    } else {
                    }
                } else {
                }
            } else if ($invoice->balance_due == 'Sale Return Invoice') {
                if ($invoice) {
                    $oldtotal = $invoice->deposit;

                    if ($invoice->transaction_id) {
                        $payment = Payment::where('transaction_id', $invoice->transaction_id)->skip(4)->first();

                        if ($payment) {
                            $payment->amount = $payment->amount - $oldtotal;
                            $payment->save();
                        } else {
                        }
                    } else {
                    }
                } else {
                }
            }
            $invoice->delete();

            //recalculate item qty
            foreach ($invoice->po_sells as $key => $po_sell) {
                $item = Item::where('id', $po_sell->item_id)
                    ->where('warehouse_id', $po_sell->warehouse)
                    ->first();

                if ($item) {
                    $item_variation = ItemVariation::where('item_id', $item->id)
                        ->where('id', $po_sell->variation_id)
                        ->first();

                    if ($item_variation) {
                        $item_quantity = ItemQuantity::where('item_id', $item_variation->item_id)
                            ->where('variation_id', $item_variation->id)
                            ->first();

                        if ($item_quantity) {
                            $item_quantity->warehouse_qty = (float)$item_quantity->warehouse_qty - (float)$po_sell->product_qty;
                            $item_quantity->available_qty = (float)$item_quantity->available_qty - (float)$po_sell->product_qty;
                            $item_quantity->save();
                        }
                    }
                }
            }


            PO_sells::where('invoiceid', $id)->delete();
            PurchaseOrderPaymentMethod::where('po_id', $id)->delete();
            PurchaseOrderMakePayment::where('po_id', $id)->delete();
            DB::commit();
            return redirect('/purchase_order_manage')->with('success', 'Purchase Order Deleted Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect('/purchase_order_manage')->with('error', 'Failed to delete purchase order.');
        }

        // return redirect('/quotation')->with('success', 'Quotation Deleted Successful!');
    }

    public function details($id)
    {
        $purchase_order = PurchaseOrder::find($id);
        $purchase_sells = PO_sells::where('invoiceid', $id)->get();
        $profile = UserProfile::all();
        $payment_methods = PurchaseOrderPaymentMethod::where('po_id', $id)->whereNull('status')->get();
        return view('purchase_order.purchase_order_details', compact('purchase_order', 'purchase_sells', 'profile', 'payment_methods'));
    }

    public function getPartPoData(Request $request)
    {
        $itemid = $request->item_id;
        $variationid = $request->variation_id;

        $location = $request->location;
        // $description = $request->description;
        // $productCode = $request->product_code;

        $cacheKey = "part_data_{$itemid}_{$location}_{$variationid}";

        $itemData = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($itemid, $location, $variationid) {
            $item = Item::where('id', $itemid)
                ->where('warehouse_id', $location)
                ->first();



            if ($item) {
                $variations = ItemVariation::where('item_id', $itemid)
                    ->where('id', $variationid)->first();

                $item_qty = ItemQuantity::where('item_id', $itemid)
                    ->where('variation_id', $variationid)
                    ->first();

                return [
                    'item' => $item,
                    'variations' => $variations,
                    'item_qty' => $item_qty,
                ];
            }

            return null;
        });

        if (!$itemData) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $resdata = [
            'item' => [
                'item_name' => $itemData['item']->item_name,
                'id' => $itemData['item']->id,
                // 'item_unit' => $itemData['item']->item_unit,
                'warehouse_id' => $itemData['item']->warehouse_id,
                'descriptions' => $itemData['item']->item_descriptions,

            ],
            'variation' => [
                'variations_barcode' => $itemData['variations']->barcode,
                'variations_desc' => $itemData['variations']->variation_desc,
                'id' => $itemData['variations']->id,
                'product_code' => $itemData['variations']->product_code,
                'expired_date' => $itemData['variations']->expired_date,
                'item_unit' => $itemData['variations']->unit,
                'model' => $itemData['variations']->model,
                'colour' => $itemData['variations']->colour,
                'size' => $itemData['variations']->size,
                'seater' => $itemData['variations']->seater,
                'retail_price' => $itemData['variations']->retail_price,
                'wholesale_price' => $itemData['variations']->wholesale_price,
                'buy_price' => $itemData['variations']->buy_price,
            ],
            'item_qty' => [
                'quantity' => $itemData['item_qty']->warehouse_qty,
                'reorder_level_stock' => $itemData['item_qty']->alert_qty,
            ],

        ];

        return response()->json($resdata);
    }

    //po status change
    public function poStatusChange(Request $request)
    {
        $status = $request->status;

        $po = PurchaseOrder::find($request->id);

        if (!$po) {
            return response()->json(['error' => 'Purchase order not found'], 404);
        } else {
            $po->po_status = $status;
            $po->update();
        }

        return response()->json($po);
    }

    public function autocompletePartCodePo(Request $request)
    {
        $query = $request->get('query');
        $location = $request->get('location');

        $cacheKey = "autocomplete_po_{$query}_{$location}";



        $items = Cache::remember($cacheKey, 60, function () use ($query, $location) {
            $itemIds = Item::where('warehouse_id', $location)
                ->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('item_name', 'like', '%' . $query . '%')
                        ->orWhereHas('variations', function ($q) use ($query) {
                            $q->where('model', 'like', '%' . $query . '%');
                        });
                })
                ->pluck('id');
            return ItemVariation::whereIn('item_id', $itemIds)
                ->where('status', '1')
                ->with('item:id,item_name,item_descriptions') // Fetch related item data
                ->get(['item_id', 'product_code', 'id', 'model', 'colour', 'size', 'seater', 'variation_desc'])
                ->map(function ($variation) {

                    // $productList = ProductList::where('item_id', $variation->item_id)
                    //     ->where('variation_id', $variation->id)
                    //     ->whereNull('deleted_at')
                    //     ->first();

                    return [
                        'item_name' => $variation->item ? $variation->item->item_name : 'Unknown Item',
                        'description' => $variation->item->item_descriptions,
                        'variation_desc' => $variation->variation_desc,
                        'product_code' => $variation->product_code,
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
    // public function autocompletePartCodePo(Request $request)
    // {
    //     $query = $request->get('query');
    //     $location = $request->get('location');

    //     $cacheKey = "autocomplete_{$query}_{$location}";
    //     $items = Cache::remember($cacheKey, 60, function () use ($query, $location) {
    //         $itemIds = Item::where('warehouse_id', $location)
    //             ->where(function ($queryBuilder) use ($query) {
    //                 $queryBuilder->where('item_name', 'like', '%' . $query . '%')
    //                     ->orWhereHas('variations', function ($q) use ($query) {
    //                         $q->where('model', 'like', '%' . $query . '%');
    //                     });
    //             })
    //             ->pluck('id');


    //         info($itemIds);

    //         return ItemVariation::whereIn('item_id', $itemIds)
    //             ->with('item:id,item_name,item_descriptions')
    //             ->get(['item_id', 'product_code', 'id', 'model', 'colour', 'size', 'seater', 'variation_desc'])
    //             ->map(function ($variation) {
    //                 return [
    //                     'item_name' => $variation->item ? $variation->item->item_name : 'Unknown Item',
    //                     'description' => $variation->item->item_descriptions,
    //                     'product_code' => $variation->product_code,
    //                     'variation_desc' => $variation->variation_desc,
    //                     'model' => $variation->model,
    //                     'colour' => $variation->colour,
    //                     'size' => $variation->size,
    //                     'seater' => $variation->seater,
    //                     'id' => $variation->id,
    //                     'item_id' => $variation->item_id,
    //                 ];
    //             });
    //     });

    //     return response()->json($items);
    // }

    public function transit(Request $request, $id)
    {

        $po = PurchaseOrder::find($id);

        foreach ($po->po_sells as $key => $po_sell) {
            $item = Item::where('id', $po_sell->item_id)
                ->where('warehouse_id', $po_sell->warehouse)
                ->first();

            if ($item) {
                $item_variation = ItemVariation::where('item_id', $item->id)
                    ->where('id', $po_sell->variation_id)
                    ->first();

                if ($item_variation) {

                    $item_quantity = ItemQuantity::where('item_id', $item_variation->item_id)->where('variation_id', $item_variation->id)->first();

                    $item_quantity->warehouse_qty += $po_sell->product_qty;
                    $item_quantity->available_qty += $po_sell->product_qty;

                    $item_quantity->save();
                }
            } else {
                continue;
            }
        }
        $po->transit_status = $request->transit_status;
        $po->transit_date = now();
        $po->update();
        return redirect()->back()->with('success', 'Transit Item successfully');
    }
}
