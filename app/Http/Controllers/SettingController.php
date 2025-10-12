<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Setting;
use App\Models\Warehouse;
use App\Models\Transaction;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function store(Request $request)
    {
        // dd($request->all());
        Setting::create($request->all());
        $location = Warehouse::find($request->location);
        return redirect(url('setting'))->with('success', ucfirst($request->category) . ' setting for ' . $location->name . ' has been created!');
    }
    public function delete(Request $request)
    {
        Setting::find($request->id)->delete();

        return redirect(url('setting'))->with('success', 'Setting has been deleted!');
    }
    public function edit(Request $request, $id)
    {
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        $setting = Setting::find($id);

        if(auth()->user()->is_admin == '1'){
            $branches = Warehouse::latest()->get();
            $transactions = Account::latest()->get();
        }else{
            $branches = Warehouse::whereIn('id',$warehousePermission)->latest()->get();
            $transactions = Account::whereIn('location',$warehousePermission)->latest()->get();
        }
        return view('setting.setting_edit', compact('setting', 'branches', 'transactions'));
    }
    public function update(Request $request, $id)
    {

        $setting = Setting::find($id);
        $setting->update($request->all());
        $location = Warehouse::find($request->location);
        return redirect(url('setting'))->with('success', ucfirst($request->category) . ' setting for ' . $location->name . ' has been updated!');
    }
    public function index($branch = null)
    {
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if(auth()->user()->is_admin == '1'){
            $transactions = Transaction::latest()->get();
            $branches = Warehouse::latest()->get();
        }else{
            $transactions = Transaction::whereIn('location',$warehousePermission)->latest()->get();
            $branches = Warehouse::whereIn('id',$warehousePermission)->latest()->get();
        }

        if ($branch) {
            $settings = Setting::where('location', $branch)->latest()->get();
        } else {
            if(auth()->user()->is_admin == '1'){
                $settings = Setting::latest()->get();
            }else{
                $settings = Setting::whereIn('location',$warehousePermission)->latest()->get(); //only permission location setting
            }  
        }
        // $transactions = Transaction::latest()->get();
        $branchNames = $branches->pluck('name', 'id');

        $currentBranchName = $branch ? $branchNames[$branch] : 'All';
        return view('setting.setting', compact('settings', 'transactions', 'branches', 'transactions', 'branchNames', 'currentBranchName'));
    }

    public function invoice(Request $request)
    {

        $invoice_setting = Setting::find(1);
        // dd($invoice_setting);
        $invoice_setting->transaction_id = $request->transaction_id;
        $invoice_setting->save();
        $transactions = Transaction::latest()->get();
        $invoice = Setting::with('transaction')->find(1);
        $invoice_return = Setting::with('transaction')->find(3);
        $exchange_order = Setting::with('transaction')->find(2);

        $eo_return = Setting::with('transaction')->find(4);
        $pos = Setting::with('transaction')->find(5);

        return view('setting.setting', compact('transactions', 'invoice', 'invoice_return',  'exchange_order', 'eo_return', 'pos'));
    }
    public function pos(Request $request)
    {

        $invoice_setting = Setting::find(5);
        // dd($invoice_setting);
        $invoice_setting->transaction_id = $request->transaction_id;
        $invoice_setting->save();
        $transactions = Transaction::latest()->get();
        $invoice = Setting::with('transaction')->find(1);
        $invoice_return = Setting::with('transaction')->find(3);
        $exchange_order = Setting::with('transaction')->find(2);

        $eo_return = Setting::with('transaction')->find(4);

        $pos = Setting::with('transaction')->find(5);


        return view('setting.setting', compact('transactions', 'invoice', 'invoice_return', 'exchange_order', 'eo_return', 'pos'));
    }
    public function pos_setting_edit(Request $request)
    {

        $invoice_setting = Setting::find(5);
        // dd($invoice_setting);
        $invoice_setting->transaction_id = $request->transaction_id;
        $invoice_setting->save();
        $transactions = Transaction::latest()->get();
        $invoice = Setting::with('transaction')->find(1);
        $invoice_return = Setting::with('transaction')->find(3);
        $exchange_order = Setting::with('transaction')->find(2);

        $eo_return = Setting::with('transaction')->find(4);

        $pos = Setting::with('transaction')->find(5);


        return view('setting.setting', compact('transactions', 'invoice', 'invoice_return', 'exchange_order', 'eo_return', 'pos'));
    }
    public function invoice_setting_edit(Request $request)
    {

        $invoice_setting = Setting::find(1);
        // dd($invoice_setting);
        $invoice_setting->transaction_id = $request->transaction_id;
        $invoice_setting->update();
        $transactions = Transaction::latest()->get();
        $invoice = Setting::with('transaction')->find(1);
        $invoice_return = Setting::with('transaction')->find(3);
        $exchange_order = Setting::with('transaction')->find(2);

        $eo_return = Setting::with('transaction')->find(4);
        $pos = Setting::with('transaction')->find(5);


        return view('setting.setting', compact('transactions', 'invoice', 'invoice_return', 'exchange_order', 'eo_return', 'pos'));
    }



    public function exchange_order(Request $request)
    {

        $payment_setting = Setting::find(2);
        // dd($invoice_setting);
        $payment_setting->transaction_id = $request->transaction_id;
        $payment_setting->save();
        $transactions = Transaction::latest()->get();
        $invoice = Setting::with('transaction')->find(1);
        $invoice_return = Setting::with('transaction')->find(3);
        $exchange_order = Setting::with('transaction')->find(2);

        $eo_return = Setting::with('transaction')->find(4);
        $pos = Setting::with('transaction')->find(5);


        return view('setting.setting', compact('transactions', 'invoice', 'invoice_return', 'exchange_order', 'eo_return', 'pos'));
    }

    public function exchange_order_setting_edit(Request $request)
    {

        $payment_setting = Setting::find(2);
        // dd($invoice_setting);
        $payment_setting->transaction_id = $request->transaction_id;
        $payment_setting->save();
        $transactions = Transaction::latest()->get();
        $invoice = Setting::with('transaction')->find(1);
        $invoice_return = Setting::with('transaction')->find(3);
        $exchange_order = Setting::with('transaction')->find(2);

        $eo_return = Setting::with('transaction')->find(4);
        $pos = Setting::with('transaction')->find(5);


        return view('setting.setting', compact('transactions', 'invoice', 'invoice_return', 'exchange_order', 'eo_return', 'pos'));
    }
    public function invoice_return(Request $request)
    {

        $customer_refund = Setting::find(3);
        $customer_refund->transaction_id = $request->transaction_id;
        $customer_refund->save();
        $transactions = Transaction::latest()->get();
        $invoice = Setting::with('transaction')->find(1);
        $invoice_return = Setting::with('transaction')->find(3);
        $exchange_order = Setting::with('transaction')->find(2);

        $eo_return = Setting::with('transaction')->find(4);


        $pos = Setting::with('transaction')->find(5);


        return view('setting.setting', compact('transactions', 'invoice', 'invoice_return', 'exchange_order', 'eo_return', 'pos'));
    }
    public function invoice_return_setting_edit(Request $request)
    {

        $payment_setting = Setting::find(3);
        // dd($invoice_setting);
        $payment_setting->transaction_id = $request->transaction_id;
        $payment_setting->save();
        $transactions = Transaction::latest()->get();
        $invoice = Setting::with('transaction')->find(1);
        $invoice_return = Setting::with('transaction')->find(3);
        $exchange_order = Setting::with('transaction')->find(2);

        $eo_return = Setting::with('transaction')->find(4);

        $pos = Setting::with('transaction')->find(5);


        return view('setting.setting', compact('transactions', 'invoice', 'invoice_return', 'exchange_order', 'eo_return', 'pos'));
    }
    public function exchange_order_return(Request $request)
    {

        $customer_refund = Setting::find(4);
        $customer_refund->transaction_id = $request->transaction_id;
        $customer_refund->save();
        $transactions = Transaction::latest()->get();
        $invoice = Setting::with('transaction')->find(1);
        $invoice_return = Setting::with('transaction')->find(3);
        $exchange_order = Setting::with('transaction')->find(2);

        $eo_return = Setting::with('transaction')->find(4);
        $pos = Setting::with('transaction')->find(5);


        return view('setting.setting', compact('transactions', 'invoice', 'invoice_return', 'exchange_order', 'eo_return', 'pos'));
    }
    public function exchange_order_return_setting_edit(Request $request)
    {

        $payment_setting = Setting::find(4);
        // dd($invoice_setting);
        $payment_setting->transaction_id = $request->transaction_id;
        $payment_setting->save();
        $transactions = Transaction::latest()->get();
        $invoice = Setting::with('transaction')->find(1);
        $invoice_return = Setting::with('transaction')->find(3);
        $exchange_order = Setting::with('transaction')->find(2);

        $eo_return = Setting::with('transaction')->find(4);
        $pos = Setting::with('transaction')->find(5);


        return view('setting.setting', compact('transactions', 'invoice', 'invoice_return', 'exchange_order', 'eo_return', 'pos'));
    }
}
