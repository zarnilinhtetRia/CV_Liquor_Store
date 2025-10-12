<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class AccountController extends Controller
{

    public function accountManagement($branch = null)
    {
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if ($branch) {
            $accounts = Account::where('location', $branch)->latest()->get();
        } else {
            if(auth()->user()->is_admin == '1'){
                $accounts = Account::latest()->get();
            }else{
                $accounts = Account::whereIn('location',$warehousePermission)->latest()->get();
            }
        }

        if(auth()->user()->is_admin == '1'){
            $branches = Warehouse::latest()->get();
        }else{
            $branches = Warehouse::whereIn('id',$warehousePermission)->latest()->get();
        }
        
        $branchNames = $branches->pluck('name', 'id');

        $currentBranchName = $branch ? $branchNames[$branch] : 'All';
        return view('finance.account.accountManagement', compact('accounts', 'branches', 'branchNames', 'currentBranchName'));
    }

    public function account_register(Request $request)
    {
        $request->validate([
            'account_number' => 'required|unique:accounts',
        ], [
            'account_number.unique' => 'Account Number Already Exists!',
        ]);
        Account::create($request->all());
        return redirect()->back()->with('success', 'Account Created Successful!');
    }

    public function account_edit($id)
    {
        $account = Account::find($id);
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if(auth()->user()->is_admin == '1'){
            $branches = Warehouse::latest()->get();
        }else{
            $branches = Warehouse::whereIn('id',$warehousePermission)->latest()->get();
        }
        return view('finance.account.accountManagementEdit', compact('account', 'branches'));
    }
    public function account_update(Request $request, $id)
    {
        Account::find($id)->update($request->all());
        return redirect('accountManagement')->with('success', 'Account Updated Successful!');
    }
    public function account_delete($id)
    {
        $transactions = Transaction::where('account_id', $id)
            ->whereNull('deleted_at')
            ->exists();

        if ($transactions) {
            return redirect('accountManagement')->with('error', 'Account cannot be deleted because there are existing transactions.');
        }

        Account::find($id)->delete();

        return redirect('accountManagement')->with('success', 'Account Deleted Successfully!');
    }
}
