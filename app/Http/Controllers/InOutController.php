<?php

namespace App\Http\Controllers;

use App\Models\InOut;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\ItemQuantity;
use App\Models\ItemVariation;
use App\Models\PurchaseOrder;
use App\Models\Sell;
use App\Models\TransferHistory;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class InOutController extends Controller
{

    public function stock_adjust($id)
    {
        $inouts = Inout::where('item_variation_id', $id)->where('in_out', 'in')->get();
        $item_variation = ItemVariation::find($id);
        $items = Item::where('id', $item_variation->item_id)->first();
        // dd($item_variation);

        $branchs = Warehouse::all();
        return view('inout.stock_adjust', compact('inouts', 'id', 'branchs', 'item_variation', 'items'));
    }

    public function in(Request $request, $id)
    {
        $item = ItemVariation::findOrFail($request->item_variation_id);

        $result = new InOut();
        $result->item_variation_id = $request->item_variation_id;
        $result->items_id = $request->items_id;
        $result->quantity = $request->quantity;
        $result->date = $request->date;
        $result->unit = $request->unit;
        $result->remark = $request->remark;
        $result->in_out = 'in';
        $result->warehouse_id = $request->warehouse_id;

        $quantity = $item->item_quantity;
        if ($quantity) {
            $quantity->warehouse_qty += $request->quantity;
            // $quantity->available_qty = $quantity->warehouse_qty;
            $result->total_quantity = $quantity->warehouse_qty;
            $quantity->save();
        } else {
            $quantity = new ItemQuantity();
            $quantity->warehouse_qty = $request->quantity;
            // $quantity->available_qty = $request->quantity;
            $quantity->save();
            $result->total_quantity = $quantity->warehouse_qty;
        }

        $result->save();

        return redirect()->back()->with('success', 'Stock adjusted Successfully');
    }

    public function damage_item($id)
    {
        $inouts = Inout::where('item_variation_id', $id)->where('in_out', 'out')->get();
        $item_variation = ItemVariation::find($id);
        $items = Item::where('id', $item_variation->item_id)->first();
        $branchs = Warehouse::all();
        return view('inout.damage_item', compact('inouts', 'id', 'branchs', 'item_variation', 'items'));
    }


    public function out(Request $request, $id)
    {
        $item = ItemVariation::findOrFail($request->item_variation_id);

        $result = new InOut();
        $result->item_variation_id = $request->item_variation_id;
        $result->items_id = $request->items_id;
        $result->quantity = $request->quantity;
        $result->date = $request->date;
        $result->unit = $request->unit;
        $result->remark = $request->remark;
        $result->in_out = 'out';
        $result->warehouse_id = $request->warehouse_id;

        $quantity = $item->item_quantity;
        if ($quantity) {
            $quantity->warehouse_qty -= $request->quantity;
            // $quantity->available_qty = $quantity->warehouse_qty;
            $result->total_quantity = $quantity->warehouse_qty;
            $quantity->save();
        } else {
            $quantity = new ItemQuantity();
            $quantity->warehouse_qty = $request->quantity;
            // $quantity->available_qty = $request->quantity;
            $quantity->save();
            $result->total_quantity = $quantity->warehouse_qty;
        }

        $result->save();

        return redirect()->back()->with('success', 'Stock adjusted Successfully');
    }



    public function display_print($id, $items_id)
    {

        $item = Item::findorfail($items_id);
        $inout = Inout::findorfail($id);

        return view('inout.print-display', compact('inout', 'item'));
    }

    public function invoice_record($id)
    {
        $sells = Sell::where('variation_id', $id)->get();
        return view('inout.invoice_record', compact('sells'));
    }

    public function transfer_record($id)
    {
        $transfer_records = TransferHistory::where('variation_id', $id)->get();
        return view('inout.transfer_record', compact('transfer_records'));
    }

    public function purchase_order_reord($id)
    {
        $items = Item::find($id);
        $invoices = PurchaseOrder::latest()->get();
        return view('inout.purchase_order_record', compact('invoices', 'items'));
    }
    public function pos_record($id)
    {
        $items = Item::find($id);
        $invoices = Invoice::where(
            'status',
            'pos'
        )->get();

        return view('inout.pos_record', compact('invoices', 'items'));
    }
}
