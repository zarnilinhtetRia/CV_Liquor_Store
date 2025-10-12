<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Account;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Warehouse;
use App\Models\MakePayment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\InvoicePaymentMethod;
use App\Models\PurchaseOrderPaymentMethod;

class PaymentController extends Controller
{

    public function payment($id)
    {

        $transaction = Transaction::find($id);
        $accounts = Account::where('location', $transaction->location)->get();
        $transactions = Transaction::with('account')->latest()->get();

        $invoices = Invoice::with('makePayments')->where('status', ['invoice', 'pos'])
            ->whereHas('makePayments', function ($query) use ($transaction) {
                $query->where('payment_method', $transaction->id)
                    ->whereMonth('invoice_date', Carbon::now()->month)
                    ->whereYear('invoice_date', Carbon::now()->year);
            })
            ->get();

        $customer_return_invoices = Invoice::with('makePayments')
            ->where('status', 'customer return')
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

        $receive_customer_return = Invoice::with('PaymentMethod')->where('status', 'customer return')
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


        $sale_customer_return = Invoice::with('PaymentMethod')->where('status', 'customer return')
            ->whereHas('PaymentMethod', function ($query) use ($transaction) {
                $query->where('payment_method', $transaction->id)
                    ->where('status', 'saleaccount')
                    ->whereMonth('invoice_date', Carbon::now()->month)
                    ->whereYear('invoice_date', Carbon::now()->year);
            })
            ->get();

        // dd($sale_invoices);


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

        $makepayment = MakePayment::where('payment_method', $id)
            ->whereNotNull('invoice_id')
            ->get();

        $purchasePayment = PurchaseOrderPaymentMethod::where('payment_method', $id)->get();

        $invoice_receive = InvoicePaymentMethod::where('payment_method', $id)
            ->whereNotNull('invoice_id')
            ->where('status', 'receivable')->get();

        $invoice_sale = InvoicePaymentMethod::where('payment_method', $id)
            ->whereNotNull('invoice_id')
            ->where('status', 'saleaccount')->get();

        return view('finance.payment.makePayment', compact('accounts', 'transaction', 'add_payments', 'invoices', 'warehouses', 'transactions', 'diff', 'purchase_orders', 'po_returns', 'sale_return_invoices', 'makepayment', 'purchasePayment', 'invoice_receive', 'receive_invoices', 'po_returns_receive', 'expenses', 'invoice_sale', 'sale_invoices', 'customer_return_invoices', 'receive_customer_return', 'sale_customer_return'));
    }



    public function getBranchTransactionNo(Request $request)
    {
        // Get the highest debit_note that starts with 'Voucher-'
        $latestVoucherNo = Payment::whereNotNull('debit_note')
            ->where('debit_note', 'LIKE', 'Voucher-%')
            ->max('debit_note');

        if ($latestVoucherNo) {
            // Extract the numeric part and increment
            $latestNumber = (int) str_replace('Voucher-', '', $latestVoucherNo);
            $nextVoucherNo = 'Voucher-' . ($latestNumber + 1);
        } else {
            // Start from Voucher-1 if no previous records exist
            $nextVoucherNo = 'Voucher-1';
        }

        return response()->json(['no' => $nextVoucherNo]);
    }


    public function payment_register(Request $request)
    {

        $data = new Payment();
        $data->fill($request->all());
        $data->save();

        if ($request->opening_balance == null) {
            $account = Account::find($request->transfer_account_id);

            if ($account) {
                $transaction = Transaction::where('account_id', $account->id)->first();


                if (!$transaction) {
                    return redirect()->back()->with('error', 'Transaction not found.');
                }
                $payment = new Payment();
                if ($payment) {
                    $payment->transaction_id = $transaction->id;
                    $payment->debit_note = $request->debit_note;
                    $payment->receiver_name = $request->receiver_name;
                    $payment->reference_no = $request->reference_no;
                    $payment->account_id = $account->id;
                    $payment->transfer_account_id = $data->account_id;
                    $payment->payment_date = $data->payment_date;
                    $payment->payment_status = ($request->payment_status === 'IN') ? 'OUT' : 'IN';
                    $payment->amount = $request->amount;
                    $payment->note = $request->note;
                    $payment->save();
                }
            }

            return redirect()->back()->with('success', 'Payment created successfully.');
        } else {
            return redirect()->back()->with('success', 'Opening balance created successfully.');
        }
    }


