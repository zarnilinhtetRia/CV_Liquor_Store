<?php

namespace App\Http\Controllers;

use App\Models\DeliveredItems;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\ItemQuantity;
use App\Models\ItemVariation;
use App\Models\Sell;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DeliveredItemsController extends Controller
{
    // public function save(Request $request)
    // {
    //     $ids = $request->input('ids');
    //     // dd($ids);

    //     if ($ids == null) {
    //         return redirect()->back()->with('error', 'Select the item to Deliver!');
    //     }

    //     $latest_delivery_group_no = DeliveredItems::latest()->first()->delivery_group_no ?? 0;

    //     foreach ($ids as $key => $id) {

    //         $delivered_item = new DeliveredItems();
    //         $delivered_item->sell_id = $request->sell_id[$id];
    //         $delivered_item->invoice_id = $request->invoice_id;
    //         $delivered_item->item_id = $request->item_id[$id];
    //         $delivered_item->variation_id = $request->variation_id[$id];
    //         $delivered_item->origin_qty = $request->origin_qty[$id];
    //         $delivered_item->delivery_group_no = (int) $latest_delivery_group_no + 1;
    //         // dd($latest_delivery_group_no);
    //         $delivered_item->delivered_qty = $request->to_deliver_qty[$id];
    //         $delivered_item->save();
    //         $delivered_item->do_no = 'DO-' . $delivered_item->id;
    //         $delivered_item->update();

    //         $sell = Sell::find($request->sell_id[$id]);
    //         $item = Item::where('id', $sell->item_id)
    //             ->where('warehouse_id', $sell->warehouse)
    //             ->first();

    //         if ($item) {
    //             $item_variation = ItemVariation::with('item_quantity')->where('item_id', $item->id)
    //                 ->where('id', $sell->variation_id)
    //                 ->first();

    //             if ($item_variation) {

    //                 $item_quantity = ItemQuantity::where('item_id', $item_variation->item_id)->where('variation_id', $item_variation->id)->first();

    //                 $new_quantity = $item_quantity->warehouse_qty - $request->to_deliver_qty[$id];

    //                 // $item_variation->item_quantity->available_qty = $new_quantity;
    //                 // $item_variation->save();
    //                 if ($new_quantity < 1) {
    //                     $item_quantity->warehouse_qty = 0;
    //                 } else {
    //                     $item_quantity->warehouse_qty = $new_quantity;
    //                 }
    //                 $item_quantity->deliver_qty -= $request->to_deliver_qty[$id];

    //                 $item_quantity->save();
    //             }
    //         }

    //         $sell->delivered_qty += $request->to_deliver_qty[$id];
    //         $sell->update();
    //     }

    //     return redirect()->back()->with('success', 'Item Delivery Successful!');
    // }

    public function save(Request $request)
    {
        $ids = $request->input('ids');

        if ($ids == null) {
            return redirect()->back()->with('error', 'Select the item to Deliver!');
        }

        $latest_delivery_group_no = DeliveredItems::latest()->first()->delivery_group_no ?? 0;
        $new_delivery_group_no = (int)$latest_delivery_group_no + 1;

        $latestDoNo = DeliveredItems::whereNotNull('do_no')
            ->where('do_no', 'LIKE', 'Do-%')
            ->max('do_no');

        if ($latestDoNo) {
            $latestNumber = (int) str_replace('Do-', '', $latestDoNo);
            $nextDoNo = 'Do-' . ($latestNumber + 1);
        } else {
            $nextDoNo = 'Do-1';
        }



        foreach ($ids as $key => $id) {
            $delivered_item = new DeliveredItems();
            $delivered_item->sell_id = $request->sell_id[$id];
            $delivered_item->date = $request->date;
            $delivered_item->remark = $request->remark;
            // $delivered_item->date = $request->date[$id];
            $delivered_item->invoice_id = $request->invoice_id;
            $delivered_item->item_id = $request->item_id[$id];
            $delivered_item->variation_id = $request->variation_id[$id];
            $delivered_item->origin_qty = $request->origin_qty[$id];
            $delivered_item->delivery_group_no = $new_delivery_group_no;
            $delivered_item->delivered_qty = $request->to_deliver_qty[$id];
            $delivered_item->do_no = $nextDoNo;
            $delivered_item->save();

            $sell = Sell::find($request->sell_id[$id]);
            $item = Item::where('id', $sell->item_id)
                ->where('warehouse_id', $sell->warehouse)
                ->first();

            if ($item) {
                $item_variation = ItemVariation::with('item_quantity')
                    ->where('item_id', $item->id)
                    ->where('id', $sell->variation_id)
                    ->first();

                if ($item_variation) {
                    $item_quantity = ItemQuantity::where('item_id', $item_variation->item_id)
                        ->where('variation_id', $item_variation->id)
                        ->first();

                    $new_quantity = $item_quantity->warehouse_qty - $request->to_deliver_qty[$id];
                    $item_quantity->warehouse_qty = max(0, $new_quantity);
                    $item_quantity->deliver_qty -= $request->to_deliver_qty[$id];
                    $item_quantity->save();
                }
            }

            $sell->delivered_qty += $request->to_deliver_qty[$id];
            $sell->update();
        }

        return redirect()->back()->with('success', 'Item Delivery Successful!');
    }



    public function doFormView($no)
    {
        $delivered_items = DeliveredItems::where('delivery_group_no', $no)->get();
        $invoice = DeliveredItems::where('delivery_group_no', $no)->first()->invoice;
        // $sells = Sell::with('variations')->where('invoiceid', $invoice->id)->get();
        // $payment_methods = InvoicePaymentMethod::where('invoice_id', $invoice->id)->get();
        return view('invoice.delivery_order_form', [
            'delivered_items' => $delivered_items,
            'invoice' => $invoice,
            // 'payment_methods' => $payment_methods,
        ]);
    }


    public function daily_delivery()
    {
        $today = Carbon::today();
        $branchs = Warehouse::all();
        $dailys = DeliveredItems::whereDate('created_at', $today)
            ->latest()
            ->get();

        return view('daily_delivery.daily_delivery', compact('dailys', 'branchs'));
    }

    public function daily_delivery_search(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $branch_id = $request->input('branch');

        $branchs = Warehouse::all();

        $dailys = DeliveredItems::whereHas('invoice', function ($query) use ($branch_id) {
            $query->where('branch', $branch_id);
        })
            ->whereDate('date', '>=', $start_date)
            ->whereDate('date', '<=', $end_date)
            ->latest()
            ->get();

        return view('daily_delivery.daily_delivery', compact('dailys', 'branchs'));
    }
}
