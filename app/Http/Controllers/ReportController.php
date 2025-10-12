<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Sell;
use App\Models\Account;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\MakePayment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\ExpenseCategory;
use Illuminate\Support\Facades\DB;
use App\Models\InvoicePaymentMethod;
use App\Models\PurchaseOrderMakePayment;
use App\Models\PurchaseOrderPaymentMethod;

class ReportController extends Controller
{
    public function purchase_items()
    {
        $purchases = PurchaseOrder::select('supplier_id', DB::raw('SUM(total) as total_amount'), DB::raw('MAX(id) as id'))
            ->groupBy('supplier_id')->whereNotNull('supplier_id')
            ->whereMonth('po_date', Carbon::now()->month)
            ->whereYear('po_date', Carbon::now()->year)
            ->get();
        $start_date = Carbon::now()->startOfMonth()->format('Y-m-d');
        $end_date = Carbon::now()->endOfMonth()->format('Y-m-d');

        return view('report.purchase_item', compact('purchases', 'start_date', 'end_date'));
    }
    public function purchase_product_search(Request $request)
    {
        $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d');
        $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d');

        $purchases = PurchaseOrder::select('supplier_id', DB::raw('SUM(total) as total_amount'),  DB::raw('MAX(id) as id'))
            ->groupBy('supplier_id')->whereNotNull('supplier_id')
            ->whereDate('po_date', '>=', $start_date)
            ->whereDate('po_date', '<=', $end_date)
            ->get();

        return view('report.purchase_item', compact('purchases', 'start_date', 'end_date'));
    }


    public function supplier_items($id, $start_date, $end_date)
    {


        $purchases = PurchaseOrder::where('supplier_id', $id)
            ->whereDate('po_date', '>=', $start_date)
            ->whereDate('po_date', '<=', $end_date)
            ->get();

        return view('report.supplier_items', compact('purchases', 'id'));
    }


    public function payable($branch = null)
    {
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if (auth()->user()->is_admin == '1') {
            if ($branch) {
                $supplier = Supplier::latest()->get();
                $purchase_orders = PurchaseOrder::where('status', 'Invoice')
                    ->whereNotNull('supplier_id')
                    ->where('branch', $branch)
                    ->where('remain_balance', '!=', 0)
                    ->get();
            } else {
                $supplier = Supplier::latest()->get();
                $purchase_orders = PurchaseOrder::where('status', 'Invoice')
                    ->whereNotNull('supplier_id')
                    ->where('remain_balance', '!=', 0)
                    ->get();
            }
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
        $branchs = Warehouse::all();
        $branchNames = $branchs->pluck('name', 'id');

        $currentBranchName = $branch ? $branchNames[$branch] : 'All';
        return view('report.payable', compact('purchase_orders', 'supplier', 'total_amount', 'balance', 'deposit', 'branchs', 'currentBranchName'));
    }
    public function report_invoice($branch = null)
    {
        $today = Carbon::today();
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        $invoicesQuery = Invoice::whereDate('created_at', $today)
            ->where('status', 'invoice')
            ->where('balance_due', 'Invoice');

        if ($branch) {
            $invoicesQuery->where('branch', $branch);
        }

        if (auth()->user()->is_admin == '1' || auth()->user()->level == 'Admin') {
            $invoices = $invoicesQuery->get();
        } else {
            $invoicesQuery->whereIn('branch', $warehousePermission);
            $invoices = $invoicesQuery->get();
        }

        $invoiceIds = $invoices->pluck('id');

        $invoicePaymentMethod = InvoicePaymentMethod::whereIn('invoice_id', $invoiceIds)->get();

        $invoiceMakePayment = MakePayment::whereIn('invoice_id', $invoiceIds)->get();

        $total = $invoices->sum('total');
        $totalCash = $invoicePaymentMethod->where('payment_method', 'Cash')->sum('payment_amount');
        $totalKbz = $invoicePaymentMethod->where('payment_method', 'K Pay')->sum('payment_amount');
        $totalCB = $invoicePaymentMethod->where('payment_method', 'Wave')->sum('payment_amount');
        $totalOther = $invoicePaymentMethod->where('payment_method', 'Others')->sum('payment_amount');

        $totalCashMakePayment = $invoiceMakePayment->where('payment_method', 'Cash')->sum('amount');
        $totalKbzMakePayment = $invoiceMakePayment->where('payment_method', 'K Pay')->sum('amount');
        $totalCBMakePayment = $invoiceMakePayment->where('payment_method', 'Wave')->sum('amount');
        $totalOtherMakePayment = $invoiceMakePayment->where('payment_method', 'Others')->sum('amount');

        $branchs = Warehouse::all();
        $branchNames = $branchs->pluck('name', 'id');

        $currentBranchName = $branch ? $branchNames[$branch] : 'All Invoices';

        return view('report.report_invoice', compact('invoices', 'total', 'totalCash', 'totalKbz', 'totalCB', 'totalOther', 'branch', 'branchs', 'currentBranchName', 'totalCashMakePayment', 'totalKbzMakePayment', 'totalCBMakePayment', 'totalOtherMakePayment'));
    }
    public function receivable(Request $request, $branch = null)
    {

        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];


        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');


        $query = Invoice::where('status', 'invoice')
            ->whereNotNull('customer_id')
            ->where('remain_balance', '!=', 0);

        if (auth()->user()->is_admin == '1') {

            if ($branch) {
                $invoices = $query->where('branch', $branch)->get();
            } else {
                $invoices = $query->get();
            }
        }
        if (auth()->user()->is_admin != '1') {
            $invoices =  $query->whereIn('branch', $warehousePermission)->get();
        }


        // if ($startDate) {
        //     $query->whereDate('invoice_date', '>=', $startDate);
        // }
        // if ($endDate) {
        //     $query->whereDate('invoice_date', '<=', $endDate);
        // }



        // Calculate totals
        $total_amount = $invoices->sum('total');
        $balance = $invoices->sum('remain_balance');
        $deposit = $invoices->sum('deposit');
        $branchs = Warehouse::all();
        $branchNames = $branchs->pluck('name', 'id');

        $currentBranchName = $branch ? $branchNames[$branch] : 'All';
        // Fetch customers
        $customers = auth()->user()->is_admin == '1'
            ? Customer::latest()->get()
            : Customer::whereIn('branch', $warehousePermission)->latest()->get();

        return view('report.receivable', compact('invoices', 'total_amount', 'balance', 'deposit', 'customers', 'branch', 'branchs', 'currentBranchName'));
    }

    public function report_sale_return()
    {

        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if (auth()->user()->is_admin == '1') {
            $pos = PurchaseOrder::whereDate('created_at', today())
                ->where('quote_no', 'like', 'PO%')
                ->where('balance_due', 'Sale Return Invoice')
                ->get();

            $total = $pos->sum('total');
            $branchs = Warehouse::all();
        } else {
            $pos = PurchaseOrder::whereDate('created_at', today())
                ->whereIn('branch', $warehousePermission)
                ->where('quote_no', 'like', 'PO%')
                ->where('balance_due', 'Sale Return Invoice')
                ->get();

            $total = $pos->sum('total');
            $branchs = Warehouse::all();
        }

        return view('report.report_sale_return', compact('pos', 'total', 'branchs'));
    }


    public function report_quotation($branch = null)
    {
        $today = Carbon::today();
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        $quotationsQuery = Invoice::whereDate('created_at', $today)
            ->where('status', 'quotation');

        if ($branch) {
            $quotationsQuery->where('branch', $branch);
        }

        if (auth()->user()->is_admin == '1') {
            $quotations = $quotationsQuery->get();
        } else {
            $quotationsQuery->whereIn('branch', $warehousePermission);
            $quotations = $quotationsQuery->get();
        }

        $total = $quotations->sum('total');
        $branchs = Warehouse::all();
        $branchNames = $branchs->pluck('name', 'id');
        $currentBranchName = $branch ? $branchNames[$branch] : 'All Quotations';

        return view('report.report_quotation', compact('quotations', 'total', 'branchs', 'currentBranchName'));
    }

    public function report_po(Request $request)
    {
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];
        $branch = $request->input('branch');
        $currentBranchName = "All Branches";

        $query = PurchaseOrder::whereDate('created_at', today())->where('balance_due', 'PO');

        if (auth()->user()->is_admin != '1') {
            $query->whereIn('branch', $warehousePermission);
        }

        if ($branch) {
            $query->where('branch', $branch);
            $currentBranchName = Warehouse::find($branch)->name;
        }

        $pos = $query->get();

        $paymentMethods = PurchaseOrderPaymentMethod::whereIn('po_id', $pos->pluck('id'))
            ->get();

        $totalCash = $paymentMethods->where('payment_method', 'Cash')->sum('payment_amount');
        $totalKbz = $paymentMethods->where('payment_method', 'K Pay')->sum('payment_amount');
        $totalCB = $paymentMethods->where('payment_method', 'Wave')->sum('payment_amount');
        $totalOther = $paymentMethods->where('payment_method', 'Others')->sum('payment_amount');
        $search_total = $paymentMethods->sum('payment_amount');

        $branchs = Warehouse::all();

