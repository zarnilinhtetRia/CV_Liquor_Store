<?php

namespace App\Http\Controllers;

use App\Models\MakePayment;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderMakePayment;
use App\Models\PurchaseOrderPaymentMethod;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PurchaseOrderMakePaymentController extends Controller
{
    public function index($id)
    {
        $make_payments = PurchaseOrder::where('status', 'invoice')->where('id', $id)->first();
        $payments = PurchaseOrderMakePayment::where('po_id', $id)->get();
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];


        if (auth()->user()->is_admin == '1') {
            $warehouses = Warehouse::all();
        } else {
            $warehouses = Warehouse::whereIn('id', $warehousePermission)->get();
        }

        return view('purchase_order.make_payment', compact('make_payments', 'payments', 'warehouses'));
    }

    public function paymentStore(Request $request, $id)
    {
        if ($request->remain_balance == '0') {
            return redirect()->back()->with('error', 'Remaining Balance is 0 , Nothing To Pay!');
        }
        $make_payments = new PurchaseOrderMakePayment();

        $po = PurchaseOrder::where('status', 'invoice')->where('id', $id)->first();
        $make_payments->payment_method = $request->payment_method;
        $make_payments->amount = $request->amount;
        $make_payments->note = $request->note;
        $make_payments->po_no = $request->po_no;
        $make_payments->po_id = $po->id;
        $make_payments->payment_date = $request->payment_date;
        $make_payments->save();

        // substract receivable deposit when make makepayment
        $po_payment_method = PurchaseOrderPaymentMethod::where('po_id', $po->id)->where('status', 'payable')->first();
        $po_deposit_payment = PurchaseOrderPaymentMethod::where('po_id', $po->id)->whereNull('status')->first();

        if ($po_payment_method) {
            $po_payment_method->payment_amount = $po_payment_method->payment_amount - $request->amount;

            if ($po_payment_method->payment_amount == 0) {
                $po_payment_method->status = null;
                $po_payment_method->save();
            }
            $po_payment_method->save();
        }
        // end substract receivable deposit when make makepayment


        $po->deposit = $request->amount + $po->deposit;
        $po->remain_balance = $po->remain_balance - $request->amount;
        $po->update();

        $payment_method = new PurchaseOrderPaymentMethod();
        $payment_method->po_id = $po->id;
        $payment_method->payment_method = $request->payment_method;
        $payment_method->payment_amount = $request->amount;
        $payment_method->created_at = Carbon::now();
        $payment_method->updated_at = Carbon::now();
        $payment_method->save();

        return redirect(url('purchase_order_manage'))->with('success', 'Payment Added Successfull!');
    }

    public function poVoucherView(PurchaseOrderMakePayment $po_make_payment)
    {
        $po = PurchaseOrder::where('id', $po_make_payment->po_id)->orWhere('id', $po_make_payment->po_record)->first();
        // $payment_methods = InvoicePaymentMethod::where('invoice_id', $invoice->id)->get();
        return view('invoice.invoice_voucher', [
            'invoice' => $po,
            'make_payment' => $po_make_payment,
            // 'payment_methods' => $payment_methods,
        ]);
    }
}
