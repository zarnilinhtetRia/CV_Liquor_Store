<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    //
    public function index()
    {

        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if (auth()->user()->is_admin == '1') {
            $suppliers = Supplier::latest()->get();
            $branchs = Warehouse::all();
        } else {
            $suppliers = Supplier::whereIn('branch', $warehousePermission)->latest()->get();
            $branchs = Warehouse::all();
        }
        return view('supplier.supplier', compact('suppliers', 'branchs'));
    }
    public function store(Request $request)
    {
        try {
            $validate = $request->validate([
                'name' => 'required|unique:suppliers,name',
                'phno' => 'required|unique:suppliers,phno',
                'branch' => 'required',
            ]);

            Supplier::create($validate);

            return redirect()->back()->with('success', 'Supplier Added Successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    public function edit(Request $request, $id)
    {
        $supplier = Supplier::find($id);
        $branchs = Warehouse::all();

        return view(
            'supplier.supplier_edit',
            compact('supplier', 'branchs')
        );
    }
    public function update(Request $request, $id)
    {
        $supplier = Supplier::find($id);
        $supplier->update($request->all());
        return redirect('supplier')->with('success', 'Supplier Updated Successful!');
    }
    public function delete($id)
    {
        $supplier = Supplier::find($id);
        $supplier->delete();
        return redirect('supplier')->with('success', 'Supplier Delete Successful!');
    }

    public function all_supplier_credit()
    {
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if (auth()->user()->is_admin == '1') {
            $supplier = Supplier::latest()->get();
            $purchase_orders = PurchaseOrder::where('status', 'Invoice')
                ->whereNotNull('supplier_id')
                ->where('remain_balance', '!=', 0)
                ->get();
            $total_amount = $purchase_orders->sum('total');
            $balance = $purchase_orders->sum('remain_balance');
            $deposit = $purchase_orders->sum('deposit');
        } else {
            $supplier = Supplier::whereIn('branch', $warehousePermission)->latest()->get();
            $purchase_orders = PurchaseOrder::where('status', 'Invoice')
                ->whereNotNull('supplier_id')
                ->where('remain_balance', '!=', 0)
                ->whereIn('branch', $warehousePermission)
                ->get();
            $total_amount = $purchase_orders->sum('total');
            $balance = $purchase_orders->sum('remain_balance');
            $deposit = $purchase_orders->sum('deposit');
        }

        return view('supplier.supplier_all_credit', compact('purchase_orders', 'supplier', 'total_amount', 'balance', 'deposit'));
    }

    public function supplier_credit($id)
    {
        $supplier = Supplier::find($id);
        $purchase_orders = PurchaseOrder::where('supplier_id', $id)
            ->where('status', 'invoice')
            ->where('remain_balance', '!=', 0)
            ->get();
        $total_amount = $purchase_orders->sum('total');
        $balance = $purchase_orders->sum('remain_balance');
        $deposit = $purchase_orders->sum('deposit');
        return view('supplier.supplier_credit', compact('purchase_orders', 'supplier', 'total_amount', 'balance', 'deposit'));
    }

    public function supplier_po($id)
    {
        $supplier = Supplier::find($id);
        $purchase_orders = PurchaseOrder::where('supplier_id', $id)
            ->where('status', 'invoice')
            ->get();
        $total_amount = $purchase_orders->sum('total');
        $balance = $purchase_orders->sum('remain_balance');
        $deposit = $purchase_orders->sum('deposit');
        return view('supplier.supplier_po', compact('purchase_orders', 'supplier', 'total_amount', 'balance', 'deposit'));
    }
}
