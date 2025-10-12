<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Setting;
use App\Models\Warehouse;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\ExpenseCategory;

class ExpenseController extends Controller
{
    public function index()
    {

        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if (auth()->user()->is_admin == '1') {
            $expenses = Expense::latest()->get();
            $categories = ExpenseCategory::all();
            $branches = Warehouse::latest()->get();
        } else {
            $expenses = Expense::whereIn('branch', $warehousePermission)->latest()->get();
            $categories = ExpenseCategory::all();
            $branches = Warehouse::latest()->get();
        }

        return view('expense.expense', [
            "expenses" => $expenses,
            "categories" => $categories,
            "branches" => $branches
        ]);
    }

    public function expenseStore(Request $request)
    {

        Expense::create($request->all());
        return redirect(url('expense'))->with('success', 'Expense Created Successfully!');
    }


    public function edit(Expense $expense)
    {
        $payment_method = Transaction::find($expense->transaction_id);
        $categories = ExpenseCategory::latest()->get();
        $branches = Warehouse::latest()->get();

        // dd($payment_method);
        return view('expense.expenseEdit', compact('expense', 'categories', 'branches', 'payment_method'));
    }

    public function update(Request $request, Expense $expense)
    {

        $expense->update($request->all());
        return redirect(url('expense'))->with('success', 'Expense Updated Successfully!');
    }

    public function delete(Expense $expense)
    {
        // $unit = Expense::find($id);
        $expense->delete();
        return redirect()->back()->with('delete', 'Expense Deleted Successfully!');
    }

    public function get_part_data_unit()
    {
        $units = Expense::all();
        return response()->json($units);
    }

    public function expense_get_transaction($location)
    {
        $settings = Setting::where('location', $location)
            ->where('category', 'expense')
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

    public function expense_delete_record()
    {

        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];
        if (auth()->user()->is_admin == '1') {
            $expenses = Expense::onlyTrashed()->get();
        } else {
            $expenses = Expense::whereIn('branch', $warehousePermission)->onlyTrashed()->get();
        }

        return view('expense.expense_delete_record', compact('expenses'));
    }


    public function restore_expense($id)
    {
        $restored = Expense::onlyTrashed()
            ->where('id', $id)
            ->restore();

        if ($restored) {
            return redirect()->back()->with('success', 'Expense Restored successfully.');
        } else {
            return redirect()->back()->with('error', 'No expense found to restore.');
        }
    }
}
