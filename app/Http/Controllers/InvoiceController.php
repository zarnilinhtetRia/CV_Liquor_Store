<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Item;
use App\Models\Sell;
use App\Models\Unit;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Customer;
use App\Models\Exchange;
use App\Models\PO_sells;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\MakePayment;
use App\Models\Transaction;
use App\Models\UserProfile;
use App\Models\ItemQuantity;
use Illuminate\Http\Request;
use App\Models\ItemVariation;
use App\Models\PurchaseOrder;
use App\Models\DeliveredItems;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\InvoicePaymentMethod;
use Illuminate\Support\Facades\Cache;
use App\Models\PurchaseOrderPaymentMethod;

class InvoiceController extends Controller
{
    //
    public function index($branch = null)
    {


        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if (auth()->check() && auth()->user()->is_admin == '1') {
            if ($branch) {
                $invoices = Invoice::where('status', 'invoice')->where('branch', $branch)->latest()->get();
                $branchs = Warehouse::select('name', 'id')->get();
            } else {
                $invoices = Invoice::where('status', 'invoice')->latest()->get();
                $branchs = Warehouse::select('name', 'id')->get();
            }
        } else {
            $warehousePermission = auth()->check() ? $warehousePermission : [];
            $invoices = Invoice::whereIn('branch', $warehousePermission)
                ->where('status', 'invoice')
                ->latest()
                ->get();
            $branchs = Warehouse::select('name', 'id')->get();
        }

        $branchs = Warehouse::all();
        $branchNames = $branchs->pluck('name', 'id');

        $currentBranchName = $branch ? $branchNames[$branch] : 'All Invoices';
        return view('invoice.invoice_manage', compact('invoices', 'branchs', 'currentBranchName', 'branchNames'));
    }


    public function quotation($branch = null)
    {

        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if (auth()->user()->is_admin == '1') {
            if ($branch) {
                $quotations = Invoice::where('branch', $branch)->where('status', 'quotation')->latest()->get();
                $branchs = Warehouse::all();
            } else {
                $quotations = Invoice::where('status', 'quotation')->latest()->get();
                $branchs = Warehouse::all();
            }
        } else {
            $quotations = Invoice::whereIn('branch', $warehousePermission)->where('status', 'quotation')->latest()->get();
            $branchs = Warehouse::all();
        }

        $branchs = Warehouse::all();
        $branchNames = $branchs->pluck('name', 'id');

        $currentBranchName = $branch ? $branchNames[$branch] : 'All Quotations';
        return view('quotation.quotation_manage', compact('quotations', 'branchs', 'currentBranchName', 'branchNames'));
    }

    public function quotation_register()
    {

        $quotations = Invoice::whereNotNull('quote_no')->latest()->get();
        $quotation_no = "Quote-" . count($quotations) + 1;
        $units = Unit::all();
        $warehouses = Warehouse::all();
        // $rate = Exchange::first()->rate;

        return view('quotation.quotation', compact('quotation_no', 'units', 'warehouses'));
    }

    public function invoice()
    {
        $totalInvoices = Invoice::where('status', 'invoice')->count();
        // $invoice_no = "Invoice-" . ($totalInvoices + 1);
        $units = Unit::all();
        $warehouses = Warehouse::select('name', 'id')->get();
        $setting = Setting::where('category', '1')->get();
        if ($setting) {
        }

        return view('invoice.invoice', compact('units', 'warehouses'));
    }

    // public function invoice_no_updates()
    // {
    //     $totalInvoices = Invoice::where('status', 'invoice')->count();
    //     $invoice_no = "Invoice-" . ($totalInvoices + 1);

    //     return response()->json(['invoice_no' => $invoice_no]);
    // }
    // public function invoice_no_updates()
    // {
    //     $latestInvoice = Invoice::where('status', 'invoice')
    //         ->orderBy('created_at', 'desc')
    //         ->value('invoice_no');

    //     $nextNumber = 1;
    //     if ($latestInvoice) {
    //         preg_match('/\d+$/', $latestInvoice, $matches);
    //         $nextNumber = isset($matches[0]) ? (int)$matches[0] + 1 : 1;
    //     }

    //     $invoice_no = "Invoice-" . $nextNumber;

