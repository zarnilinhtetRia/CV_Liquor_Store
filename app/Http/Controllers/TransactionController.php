<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Warehouse;
use App\Models\MakePayment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\InvoicePaymentMethod;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderMakePayment;
use App\Models\PurchaseOrderPaymentMethod;

class TransactionController extends Controller
{

    public function transactionManagement($branch = null)
    {
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        // Fetch accounts and branches based on user role
        if (auth()->user()->is_admin == '1') {
            $account = Account::all();
            $branches = Warehouse::latest()->get();
        } else {
            $account = Account::whereIn('location', $warehousePermission)->get();
            $branches = Warehouse::whereIn('id', $warehousePermission)->latest()->get();
        }

        // Fetch transactions based on branch or permission
        if ($branch) {
            $transaction = Transaction::with('account')->where('location', $branch)->latest()->get();
        } else {
            $transaction = auth()->user()->is_admin == '1'
                ? Transaction::with('account')->latest()->get()
                : Transaction::whereIn('location', $warehousePermission)->with('account')->latest()->get();
        }

        // Calculate total amounts for different transaction types
        $sumByIn = Payment::where('payment_status', 'IN')
            ->groupBy('transaction_id')
            ->selectRaw('transaction_id, SUM(amount) as total')
            ->pluck('total', 'transaction_id');

        $sumByOut = Payment::where('payment_status', 'OUT')
            ->groupBy('transaction_id')
            ->selectRaw('transaction_id, SUM(amount) as total')
            ->pluck('total', 'transaction_id');

        $invoices = InvoicePaymentMethod::whereNotNull('invoice_id')
            ->where('status', 'receivable')
            ->whereNull('quote_no')
            ->groupBy('payment_method')
            ->selectRaw('payment_method, SUM(payment_amount) as total')
            ->pluck('total', 'payment_method');


        $customer_return_receivable = InvoicePaymentMethod::whereNotNull('invoice_id')
            ->where('status', 'receivable')
            ->where('quote_no', 'Customer Return')
            ->groupBy('payment_method')
            ->selectRaw('payment_method, SUM(payment_amount) as total')
            ->pluck('total', 'payment_method');


        $sale_invoice = InvoicePaymentMethod::whereNotNull('invoice_id')
            ->where('status', 'saleaccount')
            ->whereNull('quote_no')
            ->groupBy('payment_method')
            ->selectRaw('payment_method, SUM(payment_amount) as total')
            ->pluck('total', 'payment_method');

        $customer_return_sale_invoice = InvoicePaymentMethod::whereNotNull('invoice_id')
            ->where('status', 'saleaccount')
            ->where('quote_no', 'Customer Return')
            ->groupBy('payment_method')
            ->selectRaw('payment_method, SUM(payment_amount) as total')
            ->pluck('total', 'payment_method');

        $po = PurchaseOrderMakePayment::groupBy('payment_method')
            ->selectRaw('payment_method, SUM(amount) as total')
            ->pluck('total', 'payment_method');

        $payable_po = PurchaseOrderPaymentMethod::where('status', 'payable')
            ->groupBy('payment_method')
            ->selectRaw('payment_method, SUM(payment_amount) as total')
            ->pluck('total', 'payment_method');



        $buyaccount_po = PurchaseOrderPaymentMethod::where('status', 'buyaccount')
            ->groupBy('payment_method')
            ->selectRaw('payment_method, SUM(payment_amount) as total')
            ->pluck('total', 'payment_method');

        $invoices_make_payments = MakePayment::whereNotNull('invoice_id')
            ->whereNot('invoice_no', 'Customer Return')
            ->groupBy('payment_method')
            ->selectRaw('payment_method, SUM(amount) as total')
            ->pluck('total', 'payment_method');

        $customer_return_make_payments = MakePayment::whereNotNull('invoice_id')
            ->where('invoice_no', 'Customer Return')
            ->groupBy('payment_method')
            ->selectRaw('payment_method, SUM(amount) as total')
            ->pluck('total', 'payment_method');

        $expense = Expense::groupBy('transaction_id')
            ->selectRaw('transaction_id, SUM(amount_mmk) as total')
            ->pluck('total', 'transaction_id');

        $allTransactionIds = collect([
            $sumByIn->keys(),
            $sumByOut->keys(),
            $invoices->keys(),
            $sale_invoice->keys(),
            $customer_return_make_payments->keys(),
            $invoices_make_payments->keys(),
            $customer_return_sale_invoice->keys(),
            $customer_return_receivable->keys(),
            $po->keys(),
            $expense->keys(),
            $payable_po->keys(),
            $buyaccount_po->keys(),
        ])->flatten()->unique();

        $collections = [
            $sumByIn,
            $sumByOut,
            $invoices,
            $sale_invoice,
            $customer_return_sale_invoice,
            $customer_return_receivable,
            $invoices_make_payments,
            $customer_return_make_payments,
            $po,
            $expense,
            $payable_po,
            $buyaccount_po
        ];

        $collections = collect($collections)->map(function ($collection) use ($allTransactionIds) {
            return $allTransactionIds->mapWithKeys(function ($transactionId) use ($collection) {
                return [$transactionId => $collection->get($transactionId, 0)];
            });
        });

        [
            $sumByIn,
            $sumByOut,
            $invoices,
            $sale_invoice,
            $customer_return_sale_invoice,
            $customer_return_receivable,
            $invoices_make_payments,
            $customer_return_make_payments,
            $po,
            $expense,
            $payable_po,
            $buyaccount_po
        ] = $collections;

        $diff = $allTransactionIds->mapWithKeys(function ($transactionId) use (
            $sumByIn,
            $sumByOut,
            $invoices,
            $sale_invoice,
            $customer_return_sale_invoice,
            $customer_return_receivable,
            $invoices_make_payments,
            $customer_return_make_payments,
            $po,
            $expense,
            $payable_po,
            $buyaccount_po
        ) {
            $totalIn = $sumByIn->get($transactionId, 0);
            $totalOut = $sumByOut->get($transactionId, 0);
            $invoiceTotal = $invoices->get($transactionId, 0);
            $customerReturnTotal = $customer_return_make_payments->get($transactionId, 0);
            $saleTotal = $sale_invoice->get($transactionId, 0);
            $saleCustomerReturnTotal = $customer_return_sale_invoice->get($transactionId, 0);
            $saleCustomerReturnReceivable = $customer_return_receivable->get($transactionId, 0);
            $invoicePaymentTotal = $invoices_make_payments->get($transactionId, 0);
            $poTotal = $po->get($transactionId, 0);
            $expenseTotal = $expense->get($transactionId, 0);
            $payablePoTotal = $payable_po->get($transactionId, 0);
            $buyAccountPoTotal = $buyaccount_po->get($transactionId, 0);

            // Calculate final balance
            $finalDiff = $totalIn - $totalOut +
                $invoiceTotal + $saleTotal +
                $invoicePaymentTotal - $customerReturnTotal - $saleCustomerReturnTotal - $saleCustomerReturnReceivable -
                $poTotal - $expenseTotal - $payablePoTotal - $buyAccountPoTotal;

            return [$transactionId => $finalDiff];
        });

        $branchNames = $branches->pluck('name', 'id');
        $currentBranchName = $branch ? $branchNames[$branch] : 'All';

        return view('finance.transaction.transactionManagement', compact(
            'account',
            'transaction',
            'diff',
            'branches',
            'currentBranchName',
            'branchNames'
        ));
    }