    // Direct finance Make payment Page
    public function makePaymentEdit($id)
    {
        $show = Payment::find($id);
        $transaction = Transaction::find($show->transaction_id);
        $accounts = Account::where('location', $transaction->location)->get();

        // $accounts = Account::all();

        return view('finance.payment.makePaymentEdit', compact('show', 'accounts'));
    }
    public function paymentUpdate(Request $request, $id)
    {
        $update = Payment::find($id);
        $update->payment_status = $request->input('payment_status');
        $update->amount = $request->input('amount');
        $update->note = $request->input('note');
        $update->payment_date = $request->input('payment_date');
        $update->receiver_name = $request->input('receiver_name');
        $update->reference_no = $request->input('reference_no');

        if ($update->opening_balance == null) {
            $account_old = Account::find($update->transfer_account_id);
            if ($account_old) {
                $transaction_old = Transaction::where('account_id', $account_old->id)->first();
                if ($transaction_old) {

                    $payment_old = Payment::where('transaction_id', $transaction_old->id)
                        ->where('debit_note', $update->debit_note)
                        ->first();
                    if ($payment_old) {
                        $payment_old->delete_status = 'edit_delete';
                        $payment_old->save();
                        $payment_old->delete();
                    }
                } else {
                    return redirect(url('payment', $update->transaction_id))->with('error', 'Transaction not found.');
                }
            }
            $account = Account::find($request->transfer_account_id);

            if ($account) {
                $transaction = Transaction::where('account_id', $account->id)->first();

                if (!$transaction) {
                    return redirect()->back()->with('error', 'Transaction not found.');
                }

                $new_payment = new Payment();
                $new_payment->transaction_id = $transaction->id;
                $new_payment->account_id = $account->id;
                $new_payment->transfer_account_id = $update->account_id;
                $new_payment->debit_note = $update->debit_note;
                $new_payment->receiver_name = $update->receiver_name;
                $new_payment->reference_no = $update->reference_no;
                $new_payment->payment_date = $update->payment_date;
                $new_payment->payment_status = ($request->payment_status === 'IN') ? 'OUT' : 'IN';
                $new_payment->amount = $request->amount;
                $new_payment->note = $request->note;
                $new_payment->save();
            }
            $update->transfer_account_id = $request->input('transfer_account_id');
            $update->save();
            return redirect(url('payment', $update->transaction_id))->with('updateStatus', 'Payment Update Successful');
        } else {
            $update->save();
            return redirect(url('payment', $update->transaction_id))->with('updateStatus', 'Opening Balance Update Successful');
        }
    }
    public function paymentDelete($debit_note)
    {
        Payment::where('debit_note', $debit_note)->delete();
        return redirect()->back()->with('deleteStatus', 'Payment Deleted Successful!');
    }


    public function account_invoice_search(Request $request, $id)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $transaction = Transaction::find($id);
        // $invoices = Invoice::where('transaction_id', $transaction->id)
        //     ->where('status', 'invoice')
        //     ->where('balance_due', 'Invoice')
        //     ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
        //         return $query->whereBetween('invoice_date', [$startDate, $endDate]);
        //     })
        //     ->get();
        // $invoices = Invoice::with('makePayments')->where('status', 'invoice')->where('balance_due', 'Invoice')
        //     ->whereHas('makePayments', function ($query) use ($id, $startDate, $endDate) {
        //         $query->where('payment_method', $id)
        //             ->whereDate('created_at', ">=", $startDate)
        //             ->whereDate('created_at', "<=", $endDate);
        //     })
        //     ->get();
        $invoices = Invoice::with('makePayments')
            ->where('status', 'invoice')
            ->where('balance_due', 'Invoice')
            ->where(function ($query) use ($id, $startDate, $endDate) {
                // Check if `makePayments` exists with the given conditions
                $query->whereHas('makePayments', function ($query) use ($id, $startDate, $endDate) {
                    $query->where('payment_method', $id)
                        ->whereDate('created_at', ">=", $startDate)
                        ->whereDate('created_at', "<=", $endDate);
                })
                    // OR check if `paymentMethod` exists
                    ->orWhereHas('paymentMethod', function ($query) use ($id, $startDate, $endDate) {
                        $query->where('payment_method', $id)
                            ->whereDate('created_at', ">=", $startDate)
                            ->whereDate('created_at', "<=", $endDate);
                    });
            })
            ->get();


        $warehouses = Warehouse::all();

        if ($request->ajax()) {
            return response()->json([
                'invoices' => $invoices,
                'warehouses' => $warehouses
            ]);
        }

