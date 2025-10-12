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

        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if (auth()->user()->is_admin == '1') {
            $customers = Customer::latest()->get();
            $branchs = Warehouse::select('name', 'id')->get();
        } else {
            $customers = Customer::whereIn('branch', $warehousePermission)->latest()->get();
            $branchs = Warehouse::select('name', 'id')->get();
        }


        return view('customer.customer', compact('customers', 'branchs'));
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
                    'name' => 'required',
                    'phno' => 'nullable',
                    'type' => 'nullable',
                    'address' => 'nullable',
                    'branch' => 'required',
                    'email' => 'nullable',
                ],
                ['type.required' => 'Customer Type is required']
            );

            Customer::create($validated);

            return redirect()->back()->with('success', 'New Customer Added Successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    public function edit(Request $request, $id)
    {
        $showCustomer = Customer::find($id);
        $branchs = Warehouse::select('name', 'id')->get();
        return view('customer.customer_edit', compact('showCustomer', 'branchs'));
    }
    public function update($id, Request $request)
    {
        $customer = Customer::find($id);
        $customer->update($request->all());
        $customers = Customer::latest()->get();
        return redirect('customer')->with('success', 'Customer Updated Successful!');
    }
    public function delete($id)
    {
        $customer = Customer::find($id);
        $customer->delete();
        return redirect('customer')->with('success', 'Customer Deleted Successful!');
    }

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

            $import = new CustomerImport($warehouseId);
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

            return Excel::download(new CustomerExport($warehouseId), 'customers.xlsx');
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred during export: ' . $e->getMessage()], 500);
        }
    }

    public function fileImportTemplate()
    {
        return Excel::download(new CustomerImporttemplate, 'customers.xlsx');
    }
}