    public function getAccountsByLocation()
    {
        $locationId = request()->input('locationId');
        $accounts = Account::where('location', $locationId)->get(['id', 'account_name']);
        info($locationId);
        info($accounts);
        return response()->json($accounts);
    }

    //transaction register
    public function transaction_register(Request $request)
    {

        $existingTransaction = Transaction::where('account_id', $request->input('account_id'))
            ->where('location', $request->input('location'))
            ->first();

        if ($existingTransaction) {
            return redirect()->back()->with('error', 'Transaction already exists for this location and account.');
        }

        $trasaction = new Transaction();
        $trasaction->transaction_code = $request->transaction_code;
        $trasaction->transaction_name = $request->transaction_name;
        $trasaction->location = $request->location;
        $trasaction->description = $request->description;
        $account = Account::find($request->input('account_id'));
        $account->transaction()->save($trasaction);



        return redirect()->back()->with('success', 'Transaction Created Successful!');
    }

    public function transactionManagementEdit($id)
    {
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];
        $transaction = Transaction::with('account')->find($id);

        if (auth()->user()->is_admin == '1') {
            $accounts = Account::latest()->get();
            $branches = Warehouse::latest()->get();
        } else {
            $accounts = Account::whereIn('location', $warehousePermission)->latest()->get();
            $branches = Warehouse::whereIn('id', $warehousePermission)->latest()->get();
        }
        return view('finance.transaction.transactionManagementEdit', compact('transaction', 'accounts', 'branches'));
    }

    public function transactionUpdate(Request $request, $id)
    {

        $existingTransaction = Transaction::where('account_id', $request->input('account_id'))
            ->where('location', $request->input('location'))
            ->where('id', '!=', $id)
            ->first();

        if ($existingTransaction) {
            return redirect('/transactionManagement')->with('error', 'Duplicate transaction found for the same account and location.');
        }

        $transaction = Transaction::find($id);

        if ($transaction->account_id != $request->input('account_id')) {
            $transaction->account()->associate(Account::find($request->input('account_id')));
        }
        $transaction->update([
            'transaction_code' => $request->input('transaction_code'),
            'transaction_name' => $request->input('transaction_name'),
            'description' => $request->input('description'),
        ]);
        return redirect('/transactionManagement')->with('updateStatus', 'Transaction Update is Successfull');
    }
    public function transactionDelete($id)
    {
        $hasInvoices = InvoicePaymentMethod::where('payment_method', $id)
            ->whereNull('deleted_at')
            ->exists();
        $hasPurchaseOrders = PurchaseOrderPaymentMethod::where('payment_method', $id)
            ->whereNull('deleted_at')
            ->exists();
        $hasExpenses = Expense::where('transaction_id', $id)
            ->whereNull('deleted_at')
            ->exists();

        if ($hasInvoices || $hasPurchaseOrders || $hasExpenses) {
            return redirect('transactionManagement')->with('error', 'Transaction cannot be deleted because there are existing related records.');
        }

        Transaction::find($id)->delete();

        return redirect('transactionManagement')->with('success', 'Transaction Deleted Successfully!');
    }
}