        return view('finance.payment.makePayment', compact('transaction', 'invoices', 'warehouses'));
    }

    public function account_po_search(Request $request, $id)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $transaction = Transaction::find($id);

        // $purchase_orders = PurchaseOrder::where('transaction_id', $transaction->id)
        //     ->where('balance_due', 'PO')
        //     ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
        //         return $query->whereBetween('po_date', [$startDate, $endDate]);
        //     })
        //     ->get();
        $purchase_orders = PurchaseOrder::with('purchasePayment')->where('balance_due', 'PO')
            ->whereHas('purchasePayment', function ($query) use ($id, $startDate, $endDate) {
                $query->where('payment_method', $id)
                    ->whereDate('po_date', ">=", $startDate)
                    ->whereDate('po_date', "<=", $endDate);
            })
            ->get();
        $warehouses = Warehouse::all();

        if ($request->ajax()) {
            return response()->json([
                'purchase_orders' => $purchase_orders,
                'warehouses' => $warehouses,
            ]);
        }

        return view('finance.payment.makePayment', compact('transaction', 'purchase_orders', 'warehouses', 'startDate', 'endDate'));
    }



    public function account_pos_search(Request $request, $id)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $transaction = Transaction::find($id);

        $point_of_sales = Invoice::where('transaction_id', $transaction->id)
            ->where('status', 'pos')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('invoice_date', [$startDate, $endDate]);
            })
            ->get();

        $warehouses = Warehouse::all();

        if ($request->ajax()) {
            return response()->json([
                'point_of_sales' => $point_of_sales,
                'warehouses' => $warehouses,
            ]);
        }
        return view('finance.payment.makePayment', compact('transaction', 'point_of_sales', 'warehouses', 'startDate', 'endDate'));
    }



    public function purchase_return_invoice_search(Request $request, $id)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $transaction = Transaction::find($id);

        $po_returns = Invoice::where('transaction_id', $transaction->id)
            ->where('status', 'invoice')
            ->where('balance_due', 'Po Return')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereDate('invoice_date', ">=", $startDate)
                    ->whereDate('invoice_date', "<=", $endDate);
            })
            ->get();

        $warehouses = Warehouse::all();

        if ($request->ajax()) {
            return response()->json([
                'po_returns' => $po_returns,
                'warehouses' => $warehouses,
            ]);
        }

        return view('finance.payment.makePayment', compact('transaction', 'po_returns', 'warehouses', 'startDate', 'endDate'));
    }


    public function sale_return_invoice_search(Request $request, $id)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $transaction = Transaction::find($id);
        // $sale_return_invoices = PurchaseOrder::where('transaction_id', $transaction->id)
        //     ->where('balance_due', 'Sale Return Invoice')
        //     ->whereBetween('po_date', [$startDate, $endDate])
        //     ->get();
        $sale_return_invoices
            = PurchaseOrder::with('purchasePayment')->where('balance_due', 'Sale Return Invoice')
            ->whereHas('purchasePayment', function ($query) use ($id, $startDate, $endDate) {
                $query->where('payment_method', $id)
                    ->whereDate('po_date', ">=", $startDate)
                    ->whereDate('po_date', "<=", $endDate);
            })
            ->get();
        $warehouses = Warehouse::all();

        if ($request->ajax()) {
            return response()->json([
                'sale_return_invoices' => $sale_return_invoices,
                'warehouses' => $warehouses
            ]);
        }

        return view('finance.payment.makePayment', compact('transaction', 'sale_return_invoices', 'warehouses', 'startDate', 'endDate'));
    }


    public function sale_return_pos_search(Request $request, $id)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $transaction = Transaction::find($id);

        $sale_return_pos = PurchaseOrder::where('transaction_id', $transaction->id)
            ->whereBetween('po_date', [$startDate, $endDate])
            ->where('balance_due', 'Sale Return')
            ->get();

        $warehouses = Warehouse::all();

        if ($request->ajax()) {
            return response()->json([
                'sale_return_pos' => $sale_return_pos,
                'warehouses' => $warehouses
            ]);
        }

        return view('finance.payment.makePayment', compact('transaction', 'sale_return_pos', 'warehouses', 'startDate', 'endDate'));
    }
    public function expense_search(Request $request, $id)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $transaction = Transaction::find($id);

        $expenses = Expense::with('catego')->where('transaction_id', $transaction->id)
            ->whereDate('date', ">=", $startDate)
            ->whereDate('date', "<=", $endDate)

            ->get();

        $warehouses = Warehouse::all();

        if ($request->ajax()) {
            return response()->json([
                'expneses' => $expenses,
                'warehouses' => $warehouses
            ]);
        }

        return view('finance.payment.makePayment', compact('transaction', 'expenses', 'warehouses', 'startDate', 'endDate'));
    }

    public function details($id)
    {

        $payment = Payment::find($id);


        return view('finance.payment.makePayment_details', compact('payment'));
    }

    public function delete_record_payment($id)
    {
        $payments = Payment::onlyTrashed()
            ->where('transaction_id', $id)
            ->whereNull('delete_status')
            ->get();

        return view('finance.payment.makePayment_delete_record', compact('payments', 'id'));
    }
    public function restore_payment($id)
    {
        $restored = Payment::onlyTrashed()
            ->where('id', $id)
            ->restore();

        if ($restored) {
            $payment = Payment::find($id);
            return redirect(url('payment', $payment->transaction_id))->with('success', 'Payments Restored successfully.');
        } else {
            return redirect()->back()->with('error', 'No payments found to restore.');
        }
    }
}