        return view('report.report_po', compact(
            'pos',
            'search_total',
            'branchs',
            'totalCash',
            'totalKbz',
            'totalCB',
            'totalOther',
            'currentBranchName'
        ));
    }



    public function report_purchase_return()
    {

        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if (auth()->user()->is_admin == '1') {
            $invoices = Invoice::whereDate('created_at', today())->where('status', 'invoice')->where('balance_due', 'PO Return')->get();
            $total = $invoices->sum('total');
            $branchs = Warehouse::all();
        } else {
            $invoices = Invoice::whereDate('created_at', today())->where('status', 'invoice')->where('balance_due', 'PO Return')->whereIn('branch', $warehousePermission)->get();
            $total = $invoices->sum('total');
            $branchs = Warehouse::all();
        }

        return view('report.report_purchase_return', compact('invoices', 'total', 'branchs'));
    }

    // public function report_item()
    // {
    //     $warehouse_name = [];
    //     $warehouses = Warehouse::all();
    //     foreach ($warehouses as $key => $ware) {
    //         $warehouse_name[$key] = $ware->name;
    //     }

    //     $query = DB::table('items')
    //         ->whereNull('items.deleted_at')
    //         ->join('warehouses', 'items.warehouse_id', '=', 'warehouses.id')
    //         ->leftJoin('item_variations', 'items.id', '=', 'item_variations.item_id')
    //         ->leftJoin('item_quantities', 'items.id', '=', 'item_quantities.item_id')
    //         ->select(
    //             'items.item_name',
    //             'items.item_type',
    //             'warehouses.id as warehouse_id',
    //             'warehouses.name as warehouse_name',
    //             DB::raw('SUM(item_quantities.warehouse_qty) AS total_quantity'),
    //             'item_variations.retail_price AS variation_retail_price',
    //             'item_variations.wholesale_price AS variation_wholesale_price'
    //         )
    //         ->groupBy(
    //             'items.item_name',
    //             'items.item_type',
    //             'warehouses.id',
    //             'item_variations.retail_price',
    //             'item_variations.wholesale_price'
    //         );

    //     $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

    //     if (auth()->user()->is_admin == '1') {
    //         $items = $query->get();
    //     } else {
    //         $items = $query->whereIn('warehouses.id', $warehousePermission)->get();
    //     }

    //     $groupedItems = [];
    //     foreach ($items as $item) {
    //         $itemId = $item->item_name;
    //         if (!isset($groupedItems[$itemId])) {
    //             $groupedItems[$itemId] = [
    //                 'item_name' => $item->item_name,
    //                 'item_type' => $item->item_type,
    //                 'total_quantity' => 0,
    //                 'warehouse_quantities' => [],
    //                 'variation_retail_price' => $item->variation_retail_price,
    //                 'variation_wholesale_price' => $item->variation_wholesale_price
    //             ];
    //         }
    //         $groupedItems[$itemId]['total_quantity'] += $item->total_quantity;
    //         $groupedItems[$itemId]['warehouse_quantities'][$item->warehouse_id] = $item->total_quantity;
    //     }

    //     // Convert grouped items to collection
    //     $items = collect($groupedItems)->values();

    //     return view('report.report_item', compact('items', 'warehouse_name', 'warehouses'));
    // }


    public function report_item(Request $request)
    {
        // Fetch all warehouses
        $warehouses = Warehouse::all();

        // Convert warehouses to an associative array
        $warehouse_name = $warehouses->pluck('name')->toArray();

        // Start Query
        $query = DB::table('item_variations')
            ->whereNull('item_variations.deleted_at')
            ->join('items', 'item_variations.item_id', '=', 'items.id')
            ->join('warehouses', 'items.warehouse_id', '=', 'warehouses.id')
            ->leftJoin('item_quantities', 'item_variations.id', '=', 'item_quantities.variation_id')
            ->select(
                'items.item_name',
                'items.item_type',
                'warehouses.id as warehouse_id',
                'warehouses.name as warehouse_name',
                'item_variations.variation_desc',
                DB::raw('COALESCE(SUM(item_quantities.warehouse_qty), 0) AS total_quantity'),
                'item_variations.buy_price',
                'item_variations.retail_price',
                'item_variations.wholesale_price'
            )
            ->groupBy(
                'items.item_name',
                'items.item_type',
                'warehouses.id',
                'warehouses.name',
                'item_variations.id',
                'item_variations.variation_desc',
                'item_variations.retail_price',
                'item_variations.wholesale_price',
                'item_variations.buy_price'
            );



        // **Apply Date Filter**
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = Carbon::parse($request->start_date)->startOfDay();
            $end_date = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('item_variations.created_at', [$start_date, $end_date]);
        }

        // **Warehouse Permission Check**
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if (auth()->user()->is_admin != '1') {
            $query->whereIn('warehouses.id', $warehousePermission);
        }

        // Execute Query
        $variations = $query->get();

        // **Group Variations Properly**
        $groupedVariations = [];

        foreach ($variations as $variation) {
            $variationId = $variation->variation_desc;

            if (!isset($groupedVariations[$variationId])) {
                $groupedVariations[$variationId] = [
                    'item_name' => $variation->item_name,
                    'item_type' => $variation->item_type,
                    'variation_desc' => $variation->variation_desc,
                    'total_quantity' => 0,
                    'warehouse_quantities' => [],
                    'buy_price' => $variation->buy_price,
                    'retail_price' => $variation->retail_price,
                    'wholesale_price' => $variation->wholesale_price
                ];
            }

            // Initialize warehouse quantities to 0 for all warehouses
            foreach ($warehouses as $warehouse) {
                if (!isset($groupedVariations[$variationId]['warehouse_quantities'][$warehouse->id])) {
                    $groupedVariations[$variationId]['warehouse_quantities'][$warehouse->id] = 0;
                }
            }

            // Add quantities per warehouse
            $groupedVariations[$variationId]['total_quantity'] += $variation->total_quantity;
            $groupedVariations[$variationId]['warehouse_quantities'][$variation->warehouse_id] = $variation->total_quantity;
        }

        // Convert grouped variations to collection
        $variations = collect($groupedVariations)->values();

        return view('report.report_item', compact('variations', 'warehouse_name', 'warehouses'));
    }



    // public function monthly_item_search(Request $request)
    // {
    //     $warehouse_name = [];
    //     $warehouses = Warehouse::all();
    //     foreach ($warehouses as $key => $ware) {
    //         $warehouse_name[$key] = $ware->name;
    //     }

    //     $query = DB::table('items')
    //         ->whereNull('items.deleted_at')
    //         ->join('warehouses', 'items.warehouse_id', '=', 'warehouses.id')
    //         ->leftJoin('item_variations', 'items.id', '=', 'item_variations.item_id')
    //         ->leftJoin('item_quantities', 'item_variations.id', '=', 'item_quantities.variation_id')
    //         ->select(
    //             'items.item_name',
    //             'items.item_type',
    //             'warehouses.id as warehouse_id',
    //             'warehouses.name as warehouse_name',
    //             DB::raw('SUM(item_quantities.warehouse_qty) AS total_quantity'),
    //             'item_variations.retail_price AS variation_retail_price',
    //             'item_variations.wholesale_price AS variation_wholesale_price'
    //         )
    //         ->groupBy(
    //             'items.item_name',
    //             'items.item_type',
    //             'warehouses.id',
    //             'warehouses.name',
    //             'item_variations.retail_price',
    //             'item_variations.wholesale_price'
    //         );

    //     if ($request->has('start_date') && $request->has('end_date')) {
    //         $start_date = Carbon::parse($request->start_date)->startOfDay();
    //         $end_date = Carbon::parse($request->end_date)->endOfDay();

    //         $query->whereBetween('item_variations.created_at', [$start_date, $end_date]);
    //     }

    //     $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

    //     if (auth()->user()->is_admin == '1') {
    //         $items = $query->get();
    //     } else {
    //         $items = $query->whereIn('warehouses.id', $warehousePermission)->get();
    //     }

    //     $groupedItems = [];
    //     foreach ($items as $item) {
    //         $itemId = $item->item_name;
    //         if (!isset($groupedItems[$itemId])) {
    //             $groupedItems[$itemId] = [
    //                 'item_name' => $item->item_name,
    //                 'item_type' => $item->item_type,
    //                 'total_quantity' => 0,
    //                 'warehouse_quantities' => [],
    //                 'variation_retail_price' => $item->variation_retail_price,
    //                 'variation_wholesale_price' => $item->variation_wholesale_price,
    //             ];
    //         }
    //         $groupedItems[$itemId]['total_quantity'] += $item->total_quantity;
    //         $groupedItems[$itemId]['warehouse_quantities'][$item->warehouse_id] = $item->total_quantity;
    //     }

    //     $items = collect($groupedItems)->values();

    //     return view('report.report_item', compact('items', 'warehouse_name', 'warehouses'));
    // }
    public function monthly_item_search(Request $request)
    {
        $warehouse_name = [];
        $warehouses = Warehouse::all();
        foreach ($warehouses as $key => $ware) {
            $warehouse_name[$key] = $ware->name;
        }

        $query = DB::table('item_variations')
            ->whereNull('item_variations.deleted_at')
            ->join('items', 'item_variations.item_id', '=', 'items.id')
            ->join('warehouses', 'items.warehouse_id', '=', 'warehouses.id')
            ->leftJoin('item_quantities', 'item_variations.id', '=', 'item_quantities.variation_id')
            ->select(
                'item_variations.id as variation_id',
                'item_variations.variation_desc',
                'items.item_name',
                'items.item_type',
                'warehouses.id as warehouse_id',
                'warehouses.name as warehouse_name',
                DB::raw('SUM(item_quantities.warehouse_qty) AS total_quantity'),
                'item_variations.retail_price',
                'item_variations.wholesale_price',
                'item_variations.created_at' // Include this for date filtering
            )
            ->groupBy(
                'item_variations.id',
                'item_variations.variation_desc',
                'items.item_name',
                'items.item_type',
                'warehouses.id',
                'warehouses.name',
                'item_variations.retail_price',
                'item_variations.wholesale_price',
                'item_variations.created_at'
            );

        // **Item Search Filter**
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('items.item_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('item_variations.variation_desc', 'LIKE', "%{$searchTerm}%");
            });
        }

        // **Date Filter**
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = Carbon::parse($request->start_date)->startOfDay();
            $end_date = Carbon::parse($request->end_date)->endOfDay();

            $query->whereBetween('item_variations.created_at', [$start_date, $end_date]);
        }

        // **Warehouse Permission Check**
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if (auth()->user()->is_admin == '1') {
            $variations = $query->get();
        } else {
            $variations = $query->whereIn('warehouses.id', $warehousePermission)->get();
        }

        // **Grouping Variations**
        $groupedVariations = [];
        foreach ($variations as $variation) {
            $variationId = $variation->variation_id;
            if (!isset($groupedVariations[$variationId])) {
                $groupedVariations[$variationId] = [
                    'variation_desc' => $variation->variation_desc,
                    'item_name' => $variation->item_name,
                    'item_type' => $variation->item_type,
                    'total_quantity' => 0,
                    'warehouse_quantities' => [],
                    'retail_price' => $variation->retail_price,
                    'wholesale_price' => $variation->wholesale_price,
                ];
            }
            $groupedVariations[$variationId]['total_quantity'] += $variation->total_quantity;
            $groupedVariations[$variationId]['warehouse_quantities'][$variation->warehouse_id] = $variation->total_quantity;
        }

        $variations = collect($groupedVariations)->values();

        return view('report.report_item', compact('variations', 'warehouse_name', 'warehouses'));
    }






    public function monthly_invoice_search(Request $request)
    {
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d');
        $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d');

        $invoicesQuery = Invoice::where('status', 'invoice')
            ->where('balance_due', 'Invoice')
            ->whereDate('invoice_date', '>=', $start_date)
            ->whereDate('invoice_date', '<=', $end_date);

        if (auth()->user()->is_admin == '1' || auth()->user()->level == 'Admin') {
            if ($request->has('branch') && !empty($request->input('branch'))) {
                $invoicesQuery->where('branch', $request->input('branch'));
            }
        } else {
            $invoicesQuery->whereIn('branch', $warehousePermission);
        }

        $search_invoices = $invoicesQuery->get();

        $invoiceIds = $search_invoices->pluck('id');
        $invoicePaymentMethods = InvoicePaymentMethod::whereIn('invoice_id', $invoiceIds)->get();

        $invoiceMakePayment = MakePayment::whereIn('invoice_id', $invoiceIds)->get();
        $totalCash = $invoicePaymentMethods->where('payment_method', 'Cash')->sum('payment_amount');
        $totalKbz = $invoicePaymentMethods->where('payment_method', 'K Pay')->sum('payment_amount');
        $totalCB = $invoicePaymentMethods->where('payment_method', 'Wave')->sum('payment_amount');
        $totalOther = $invoicePaymentMethods->where('payment_method', 'Others')->sum('payment_amount');

        $search_total = $search_invoices->sum('total');


        $totalCashMakePayment = $invoiceMakePayment->where('payment_method', 'Cash')->sum('amount');
        $totalKbzMakePayment = $invoiceMakePayment->where('payment_method', 'K Pay')->sum('amount');
        $totalCBMakePayment = $invoiceMakePayment->where('payment_method', 'Wave')->sum('amount');
        $totalOtherMakePayment = $invoiceMakePayment->where('payment_method', 'Others')->sum('amount');

        $branchs = Warehouse::all();
        $branchNames = $branchs->pluck('name', 'id');

        $branch = $request->input('branch');
        $currentBranchName = $branch ? $branchNames->get($branch, 'Unknown Branch') : 'All Invoices';

        return view('report.report_invoice', compact(
            'search_invoices',
            'search_total',
            'branchs',
            'totalCash',
            'totalKbz',
            'totalCB',
            'totalOther',
            'currentBranchName',
            'totalCashMakePayment',
            'totalKbzMakePayment',
            'totalCBMakePayment',
            'totalOtherMakePayment'
        ));
    }




    // public function monthly_sale_return(Request $request)
    // {

    //     $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

    //     if (auth()->user()->is_admin == '1') {
    //         $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d');
    //         $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d');
    //         $search_invoices = Invoice::where('status', 'invoice')
    //             ->where('balance_due', 'Po Return')
    //             ->whereDate('invoice_date', '>=', $start_date)
    //             ->whereDate('invoice_date', '<=', $end_date)
    //             ->get();
    //         $search_total = $search_invoices->sum('total');
    //         $branchs = Warehouse::all();
    //     } else {
    //         $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d');
    //         $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d');
    //         $search_invoices = Invoice::where('status', 'invoice')
    //             ->whereIn('branch', $warehousePermission)
    //             ->where('balance_due', 'Po Return')
    //             ->whereDate('invoice_date', '>=', $start_date)
    //             ->whereDate('invoice_date', '<=', $end_date)
    //             ->get();
    //         $search_total = $search_invoices->sum('total');
    //         $branchs = Warehouse::all();
    //     }
    //     return view('report.report_sale_return', compact('search_invoices', 'search_total', 'branchs'));
    // }
    public function monthly_sale_return(Request $request)
    {

        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if (auth()->user()->is_admin == '1') {
            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d');
            $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d');
            $search_pos = PurchaseOrder::whereDate('po_date', '>=', $start_date)
                ->whereDate('po_date', '<=', $end_date)
                ->where('quote_no', 'like', 'PO%')
                ->where('balance_due', 'Sale Return Invoice')
                ->get();
            $search_total = $search_pos->sum('total');
            $branchs = Warehouse::all();
        } else {
            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d');
            $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d');
            $search_pos = PurchaseOrder::whereDate('po_date', '>=', $start_date)
                ->whereDate('po_date', '<=', $end_date)
                ->whereIn('branch', $warehousePermission)
                ->where('quote_no', 'like', 'PO%')
                ->where('balance_due', 'Sale Return Invoice')
                ->get();
            $search_total = $search_pos->sum('total');
            $branchs = Warehouse::all();
        }

        return view('report.report_sale_return', compact('search_pos', 'search_total', 'branchs'));
    }

    public function monthly_quotation_search(Request $request)
    {
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d');
        $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d');
        $branch = $request->input('branch');

        $quotationsQuery = Invoice::where('status', 'quotation')
            ->whereDate('quote_date', '>=', $start_date)
            ->whereDate('quote_date', '<=', $end_date);

        if ($branch) {
            $quotationsQuery->where('branch', $branch);
        }

        if (auth()->user()->is_admin != '1') {
            $quotationsQuery->whereIn('branch', $warehousePermission);
        }

        $search_quotations = $quotationsQuery->get();
        $search_total = $search_quotations->sum('total');
        $branchs = Warehouse::all();
        $branchNames = $branchs->pluck('name', 'id');
        $currentBranchName = $branch ? $branchNames[$branch] : 'All Quotations';

        return view('report.report_quotation', compact('search_quotations', 'search_total', 'branchs', 'currentBranchName'));
    }
    public function monthly_po_search(Request $request)
    {
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        $branchs = Warehouse::all();
        $branch = $request->input('branch');
        $branchNames = $branchs->pluck('name', 'id');
        $currentBranchName = $branch ? $branchNames->get($branch, 'Unknown Branch') : 'All Quotations';

        $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d');
        $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d');

        $query = PurchaseOrder::whereDate('po_date', '>=', $start_date)
            ->whereDate('po_date', '<=', $end_date)
            ->where('balance_due', 'PO');

        if (auth()->user()->is_admin == '1') {
            if ($branch) {
                $query->where('branch', $branch);
            }
        } else {
            $query->whereIn('branch', $warehousePermission);

            if ($branch) {
                $query->where('branch', $branch);
            }
        }

        $pos = $query->get();

        $paymentMethods = PurchaseOrderPaymentMethod::whereIn('po_id', $pos->pluck('id'))->get();

        $totalCash = $paymentMethods->where('payment_method', 'Cash')->sum('payment_amount');
        $totalKbz = $paymentMethods->where('payment_method', 'K Pay')->sum('payment_amount');
        $totalCB = $paymentMethods->where('payment_method', 'Wave')->sum('payment_amount');
        $totalOther = $paymentMethods->where('payment_method', 'Others')->sum('payment_amount');
        $search_total = $paymentMethods->sum('payment_amount');

        return view('report.report_po', compact(
            'pos',
            'search_total',
            'branchs',
            'totalCash',
            'totalKbz',
            'totalCB',
            'totalOther',
            'currentBranchName'
        ));
    }
    public function monthly_purchase_return(Request $request)
    {
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if (auth()->user()->is_admin == '1') {
            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d');
            $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d');
            $search_invoices = Invoice::where('status', 'invoice')
                ->where('balance_due', 'Po Return')
                ->whereDate('invoice_date', '>=', $start_date)
                ->whereDate('invoice_date', '<=', $end_date)
                ->get();
            $search_total = $search_invoices->sum('total');
            $branchs = Warehouse::all();
        } else {
            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d');
            $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d');
            $search_invoices = Invoice::where('status', 'invoice')
                ->whereIn('branch', $warehousePermission)
                ->where('balance_due', 'Po Return')
                ->whereDate('invoice_date', '>=', $start_date)
                ->whereDate('invoice_date', '<=', $end_date)
                ->get();
            $search_total = $search_invoices->sum('total');
            $branchs = Warehouse::all();
        }
        return view('report.report_purchase_return', compact('search_invoices', 'search_total', 'branchs'));
    }
    public function report_pos(Request $request)
    {
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];
        $branch = $request->input('branch');

        $today = Carbon::today();
        $posQuery = Invoice::whereDate('created_at', $today)
            ->where('status', 'pos');

        if ($branch) {
            $posQuery->where('branch', $branch);
        }

        if (auth()->user()->is_admin != '1') {
            $posQuery->whereIn('branch', $warehousePermission);
        }

        $pos_data = $posQuery->get();

        $invoiceIds = $pos_data->pluck('id');
        $invoicePaymentMethods = InvoicePaymentMethod::whereIn('invoice_id', $invoiceIds)
            ->get();

        $sale_totals = DB::table('invoices')
            ->select('sale_by', DB::raw('count(*) as total_invoices'), DB::raw('sum(total) as sale_total'))
            ->whereDate('created_at', $today)
            ->where('status', 'pos')
            ->whereNull('deleted_at')
            ->when($branch, function ($query, $branch) {
                return $query->where('branch', $branch);
            })
            ->when(auth()->user()->is_admin != '1', function ($query) use ($warehousePermission) {
                return $query->whereIn('branch', $warehousePermission);
            })
            ->groupBy('sale_by')
            ->get();

        $total = $pos_data->sum('total');
        $branchs = Warehouse::all();
        $branchNames = $branchs->pluck('name', 'id');
        $currentBranchName = $branch ? $branchNames[$branch] : 'All POS';

        $totalCash = $invoicePaymentMethods->where('payment_method', 'Cash')->sum('payment_amount');
        $totalKbz = $invoicePaymentMethods->where('payment_method', 'K Pay')->sum('payment_amount');
        $totalCB = $invoicePaymentMethods->where('payment_method', 'Wave')->sum('payment_amount');
        $totalOther = $invoicePaymentMethods->where('payment_method', 'Others')->sum('payment_amount');

        return view('report.report_pos', compact('pos_data', 'total', 'sale_totals', 'branchs', 'totalCash', 'totalKbz', 'totalCB', 'totalOther', 'currentBranchName'));
    }

    public function monthly_pos_search(Request $request)
    {
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d');
        $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d');
        $branch = $request->input('branch');

        $query = Invoice::where('status', 'POS')
            ->whereDate('invoice_date', '>=', $start_date)
            ->whereDate('invoice_date', '<=', $end_date);

        if (auth()->user()->is_admin != '1') {
            $query->whereIn('branch', $warehousePermission);
        }

        if ($branch) {
            $query->where('branch', $branch);
            $currentBranchName = Warehouse::find($branch)->name;
        } else {
            $currentBranchName = "All POS";
        }

        $search_pos = $query->get();
        $search_total = $search_pos->sum('total');
        $invoiceIds = $search_pos->pluck('id');
        $invoicePaymentMethods = InvoicePaymentMethod::whereIn('invoice_id', $invoiceIds)->get();

        $sale_totals = DB::table('invoices')
            ->select('sale_by', DB::raw('count(*) as total_invoices'), DB::raw('sum(total) as sale_total'))
            ->whereDate('invoice_date', '>=', $start_date)
            ->whereDate('invoice_date', '<=', $end_date)
            ->where('status', 'POS');

        if (auth()->user()->is_admin != '1') {
            $sale_totals->whereIn('branch', $warehousePermission);
        }

        if ($branch) {
            $sale_totals->where('branch', $branch);
        }

        $sale_totals = $sale_totals->groupBy('sale_by')->get();

        $branchs = Warehouse::all();


        $totalCash = $invoicePaymentMethods->where('payment_method', 'Cash')->sum('payment_amount');
        $totalKbz = $invoicePaymentMethods->where('payment_method', 'K Pay')->sum('payment_amount');
        $totalCB = $invoicePaymentMethods->where('payment_method', 'Wave')->sum('payment_amount');
        $totalOther = $invoicePaymentMethods->where('payment_method', 'Others')->sum('payment_amount');

        return view('report.report_pos', compact('search_pos', 'search_total', 'sale_totals', 'branchs', 'totalCash', 'totalKbz', 'totalCB', 'totalOther', 'currentBranchName'));
    }
    public function reportExpense()
    {
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if (auth()->user()->is_admin == '1') {
            $branchs = Warehouse::all();
            $expenses = Expense::whereDate('created_at', today())
                ->get();
            $total = $expenses->sum('amount');
            $categorys = ExpenseCategory::all();
        } else {
            $branchs = Warehouse::all();
            $expenses = Expense::whereDate('created_at', today())
                ->whereIn('branch', $warehousePermission)
                ->get();
            $total = $expenses->sum('amount');
            $categorys = ExpenseCategory::all();
        }

        return view('report.report_expense', compact('expenses', 'total', 'branchs', 'categorys'));
    }
    public function expenseSearch(Request $request)
    {

        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if (auth()->user()->is_admin == '1') {
            $branchs = Warehouse::all();
            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d');
            $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d');
            $search_expenses = Expense::whereDate('date', '>=', $start_date)
                ->whereDate('date', '<=', $end_date)->get();

            $search_total = $search_expenses->sum('amount');
            $categorys = ExpenseCategory::all();
        } else {
            $branchs = Warehouse::all();
            $start_date = Carbon::parse($request->input('start_date'))->format('Y-m-d');
            $end_date = Carbon::parse($request->input('end_date'))->format('Y-m-d');
            $search_expenses = Expense::whereDate('date', '>=', $start_date)
                ->whereDate('date', '<=', $end_date)->whereIn('branch', $warehousePermission)->get();

            $search_total = $search_expenses->sum('amount');
            $categorys = ExpenseCategory::all();
        }

        return view('report.report_expense', compact('search_expenses', 'search_total', 'branchs', 'categorys'));
    }

    public function general_ledger(Request $request)
    {
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        $branch = $request->branch ?? null;
        $start_date = $request->start_date ?? null;
        $end_date = $request->end_date ?? null;

        // dd($branch);
        if ($branch) {
            $accounts = Account::with(['payment', 'transaction'])
                ->where('location', $branch)
                ->latest()
                ->get();
            $transaction = Transaction::with('account')->where('location', $branch)->latest()->get();
        } else {
            if (auth()->user()->is_admin == '1') {
                $accounts = Account::with(['payment', 'transaction'])
                    ->latest()
                    ->get();
                $transaction = Transaction::with('account')->latest()->get();
            } else {
                $accounts = Account::whereIn('location', $warehousePermission)->with(['payment', 'transaction'])
                    ->latest()
                    ->get();
                $transaction = Transaction::whereIn('location', $warehousePermission)->with('account')->latest()->get();
            }
        }

        $accountDepositSums = [];

        if ($start_date != null && $end_date != null) {
            foreach ($accounts as $account) {

                foreach ($account->transaction as $tran) {
                    $current_deposit = 0;
                    $current_expense = 0;
                    //for current month
                    $current_deposit += Payment::where('payment_status', 'IN')->where('transaction_id', $tran->id)
                        ->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<=', $end_date)
                        ->sum('amount');
                    $current_expense = Payment::where('payment_status', 'OUT')
                        ->where('transaction_id', $tran->id)
                        ->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<=', $end_date)
                        ->sum('amount');

                    $current_deposit += InvoicePaymentMethod::whereNotNull('invoice_id')
                        ->whereNull('quote_no')
                        ->where('status', 'receivable')
                        ->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<=', $end_date)->sum('payment_amount');

                    $current_expense += InvoicePaymentMethod::whereNotNull('invoice_id')
                        ->where('quote_no', 'Customer Return')
                        ->where('status', 'receivable')
                        ->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<=', $end_date)->sum('payment_amount');

                    $current_deposit += InvoicePaymentMethod::whereNotNull('invoice_id')
                        ->whereNull('quote_no')
                        ->where('status', 'saleaccount')
                        ->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<=', $end_date)->sum('payment_amount');

                    $current_expense += InvoicePaymentMethod::whereNotNull('invoice_id')
                        ->where('quote_no', 'Customer Return')
                        ->where('status', 'saleaccount')
                        ->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<=', $end_date)->sum('payment_amount');


                    $current_expense += PurchaseOrderPaymentMethod::where('status', null)->where('payment_method', $tran->id)->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<=', $end_date)->sum('payment_amount');
                    $current_expense += PurchaseOrderPaymentMethod::where('status', 'payable')->where('payment_method', $tran->id)->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<=', $end_date)->sum('payment_amount');

                    $current_expense += PurchaseOrderPaymentMethod::where('status', 'buyaccount')->where('payment_method', $tran->id)->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<=', $end_date)->sum('payment_amount');
                    // dd($invoices);
                    $current_deposit += MakePayment::whereNotNull('invoice_id')
                        ->where('invoice_no', '!=', 'Customer Return')
                        ->where('payment_method', $tran->id)->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<=', $end_date)->sum('amount');

                    $current_expense += MakePayment::whereNotNull('invoice_id')
                        ->where('invoice_no', '==', 'Customer Return')
                        ->where('payment_method', $tran->id)->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<=', $end_date)->sum('amount');

                    $current_expense += Expense::groupBy('transaction_id')->where('transaction_id', $tran->id)->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<=', $end_date)->sum('amount_mmk');
                    //end for current month

                    $accountDepositSums[$tran->id] = [
                        'depositcurrent' => $current_deposit,
                        'expensecurrent' => $current_expense,
                    ];
                }
            }
        } else {
            $currentMonth = now()->month;
            $currentYear = now()->year;

            foreach ($accounts as $account) {

                foreach ($account->transaction as $tran) {
                    $current_deposit = 0;
                    $current_expense = 0;
                    //for current month
                    $current_deposit += Payment::where('payment_status', 'IN')->where('transaction_id', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)
                        ->sum('amount');
                    $current_expense = Payment::where('payment_status', 'OUT')
                        ->where('transaction_id', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)
                        ->sum('amount');

                    $current_deposit += InvoicePaymentMethod::whereNotNull('invoice_id')
                        ->whereNull('quote_no')
                        ->where('status', 'receivable')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->where('payment_method', $tran->id)->sum('payment_amount');

                    $current_expense += InvoicePaymentMethod::whereNotNull('invoice_id')
                        ->where('quote_no', 'Customer Return')
                        ->where('status', 'receivable')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->where('payment_method', $tran->id)->sum('payment_amount');

                    $current_deposit += InvoicePaymentMethod::whereNotNull('invoice_id')
                        ->whereNull('quote_no')
                        ->where('status', 'saleaccount')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->where('payment_method', $tran->id)->sum('payment_amount');

                    $current_expense += InvoicePaymentMethod::whereNotNull('invoice_id')
                        ->where('quote_no', 'Customer Return')
                        ->where('status', 'saleaccount')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->where('payment_method', $tran->id)->sum('payment_amount');

                    $current_expense += PurchaseOrderPaymentMethod::where('status', null)->where('payment_method', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('payment_amount');
                    $current_expense += PurchaseOrderPaymentMethod::where('status', 'payable')->where('payment_method', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('payment_amount');

                    $current_expense += PurchaseOrderPaymentMethod::where('status', 'buyaccount')->where('payment_method', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('payment_amount');
                    // dd($invoices);

                    $current_deposit += MakePayment::whereNotNull('invoice_id')
                        ->where('invoice_no', '!=', 'Customer Return')
                        ->where('payment_method', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('amount');

                    $current_expense += MakePayment::whereNotNull('invoice_id')
                        ->where('invoice_no', '==', 'Customer Return')
                        ->where('payment_method', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('amount');

                    $current_expense += Expense::groupBy('transaction_id')->where('transaction_id', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('amount_mmk');
                    //end for current month

                    $accountDepositSums[$tran->id] = [
                        'depositcurrent' => $current_deposit,
                        'expensecurrent' => $current_expense,
                    ];
                }
            }
        }

        $branches = Warehouse::all();
        $branchNames = $branches->pluck('name', 'id');

        $currentBranchName = $branch ? $branchNames[$branch] : 'All';

        return view('report.general_ledger', compact('accounts', 'transaction', 'accountDepositSums', 'branchNames', 'currentBranchName', 'branches'));
    }


    // public function general_ledger_search(Request $request)
    // {
    //     $startDate = $request->input('start_date');
    //     $endDate = $request->input('end_date');
    //     $accountId = $request->input('account_id');

    //     $accountsQuery = Account::with(['payment', 'transaction'])->latest();
    //     if ($accountId) {
    //         $accountsQuery->where('id', $accountId);
    //     }
    //     $accounts = $accountsQuery->get();

    //     $accountDepositSums = [];

    //     foreach ($accounts as $account) {
    //         $depositInvoiceSum = 0;
    //         $depositPurchaseOrderSum = 0;
    //         $depositSaleReturnSum = 0;
    //         $depositPurchaseReturnSum = 0;
    //         $depositPaymentInSum = 0;
    //         $depositPaymentOutSum = 0;

    //         foreach ($account->transaction as $tran) {
    //             $depositPaymentInSum += Payment::where('transaction_id', $tran->id)
    //                 ->whereDate('created_at', '>=', $startDate)
    //                 ->whereDate('created_at', '<=', $endDate)
    //                 ->where('payment_status', 'IN')
    //                 ->where('id', '>', 6)
    //                 ->sum('amount');

    //             $depositPaymentOutSum += Payment::where('transaction_id', $tran->id)
    //                 ->whereDate('created_at', '>=', $startDate)
    //                 ->whereDate('created_at', '<=', $endDate)
    //                 ->where('payment_status', 'OUT')
    //                 ->where('id', '>', 6)
    //                 ->sum('amount');

    //             $depositInvoiceSum += Invoice::where('transaction_id', $tran->id)
    //                 ->whereDate('created_at', '>=', $startDate)
    //                 ->whereDate('created_at', '<=', $endDate)
    //                 ->where(function ($query) {
    //                     $query->where('status', 'invoice')
    //                         ->where('balance_due', 'invoice')
    //                         ->orWhere('status', 'pos');
    //                 })
    //                 ->sum('deposit');

    //             $depositPurchaseOrderSum += PurchaseOrder::where('transaction_id', $tran->id)
    //                 ->whereDate('created_at', '>=', $startDate)
    //                 ->whereDate('created_at', '<=', $endDate)
    //                 ->where(function ($query) {
    //                     $query->where('balance_due', 'PO')
    //                         ->orWhere('balance_due', 'Po Return');
    //                 })
    //                 ->sum('deposit');

    //             $depositPurchaseReturnSum += Invoice::where('transaction_id', $tran->id)
    //                 ->whereDate('created_at', '>=', $startDate)
    //                 ->whereDate('created_at', '<=', $endDate)
    //                 ->where(function ($query) {
    //                     $query->where('status', 'invoice')
    //                         ->where('balance_due', 'Po Return');
    //                 })
    //                 ->sum('deposit');

    //             $depositSaleReturnSum += PurchaseOrder::where('transaction_id', $tran->id)
    //                 ->whereDate('created_at', '>=', $startDate)
    //                 ->whereDate('created_at', '<=', $endDate)
    //                 ->where(function ($query) {
    //                     $query->where('balance_due', 'Sale Return Invoice')
    //                         ->orWhere('balance_due', 'Sale Return');
    //                 })
    //                 ->sum('deposit');
    //         }

    //         $accountDepositSums[$account->id] = [
    //             'depositInvoiceSum' => $depositInvoiceSum,
    //             'depositPurchaseOrderSum' => $depositPurchaseOrderSum,
    //             'depositSaleReturnSum' => $depositSaleReturnSum,
    //             'depositPurchaseReturnSum' => $depositPurchaseReturnSum,
    //             'depositPaymentInSum' => $depositPaymentInSum,
    //             'depositPaymentOutSum' => $depositPaymentOutSum,

    //         ];
    //     }

    //     return view('report.general_ledger', compact(
    //         'accounts',
    //         'accountDepositSums'
    //     ));
    // }

    public function balance_sheet($branch = null)
    {
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if ($branch) {
            $accounts = Account::with(['payment', 'transaction'])
                ->where('location', $branch)
                ->latest()
                ->get();
            $transaction = Transaction::with('account')->where('location', $branch)->latest()->get();
        } else {
            if (auth()->user()->is_admin == '1') {
                $accounts = Account::with(['payment', 'transaction'])
                    ->latest()
                    ->get();
                $transaction = Transaction::with('account')->latest()->get();
            } else {
                $accounts = Account::whereIn('location', $warehousePermission)->with(['payment', 'transaction'])
                    ->latest()
                    ->get();
                $transaction = Transaction::whereIn('location', $warehousePermission)->with('account')->latest()->get();
            }
        }
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $previousMonthDate = now()->subMonth();
        $previousMonth = $previousMonthDate->month;
        $previousYear = $previousMonthDate->year;

        $accountDepositSums = [];

        foreach ($accounts as $account) {

            foreach ($account->transaction as $tran) {
                $current_deposit = 0;
                $current_expense = 0;
                $previous_deposit = 0;
                $previous_expense = 0;
                //for current month
                $current_deposit += Payment::where('payment_status', 'IN')->where('transaction_id', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)
                    ->sum('amount');
                $current_expense = Payment::where('payment_status', 'OUT')
                    ->where('transaction_id', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)
                    ->sum('amount');
                $current_deposit += InvoicePaymentMethod::whereNotNull('invoice_id')->where('status', 'receivable')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->where('payment_method', $tran->id)->sum('payment_amount');

                $current_expense += PurchaseOrderMakePayment::where('payment_method', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('amount');
                $current_expense += PurchaseOrderPaymentMethod::where('status', 'payable')->where('payment_method', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('payment_amount');
                // dd($invoices);
                $current_deposit += MakePayment::whereNotNull('invoice_id')->where('payment_method', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('amount');
                $current_expense += Expense::groupBy('transaction_id')->where('transaction_id', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('amount_mmk');
                //end for current month

                //for previous month

                $previous_deposit += Payment::where('payment_status', 'IN')->where('transaction_id', $tran->id)->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)
                    ->sum('amount');
                $previous_expense = Payment::where('payment_status', 'OUT')
                    ->where('transaction_id', $tran->id)->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)
                    ->sum('amount');
                $previous_deposit += InvoicePaymentMethod::whereNotNull('invoice_id')->where('status', 'receivable')->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)->where('payment_method', $tran->id)->sum('payment_amount');

                $previous_expense += PurchaseOrderMakePayment::where('payment_method', $tran->id)->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)->sum('amount');
                $previous_expense += PurchaseOrderPaymentMethod::where('status', 'payable')->where('payment_method', $tran->id)->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)->sum('payment_amount');
                // dd($invoices);
                $previous_deposit += MakePayment::whereNotNull('invoice_id')->where('payment_method', $tran->id)->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)->sum('amount');
                $previous_expense += Expense::groupBy('transaction_id')->where('transaction_id', $tran->id)->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)->sum('amount_mmk');
                //end for previous month
                $accountDepositSums[$tran->id] = [
                    'depositcurrent' => $current_deposit,
                    'expensecurrent' => $current_expense,
                    'depositprevious' => $previous_deposit,
                    'expenseprevious' => $previous_expense

                ];
            }
        }

        $branches = Warehouse::all();
        $branchNames = $branches->pluck('name', 'id');

        $currentBranchName = $branch ? $branchNames[$branch] : 'All';

        return view('report.balance_sheet', compact('accounts', 'transaction', 'accountDepositSums', 'branchNames', 'currentBranchName', 'branches'));
    }

    // public function profit_loss($branch = null)
    // {
    //     $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

    //     if ($branch) {
    //         $accounts = Account::with(['payment', 'transaction'])
    //             ->where('location', $branch)
    //             ->where('account_bl_pl', 'PL') // only pl account
    //             ->latest()
    //             ->get();

    //         $transaction = Transaction::with('account')
    //             ->whereHas('account', function ($query) {
    //                 $query->where('account_bl_pl', 'PL');
    //             })->where('location', $branch)->latest()->get();
    //     } else {
    //         if (auth()->user()->is_admin == '1') {

    //             $accounts = Account::with(['payment', 'transaction'])
    //                 ->where('account_bl_pl', 'PL') // only pl account
    //                 ->latest()
    //                 ->get();

    //             $transaction = Transaction::with('account')
    //                 ->whereHas('account', function ($query) {
    //                     $query->where('account_bl_pl', 'PL');
    //                 })
    //                 ->latest()->get();
    //         } else {
    //             $accounts = Account::whereIn('location', $warehousePermission)->with(['payment', 'transaction'])
    //                 ->where('account_bl_pl', 'PL') // only pl account
    //                 ->latest()
    //                 ->get();

    //             $transaction = Transaction::whereIn('location', $warehousePermission)->with('account')
    //                 ->whereHas('account', function ($query) {
    //                     $query->where('account_bl_pl', 'PL');
    //                 })
    //                 ->latest()->get();
    //         }
    //     }

    //     $currentMonth = now()->month;
    //     $currentYear = now()->year;

    //     $previousMonthDate = now()->subMonth();
    //     $previousMonth = $previousMonthDate->month;
    //     $previousYear = $previousMonthDate->year;

    //     $accountDepositSums = [];

    //     foreach ($accounts as $account) {


    //         foreach ($account->transaction as $tran) {
    //             //for current month
    //             $current_deposit = 0;
    //             $current_expense = 0;
    //             $previous_deposit = 0;
    //             $previous_expense = 0;
    //             $current_deposit += Payment::where('payment_status', 'IN')->where('transaction_id', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)
    //                 ->sum('amount');
    //             $current_expense = Payment::where('payment_status', 'OUT')
    //                 ->where('transaction_id', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)
    //                 ->sum('amount');
    //             $current_deposit += InvoicePaymentMethod::whereNotNull('invoice_id')->where('status', 'receivable')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->where('payment_method', $tran->id)->sum('payment_amount');

    //             $current_deposit += InvoicePaymentMethod::whereNotNull('invoice_id')->where('status', 'saleaccount')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->where('payment_method', $tran->id)->sum('payment_amount');

    //             $current_expense += PurchaseOrderMakePayment::where('payment_method', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('amount');
    //             $current_expense += PurchaseOrderPaymentMethod::where('status', 'payable')->where('payment_method', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('payment_amount');
    //             // dd($invoices);
    //             $current_deposit += MakePayment::whereNotNull('invoice_id')->where('payment_method', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('amount');
    //             $current_expense += Expense::groupBy('transaction_id')->where('transaction_id', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('amount_mmk');
    //             //end for current month

    //             //for previous month

    //             $previous_deposit += Payment::where('payment_status', 'IN')->where('transaction_id', $tran->id)->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)
    //                 ->sum('amount');
    //             $previous_expense = Payment::where('payment_status', 'OUT')
    //                 ->where('transaction_id', $tran->id)->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)
    //                 ->sum('amount');
    //             $previous_deposit += InvoicePaymentMethod::whereNotNull('invoice_id')->where('status', 'receivable')->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)->where('payment_method', $tran->id)->sum('payment_amount');
    //             $previous_deposit += InvoicePaymentMethod::whereNotNull('invoice_id')->where('status', 'saleaccount')->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)->where('payment_method', $tran->id)->sum('payment_amount');
    //             $previous_expense += PurchaseOrderMakePayment::where('payment_method', $tran->id)->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)->sum('amount');
    //             $previous_expense += PurchaseOrderPaymentMethod::where('status', 'payable')->where('payment_method', $tran->id)->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)->sum('payment_amount');
    //             // dd($invoices);
    //             $previous_deposit += MakePayment::whereNotNull('invoice_id')->where('payment_method', $tran->id)->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)->sum('amount');
    //             $previous_expense += Expense::groupBy('transaction_id')->where('transaction_id', $tran->id)->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)->sum('amount_mmk');
    //             //end for previous month
    //             $accountDepositSums[$tran->id] = [
    //                 'depositcurrent' => $current_deposit,
    //                 'expensecurrent' => $current_expense,
    //                 'depositprevious' => $previous_deposit,
    //                 'expenseprevious' => $previous_expense

    //             ];
    //         }
    //     }

    //     // dd($accountDepositSums);

    //     $branches = Warehouse::all();
    //     $branchNames = $branches->pluck('name', 'id');

    //     $currentBranchName = $branch ? $branchNames[$branch] : 'All';

    //     return view('report.profit_loss', compact('accounts', 'transaction', 'accountDepositSums', 'branchNames', 'currentBranchName', 'branches'));
    // }

    public function profit_loss($branch = null)
    {
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if ($branch) {
            $accounts = Account::with(['payment', 'transaction'])
                ->where('location', $branch)
                ->where('account_bl_pl', 'PL') // only pl account
                ->latest()
                ->get();

            $transaction = Transaction::with('account')
                ->whereHas('account', function ($query) {
                    $query->where('account_bl_pl', 'PL');
                })->where('location', $branch)->latest()->get();
        } else {
            if (auth()->user()->is_admin == '1') {

                $accounts = Account::with(['payment', 'transaction'])
                    ->where('account_bl_pl', 'PL') // only pl account
                    ->latest()
                    ->get();

                $transaction = Transaction::with('account')
                    ->whereHas('account', function ($query) {
                        $query->where('account_bl_pl', 'PL');
                    })
                    ->latest()->get();
            } else {
                $accounts = Account::whereIn('location', $warehousePermission)->with(['payment', 'transaction'])
                    ->where('account_bl_pl', 'PL') // only pl account
                    ->latest()
                    ->get();

                $transaction = Transaction::whereIn('location', $warehousePermission)->with('account')
                    ->whereHas('account', function ($query) {
                        $query->where('account_bl_pl', 'PL');
                    })
                    ->latest()->get();
            }
        }

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $previousMonthDate = now()->subMonth();
        $previousMonth = $previousMonthDate->month;
        $previousYear = $previousMonthDate->year;

        $accountDepositSums = [];

        foreach ($accounts as $account) {


            foreach ($account->transaction as $tran) {
                //for current month
                $current_deposit = 0;
                $current_expense = 0;
                $previous_deposit = 0;
                $previous_expense = 0;
                $current_deposit += Payment::where('payment_status', 'IN')->where('transaction_id', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)
                    ->sum('amount');
                $current_expense = Payment::where('payment_status', 'OUT')
                    ->where('transaction_id', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)
                    ->sum('amount');
                $current_deposit += InvoicePaymentMethod::whereNotNull('invoice_id')
                    ->whereNull('quote_no')
                    ->where('status', 'receivable')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->where('payment_method', $tran->id)->sum('payment_amount');

                $current_deposit += InvoicePaymentMethod::whereNotNull('invoice_id')
                    ->whereNull('quote_no')
                    ->where('status', 'saleaccount')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->where('payment_method', $tran->id)->sum('payment_amount');

                $current_expense += InvoicePaymentMethod::whereNotNull('invoice_id')
                    ->where('quote_no', 'Customer Return')
                    ->where('status', 'receivable')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->where('payment_method', $tran->id)->sum('payment_amount');

                $current_expense += InvoicePaymentMethod::whereNotNull('invoice_id')
                    ->where('quote_no', 'Customer Return')
                    ->where('status', 'saleaccount')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->where('payment_method', $tran->id)->sum('payment_amount');
                $current_expense += PurchaseOrderMakePayment::where('payment_method', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('amount');
                $current_expense += PurchaseOrderPaymentMethod::where('status', 'payable')->where('payment_method', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('payment_amount');
                // dd($invoices);
                $current_deposit += MakePayment::whereNotNull('invoice_id')
                    ->where('invoice_no', '!=', 'Customer Return')
                    ->where('payment_method', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('amount');

                $current_expense += MakePayment::whereNotNull('invoice_id')
                    ->where('invoice_no',  'Customer Return')
                    ->where('payment_method', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('amount');

                $current_expense += Expense::groupBy('transaction_id')->where('transaction_id', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('amount_mmk');
                //end for current month

                //for previous month

                $previous_deposit += Payment::where('payment_status', 'IN')->where('transaction_id', $tran->id)->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)
                    ->sum('amount');
                $previous_expense = Payment::where('payment_status', 'OUT')
                    ->where('transaction_id', $tran->id)->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)
                    ->sum('amount');

                $previous_deposit += InvoicePaymentMethod::whereNotNull('invoice_id')->where('status', 'receivable')
                    ->whereNull('quote_no')
                    ->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)->where('payment_method', $tran->id)->sum('payment_amount');

                $previous_expense += InvoicePaymentMethod::whereNotNull('invoice_id')->where('status', 'receivable')
                    ->where('quote_no',  'Customer Return')
                    ->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)->where('payment_method', $tran->id)->sum('payment_amount');

                $previous_deposit += InvoicePaymentMethod::whereNotNull('invoice_id')->where('status', 'saleaccount')
                    ->whereNull('quote_no')
                    ->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)->where('payment_method', $tran->id)->sum('payment_amount');

                $previous_expense += InvoicePaymentMethod::whereNotNull('invoice_id')
                    ->where('quote_no',  'Customer Return')
                    ->where('status', 'saleaccount')->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)->where('payment_method', $tran->id)->sum('payment_amount');

                $previous_expense += PurchaseOrderMakePayment::where('payment_method', $tran->id)->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)->sum('amount');


                $previous_expense += PurchaseOrderPaymentMethod::where('status', 'payable')->where('payment_method', $tran->id)->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)->sum('payment_amount');
                // dd($invoices);
                $previous_deposit += MakePayment::whereNotNull('invoice_id')
                    ->where('invoice_no', '!=', 'Customer Return')
                    ->where('payment_method', $tran->id)->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)->sum('amount');

                $previous_expense += MakePayment::whereNotNull('invoice_id')
                    ->where('invoice_no', '==', 'Customer Return')
                    ->where('payment_method', $tran->id)->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)->sum('amount');



                $previous_expense += Expense::groupBy('transaction_id')->where('transaction_id', $tran->id)->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)->sum('amount_mmk');
                //end for previous month
                $accountDepositSums[$tran->id] = [
                    'depositcurrent' => $current_deposit,
                    'expensecurrent' => $current_expense,
                    'depositprevious' => $previous_deposit,
                    'expenseprevious' => $previous_expense

                ];
            }
        }

        // dd($accountDepositSums);

        $branches = Warehouse::all();
        $branchNames = $branches->pluck('name', 'id');

        $currentBranchName = $branch ? $branchNames[$branch] : 'All';

        return view('report.profit_loss', compact('accounts', 'transaction', 'accountDepositSums', 'branchNames', 'currentBranchName', 'branches'));
    }


    public function profit_loss_search($branch = null, Request $request)
    {
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if ($branch) {
            $accounts = Account::with(['payment', 'transaction'])
                ->where('location', $branch)
                ->where('account_bl_pl', 'PL') // only pl account
                ->latest()
                ->get();
            $transaction = Transaction::with('account')
                ->whereHas('account', function ($query) {
                    $query->where('account_bl_pl', 'PL');
                })->where('location', $branch)->latest()->get();
        } else {

            if (auth()->user()->is_admin == '1') {

                $accounts = Account::with(['payment', 'transaction'])
                    ->where('account_bl_pl', 'PL') // only pl account
                    ->latest()
                    ->get();
                $transaction = Transaction::with('account')
                    ->whereHas('account', function ($query) {
                        $query->where('account_bl_pl', 'PL');
                    })->latest()->get();
            } else {
                $accounts = Account::whereIn('location', $warehousePermission)->with(['payment', 'transaction'])
                    ->where('account_bl_pl', 'PL') // only pl account
                    ->latest()
                    ->get();

                $transaction = Transaction::whereIn('location', $warehousePermission)->with('account')
                    ->whereHas('account', function ($query) {
                        $query->where('account_bl_pl', 'PL');
                    })
                    ->latest()->get();
            }
        }
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $previousMonthDate = now()->subMonth();
        $previousMonth = $previousMonthDate->month;
        $previousYear = $previousMonthDate->year;

        $accountDepositSums = [];

        foreach ($accounts as $account) {
            foreach ($account->transaction as $tran) {
                //for current month
                $current_deposit = 0;
                $current_expense = 0;
                $previous_deposit = 0;
                $previous_expense = 0;
                $current_deposit += Payment::where('payment_status', 'IN')->where('transaction_id', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)
                    ->sum('amount');
                $current_expense = Payment::where('payment_status', 'OUT')
                    ->where('transaction_id', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)
                    ->sum('amount');
                $current_deposit += InvoicePaymentMethod::whereNotNull('invoice_id')->where('status', 'receivable')
                    ->whereNull('quote_no')
                    ->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->where('payment_method', $tran->id)->sum('payment_amount');


                $current_expense += InvoicePaymentMethod::whereNotNull('invoice_id')->where('status', 'receivable')
                    ->where('quote_no', 'Customer Return')
                    ->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->where('payment_method', $tran->id)->sum('payment_amount');

                $current_deposit += InvoicePaymentMethod::whereNotNull('invoice_id')->where('status', 'saleaccount')
                    ->whereNull('quote_no')
                    ->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->where('payment_method', $tran->id)->sum('payment_amount');

                $current_expense += InvoicePaymentMethod::whereNotNull('invoice_id')->where('status', 'saleaccount')
                    ->where('quote_no', 'Customer Return')
                    ->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->where('payment_method', $tran->id)->sum('payment_amount');

                $current_expense += PurchaseOrderMakePayment::where('payment_method', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('amount');
                $current_expense += PurchaseOrderPaymentMethod::where('status', 'payable')->where('payment_method', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('payment_amount');
                // dd($invoices);

                $current_deposit += MakePayment::whereNotNull('invoice_id')
                    ->where('invoice_no', '!=', 'Customer Return')
                    ->where('payment_method', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('amount');


                $current_expense += MakePayment::whereNotNull('invoice_id')
                    ->where('invoice_no', '==', 'Customer Return')
                    ->where('payment_method', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('amount');

                $current_expense += Expense::groupBy('transaction_id')->where('transaction_id', $tran->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('amount_mmk');
                //end for current month

                //for previous month

                $previous_deposit += Payment::where('payment_status', 'IN')->where('transaction_id', $tran->id)->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)
                    ->sum('amount');
                $previous_expense = Payment::where('payment_status', 'OUT')
                    ->where('transaction_id', $tran->id)->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)
                    ->sum('amount');
                $previous_deposit += InvoicePaymentMethod::whereNotNull('invoice_id')->where('status', 'receivable')
                    ->whereNull('quote_no')
                    ->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)->where('payment_method', $tran->id)->sum('payment_amount');

                $previous_expense += InvoicePaymentMethod::whereNotNull('invoice_id')->where('status', 'receivable')
                    ->where('quote_no', 'Customer Return')
                    ->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)->where('payment_method', $tran->id)->sum('payment_amount');


                $previous_deposit += InvoicePaymentMethod::whereNotNull('invoice_id')->where('status', 'saleaccount')
                    ->whereNull('quote_no')
                    ->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)->where('payment_method', $tran->id)->sum('payment_amount');


                $previous_expense += InvoicePaymentMethod::whereNotNull('invoice_id')->where('status', 'saleaccount')
                    ->where('quote_no', 'Customer Return')
                    ->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)->where('payment_method', $tran->id)->sum('payment_amount');

                $previous_expense += PurchaseOrderMakePayment::where('payment_method', $tran->id)->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)->sum('amount');

                $previous_expense += PurchaseOrderPaymentMethod::where('status', 'payable')->where('payment_method', $tran->id)->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)->sum('payment_amount');
                // dd($invoices);
                $previous_deposit += MakePayment::whereNotNull('invoice_id')->where('payment_method', $tran->id)
                    ->where('invoice_no', '!=', 'Customer Return')
                    ->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)->sum('amount');

                $previous_expense += MakePayment::whereNotNull('invoice_id')
                    ->where('invoice_no', '==', 'Customer Return')
                    ->where('payment_method', $tran->id)->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)->sum('amount');

                $previous_expense += Expense::groupBy('transaction_id')->where('transaction_id', $tran->id)->whereMonth('created_at', $previousMonth)->whereYear('created_at', $previousYear)->sum('amount_mmk');
                //end for previous month
                $accountDepositSums[$tran->id] = [
                    'depositcurrent' => $current_deposit,
                    'expensecurrent' => $current_expense,
                    'depositprevious' => $previous_deposit,
                    'expenseprevious' => $previous_expense

                ];
            }
        }

        $branches = Warehouse::all();
        $branchNames = $branches->pluck('name', 'id');

        $currentBranchName = $branch ? $branchNames[$branch] : 'All';

        return view('report.profit_loss', compact('accounts', 'transaction', 'accountDepositSums', 'branchNames', 'currentBranchName', 'branches'));
    }
    public function report_account_transaction_payment($account_id, $id)
    {

        $accounts = Account::all();
        $transaction = Transaction::find($id);
        $transactions = Transaction::with('account')->latest()->get();




        $invoices = Invoice::with('makePayments')->where('status', 'invoice')
            ->whereHas('makePayments', function ($query) use ($transaction) {
                $query->where('payment_method', $transaction->id)
                    ->whereMonth('invoice_date', Carbon::now()->month)
                    ->whereYear('invoice_date', Carbon::now()->year);
            })
            ->get();

        $receive_invoices = Invoice::with('PaymentMethod')->where('status', 'invoice')
            ->whereHas('PaymentMethod', function ($query) use ($transaction) {
                $query->where('payment_method', $transaction->id)
                    ->where('status', 'receivable')
                    ->whereMonth('invoice_date', Carbon::now()->month)
                    ->whereYear('invoice_date', Carbon::now()->year);
            })
            ->get();

        $sale_invoices = Invoice::with('PaymentMethod')->where('status', 'invoice')
            ->whereHas('PaymentMethod', function ($query) use ($transaction) {
                $query->where('payment_method', $transaction->id)
                    ->where('status', 'saleaccount')
                    ->whereMonth('invoice_date', Carbon::now()->month)
                    ->whereYear('invoice_date', Carbon::now()->year);
            })
            ->get();


        $po_returns
            = Invoice::with('makePayments')->where('status', 'invoice')->where('balance_due', 'Po Return')
            ->whereHas('makePayments', function ($query) use ($transaction) {
                $query->where('payment_method', $transaction->id)
                    ->whereMonth('invoice_date', Carbon::now()->month)
                    ->whereYear('invoice_date', Carbon::now()->year);
            })
            ->get();
        // dd($po_returns);

        $po_returns_receive
            = Invoice::with('PaymentMethod')->where('status', 'invoice')->where('balance_due', 'Po Return')
            ->whereHas('PaymentMethod', function ($query) use ($transaction) {
                $query->where('payment_method', $transaction->id)
                    ->where('status', 'receivable')
                    ->whereMonth('invoice_date', Carbon::now()->month)
                    ->whereYear('invoice_date', Carbon::now()->year);
            })
            ->get();
        // dd($po_returns_receive);


        // $purchase_orders = PurchaseOrder::where('transaction_id', $transaction->id)
        //     ->whereMonth('created_at', Carbon::now()->month)
        //     ->whereYear('created_at', Carbon::now()->year)
        //     ->where('balance_due', 'PO')
        //     ->get();
        $purchase_orders = PurchaseOrder::with('purchasePayment')->where('balance_due', 'PO')
            ->whereHas('purchasePayment', function ($query) use ($transaction) {
                $query->where('payment_method', $transaction->id)
                    ->whereMonth('po_date', Carbon::now()->month)
                    ->whereYear('po_date', Carbon::now()->year);
            })
            ->get();
        // dd($purchase_orders);



        $sale_return_invoices
            = PurchaseOrder::with('purchasePayment')->where('balance_due', 'Sale Return Invoice')
            ->whereHas('purchasePayment', function ($query) use ($transaction) {
                $query->where('payment_method', $transaction->id)
                    ->whereMonth('po_date', Carbon::now()->month)
                    ->whereYear('po_date', Carbon::now()->year);
            })
            ->get();
        $expenses = Expense::whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->where('transaction_id', $transaction->id)
            ->get();



        $sumByIn = Payment::where('payment_status', 'IN')
            ->groupBy('transaction_id')
            ->selectRaw('transaction_id, SUM(amount) as total')
            ->pluck('total', 'transaction_id');
        $sumByOut = Payment::where('payment_status', 'OUT')
            ->groupBy('transaction_id')
            ->selectRaw('transaction_id, SUM(amount) as total')
            ->pluck('total', 'transaction_id');

        $diff = collect($sumByIn)->map(function ($totalIn, $transactionId) use ($sumByOut) {
            $totalOut = $sumByOut->get($transactionId, 0);

            return $totalIn - $totalOut;
        });
        $warehouses = Warehouse::all();
        $add_payments = Payment::where('transaction_id', $id)->get();
        $makepayment = MakePayment::where('payment_method', $id)->whereNotNull('invoice_id')->get();
        $purchasePayment = PurchaseOrderPaymentMethod::where('payment_method', $id)->get();
        $invoice_receive = InvoicePaymentMethod::where('payment_method', $id)->where('status', 'receivable')->get();

        $invoice_sale = InvoicePaymentMethod::where('payment_method', $id)->where('status', 'saleaccount')->get();
        return view('report.report_account_transaction_payment', compact('accounts', 'transaction', 'add_payments', 'invoices', 'warehouses', 'transactions', 'diff', 'purchase_orders', 'po_returns', 'sale_return_invoices', 'makepayment', 'purchasePayment', 'invoice_receive', 'receive_invoices', 'po_returns_receive', 'expenses', 'sale_invoices', 'invoice_sale'));
    }

    public function report_account_transaction_payment_search($id, Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $accounts = Account::where('id', $id)->get();
        $transaction = Transaction::find($id);
        $transactions = Transaction::with('account')->latest()->get();

        $startDate = $request->input('start_date', Carbon::now()->startOfDay());
        $endDate = $request->input('end_date', Carbon::now()->endOfDay());

        $payment = Payment::where('id', '>', 6)
            ->where('transaction_id', $transaction->id)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->get();

        $invoices = Invoice::where('transaction_id', $transaction->id)
            ->where('status', 'invoice')
            ->where('balance_due', 'Invoice')
            ->whereDate('invoice_date', '>=', $startDate)
            ->whereDate('invoice_date', '<=', $endDate)
            ->get();

        $po_returns = Invoice::where('transaction_id', $transaction->id)
            ->where('status', 'invoice')
            ->where('balance_due', 'Po Return')
            ->whereDate('invoice_date', '>=', $startDate)
            ->whereDate('invoice_date', '<=', $endDate)
            ->get();

        $purchase_orders = PurchaseOrder::where('transaction_id', $transaction->id)
            ->whereDate('po_date', '>=', $startDate)
            ->whereDate('po_date', '<=', $endDate)
            ->where('balance_due', 'PO')
            ->get();

        $point_of_sales = Invoice::where('transaction_id', $transaction->id)
            ->whereDate('invoice_date', '>=', $startDate)
            ->whereDate('invoice_date', '<=', $endDate)
            ->where('status', 'pos')
            ->get();

        $sale_return_invoices = PurchaseOrder::where('transaction_id', $transaction->id)
            ->whereDate('po_date', '>=', $startDate)
            ->whereDate('po_date', '<=', $endDate)
            ->where('balance_due', 'Sale Return Invoice')
            ->get();

        $sale_return_pos = PurchaseOrder::where('transaction_id', $transaction->id)
            ->whereDate('po_date', '>=', $startDate)
            ->whereDate('po_date', '<=', $endDate)
            ->where('balance_due', 'Sale Return')
            ->get();

        $total_deposit_invoices = $invoices->sum('deposit');
        $total_deposit_po_returns = $po_returns->sum('deposit');
        $total_deposit_purchase_orders = $purchase_orders->sum('deposit');
        $total_deposit_point_of_sales = $point_of_sales->sum('deposit');
        $total_deposit_sale_return_invoices = $sale_return_invoices->sum('deposit');
        $total_deposit_sale_return_pos = $sale_return_pos->sum('deposit');
        $total_payment = $payment->sum('amount');

        $warehouses = Warehouse::all();
        $payment = Payment::where('transaction_id', $id)->get();

        return view('report.report_account_transaction_payment', compact(
            'id',
            'accounts',
            'transaction',
            'payment',
            'invoices',
            'warehouses',
            'transactions',
            'purchase_orders',
            'point_of_sales',
            'po_returns',
            'sale_return_invoices',
            'sale_return_pos',
            'total_deposit_invoices',
            'total_deposit_po_returns',
            'total_deposit_purchase_orders',
            'total_deposit_point_of_sales',
            'total_deposit_sale_return_invoices',
            'total_deposit_sale_return_pos',
            'total_payment'
        ));
    }


    public function selling_report(Request $request)
    {

        if (auth()->user()->is_admin == '1') {
            $query = Sell::with('invoice')->latest();
            $warehouses = Warehouse::all();
        } else {
            $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];
            $query = Sell::with('invoice')->whereIn('warehouse', $warehousePermission)->latest();
            $warehouses = Warehouse::whereIn('id', $warehousePermission)->get();
        }

        if ($request->has('start_date') && $request->has('end_date') && $request->input('start_date') && $request->input('end_date')) {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $query->whereHas('invoice', function ($q) use ($startDate, $endDate) {
                $q->whereDate('invoice_date', '>=', $startDate)
                    ->whereDate('invoice_date', '<=', $endDate);
            });
        }

        $branch = $request->branch;

        // Fetch branch names
        $branches = Warehouse::pluck('name', 'id');
        $currentBranchName = $branch ? $branches[$branch] : 'All Locations';
        if ($branch) {
            $query->whereHas('branch', function ($warehouseQuery) use ($branch) {
                $warehouseQuery->where('id', $branch);
            });
        } else {
        }

        $sells = $query->get();

        return view('report.selling_report', compact('sells', 'warehouses'));
    }

    public function supplier_monthly_report(Request $request)
    {

        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        // Set default dates to current month if not provided
        $start_date = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->format('Y-m-d')
            : Carbon::now()->startOfMonth()->format('Y-m-d');

        $end_date = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->format('Y-m-d')
            : Carbon::now()->endOfMonth()->format('Y-m-d');

        $branch = $request->input('branch');
        if (auth()->user()->is_admin == '1') {
            // Fetch suppliers, filter by branch if selected
            $suppliers = Supplier::when($branch, function ($query, $branch) {
                $query->where('branch', $branch);
            })->latest()->get();

            $branchs = Warehouse::all();

            // Prepare an array to store supplier-wise purchase orders
            $supplier_reports = [];
        } else {
            // Fetch suppliers, filter by branch if selected
            $suppliers = Supplier::whereIn('branch', $warehousePermission)->when($branch, function ($query, $branch) {
                $query->where('branch', $branch);
            })->latest()->get();

            $branchs = Warehouse::whereIn('id', $warehousePermission)->get();

            // Prepare an array to store supplier-wise purchase orders
            $supplier_reports = [];
        }


        foreach ($suppliers as $supplier) {
            $purchase_orders = PurchaseOrder::where('supplier_id', $supplier->id)
                ->when($branch, function ($query, $branch) {
                    $query->where('branch', $branch);
                })
                ->whereDate('po_date', '>=', $start_date)
                ->whereDate('po_date', '<=', $end_date)
                ->get();

            $supplier_reports[$supplier->id] = [
                'supplier' => $supplier,
                'orders' => $purchase_orders
            ];
        }

        return view('report.supplier_report', compact('supplier_reports', 'start_date', 'end_date', 'branchs', 'branch'));
    }
}