    //     return response()->json(['invoice_no' => $invoice_no]);
    // }
    public function invoice_no_updates(Request $request)
    {
        try {
            $locationId = $request->query('location_id'); // Get location ID

            if (!$locationId) {
                return response()->json(['error' => 'Location ID is missing'], 400);
            }
            // Fetch the maximum numeric part of invoice numbers
            $latestNumber = Invoice::where('status', 'invoice')
                ->where('branch',  $locationId)
                ->selectRaw("MAX(CAST(SUBSTRING_INDEX(invoice_no, '-', -1) AS UNSIGNED)) as max_invoice_no")
                ->value('max_invoice_no');



            $nextNumber = $latestNumber ? $latestNumber + 1 : 1;


            $invoice_no = "Invoice-" . $nextNumber;

            return response()->json(['invoice_no' => $invoice_no]);
        } catch (\Exception $e) {
            Log::error("Error fetching invoice: " . $e->getMessage()); // Log error
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function pos_no_updates()
    {
        // Fetch the maximum numeric part of invoice numbers
        $latestNumber = Invoice::where('status', 'pos')
            ->selectRaw("MAX(CAST(SUBSTRING_INDEX(invoice_no, '-', -1) AS UNSIGNED)) as max_invoice_no")
            ->value('max_invoice_no');


        // Calculate the next invoice number
        $nextNumber = $latestNumber ? $latestNumber + 1 : 1;

        // Generate the new invoice number
        $invoice_no = "POS-" . $nextNumber;

        return response()->json(['invoice_no' => $invoice_no]);
    }

    // public function customer_return_no_updates()
    // {
    //     // Fetch the maximum numeric part of invoice numbers
    //     $latestNumber = Invoice::where('status', 'customer return')
    //         ->selectRaw("MAX(CAST(SUBSTRING_INDEX(invoice_no, '-', -1) AS UNSIGNED)) as max_invoice_no")
    //         ->value('max_invoice_no');

    //     // Calculate the next invoice number
    //     $nextNumber = $latestNumber ? $latestNumber + 1 : 1;

    //     // Generate the new invoice number
    //     $invoice_no = "CR-" . $nextNumber;

    //     return response()->json(['invoice_no' => $invoice_no]);
    // }

    public function customer_return_no_updates(Request $request)
    {
        $branch = $request->input('branch'); // Get branch from request

        // If a branch is provided, use it to filter the invoices
        $latestNumber = Invoice::where('status', 'customer return')
            ->where('branch', $branch) // Assuming you have a 'branch' column
            ->selectRaw("MAX(CAST(SUBSTRING_INDEX(invoice_no, '-', -1) AS UNSIGNED)) as max_invoice_no")
            ->value('max_invoice_no');

        $nextNumber = $latestNumber ? $latestNumber + 1 : 1;
        $invoice_no = "CR-" . $nextNumber;

        return response()->json(['invoice_no' => $invoice_no]);
    }



    private function getInvoicePrefix(string $warehouse_name): string
    {
        $prefixes = [
            'casabella yangon' => 'CSY',
            'casabella mandalay' => 'CSM',
            'casabella naypyitaw' => 'CSN',
            'goldcoil' => 'GC',
            'natray wellness' => 'NRW',
        ];

        foreach ($prefixes as $key => $prefix) {
            if (stripos($warehouse_name, $key) !== false) {
                return $prefix;
            }
        }

        return 'UNKNOWN'; // Default case if no match is found
    }

    private function getInvoiceStartingNumber(string $prefix): int
    {

        // Starting numbers for each prefix
        $starting_numbers = [
            'CSY' => 108001,
            'CSM' => 108001,
            'CSN' => 108001,
            'GC'  => 5001,
        ];

        return $starting_numbers[$prefix] ?? 1;
    }


    public function getBranchInvoiceNo(Request $request)
    {
        $warehouse = Warehouse::find($request->warehouse);

        if (!$warehouse) {
            return response()->json(['error' => 'Warehouse not found'], 404);
        }

        $warehouse_name = strtolower($warehouse->name);
        $prefix = $this->getInvoicePrefix($warehouse_name);

        if ($prefix === 'UNKNOWN') {
            return response()->json(['error' => 'Warehouse name not recognized'], 400);
        }

        $starting_number = $this->getInvoiceStartingNumber($prefix);

        // Retrieve the latest transfer number for the given warehouse and prefix
        $latest_number = Invoice::where('branch', $warehouse->id)
            ->where('status', 'invoice')
            ->where('branch_invoice_no', 'LIKE', "$prefix - %")
            ->latest()
            ->first();


        if ($latest_number) {
            // Extract the numeric part of the latest transfer number
            preg_match('/\d+$/', $latest_number->branch_invoice_no, $matches);
            $last_number = isset($matches[0]) ? (int)$matches[0] : $starting_number - 1;

            // Increment the number
            $invoice_number = $last_number + 1;
        } else {
            // Start with the predefined starting number if no transfers exist
            $invoice_number = $starting_number;
        }

        if ($prefix === 'GC') {
            $invoice_number = str_pad($invoice_number, 6, '0', STR_PAD_LEFT);
        }

        $branch_invoice_no = "$prefix - $invoice_number";

        return response()->json(['no' => $branch_invoice_no]);
    }
    public function getBranchPOSNo(Request $request)
    {
        $warehouse = Warehouse::find($request->warehouse);

        if (!$warehouse) {
            return response()->json(['error' => 'Warehouse not found'], 404);
        }

        $warehouse_name = strtolower($warehouse->name);
        $prefix = 'POSB';

        if ($prefix === 'UNKNOWN') {
            return response()->json(['error' => 'Warehouse name not recognized'], 400);
        }

        $starting_number = 1;

        // Retrieve the latest transfer number for the given warehouse and prefix
        $latest_number = Invoice::where('branch', $warehouse->id)
            ->where('status', 'pos')
            ->where('branch_invoice_no', 'LIKE', "$prefix - %")
            ->latest()
            ->first();
        // dd($latest_number);

        if ($latest_number) {
            // Extract the numeric part of the latest transfer number
            preg_match('/\d+$/', $latest_number->branch_invoice_no, $matches);
            $last_number = isset($matches[0]) ? (int)$matches[0] : $starting_number - 1;

            // Increment the number
            $invoice_number = $last_number + 1;
        } else {
            // Start with the predefined starting number if no transfers exist
            $invoice_number = $starting_number;
        }

        if ($prefix === 'GC') {
            $invoice_number = str_pad($invoice_number, 6, '0', STR_PAD_LEFT);
        }

        $branch_invoice_no = "$prefix - $invoice_number";

        return response()->json(['no' => $branch_invoice_no]);
    }


    private function getCustomerReturnStartingNumber(string $prefix): int
    {

        // Starting numbers for each prefix
        $starting_numbers = [
            'CSY' => 25001,
            'CSM' => 25001,
            'CSN' => 25001,
            'GC'  => 25001,

        ];

        return $starting_numbers[$prefix] ?? 1;
    }

    public function getBranchCustomerReturnNo(Request $request)
    {
        $warehouse = Warehouse::find($request->warehouse);

        if (!$warehouse) {
            return response()->json(['error' => 'Warehouse not found'], 404);
        }

        $warehouse_name = strtolower($warehouse->name);
        $prefix = $this->getInvoicePrefix($warehouse_name);

        if ($prefix === 'UNKNOWN') {
            return response()->json(['error' => 'Warehouse name not recognized'], 400);
        }

        $starting_number = $this->getCustomerReturnStartingNumber($prefix);

        // Retrieve the latest transfer number for the given warehouse and prefix
        $latest_number = Invoice::where('branch', $warehouse->id)
            ->where('status', 'customer return')
            ->where('branch_invoice_no', 'LIKE', "$prefix - %")
            ->latest()
            ->first();


        if ($latest_number) {
            // Extract the numeric part of the latest transfer number
            preg_match('/\d+$/', $latest_number->branch_invoice_no, $matches);
            $last_number = isset($matches[0]) ? (int)$matches[0] : $starting_number - 1;

            // Increment the number
            $invoice_number = $last_number + 1;
        } else {
            // Start with the predefined starting number if no transfers exist
            $invoice_number = $starting_number;
        }



        $branch_invoice_no = "$prefix - $invoice_number";

        return response()->json(['no' => $branch_invoice_no]);
    }




    public function invoice_get_transaction(Request $request)
    {

        $location = $request->locationId;
        $mode = $request->register_mode;

        if ($mode == "Invoice") {
            $settings = Setting::where('location', $location)->where('category', 'invoice')->get();
        } else {
            $settings = Setting::where('location', $location)->where('category', 'purchase order return')->get();
        }

        if ($settings->isNotEmpty()) {
            $transactions = collect();

            foreach ($settings as $setting) {
                $transactionsForSetting = Transaction::where('location', $location)
                    ->where('account_id', $setting->transaction_id)
                    ->get();
                $transactions = $transactions->merge($transactionsForSetting);
            }

            if ($transactions->isNotEmpty()) {
                return response()->json($transactions);
            } else {
                return response()->json(['error' => 'Transactions not found'], 404);
            }
        } else {
            return response()->json(['error' => 'Settings not found'], 404);
        }
    }


    // public function quotation_get_transaction($location)
    // {


    //     $setting = Setting::where('location', $location)->where('category', 'invoice')->first();

    //     if ($setting) {
    //         $transaction = Transaction::where('location', $location)->where('account_id', $setting->transaction_id)->get();
    //         if ($transaction) {
    //             return response()->json($transaction);
    //         } else {
    //             return ['error' => 'Transaction not found'];
    //         }
    //     } else {
    //         return ['error' => 'Setting not found'];
    //     }
    // }


    public function quotation_get_transaction($location)
    {
        $settings = Setting::where('location', $location)
            ->where('category', 'invoice')
            ->get();

        if ($settings->isNotEmpty()) {
            $transaction = collect();

            foreach ($settings as $setting) {
                $relatedTransactions = Transaction::where('location', $location)
                    ->where('account_id', $setting->transaction_id)
                    ->get();

                $transaction = $transaction->merge($relatedTransactions);
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


    public function pos_register()
    {
        $invoices = Invoice::withTrashed()->whereIn('status',  ['pos', 'suspend'])->latest()->get();
        $suspends = Invoice::where('status', 'suspend')->latest()->get();
        $invoice_no = "POS-" . count($invoices) + 1;
        $units = Unit::all();
        $warehouses = Warehouse::select('name', 'id')->get();
        return view('invoice.pos', compact('invoice_no', 'units', 'warehouses', 'suspends'));
    }

    public function pos()
    {

        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if (auth()->user()->is_admin == '1') {
            $invoices = Invoice::where('status', 'pos')->latest()->get();
            $branchs = Warehouse::select('name', 'id')->get();
        } else {
            $invoices = Invoice::whereIn('branch', $warehousePermission)->where('status', 'pos')->latest()->get();
            $branchs = Warehouse::select('name', 'id')->get();
        }

        return view('invoice.pos_manage', compact(
            'invoices',
            'branchs'
        ));
    }

    public function invoice_register(Request $request)
    {
        // dd($request->all());

        $invoice_number = Invoice::where('invoice_no', $request->invoice_no)->where('branch', $request->branch)->get();
        $count = count($invoice_number);



        if ($count < 1) {
            $inv_number = $request->invoice_no;
        } else {
            $inv_number = $request->invoice_no;
            do {
                $inv_number++;
                $count = Invoice::where('invoice_no', $inv_number)->where('branch', $request->branch)->count();
            } while ($count > 0);
        }


        $count = count($request->part_number);
        $invoice = new Invoice();
        $invoice->customer_id = $request->customer_id;
        $invoice->customer_name = $request->customer_name;
        $invoice->invoice_category = $request->quote_category;
        $invoice->sale_price_category = $request->sale_price_category;
        $invoice->phno  = $request->phno;
        $invoice->status  = $request->status;
        $invoice->site = $request->site;

        $invoice->branch  = $request->branch;
        // $invoice->sale_by  = $request->sale_by;
        $invoice->sale_by = auth()->user()->name;
        $invoice->location = $request->location;
        $invoice->type  = $request->type;
        $invoice->address  = $request->address;
        $invoice->invoice_no  = $request->invoice_no;
        $invoice->invoice_date = $request->invoice_date;
        $invoice->quote_date = $request->quote_date;
        $invoice->quote_no  = $request->quote_no;
        $invoice->overdue_date  = $request->overdue_date;
        $invoice->sub_total  = $request->sub_total;
        $invoice->total  = $request->total;
        $invoice->balance_due  = $request->balance_due;
        $invoice->discount_total  = $request->total_discount;
        $invoice->deposit  = $request->paid;
        $invoice->remain_balance  = $request->balance;
        $invoice->remark = $request->remark;
        //new column
        $invoice->currency_method = $request->currency_method;
        $invoice->exchange_rate = $request->exchange_rate;
        $invoice->overall_discount_mmk = $request->overall_discount_mmk;
        $invoice->branch_invoice_no = $request->branch_invoice_no;

        $invoice->save();
        $last_id = $invoice->id;



        for ($i = 0; $i < $count; $i++) {
            $result = new Sell();
            $result->invoiceid = $last_id;
            $result->customer_id = $request->customer_id;
            $result->description = $request->part_description[$i];
            $result->part_number = $request->part_number[$i];
            $result->variation_id = $request->result_id[$i];
            $result->item_id = $request->item_id[$i];
            $result->warranty = $request->warranty[$i] ?? '';
            //

            $result->product_code = $request->result_product_code[$i];
            $result->model = $request->model[$i];
            $result->colour = $request->colour[$i];
            $result->size = $request->size[$i];
            $result->seater = $request->seater[$i];
            //
            $result->product_qty = $request->product_qty[$i];
            $result->discount = $request->discount[$i];
            $result->buy_price = $request->buy_price[$i] ?? 0;
            // $result->product_price = $request->product_price[$i];
            $result->retail_price = $request->retail_price[$i];
            $result->exp_date = $request->exp_date[$i];
            $result->unit = $request->item_unit[$i];
            $result->warehouse = $request->warehouse[$i];
            $result->product_category = $request->product_category[$i] ?? '';

            $result->product_name = $request->result_item_name[$i];
            //new column
            $result->discount_category = $request->discount_category[$i];
            $result->discount_amt = $request->discount_amt[$i];
            // $result->foc = $request->foc[$i];

            $result->retail_unit_price = $request->retail_unit_price[$i];

            $result->retail_set_price = $request->retail_set_price[$i];
            $result->promotion_retail_unit = $request->promotion_retail_unit[$i];
            $result->promotion_retail_set = $request->promotion_retail_set[$i];

            $result->price_category = $request->price_category[$i];

            $result->save();
        }



        if ($invoice->status === 'pos' || $invoice->status === 'invoice' || $invoice->status === 'suspend') {
            $count2 = count($request->payment_method);


            $payment_amount = 0;
            for ($i = 0; $i < $count2; $i++) {
                if ($request->paid > 0) {
                    $latestInvoice = MakePayment::whereNotNull('invoice_id')
                        ->where('invoice_no', 'LIKE', 'Cash-%')
                        ->orderByRaw("CAST(SUBSTRING(invoice_no, 6) AS UNSIGNED) DESC")
                        ->first();

                    if ($latestInvoice && preg_match('/Cash-(\d+)/', $latestInvoice->invoice_no, $matches)) {
                        $latestNumber = (int) $matches[1]; // Extract the numeric part
                        $nextInvoiceNo = 'Cash-' . ($latestNumber + 1);
                    } else {
                        $nextInvoiceNo = 'Cash-1'; // Start from 'Cash-1' if no record exists
                    }

                    $make_payments = new MakePayment();
                    $make_payments->payment_method = $request->payment_method[$i];
                    $make_payments->amount = $request->payment_amount[$i];
                    $make_payments->note = $request->remark;
                    $make_payments->invoice_no = $nextInvoiceNo;
                    $make_payments->invoice_id = $invoice->id;
                    $make_payments->invoice_record = $invoice->id;
                    $make_payments->payment_date = $request->invoice_date;
                    $make_payments->save();
                } else {

                    $make_payments = new MakePayment();
                    $make_payments->payment_method = $request->payment_method[$i];
                    $make_payments->amount = $request->payment_amount[$i];
                    $make_payments->note = $request->remark;
                    $make_payments->invoice_no = null;
                    $make_payments->invoice_id = $invoice->id;
                    $make_payments->invoice_record = $invoice->id;
                    $make_payments->payment_date = $request->invoice_date;
                    $make_payments->save();
                }


                $payment_method = new InvoicePaymentMethod();
                $payment_method->invoice_id = $last_id;
                $payment_method->make_payment_id = $make_payments->id;
                $payment_method->payment_method = $request->payment_method[$i];
                $payment_method->payment_amount = $request->payment_amount[$i];
                $payment_method->save();
                $payment_amount += $request->payment_amount[$i];
            }
            $setting_receive = Setting::where('location', $request->branch)->where('category', 'receivable')->first();
            $setting_sale_account = Setting::where('location', $request->branch)->where('category', 'saleaccount')->first();

            if ($setting_receive) {
                $transaction = Transaction::where('location', $setting_receive->location)->where('account_id', $setting_receive->transaction_id)->first();

                if ($payment_amount < $request->total && $transaction->id) {
                    $payment_method = new InvoicePaymentMethod();
                    $payment_method->invoice_id = $last_id;
                    $payment_method->payment_method = $transaction->id;
                    $payment_method->payment_amount = $request->total - $payment_amount;
                    $payment_method->status = 'receivable';
                    $payment_method->save();
                }
            }

            if ($setting_sale_account) {
                $transaction = Transaction::where('location', $setting_sale_account->location)->where('account_id', $setting_sale_account->transaction_id)->first();
                $payment_method = new InvoicePaymentMethod();
                $payment_method->invoice_id = $last_id;
                $payment_method->payment_method = $transaction->id;
                $payment_method->payment_amount = $request->total;
                $payment_method->status = 'saleaccount';
                $payment_method->save();
            }
        } elseif ($invoice->status === 'quotation') { //for quotation payment
            $count3 = count($request->payment_method);
            $payment_amount = 0;
            for ($i = 0; $i < $count3; $i++) {
                $payment_method = new InvoicePaymentMethod();
                $payment_method->quote_no = $request->quote_no;
                $payment_method->payment_method = $request->payment_method[$i];
                $payment_method->payment_amount = $request->payment_amount[$i];
                $payment_method->save();
                $payment_amount
                    += $request->payment_amount[$i];
            }
            $setting_receive = Setting::where('location', $request->branch)->where('category', 'receivable')->first();
            $setting_sale_account = Setting::where('location', $request->branch)->where('category', 'saleaccount')->first();
            if ($setting_receive) {
                $transaction = Transaction::where('location', $setting_receive->location)->where('account_id', $setting_receive->transaction_id)->first();

                if (
                    $payment_amount < $request->total && $transaction->id
                ) {
                    $payment_method = new InvoicePaymentMethod();
                    $payment_method->quote_no = $request->quote_no;
                    $payment_method->payment_method = $transaction->id;
                    $payment_method->payment_amount = $request->total - $payment_amount;
                    $payment_method->status = 'receivable';
                    $payment_method->save();
                }
            }

            if ($setting_sale_account) {
                $transaction = Transaction::where('location', $setting_sale_account->location)->where('account_id', $setting_sale_account->transaction_id)->first();
                $payment_method = new InvoicePaymentMethod();
                $payment_method->quote_no = $request->quote_no;
                $payment_method->payment_method = $transaction->id;
                $payment_method->payment_amount = $request->total;
                $payment_method->status = 'saleaccount';
                $payment_method->save();
            }
        } elseif ($invoice->status === 'customer return') {
            $count2 = count($request->payment_method);
            $payment_amount = 0;
            for ($i = 0; $i < $count2; $i++) {
                $payment_method = new InvoicePaymentMethod();
                $payment_method->invoice_id = $last_id;
                $payment_method->payment_method = $request->payment_method[$i];
                $payment_method->payment_amount = $request->payment_amount[$i];
                $payment_method->save();
                $payment_amount += $request->payment_amount[$i];
            }
            $setting_receive = Setting::where('location', $request->branch)->where('category', 'receivable')->first();
            $setting_sale_account = Setting::where('location', $request->branch)->where('category', 'saleaccount')->first();

            if ($setting_receive) {
                $transaction = Transaction::where('location', $setting_receive->location)->where('account_id', $setting_receive->transaction_id)->first();
                if ($payment_amount < $request->total && $transaction->id) {
                    $payment_method = new InvoicePaymentMethod();
                    $payment_method->invoice_id = $last_id;
                    $payment_method->quote_no = 'Customer Return';
                    $payment_method->payment_method = $transaction->id;
                    $payment_method->payment_amount = $request->total - $payment_amount;
                    $payment_method->status = 'receivable';
                    $payment_method->save();
                }
            }

            if ($setting_sale_account) {
                $transaction = Transaction::where('location', $setting_sale_account->location)->where('account_id', $setting_sale_account->transaction_id)->first();
                $payment_method = new InvoicePaymentMethod();
                $payment_method->quote_no = 'Customer Return';
                $payment_method->invoice_id = $last_id;
                $payment_method->payment_method = $transaction->id;
                $payment_method->payment_amount = $request->total;
                $payment_method->status = 'saleaccount';
                $payment_method->save();
            }
        }



        if ($request->status == 'invoice' || $request->status == 'pos') {
            // dd($request->paid);


        } elseif ($request->status == 'customer return') {
            $count2 = count($request->payment_method);
            for ($i = 0; $i < $count2; $i++) {
                $make_payments = new MakePayment();
                $make_payments->payment_method = $request->payment_method[$i];
                $make_payments->amount = $request->payment_amount[$i];
                $make_payments->note = $request->remark;
                $make_payments->invoice_no = 'Customer Return';
                $make_payments->invoice_id = $invoice->id;
                $make_payments->invoice_record = $invoice->id;
                $make_payments->payment_date = $request->invoice_date;
                $make_payments->save();
            }
        }


        if ($request->status == 'quotation') {
            return redirect('/quotation')->with('success', 'Quotation Added Successful!');
        } elseif ($request->status == 'invoice') {
            foreach ($invoice->sells as $sell) {

                $item = Item::where('id', $sell->item_id)
                    ->where('warehouse_id', $sell->warehouse)
                    ->first();

                if ($item) {
                    $item_variation = ItemVariation::with('item_quantity')->where('item_id', $item->id)
                        ->where('id', $sell->variation_id)
                        ->first();

                    if ($item_variation) {
                        $item_quantity = ItemQuantity::where('item_id', $item_variation->item_id)->where('variation_id', $item_variation->id)->first();

                        if ($request->balance_due == 'Invoice') {
                            $new_quantity = (float)$item_quantity->available_qty - (float)$sell->product_qty;
                            $item_quantity->available_qty = $new_quantity;
                            $item_quantity->deliver_qty += (float)$sell->product_qty;
                        } else {
                            $new_quantity = (float)$item_quantity->available_qty - (float)$sell->product_qty;
                            $item_quantity->available_qty = $new_quantity;

                            $newWarehouseQty = (float)$item_quantity->warehouse_qty - (float)$sell->product_qty;
                            $item_quantity->warehouse_qty = $newWarehouseQty;
                        }

                        $item_quantity->save();
                    }
                } else {
                    continue;
                }
            }

            return redirect('/invoice')->with('success', 'Invoice Added Successfully!');
        } elseif ($invoice->status === 'pos') {


            foreach ($invoice->sells as $sell) {
                $item = Item::where('id', $sell->item_id)
                    ->where('warehouse_id', $sell->warehouse)
                    ->first();

                if ($item) {
                    $item_variation = ItemVariation::where('item_id', $item->id)
                        ->where('id', $sell->variation_id)
                        ->first();

                    if ($item_variation) {
                        $new_quantity = $item_variation->quantity - $sell->product_qty;


                        $item_variation->quantity = $new_quantity;
                        $item_variation->save();
                    }
                } else {
                    continue;
                }
            }


            return redirect()->route('invoice_detail', ['invoice' => $invoice->id])->with('success', 'POS Register Successfully');
        } else if ($invoice->status === 'suspend') {
            return redirect()->back()->with('success', 'Suspend Added Successful!');
        } else {
            foreach ($invoice->sells as $sell) {

                $item = Item::where('id', $sell->item_id)
                    ->where('warehouse_id', $sell->warehouse)
                    ->first();

                if ($item) {
                    $item_variation = ItemVariation::with('item_quantity')->where('item_id', $item->id)
                        ->where('id', $sell->variation_id)
                        ->first();

                    if ($item_variation) {
                        $item_quantity = ItemQuantity::where('item_id', $item_variation->item_id)->where('variation_id', $item_variation->id)->first();


                        $new_quantity = $item_quantity->available_qty + $sell->product_qty;
                        $item_quantity->available_qty = $new_quantity;

                        $newWarehouseQty = $item_quantity->warehouse_qty + $sell->product_qty;

                        $item_quantity->warehouse_qty = $newWarehouseQty;


                        $item_quantity->save();
                    }
                } else {
                    continue;
                }
            }
            return redirect('/customer_return_manage')->with('success', 'Customer Return  Successfully!');
        }
    }

    public function customer_service_search(Request $request)
    {
        $query = $request->get('query');
        $location = $request->location;

        $cacheKey = "customer_service_search_{$query}_{$location}";
        $data = Cache::remember($cacheKey, 60, function () use ($query, $location) {
            return Customer::select('name', 'phno')
                ->where('branch', $location)
                ->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('name', 'LIKE', '%' . $query . '%')
                        ->orWhere('phno', 'LIKE', '%' . $query . '%');
                })
                ->get();
        });

        info($request->location);
        return response()->json($data);
    }

    public function customer_service_search_fill(Request $request)
    {
        $model = $request->model;
        $location = $request->location;

        $cacheKey = "customer_service_search_fill_{$model}_{$location}";

        $responseData = Cache::remember($cacheKey, 60, function () use ($model, $location) {
            $product = Customer::where(function ($query) use ($model) {
                $query->where('name', $model)
                    ->orWhere('phno', $model);
            })
                ->where('branch', $location)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$product) {
                return ['error' => 'Customer not found'];
            }

            return [
                'customer' => $product,
            ];
        });

        if (isset($responseData['error'])) {
            return response()->json($responseData, 404);
        }

        return response()->json($responseData);
    }


    public function quotation_delete($id)
    {
        $quotation = Invoice::find($id);
        Sell::where('invoiceid', $id)->delete();
        InvoicePaymentMethod::where('quote_no', $quotation->quote_no)->delete();  //delete paymentmethod related to quotation
        $quotation->delete();
        return redirect('/quotation')->with('success', 'Quotation Deleted Successful!');
    }
    public function invoice_delete($id)
    {
        $invoice = Invoice::find($id);

        if ($invoice->status == 'invoice' && $invoice->balance_due == 'Invoice') {

            if ($invoice) {
                $oldtotal = $invoice->deposit;

                if ($invoice->transaction_id) {
                    $payment = Payment::where('transaction_id', $invoice->transaction_id)->first();

                    if ($payment) {
                        $payment->amount = $payment->amount - $oldtotal;
                        $payment->save();
                    } else {
                    }
                } else {
                }
            } else {
            }
        } elseif ($invoice->balance_due == 'Po Return') {
            $oldtotal = $invoice->deposit;

            if ($invoice->transaction_id) {
                $tran = Payment::where('transaction_id', $invoice->transaction_id)->get();
                $payment = $tran->skip(3)->first();
                if ($payment) {
                    $payment->amount = $payment->amount - $oldtotal;
                    $payment->save();
                } else {
                }
            } else {
            }
        }


        if (!$invoice) {
            return redirect()->back()->with('error', 'Invoice not found');
        }


        if ($invoice->status == 'invoice' || $invoice->status == 'pos') {
            if ($invoice) {

                //recalculate item qty before delete
                foreach ($invoice->sells as $key => $sell) {

                    $item = Item::where('id', $sell->item_id)
                        ->where('warehouse_id', $sell->warehouse)
                        ->first();


                    if (!$item) {
                        continue;
                    }

                    $item_variation = ItemVariation::with('item_quantity')->where('item_id', $item->id)
                        ->where('id', $sell->variation_id)
                        ->first();

                    if (!$item_variation) {
                        continue;
                    }
                    $newItems[$sell->item_id] = $item_variation;

                    $item_quantity = ItemQuantity::where('item_id', $item_variation->item_id)->where('variation_id', $item_variation->id)->first();

                    // $currentQuantity = $item_variation->quantity;
                    if ($invoice->balance_due == 'Invoice') {

                        $currentQuantity = $item_quantity->available_qty;
                        $newQuantity = $currentQuantity + $sell->product_qty;
                        $currentDeliverQty = $item_quantity->deliver_qty;

                        // if ($invoice->delivery_status != 'done') {
                        //     $newDeliverQty = $currentDeliverQty - $sell->product_qty;
                        //     // $newDeliverQty = $request->input('product_qty')[$key];
                        // } else {


                        if ($sell->delivered_qty == '0') {
                            $currentWarehouseQty = $item_quantity->warehouse_qty;
                            $newWarehouseQty = $currentWarehouseQty;
                            $newDeliverQty = $currentDeliverQty - $sell->product_qty;
                        } elseif ($sell->product_qty >= $sell->delivered_qty) {
                            $currentWarehouseQty = $item_quantity->warehouse_qty;
                            $newWarehouseQty = $currentWarehouseQty + $sell->product_qty;
                            $newDeliverQty = $currentDeliverQty + $sell->delivered_qty - $sell->product_qty;
                        } else {
                            $newDeliverQty = $currentDeliverQty - $sell->product_qty;
                        }
                        // }

                        // $item_variation->quantity = $newQuantity;
                        // $item_variation->save();
                        $item_quantity->available_qty = $newQuantity;
                        $item_quantity->warehouse_qty = $newWarehouseQty;
                        $item_quantity->deliver_qty = $newDeliverQty;
                    } else { //for po return
                        $currentQuantity = $item_quantity->available_qty;
                        $newQuantity = $currentQuantity + $sell->product_qty;
                        $currentWarehouseQty = $item_quantity->warehouse_qty;
                        $newWarehouseQty = $currentWarehouseQty + $sell->product_qty;

                        $item_quantity->available_qty = $newQuantity;
                        $item_quantity->warehouse_qty = $newWarehouseQty;
                    }
                    $item_quantity->save();
                }

                Sell::where('invoiceid', $id)->delete();
                MakePayment::where('invoice_id', $id)->orWhere('invoice_record', $id)->delete();
                InvoicePaymentMethod::where('invoice_id', $invoice->id)->delete();
                $invoice->delete();
                if ($invoice->status == 'pos') {
                    return back()->with('success', 'POS Deleted Successfully!');
                } else {
                    return redirect()->back()->with('success', 'Deleted Successfully!');
                }
            } else {
                return redirect('/invoice')->with('error', 'Invoice Not Found!');
            }
        } elseif ($invoice->status == 'customer return') {
            if ($invoice) {

                //recalculate item qty before delete
                foreach ($invoice->sells as $key => $sell) {

                    $item = Item::where('id', $sell->item_id)
                        ->where('warehouse_id', $sell->warehouse)
                        ->first();

                    $item_variation = ItemVariation::with('item_quantity')->where('item_id', $item->id)
                        ->where('id', $sell->variation_id)
                        ->first();

                    if (!$item_variation) {
                        continue;
                    }
                    $newItems[$sell->item_id] = $item_variation;

                    $item_quantity = ItemQuantity::where('item_id', $item_variation->item_id)->where('variation_id', $item_variation->id)->first();

                    // $currentQuantity = $item_variation->quantity;
                    if ($invoice->balance_due == 'Invoice') {

                        $currentQuantity = $item_quantity->available_qty;
                        $currentQuantityWarehouse = $item_quantity->warehouse_qty;
                        $newQuantity = $currentQuantity - $sell->product_qty;
                        $newQuantityWarehouse = $currentQuantityWarehouse - $sell->product_qty;



                        $item_quantity->available_qty = $newQuantity;
                        $item_quantity->warehouse_qty = $newQuantityWarehouse;
                    }
                    $item_quantity->save();
                }

                Sell::where('invoiceid', $id)->delete();
                MakePayment::where('invoice_id', $id)->orWhere('invoice_record', $id)->delete();
                InvoicePaymentMethod::where('invoice_id', $invoice->id)->delete();
                $invoice->delete();
                if ($invoice->status == 'pos') {
                    return back()->with('success', 'POS Deleted Successfully!');
                } else {
                    return redirect()->back()->with('success', 'Deleted Successfully!');
                }
            } else {
                return redirect('/invoice')->with('error', 'Invoice Not Found!');
            }
        }
    }

    public function quotation_edit($id)
    {
        $quotation = Invoice::find($id);
        $sell = Sell::where('invoiceid', $id)->get();
        $warehouses = Warehouse::all();
        $payment_methods = InvoicePaymentMethod::where('quote_no', $quotation->quote_no)->whereNull('status')->get();
        $setting = Setting::where('category', 'invoice')->where('location', $quotation->branch)->get();
        // dd($setting);
        //


        $transactions = [];
        if ($setting) {
            foreach ($setting as $singleSetting) {
                $transactions[] = Transaction::where('account_id', $singleSetting->transaction_id)
                    ->where('location', $quotation->branch)
                    ->get();
            }
        }

        return view('quotation.quotation_edit', compact('quotation', 'sell', 'warehouses', 'payment_methods', 'transactions'));
    }

    public function suspend_delete($id)
    {
        $suspend = Invoice::find($id);
        Sell::where('invoiceid', $id)->delete();
        InvoicePaymentMethod::where('invoice_id', $id)->delete();
        $suspend->delete();
        return redirect()->back()->with('delete', 'Suspend Deleted Successful!');
    }

    public function invoice_edit($id)
    {
        $invoice = Invoice::find($id);
        $warehouses = Warehouse::select('name', 'id')->get();
        $payment_method = InvoicePaymentMethod::where('invoice_id', $id)->whereNull('status')->get();
        // dd($payment_method);
        $setting = Setting::where('category', 'invoice')->where('location', $invoice->branch)
            ->get();

        $transactions = [];
        if ($setting) {
            foreach ($setting as $singleSetting) {
                $transactions[] = Transaction::where('account_id', $singleSetting->transaction_id)
                    ->where('location', $invoice->branch)
                    ->get();
            }
        }

        if ($invoice->status === 'suspend') {
            $sells = Sell::where('invoiceid', $id)->get();
            return view('invoice.pos_suspend', compact('invoice', 'sells', 'warehouses', 'payment_method', 'transactions'));
        } else if ($invoice->status === 'pos') {
            $sells = Sell::where('invoiceid', $id)->get();
            return view('invoice.pos_edit', compact('invoice', 'sells', 'warehouses', 'payment_method', 'transactions'));
        } else {
            $sell = Sell::where('invoiceid', $id)->get();
            return view('invoice.invoice_edit', compact('invoice', 'sell', 'warehouses', 'payment_method', 'transactions'));
        }
    }


    public function invoice_update(Request $request, $id)
    {

        $invoice = Invoice::find($id);

        if (!$invoice) {
            return redirect()->back()->with('error', 'Invoice not found');
        }

        $invoice->customer_id = $request->customer_id;
        $invoice->customer_name = $request->customer_name;
        $invoice->invoice_category = $request->quote_category;
        $invoice->sale_price_category = $request->sale_price_category;
        $invoice->phno  = $request->phno;
        if ($request->status == 'suspend') {
            $invoice->status = 'pos';
        } else {
            $invoice->status = $request->status;
        }

        $invoice->type  = $request->type;
        $invoice->address  = $request->address;
        $invoice->invoice_no  = $request->invoice_no;
        $invoice->invoice_date = $request->invoice_date;
        $invoice->quote_date = $request->quote_date;
        $invoice->quote_no  = $request->quote_no;
        $invoice->overdue_date  = $request->overdue_date;
        // $invoice->sale_by  = $request->sale_by;
        $invoice->sale_by = auth()->user()->name;
        $invoice->sub_total  = $request->sub_total;
        $invoice->total  = $request->total;
        $invoice->location = $request->location;
        $invoice->branch  = $request->branch;
        $invoice->balance_due  = $request->balance_due;
        $invoice->discount_total  = $request->total_discount;
        $invoice->deposit  = $request->paid;
        $invoice->remain_balance  = $request->balance;
        $invoice->remark = $request->remark;
        $invoice->site = $request->site;

        //extra
        $invoice->currency_method = $request->currency_method;
        $invoice->exchange_rate = $request->exchange_rate;
        $invoice->overall_discount_mmk = $request->overall_discount_mmk;
        $invoice->branch_invoice_no = $request->branch_invoice_no;

        // $invoice->payment_method   = $request->payment_method;
        $invoice->save();

        $sellsData = [];
        $now = Carbon::now();
        if ($request->input('part_number')) {
            foreach ($request->input('part_number') as $key => $partNumber) {
                $sellsData[] = [
                    'part_number' => $partNumber,
                    'description' => $request->input('part_description')[$key],
                    'item_id' => $request->input('item_id')[$key],
                    'variation_id' => $request->input('result_id')[$key],
                    //
                    'product_code' => $request->input('result_product_code')[$key],
                    'model' => $request->input('model')[$key],
                    'colour' => $request->input('colour')[$key],
                    'size' => $request->input('size')[$key],
                    'seater' => $request->input('seater')[$key],
                    'warranty' => $request->input('warranty')[$key],

                    'product_qty' => $request->input('product_qty')[$key],
                    // 'product_price' => $request->input('product_price')[$key],
                    'buy_price' => $request->input('buy_price')[$key] ?? 0,
                    'discount' => $request->input('discount')[$key],
                    'retail_price' => $request->input('retail_price')[$key],
                    'unit' => $request->input('item_unit')[$key],
                    'exp_date' => $request->input('exp_date')[$key],
                    'warehouse' => $request->input('warehouse')[$key],
                    'product_category' => $request->input('product_category')[$key] ?? '',
                    'product_name' => $request->input('result_item_name')[$key],
                    'invoiceid' => $id,
                    'created_at' => $now, // Add created_at field
                    'updated_at' => $now, // Add updated_at field
                    //new column
                    'discount_category' => $request->discount_category[$key],
                    'discount_amt' => $request->discount_amt[$key],
                    // 'foc' => $request->foc[$key],
                    'retail_unit_price' => $request->retail_unit_price[$key],
                    'retail_set_price' => $request->retail_set_price[$key],
                    'promotion_retail_unit' => $request->promotion_retail_unit[$key],
                    'promotion_retail_set' => $request->promotion_retail_set[$key],
                    'price_category' => $request->price_category[$key],
                    'delivered_qty' => $request->delivered_qty[$key] ?? 0 //add new column
                ];
            }
        }

        if ($invoice->status === 'invoice' || $invoice->status === 'pos' || $invoice->status === 'suspend') {
            InvoicePaymentMethod::where('invoice_id', $id)->delete();

            $existingPayments = MakePayment::where('invoice_record', $invoice->id)->get();

            $existingPayments->each->delete();

            if ($request->input('payment_method')) {
                foreach ($request->input('payment_method') as $key => $paymentMethod) {

                    $make_payment = new MakePayment();

                    // Get the invoice_no from the existing payments
                    $oldInvoiceNo = isset($existingPayments[$key]) ? $existingPayments[$key]->invoice_no : null;

                    $make_payment->payment_method = $request->payment_method[$key];
                    $make_payment->amount = $request->payment_amount[$key];
                    $make_payment->note = $request->remark;
                    $make_payment->invoice_no = $oldInvoiceNo;
                    $make_payment->invoice_id = $invoice->id;
                    $make_payment->invoice_record = $invoice->id;
                    $make_payment->payment_date = $request->invoice_date;
                    $make_payment->save();


                    $payment_amount = $request->input('payment_amount')[$key] ?? 0;
                    $payment_method = new InvoicePaymentMethod();
                    $payment_method->invoice_id = $id;
                    $payment_method->make_payment_id = $make_payment->id;
                    $payment_method->payment_method = $paymentMethod;
                    $payment_method->payment_amount = $payment_amount;
                    $payment_method->created_at = $now;
                    $payment_method->updated_at = $now;
                    $payment_method->save();
                }
            }
            $setting_receive = Setting::where('location', $request->branch)->where('category', 'receivable')->first();

            $setting_sale_account = Setting::where('location', $request->branch)->where('category', 'saleaccount')->first();

            if ($setting_receive) {
                $transaction = Transaction::where('location', $setting_receive->location)->where('account_id', $setting_receive->transaction_id)->first();

                if (
                    $payment_amount < $request->total && $transaction->id
                ) {
                    $payment_method = new InvoicePaymentMethod();
                    $payment_method->invoice_id = $invoice->id;
                    $payment_method->payment_method = $transaction->id;
                    $payment_method->payment_amount = $request->balance;
                    $payment_method->status = 'receivable';
                    $payment_method->save();
                }
            }

            if ($setting_sale_account) {
                $transaction = Transaction::where('location', $setting_sale_account->location)->where('account_id', $setting_sale_account->transaction_id)->first();
                $payment_method = new InvoicePaymentMethod();
                $payment_method->invoice_id = $invoice->id;
                $payment_method->payment_method = $transaction->id;
                $payment_method->payment_amount = $request->total;
                $payment_method->status = 'saleaccount';
                $payment_method->save();
            }

            //update paymentmethod for quotation
        } elseif ($invoice->status === 'quotation') {
            InvoicePaymentMethod::where('quote_no', $invoice->quote_no)->delete();
            if ($request->input('payment_method')) {
                foreach ($request->input('payment_method') as $key => $paymentMethod) {
                    $payment_amount = $request->input('payment_amount')[$key] ?? 0;
                    $payment_method = new InvoicePaymentMethod();
                    $payment_method->quote_no = $request->quote_no;
                    $payment_method->payment_method = $paymentMethod;
                    $payment_method->payment_amount = $payment_amount;
                    $payment_method->created_at = $now;
                    $payment_method->updated_at = $now;
                    $payment_method->save();
                }
            }
        }


        // if ($invoice->status === 'invoice') {
        //     $oldQuantities = [];
        //     $oldItems = [];
        //     $newItems = [];
        //     foreach ($invoice->sells as $key => $sell) {
        //         $oldQuantities[$sell->item_id][$sell->variation_id] = $sell->product_qty;
        //         $oldItems[$sell->variation_id] = $sell->item_id . '-' . $sell->variation_id;
        //     }


        //     foreach ($request->input('item_id') as $key => $itemID) {

        //         $item = Item::where('id', $itemID)
        //             ->where('warehouse_id', $request->warehouse[$key])
        //             ->first();

        //         $item_variation = ItemVariation::with('item_quantity')->where('item_id', $item->id)
        //             ->where('id', $request->result_id[$key])
        //             ->first();
        //         if (!$item_variation) {
        //             continue;
        //         }
        //         $newItems[$itemID . '-' . $item_variation->id] = $item_variation->id;


        //         $item_quantity = ItemQuantity::where('item_id', $item_variation->item_id)->where('variation_id', $item_variation->id)->first();

        //         if (!$item_quantity) {
        //             continue;
        //         }
        //         // $currentQuantity = $item_variation->quantity;
        //         if ($request->balance_due == 'Invoice') {

        //             $currentQuantity = $item_quantity->warehouse_qty;
        //             $newQuantity = $currentQuantity + ($oldQuantities[$itemID][$item_variation->id] ?? 0) - $request->input('product_qty')[$key];
        //             // $currentDeliverQty = $item_quantity->deliver_qty;

        //             // if ($invoice->delivery_status != 'done') {
        //             //     $newDeliverQty = ($currentDeliverQty - ($oldQuantities[$itemID][$item_variation->id] ?? 0)) + $request->input('product_qty')[$key];
        //             //     // $newDeliverQty = $request->input('product_qty')[$key];
        //             // } else {
        //             //     $currentWarehouseQty = $item_quantity->warehouse_qty;
        //             //     $newWarehouseQty = $currentWarehouseQty + ($oldQuantities[$itemID][$item_variation->id] ?? 0) - $request->input('product_qty')[$key];
        //             //     //check whether new quantity is minus value
        //             //     if ($newWarehouseQty < 0) {
        //             //         $item_quantity->warehouse_qty = 0;
        //             //     } else {
        //             //         $item_quantity->warehouse_qty = $newWarehouseQty;
        //             //     }
        //             //     $newDeliverQty = $currentDeliverQty;
        //             // }


        //             $item_quantity->warehouse_qty = $newQuantity;
        //             // $item_quantity->deliver_qty = $newDeliverQty;
        //         } else { //for po return
        //             // $currentQuantity = $item_quantity->available_qty;
        //             // $newQuantity = $currentQuantity + ($oldQuantities[$itemID][$item_variation->id] ?? 0) - $request->input('product_qty')[$key];
        //             $currentWarehouseQty = $item_quantity->warehouse_qty;
        //             $newWarehouseQty = $currentWarehouseQty + ($oldQuantities[$itemID][$item_variation->id] ?? 0) - $request->input('product_qty')[$key];

        //             // $item_quantity->warehouse_qty = $newQuantity;

        //             if ($newWarehouseQty < 0) {
        //                 $item_quantity->warehouse_qty = 0;
        //             } else {
        //                 $item_quantity->warehouse_qty = $newWarehouseQty;
        //             }
        //         }
        //         $item_quantity->save();
        //     }
        // } elseif ($request->status == 'suspend') {
        //     foreach ($invoice->sells as $sell) {
        //         $item = Item::where('id', $sell->item_id)
        //             ->where('warehouse_id', $sell->warehouse)
        //             ->first();

        //         if ($item) {
        //             $item_variation = ItemVariation::where('item_id', $item->id)
        //                 ->where('id', $sell->variation_id)
        //                 ->first();

        //             if ($item_variation) {
        //                 $item_quantity = ItemQuantity::where('item_id', $item_variation->item_id)->where('variation_id', $item_variation->id)->first();

        //                 if ($request->balance_due == 'Invoice') {
        //                     $new_quantity = $item_quantity->available_qty - $sell->product_qty;
        //                     $warehouse_qty = $item_quantity->warehouse_qty - $sell->product_qty;
        //                     // $item_variation->item_quantity->available_qty = $new_quantity;
        //                     // $item_variation->save();
        //                     $item_quantity->available_qty = $new_quantity;
        //                     $item_quantity->warehouse_qty = $warehouse_qty;
        //                 } else { //for po return
        //                     $new_quantity = $item_quantity->available_qty - $sell->product_qty;
        //                     $item_quantity->available_qty = $new_quantity;

        //                     $newWarehouseQty = $item_quantity->warehouse_qty - $sell->product_qty;
        //                     if ($newWarehouseQty < 0) {
        //                         $item_quantity->warehouse_qty = 0;
        //                     } else {
        //                         $item_quantity->warehouse_qty = $newWarehouseQty;
        //                     }
        //                 }
        //                 $item_quantity->save();
        //             }
        //         } else {
        //             continue;
        //         }
        //     }
        //     return redirect()->route('invoice_detail', ['invoice' => $invoice->id])->with('success', 'POS Register Successfully');
        // } elseif ($request->status == 'pos') {
        //     $oldQuantities = [];
        //     $oldItems = [];
        //     $newItems = [];
        //     foreach ($invoice->sells as $key => $sell) {
        //         $oldQuantities[$sell->item_id][$sell->variation_id] = $sell->product_qty;
        //         $oldItems[$sell->variation_id] = $sell->item_id . '-' . $sell->variation_id;
        //     }


        //     foreach ($request->input('item_id') as $key => $itemID) {

        //         $item = Item::where('id', $itemID)
        //             ->where('warehouse_id', $request->warehouse[$key])
        //             ->first();

        //         $item_variation = ItemVariation::with('item_quantity')->where('item_id', $item->id)
        //             ->where('id', $request->result_id[$key])
        //             ->first();

        //         if (!$item_variation) {
        //             continue;
        //         }
        //         $newItems[$itemID . '-' . $item_variation->id] = $item_variation->id;


        //         $item_quantity = ItemQuantity::where('item_id', $item_variation->item_id)->where('variation_id', $item_variation->id)->first();

        //         if (!$item_quantity) {
        //             continue;
        //         }
        //         // $currentQuantity = $item_variation->quantity;


        //         $currentQuantity = $item_quantity->available_qty;
        //         $newQuantity = $currentQuantity + ($oldQuantities[$itemID][$item_variation->id] ?? 0) - $request->input('product_qty')[$key];


        //         $currentWarehouseQty = $item_quantity->warehouse_qty;
        //         $newWarehouseQty = $currentWarehouseQty + ($oldQuantities[$itemID][$item_variation->id] ?? 0) - $request->input('product_qty')[$key];

        //         $item_quantity->available_qty = $newQuantity;
        //         $item_quantity->warehouse_qty = $newWarehouseQty;

        //         $item_quantity->save();
        //     }
        // }

        if ($invoice->status === 'invoice') {
            $oldQuantities = [];
            $oldItems = [];
            $newItems = [];
            foreach ($invoice->sells as $key => $sell) {
                $oldQuantities[$sell->item_id][$sell->variation_id] = $sell->product_qty;
                $oldItems[$sell->variation_id] = $sell->item_id . '-' . $sell->variation_id;
            }


            foreach ($request->input('item_id') as $key => $itemID) {

                $item = Item::where('id', $itemID)
                    ->where('warehouse_id', $request->warehouse[$key])
                    ->first();


                if (!$item) {
                    continue;
                }
                $item_variation = ItemVariation::with('item_quantity')->where('item_id', $item->id)
                    ->where('id', $request->result_id[$key])
                    ->first();

                if (!$item_variation) {
                    continue;
                }
                $newItems[$itemID . '-' . $item_variation->id] = $item_variation->id;


                $item_quantity = ItemQuantity::where('item_id', $item_variation->item_id)->where('variation_id', $item_variation->id)->first();

                if (!$item_quantity) {
                    continue;
                }
                // $currentQuantity = $item_variation->quantity;
                if ($request->balance_due == 'Invoice') {

                    $currentQuantity = $item_quantity->available_qty;
                    $newQuantity = $currentQuantity + ($oldQuantities[$itemID][$item_variation->id] ?? 0) - $request->input('product_qty')[$key];
                    $currentDeliverQty = $item_quantity->deliver_qty;

                    if ($invoice->delivery_status != 'done') {
                        $newDeliverQty = ($currentDeliverQty - ($oldQuantities[$itemID][$item_variation->id] ?? 0)) + $request->input('product_qty')[$key];
                        // $newDeliverQty = $request->input('product_qty')[$key];
                    } else {
                        $currentWarehouseQty = $item_quantity->warehouse_qty;
                        $newWarehouseQty = $currentWarehouseQty + ($oldQuantities[$itemID][$item_variation->id] ?? 0) - $request->input('product_qty')[$key];
                        //check whether new quantity is minus value

                        $item_quantity->warehouse_qty = $newWarehouseQty;

                        $newDeliverQty = $currentDeliverQty;
                    }


                    $item_quantity->available_qty = $newQuantity;
                    $item_quantity->deliver_qty = $newDeliverQty;
                } else { //for po return
                    $currentQuantity = $item_quantity->available_qty;
                    $newQuantity = $currentQuantity + ($oldQuantities[$itemID][$item_variation->id] ?? 0) - $request->input('product_qty')[$key];
                    $currentWarehouseQty = $item_quantity->warehouse_qty;
                    $newWarehouseQty = $currentWarehouseQty + ($oldQuantities[$itemID][$item_variation->id] ?? 0) - $request->input('product_qty')[$key];

                    $item_quantity->available_qty = $newQuantity;


                    $item_quantity->warehouse_qty = $newWarehouseQty;
                }
                $item_quantity->save();
            }
            // dd($oldItems);
            //recalculate qty for remove item
            foreach ($oldItems as $key => $oldItem) {
                if (!array_key_exists($oldItem, $newItems)) {

                    // $item = Item::where('id', $oldItem)->first();

                    // $item_variation = ItemVariation::with('item_quantity')->where('item_id', $item->id)
                    //     ->where('id',$key)
                    //     ->first();
                    $item_variation = ItemVariation::where('id', $key)->first();

                    if ($item_variation) {
                        $item_quantity = ItemQuantity::where('item_id', $item_variation->item_id)->where('variation_id', $item_variation->id)->first();

                        $item_quantity->available_qty += $oldQuantities[$item_variation->item_id][$item_variation->id] ?? 0;

                        if ($invoice->delivery_status != 'done') {
                            $item_quantity->deliver_qty -= $oldQuantities[$item_variation->item_id][$item_variation->id] ?? 0;
                        } else {
                            $item_quantity->warehouse_qty += $oldQuantities[$item_variation->item_id][$item_variation->id] ?? 0;
                        }
                        $item_quantity->save();
                    }
                }
            }
        } elseif ($request->status == 'pos') {

            foreach ($request->input('item_id') as $key => $itemID) {
                $item = Item::where('id', $itemID)
                    ->where('warehouse_id', $request->warehouse[$key])
                    ->first();


                if ($item) {
                    $item_variation = ItemVariation::where('item_id', $item->id)
                        ->where('id', $request->result_id[$key])
                        ->first();

                    if ($item_variation) {
                        $new_quantity = $item_variation->quantity - $request->input('product_qty')[$key];

                        $item_variation->quantity = $new_quantity;
                        $item_variation->save();
                    }
                } else {
                    continue;
                }
            }
        }

        Sell::where('invoiceid', $id)->delete();
        Sell::insert($sellsData);

        $delivered_items = DeliveredItems::where('invoice_id', $id)->get();
        foreach ($delivered_items as $delivered) {
            // Find the matching Sell record
            $sell = Sell::where('invoiceid', $id)
                ->where('item_id', $delivered->item_id)
                ->where('variation_id', $delivered->variation_id)
                ->first();

            if ($sell) {
                // Update the sell_id in the delivered item
                $delivered->sell_id = $sell->id;
                $delivered->save();
            }
        }

        if ($invoice->status === 'quotation') {
            return redirect('quotation')->with('success', 'Quotation Update Successful');
        } elseif ($invoice->status === 'invoice') {
            return redirect('invoice')->with('success', 'Invoice Update Successful');
        } elseif ($invoice->status === 'pos') {
            return redirect(url('invoice_detail', $invoice->id))->with('success', 'POS Update Successful');
        }
        return redirect()->back()->with('error', 'Failed to update invoice');
    }

    public function change_invoice($id)
    {
        // Retrieve all invoices marked as "invoice" with trashed records
        $invoice = Invoice::where('status', 'invoice')->get();

        // Find the invoice by its ID
        $invoices = Invoice::find($id);

        // Adjust item quantities based on the sells in the invoice
        foreach ($invoices->sells as $sell) {
            $item = Item::where('id', $sell->item_id)
                ->where('warehouse_id', $sell->warehouse)
                ->first();

            if ($item) {
                $item_variation = ItemVariation::where('item_id', $item->id)
                    ->where('id', $sell->variation_id)
                    ->first();

                $item_quantity = ItemQuantity::where('item_id', $item_variation->item_id)
                    ->where('variation_id', $item_variation->id)
                    ->first();

                if ($item_quantity) {
                    // Update available and delivered quantities
                    $new_quantity = $item_quantity->available_qty - $sell->product_qty;
                    $item_quantity->available_qty = $new_quantity;
                    $item_quantity->deliver_qty += $sell->product_qty;
                    $item_quantity->save();
                }
            }
        }

        // Update payment methods associated with the invoice
        $payments = InvoicePaymentMethod::where('quote_no', $invoices->quote_no)->get();
        foreach ($payments as $payment_method) {


            $latestInvoiceNo = MakePayment::whereNotNull('invoice_id')
                ->where('invoice_no', 'LIKE', 'Cash-%')
                ->max('invoice_no');

            if ($latestInvoiceNo) {
                $latestNumber = (int) str_replace('Cash-', '', $latestInvoiceNo);
                $nextInvoiceNo = 'Cash-' . ($latestNumber + 1);
            } else {
                $nextInvoiceNo = 'Cash-1';
            }

            $make_payment = new MakePayment();
            $make_payment->payment_method = $payment_method->payment_method;
            $make_payment->amount = $payment_method->payment_amount;
            $make_payment->note = $invoices->remark;
            $make_payment->invoice_no = $nextInvoiceNo;
            $make_payment->invoice_id = $payment_method->status === 'receivable' || $payment_method->status === 'saleaccount'
                ? null
                : $invoices->id;
            $make_payment->invoice_record = $invoices->id;
            $make_payment->payment_date = $invoices->invoice_date;
            $make_payment->save();

            $payment_method->make_payment_id = $make_payment->id;
            $payment_method->quote_no = null;
            $payment_method->invoice_id = $id;
            $payment_method->update();
        }






        $latestInvoiceNo = Invoice::where('status', 'invoice')
            ->where('invoice_no', 'LIKE', 'Invoice-%')
            ->where('branch', $invoices->branch)
            ->max('invoice_no');

        if ($latestInvoiceNo) {
            // Extract the numeric part and increment
            $latestNumber = (int) str_replace('Invoice-', '', $latestInvoiceNo);
            $nextInvoiceNo = 'Invoice-' . ($latestNumber + 1);
        } else {
            // Start from Invoice-1 if no previous records exist
            $nextInvoiceNo = 'Invoice-1';
        }

        echo $nextInvoiceNo;
        $quotation = Invoice::find($id);
        $quotation->status = 'invoice';
        $invoice_no =  "Invoice-" . count($invoice) + 1;
        $quotation = Invoice::find($id);
        $quotation->status = 'invoice';
        $quotation->invoice_no = $nextInvoiceNo;
        $quotation->balance_due = 'Invoice';
        $quotation->invoice_date = Carbon::today()->format('Y-m-d');
        $quotation->update();

        return redirect('/invoice')->with('success', 'Change Invoice Successful!');
    }



    //invoice , quotation , purchaseOrder
    public function autocompletePartCodeInvoice(Request $request)
    {
        $query = $request->get('query');
        $location = $request->get('location');

        $cacheKey = "autocomplete_{$query}_{$location}";
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
                ->with('item:id,item_name,item_descriptions')
                ->get(['item_id', 'product_code', 'id', 'model', 'colour', 'size', 'seater', 'variation_desc'])
                ->map(function ($variation) {
                    return [
                        'item_name' => $variation->item ? $variation->item->item_name : 'Unknown Item',
                        'description' => $variation->item->item_descriptions,
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



    public function getPartDataInvoice(Request $request)
    {
        try {
            $item_id = $request->item_id;
            $variation_id = $request->variation_id;
            $location = $request->location;

            $cacheKey = "part_data_{$item_id}_{$location}_{$variation_id}";
            $result = Cache::remember($cacheKey, 60, function () use ($item_id, $variation_id) {
                $item = Item::find($item_id);

                if ($item) {
                    $variation = ItemVariation::where('id', $variation_id)->where('item_id', $item_id)->first();

                    $item_qty = ItemQuantity::where('item_id', $variation->item_id)
                        ->where('variation_id', $variation->id)
                        ->first();

                    if ($variation) {
                        return [

                            'product_code' => $variation->product_code,
                            'variation_desc' => $variation->variation_desc,
                            'model' => $variation->model,
                            'colour' => $variation->colour,
                            'size' => $variation->size,
                            'seater' => $variation->seater,
                            'warranty' => $variation->warranty,
                            'item_unit' => $variation->unit,
                            'wholesale_price' => $variation->wholesale_price,
                            'retail_set_price' => $variation->retail_set_price,
                            'promotion_retail_unit' => $variation->promotion_retail_unit,
                            'promotion_retail_set' => $variation->promotion_retail_set,
                            'retail_price' => $variation->retail_price,
                            'expired_date' => $variation->expired_date,
                            'buy_price' => $variation->buy_price,
                            'warehouse_id' => $item->warehouse_id,
                            'descriptions' => $item->item_descriptions,
                            'quantity' => $item_qty->warehouse_qty,
                            'reorder_level_stock' => $item_qty->alert_qty,
                        ];
                    }
                }

                return null;
            });

            if (!$result) {
                return response()->json(['error' => 'Product not found'], 404);
            }

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }



    // public function getPartDataInvoice(Request $request)
    // {
    //     $itemName = $request->item_name;
    //     $location = $request->location;

    //     $cacheKey = "part_data_{$itemName}_{$location}";

    //     $result = Cache::remember($cacheKey, 60, function () use ($itemName, $location) {
    //         return Item::where('item_name', $itemName)
    //             ->where('warehouse_id', $location)
    //             ->first();
    //     });

    //     if (!$result) {
    //         return response()->json(['error' => 'Product not found'], 404);
    //     }

    //     return response()->json($result);
    // }

    // public function getPartDataInvoice(Request $request)
    // {
    //     $itemName = $request->item_name;
    //     $location = $request->location;

    //     $itemParts = explode('-', $itemName);
    //     info($itemParts);

    //     $cacheKey = "part_data_" . implode('_', $itemParts) . "_{$location}";

    //     $result = Cache::remember($cacheKey, 60, function () use ($itemParts, $location) {
    //         return Item::whereIn('item_name', $itemParts)
    //             ->where('warehouse_id', $location)
    //             ->first();
    //     });

    //     if (!$result) {
    //         return response()->json(['error' => 'Product not found'], 404);
    //     }

    //     return response()->json($result);
    // }

    //End invoice , quotation , purchaseOrder


    //Pos
    public function autocompletePartCode(Request $request)
    {
        $query = $request->get('query');
        $location = $request->get('location');

        $cacheKey = "autocomplete_partcode_{$query}_{$location}";

        $items = Cache::remember($cacheKey, 60, function () use ($query, $location) {
            $itemIds = Item::where('item_name', 'like', '%' . $query . '%')
                ->where('warehouse_id', $location)
                ->pluck('id');

            return ItemVariation::whereIn('item_id', $itemIds)
                ->get(['item_id', 'descriptions', 'product_code', 'id'])
                ->map(function ($variation) {
                    $item = Item::find($variation->item_id);
                    return [
                        'item_name' => $item ? $item->item_name : 'Unknown Item',
                        'description' => $variation->descriptions,
                        'product_code' => $variation->product_code,
                        'id' => $variation->id,
                        'item_id' => $variation->item_id,
                    ];
                });
        });

        return response()->json($items);
    }

    public function autocompleteBarCode(Request $request)
    {
        $query = $request->get('query');
        $location = $request->get('location');

        $barcodeResults = Cache::remember("autocomplete_barcode_{$query}_{$location}", 60, function () use ($query, $location) {
            return ItemVariation::where('variations_barcode', 'like', '%' . $query . '%')
                ->whereHas('item', function ($query) use ($location) {
                    $query->where('warehouse_id', $location);
                })
                ->with('item')
                ->get()
                ->map(function ($variation) {
                    $item = $variation->item;
                    return [
                        'item_name' => $item ? $item->item_name : 'Unknown Item',
                        'variations_barcode' => $variation->variations_barcode,
                        'description' => $variation->descriptions,
                        'product_code' => $variation->product_code,
                        'id' => $variation->id,
                        'item_id' => $variation->item_id,
                    ];
                });
        });

        return response()->json($barcodeResults);
    }




    // public function autocompleteBarCode(Request $request)
    // {
    //     $query = $request->get('query');

    //     $location = $request->get('location');
    //     $barcode = Cache::remember("items_{$query}_{$location}", 60, function () use ($query, $location) {
    //         return  Item::where('barcode', 'like', '%' . $query . '%')->where('warehouse_id', $location)
    //             ->pluck('barcode');
    //     });
    //     info($barcode);
    //     return response()->json($barcode);
    // }

    public function getPartData(Request $request)
    {
        $itemName = $request->itemname;
        $location = $request->location;
        $description = $request->description;
        $productCode = $request->product_code;


        $cacheKey = "part_data_pos_{$itemName}_{$location}_{$description}_{$productCode}";

        $itemData = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($itemName, $location, $description, $productCode) {
            $item = Item::where('item_name', $itemName)
                ->where('warehouse_id', $location)
                ->first();

            if ($item) {
                $variations = ItemVariation::where('item_id', $item->id)
                    ->where('variation_desc', $description)
                    ->where('product_code', $productCode)
                    ->get();

                return [
                    'item' => $item,
                    'variations' => $variations,
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
                // 'item_unit' => $itemData['variations']->unit,
                'warehouse_id' => $itemData['item']->warehouse_id,
            ],
            'variations' => $itemData['variations']->map(function ($variation) {
                return [
                    'descriptions' => $variation->variation_desc,
                    'variations_barcode' => $variation->barcode,
                    'model' => $variation->model,
                    'colour' => $variation->colour,
                    'size' => $variation->size,
                    'seater' => $variation->seater,
                    'id' => $variation->id,
                    'item_unit' => $variation->unit,
                    'product_code' => $variation->product_code,
                    'wholesale_price' => $variation->wholesale_price,
                    'retail_set_price' => $variation->retail_set_price,
                    'promotion_retail_unit' => $variation->promotion_retail_unit,
                    'promotion_retail_set' => $variation->promotion_retail_set,
                    'retail_price' => $variation->retail_price,
                    'expired_date' => $variation->expired_date,
                    'buy_price' => $variation->buy_price,
                    'quantity' => $variation->quantity,
                    'reorder_level_stock' => $variation->reorder_level_stock,
                ];
            }),
        ];

        return response()->json($resdata);
    }



    public function getBarcodeData(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
            'location' => 'required|integer',
        ]);

        $barcode = $request->barcode;
        $location = $request->location;

        $cacheKey = "variations_data_{$barcode}_{$location}";

        $variationsData = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($barcode, $location) {
            return ItemVariation::where('barcode', $barcode)
                ->join('items', 'item_variations.item_id', '=', 'items.id')
                ->where('items.warehouse_id', $location)
                ->select('item_variations.*', 'items.item_name', 'items.warehouse_id')
                ->get();
        });

        if ($variationsData->isEmpty()) {
            return response()->json(['error' => 'No variations found for this barcode'], 404);
        }

        $resdata = [
            'item' => [
                'item_name' => $variationsData->first()->item_name,
                'id' => $variationsData->first()->item_id,
                'item_unit' => $variationsData->first()->item_unit,
                'warehouse_id' => $variationsData->first()->warehouse_id,
            ],
            'variations' => $variationsData->map(function ($variation) {
                return [
                    'descriptions' => $variation->variation_desc,
                    'variations_barcode' => $variation->barcode,
                    'model' => $variation->model,
                    'colour' => $variation->colour,
                    'size' => $variation->size,
                    'seater' => $variation->seater,
                    'id' => $variation->id,
                    'item_unit' => $variation->unit,
                    'product_code' => $variation->product_code,
                    'wholesale_price' => $variation->wholesale_price,
                    'retail_set_price' => $variation->retail_set_price,
                    'promotion_retail_unit' => $variation->promotion_retail_unit,
                    'promotion_retail_set' => $variation->promotion_retail_set,
                    'retail_price' => $variation->retail_price,
                    'expired_date' => $variation->expired_date,
                    'buy_price' => $variation->buy_price,
                    'quantity' => $variation->quantity,
                    'reorder_level_stock' => $variation->reorder_level_stock,
                ];
            }),
        ];

        // Return the JSON response
        return response()->json($resdata);
    }

    //End Pos

    // public function invoice_detail(Invoice $invoice)
    public function invoice_detail(Invoice $invoice)

    {

        if ($invoice->status === 'pos') {
            $profile = UserProfile::all();
            $sells = Sell::where('invoiceid', $invoice->id)->get();
            $invoices = Invoice::where('id', $invoice->id)->get();
            $payment_methods = InvoicePaymentMethod::where('invoice_id', $invoice->id)->get();
            return view('invoice.pos_detail', [
                'invoice' => $invoice,
                'invoices' => $invoices,
                'sells' => $sells,
                'profile' => $profile,
                'payment_methods' => $payment_methods,
            ]);
        } else {
            $invoices = Invoice::where('id', $invoice->id)->get();
            $payment_methods = InvoicePaymentMethod::where('invoice_id', $invoice->id)->get();
            $profile = UserProfile::all();
            $sells = Sell::where('invoiceid', $invoice->id)->get();
            $make_payments = MakePayment::where('invoice_id', $invoice->id)->get();
            $payment_category = MakePayment::with('transaction')->where('invoice_id', $invoice->id)->distinct('payment_method')->get(['payment_method']);
            // dd($payment_category);

            $return_data = [
                'invoice' => $invoice,
                'invoices' => $invoices,
                'sells' => $sells,
                'profile' => $profile,
                'payment_methods' => $payment_methods,
                'make_payments' => $make_payments,
                'payment_category' => $payment_category
            ];

            $warehouse_name = strtolower($invoice->warehouse->name);

            $keywords = ['casabella', 'casabel', 'csy', 'csm', 'csn'];
            $keywords2 = ['goldcoil', 'goldc', 'gold', 'gc'];

            if ($this->str_include($warehouse_name, $keywords)) {
                return view('invoice.invoice_details', $return_data);
            } elseif ($this->str_include($warehouse_name, $keywords2)) {
                return view('invoice.invoice_details_2', $return_data);
            } else {
                return view('invoice.invoice_details_3', $return_data);
            }
        }
    }

    //find the name
    public function str_include($haystack, $needles)
    {
        foreach ($needles as $needle) {
            if (stripos($haystack, $needle) !== false) { // Case-insensitive search
                return true;
            }
        }
        return false;
    }

    public function invoice_receipt_print(Invoice $invoice)

    {
        $profile = UserProfile::all();
        $sells = Sell::where('invoiceid', $invoice->id)->get();
        $invoices = Invoice::where('id', $invoice->id)->get();
        $payment_methods = InvoicePaymentMethod::where('invoice_id', $invoice->id)->get();
        return view('invoice.invoice_receipt_print', [
            'invoice' => $invoice,
            'invoices' => $invoices,
            'sells' => $sells,
            'profile' => $profile,
            'payment_methods' => $payment_methods,
        ]);
    }


    public function daily_sales()
    {


        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if (auth()->user()->is_admin == '1') {
            $daily_pos = Invoice::whereDate('created_at', Carbon::today())->where('status', 'pos')->get();
            $branchs = Warehouse::select('name', 'id')->get();
        } else {
            $daily_pos = Invoice::whereDate('created_at', Carbon::today())->whereIn('branch', $warehousePermission)->where('status', 'pos')->get();
            $branchs = Warehouse::select('name', 'id')->get();
        }

        return view('invoice.daily_sales', compact('daily_pos', 'branchs'));
    }
    public function item_search(Request $request)
    {
        $query = $request->get('query');
        $warehouse = $request->get('warehouse');

        $cacheKey = "item_search_{$query}_{$warehouse}";

        $data = Cache::remember($cacheKey, 60, function () use ($query, $warehouse) {
            return Item::select('item_name')
                ->where('warehouse_id', $warehouse)
                ->where('item_name', 'LIKE', '%' . $query . '%')
                ->where('parent_id', 0)
                ->pluck('item_name');
        });

        return response()->json($data);
    }

    public function item_data_search_fill(Request $request)
    {
        $model = $request->model;
        $location = $request->location;

        $cacheKey = "item_data_search_fill_{$model}_{$location}";

        $responseData = Cache::remember($cacheKey, 60, function () use ($model, $location) {
            $product = Item::where('item_name', $model)
                ->where('warehouse_id', $location)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$product) {
                return ['error' => 'Item not found'];
            }

            $warehouse = Warehouse::find($product->warehouse_id);
            $variation = ItemVariation::with('item_quantity')->where('item_id', $product->id)->first();
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


    public function quotation_detail($id)
    {
        $quotation = Invoice::find($id);
        $sells = Sell::where('invoiceid', $id)->get();
        $profile = UserProfile::all();
        $payment_methods = InvoicePaymentMethod::where('quote_no', $quotation->quote_no)->get();
        // $make_payments = MakePayment::where('invoice_id',$quotation->id)->get();
        $payment_category = InvoicePaymentMethod::with('transaction')->where('quote_no', $quotation->quote_no)->distinct('payment_method')->get(['payment_method']);
        return view('quotation.quotation_details', compact('quotation', 'sells', 'profile', 'payment_methods', 'payment_category'));
    }

    public function sale_return()
    {

        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if (auth()->user()->is_admin == '1') {
            $today = Carbon::today();
            $po = PurchaseOrder::where('quote_no', 'like', 'SR%')
                ->whereDate('created_at', $today)
                ->latest()
                ->get();

            $po_total = PurchaseOrder::where('quote_no', 'like', 'SR%')
                ->whereDate('created_at', $today)
                ->sum('total');
            $branchs = Warehouse::all();
        } else {
            $today = Carbon::today();
            $po = PurchaseOrder::where('quote_no', 'like', 'SR%')
                ->whereDate('created_at', $today)
                ->whereIn('branch', $warehousePermission)
                ->latest()
                ->get();

            $po_total = PurchaseOrder::where('quote_no', 'like', 'SR%')
                ->whereDate('created_at', $today)
                ->whereIn('branch', $warehousePermission)
                ->sum('total');
            $branchs = Warehouse::all();
        }

        return view('invoice.sale_return', compact('po', 'po_total', 'branchs'));
    }

    public function sale_return_search(Request $request)
    {


        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if (auth()->user()->is_admin == '1') {
            $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
            $endDate = Carbon::parse($request->input('end_date'))->endOfDay();

            $po = PurchaseOrder::where('quote_no', 'like', 'SR%')
                ->whereBetween('po_date', [$startDate, $endDate])
                ->latest()
                ->get();

            $po_total = PurchaseOrder::where('quote_no', 'like', 'SR%')
                ->whereBetween('po_date', [$startDate, $endDate])
                ->sum('total');
            $branchs = Warehouse::all();
        } else {

            $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
            $endDate = Carbon::parse($request->input('end_date'))->endOfDay();

            $po = PurchaseOrder::where('quote_no', 'like', 'SR%')
                ->whereBetween('po_date', [$startDate, $endDate])
                ->whereIn('branch', $warehousePermission)
                ->latest()
                ->get();

            $po_total = PurchaseOrder::where('quote_no', 'like', 'SR%')
                ->whereBetween('po_date', [$startDate, $endDate])
                ->whereIn('branch', $warehousePermission)
                ->sum('total');
            $branchs = Warehouse::all();
        }


        return view('invoice.sale_return', compact('po', 'po_total', 'branchs'));
    }



    public function sale_return_register()
    {
        $po_number = PurchaseOrder::withTrashed()->where('quote_no', 'like', 'SR%')->latest()->get();
        $units = Unit::all();
        $po_no = 'SR-' . (count($po_number) + 1);
        $warehouses = Warehouse::all();
        return view('invoice.sale_return_register', compact('po_no', 'units', 'warehouses'));
    }

    public function sale_return_edit($id)
    {
        $suppliers = Supplier::all();
        $purchase_orders = PurchaseOrder::find($id);
        $purchase_sells = PO_sells::where('invoiceid', $id)->get();
        $warehouses = Warehouse::all();
        $payment_method = PurchaseOrderPaymentMethod::where('po_id', $id)->get();

        return view('invoice.sale_return_edit', compact('purchase_orders', 'suppliers', 'purchase_sells', 'warehouses', 'payment_method'));
    }

    public function sale_return_detail($id)
    {
        $purchase_order = PurchaseOrder::find($id);
        $purchase_sells = PO_sells::where('invoiceid', $id)->get();
        $profile = UserProfile::all();
        $payment_methods = PurchaseOrderPaymentMethod::where('po_id', $id)->get();
        return view('invoice.sale_return_detail', compact('purchase_order', 'purchase_sells', 'profile', 'payment_methods'));
    }

    public function sale_return_delete($id)
    {

        DB::beginTransaction();

        try {

            $invoice = PurchaseOrder::findOrFail($id);
            if ($invoice) {
                $oldtotal = $invoice->deposit;
                if ($invoice->transaction_id) {
                    $payment = Payment::where('transaction_id', $invoice->transaction_id)->skip(5)->first();

                    if ($payment) {
                        $payment->amount = $payment->amount - $oldtotal;
                        $payment->save();
                    } else {
                    }
                } else {
                }
            } else {
            }
            $invoice->delete();


            PO_sells::where('invoiceid', $id)->delete();


            DB::commit();

            return redirect('/sale_return')->with('success', 'Sale Return Deleted Successfully!');
        } catch (\Exception $e) {

            DB::rollback();

            return redirect('/sale_return')->with('error', 'Failed to delete sale return.');
        }

        // return redirect('/quotation')->with('success', 'Quotation Deleted Successful!');
    }

    public function deliver(Invoice $invoice)
    {
        $sells = Sell::where('invoiceid', $invoice->id)->get();
        $delivered_qty = $sells->sum('delivered_qty');
        $total_deliver = $sells->sum('product_qty');

        $latestdeliverIds = DeliveredItems::select(DB::raw('MAX(id) as id'))->groupBy('delivery_group_no')
            ->pluck('id'); // Get only the IDs

        // Retrieve the full records for those IDs
        $delivered_items = DeliveredItems::whereIn('id', $latestdeliverIds)
            ->where('invoice_id', $invoice->id)
            ->orderBy('id', 'asc')
            ->get();
        // dd($delivered_items);

        return view('invoice.delivered_items', compact('invoice', 'sells', 'total_deliver', 'delivered_qty', 'delivered_items'));
    }


    public function payment($id)
    {
        $make_payments = Invoice::where('status', 'invoice')->where('id', $id)->first();
        $payments = MakePayment::where('invoice_id', $id)
            ->where('invoice_no', '!=', null)
            ->get();
        $payments_number = MakePayment::latest()->first();
        return view('invoice.make_payment', compact('make_payments', 'payments', 'payments_number'));
    }

    public function payment_no_updates(Request $request)
    {
        // Fetch the latest invoice number (removing 'Cash-' prefix before casting it to integer)
        $latestInvoiceNo = MakePayment::whereNotNull('invoice_id')
            ->where('invoice_no', 'LIKE', 'Cash-%')
            ->orderByRaw("CAST(SUBSTRING(invoice_no, 6) AS UNSIGNED) DESC")  // Ordering by the numeric part after 'Cash-'
            ->first();

        // If a valid invoice number is found, increment it
        if ($latestInvoiceNo) {
            $latestNumber = (int) substr($latestInvoiceNo->invoice_no, 5); // Get the numeric part after 'Cash-'
            $nextInvoiceNo = 'Cash-' . ($latestNumber + 1);
        } else {
            // If no invoice is found, start from 'Cash-1'
            $nextInvoiceNo = 'Cash-1';
        }

        return response()->json(['invoice_no' => $nextInvoiceNo]);
    }


    public function payment_edit($id)
    {

        $make_payments = MakePayment::where('id', $id)->first();

        return view('invoice.makepayment_edit', compact('make_payments'));
    }

    public function payment_update($id, Request $request)
    {

        $make_payments = MakePayment::where('id', $id)->first();
        $make_payments->invoice_no = $request->invoice_no;
        $make_payments->save();

        return redirect()->back();
    }


    public function payment_store(Request $request, $id)
    {

        if ($request->remain_balance == '0') {
            return redirect()->back()->with('error', 'Remaining Balance is 0 , Nothing To Pay!');
        }

        $make_payments = new MakePayment();

        $invoice = Invoice::where('status', 'invoice')->where('id', $id)->first();
        $make_payments->payment_method = $request->payment_method;
        $make_payments->amount = $request->amount;
        $make_payments->note = $request->note;
        $make_payments->invoice_no = $request->invoice_no;
        $make_payments->invoice_id = $invoice->id;
        $make_payments->payment_date = $request->payment_date;
        $make_payments->save();

        //substract receivable deposit when make makepayment
        $invoice_payment_method = InvoicePaymentMethod::where('invoice_id', $invoice->id)->where('status', 'receivable')->first();


        // $invoice_deposit_payment = InvoicePaymentMethod::where('invoice_id', $invoice->id)->whereNull('status')->first();
        // if ($invoice_deposit_payment) {
        //     $invoice_deposit_payment->payment_amount = $invoice_deposit_payment->payment_amount + $request->amount;
        //     $invoice_deposit_payment->save();
        // }

        if ($invoice_payment_method) {
            $invoice_payment_method->payment_amount = $invoice_payment_method->payment_amount - $request->amount;
            $invoice_payment_method->save();
        }


        //end substract receivable deposit when make makepayment


        $invoice->deposit = $request->amount + $invoice->deposit;
        $invoice->remain_balance = $invoice->remain_balance - $request->amount;
        $invoice->update();

        $payment_method = new InvoicePaymentMethod();
        $payment_method->invoice_id = $invoice->id;
        $payment_method->payment_method = $request->payment_method;
        $payment_method->payment_amount = $request->amount;
        $payment_method->created_at = Carbon::now();
        $payment_method->updated_at = Carbon::now();
        $payment_method->save();

        return redirect(url('invoice'))->with('success', 'Payment Added Successfull!');
    }

    public function voucherView(MakePayment $make_payment)
    {
        $invoice = Invoice::where('id', $make_payment->invoice_id)->orWhere('id', $make_payment->invoice_record)->first();
        // $payment_methods = InvoicePaymentMethod::where('invoice_id', $invoice->id)->get();
        return view('invoice.invoice_voucher', [
            'invoice' => $invoice,
            'make_payment' => $make_payment,
            // 'payment_methods' => $payment_methods,
        ]);
    }





    // public function invoice_get_transaction($location, $mode)
    // {
    //     if ($mode == "Invoice") {
    //         $setting = Setting::where('location', $location)->where('category', 'invoice')->first();
    //     } else {
    //         $setting = Setting::where('location', $location)->where('category', 'purchase order return')->first();
    //     }
    //     if ($setting) {
    //         $transaction = Transaction::where('location', $location)->where('account_id', $setting->transaction_id)->get();
    //         if ($transaction) {
    //             return response()->json($transaction);
    //         } else {
    //             return ['error' => 'Transaction not found'];
    //         }
    //     } else {
    //         return ['error' => 'Setting not found'];
    //     }
    // }

    // public function quotation_get_transaction($location)
    // {
    //     $setting = Setting::where('location', $location)->where('category', 'invoice')->first();
    //     if ($setting) {
    //         $transaction = Transaction::where('location', $location)->where('account_id', $setting->transaction_id)->get();
    //         if ($transaction) {
    //             return response()->json($transaction);
    //         } else {
    //             return ['error' => 'Transaction not found'];
    //         }
    //     } else {
    //         return ['error' => 'Setting not found'];
    //     }
    // }


    public function customer_return()
    {
        $totalInvoices = Invoice::where('status', 'invoice')->count();
        // $invoice_no = "Invoice-" . ($totalInvoices + 1);
        $units = Unit::all();
        $warehouses = Warehouse::select('name', 'id')->get();
        $setting = Setting::where('category', '1')->get();
        if ($setting) {
        }

        return view('customer_return.customer_return', compact('units', 'warehouses'));
    }


    public function customer_return_manage($branch = null)
    {
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if (auth()->check() && auth()->user()->is_admin == '1') {
            if ($branch) {
                $invoices = Invoice::where('status', 'customer return')->where('branch', $branch)->latest()->get();
                $branchs = Warehouse::select('name', 'id')->get();
            } else {
                $invoices = Invoice::where('status', 'customer return')->latest()->get();
                $branchs = Warehouse::select('name', 'id')->get();
            }
        } else {
            $warehousePermission = auth()->check() ? $warehousePermission : [];
            $invoices = Invoice::whereIn('branch', $warehousePermission)
                ->where('status', 'customer return')
                ->latest()
                ->get();
            $branchs = Warehouse::select('name', 'id')->get();
        }

        $branchs = Warehouse::all();
        $branchNames = $branchs->pluck('name', 'id');

        $currentBranchName = $branch ? $branchNames[$branch] : 'All Locations';
        return view('customer_return.customer_return_manage', compact('invoices', 'branchs', 'currentBranchName', 'branchNames'));
    }

    public function cash_voucher_delete($id)
    {

        $cash_voucher = MakePayment::find($id);
        $payment_method = InvoicePaymentMethod::where('make_payment_id', $cash_voucher->id)->delete();

        $invoice = Invoice::where('id', $cash_voucher->invoice_id)->first();
        $invoice->deposit = $invoice->deposit - $cash_voucher->amount;
        $invoice->remain_balance = $invoice->remain_balance + $cash_voucher->amount;
        $invoice->update();

        $cash_voucher->delete();

        return redirect()->back()->with('success', 'Cash Voucher Deleted Successfully!');
    }
}
