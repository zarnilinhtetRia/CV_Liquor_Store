<?php

namespace App\Http\Controllers;

use App\Exports\CustomerExport;
use App\Exports\CustomerImporttemplate;
use App\Imports\CustomerImport;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    //index
    public function index()
    {




        $customers = Customer::latest()->get();




        return view('customer.customer', compact('customers'));
    }

    public function credit(Request $request, $id)
    {
        $customer = Customer::find($id);

        // Get the date filters from the request
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Base query
        $query = Invoice::where('customer_id', $id)
            ->where('status', 'Invoice')
            ->where('remain_balance', '!=', 0);

        // Apply whereDate filters if provided
        if ($startDate) {
            $query->whereDate('invoice_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('invoice_date', '<=', $endDate);
        }

        // Get filtered invoices
        $invoices = $query->get();

        // Calculate totals
        $total_amount = $invoices->sum('total');
        $balance = $invoices->sum('remain_balance');
        $deposit = $invoices->sum('deposit');

        return view('customer.credit', compact('invoices', 'customer', 'total_amount', 'balance', 'deposit'));
    }

    public function all_customer_credit(Request $request)
    {
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        // Get date inputs
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Base query
        $query = Invoice::where('status', 'Invoice')
            ->whereNotNull('customer_id')
            ->where('remain_balance', '!=', 0);

        // Apply branch filtering for non-admin users
        if (auth()->user()->is_admin != '1') {
            $query->whereIn('branch', $warehousePermission);
        }

        // Apply whereDate conditions (only if values exist)
        if ($startDate) {
            $query->whereDate('invoice_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('invoice_date', '<=', $endDate);
        }

        $invoices = $query->get();

        // Calculate totals
        $total_amount = $invoices->sum('total');
        $balance = $invoices->sum('remain_balance');
        $deposit = $invoices->sum('deposit');

        // Fetch customers
        $customers = auth()->user()->is_admin == '1'
            ? Customer::latest()->get()
            : Customer::whereIn('branch', $warehousePermission)->latest()->get();

        return view('customer.customer_all_credit', compact('invoices', 'customers', 'total_amount', 'balance', 'deposit'));
    }

    public function customer_invoice($id)
    {
        $customer = Customer::find($id);
        $invoices = Invoice::where('customer_id', $id)
            ->where('status', 'Invoice')
            ->get();
        $total_amount = $invoices->sum('total');
        $balance = $invoices->sum('remain_balance');
        $deposit = $invoices->sum('deposit');
        return view('customer.customer_invoice', compact('invoices', 'customer', 'total_amount', 'balance', 'deposit'));
    }

    public function store(Request $request)
    {
        try {

            $validated = $request->validate(
                [


                    // POS & Transaction Details
                    'pos' => 'nullable',
                    'date' => 'nullable',
                    'time' => 'nullable',
                    'cashier_id' => 'nullable',
                    'cashier_name' => 'nullable',
                    'receipt_no' => 'nullable',
                    'transaction_no' => 'nullable',
                    'reprinted_by' => 'nullable',
                    'reprinted_datetime' => 'nullable',

                    // Passenger / Travel Info
                    'passport_no' => 'nullable',
                    'nationality' => 'nullable',
                    'flight_code' => 'nullable',

                    // Item / Purchase Info
                    'item_name' => 'nullable',
                    'qty' => 'nullable|numeric',
                    'price' => 'nullable|numeric',
                    'discount' => 'nullable|numeric',
                    'total' => 'nullable|numeric',

                    // Summary Fields
                    'sub_total' => 'nullable|numeric',
                    'gst' => 'nullable|numeric',
                    'total_items' => 'nullable|numeric',
                    'total_discount' => 'nullable|numeric',
                    'other_disc' => 'nullable|numeric',
                    'final_total' => 'nullable|numeric',

                    // Payment
                    'cash' => 'nullable|numeric',
                    'change_back' => 'nullable|numeric',
                    'adjust' => 'nullable|numeric',

                    // Member info
                    'member_tier' => 'nullable',
                    'tier_validity' => 'nullable',
                    'nett_spend' => 'nullable',
                    'issued_points' => 'nullable',

                    // Points
                    'py2025_bal' => 'nullable',
                    'py2025_redeem' => 'nullable',
                    'py2024_points' => 'nullable',
                    'barcode' => 'nullable',

                ],

            );

            // Save the data
            Customer::create($validated);

            return redirect()->back()->with('success', 'New Invoices Added Successfully!');
        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit(Request $request, $id)
    {
        $data = Customer::find($id);

        return view('customer.customer_edit', compact('data'));
    }
    public function update($id, Request $request)
    {
        $customer = Customer::find($id);
        $customer->update($request->all());

        return redirect('customer')->with('success', 'Invoices Updated Successful!');
    }
    public function delete($id)
    {
        $customer = Customer::find($id);
        $customer->delete();
        return redirect('customer')->with('success', 'Invoices Deleted Successful!');
    }
    public function view($id)
    {
        $customer = Customer::find($id);
        return view('customer.view', compact('customer'));
    }
}
