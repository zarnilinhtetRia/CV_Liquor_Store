<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProfitController extends Controller
{
    // public function index()
    // {

    //     $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

    //     if (auth()->user()->is_admin == '1') {
    //         $currentMonth = Carbon::now()->month;
    //         $currentYear = Carbon::now()->year;

    //         $invoices = Invoice::whereMonth('invoice_date', $currentMonth)
    //             ->whereYear('invoice_date', $currentYear)
    //             ->get();

    //         $purchases = PurchaseOrder::whereMonth('po_date', $currentMonth)
    //             ->whereYear('po_date', $currentYear)
    //             ->get();

    //         $expenses = Expense::whereMonth('date', $currentMonth)
    //             ->whereYear('date', $currentYear)
    //             ->get();

    //         $totalSum = $invoices->sum('total');
    //         $totalPurchase = $purchases->sum('total');
    //         $totalExpense = $expenses->sum('amount');
    //     } else {
    //         $currentMonth = Carbon::now()->month;
    //         $currentYear = Carbon::now()->year;

    //         $invoices = Invoice::whereMonth('invoice_date', $currentMonth)
    //             ->whereYear('invoice_date', $currentYear)
    //             ->whereIn('branch', $warehousePermission)
    //             ->get();

    //         $purchases = PurchaseOrder::whereMonth('po_date', $currentMonth)
    //             ->whereYear('po_date', $currentYear)
    //             ->whereIn('branch', $warehousePermission)
    //             ->get();

    //         $expenses = Expense::whereMonth('date', $currentMonth)
    //             ->whereYear('date', $currentYear)
    //             ->whereIn('branch', $warehousePermission)
    //             ->get();

    //         $totalSum = $invoices->sum('total');
    //         $totalPurchase = $purchases->sum('total');
    //         $totalExpense = $expenses->sum('amount');
    //     }

    //     return view('profit.profit', compact('invoices', 'totalSum', 'totalPurchase', 'totalExpense'));
    // }
    // public function index(Request $request)
    // {
    //     // Get the current user's branch permissions
    //     $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];
    //     $branch = $request->query('branch'); // Get the branch from the query parameters

    //     $currentMonth = Carbon::now()->month;
    //     $currentYear = Carbon::now()->year;

    //     // Initialize the base queries
    //     $invoicesQuery = Invoice::whereMonth('invoice_date', $currentMonth)
    //         ->whereYear('invoice_date', $currentYear);

    //     $purchasesQuery = PurchaseOrder::whereMonth('po_date', $currentMonth)
    //         ->whereYear('po_date', $currentYear);

    //     $expensesQuery = Expense::whereMonth('date', $currentMonth)
    //         ->whereYear('date', $currentYear);

    //     // Check if the user is an admin
    //     if (auth()->user()->is_admin == '1') {
    //         // Admins can filter by any branch
    //         if ($branch) {
    //             $invoicesQuery->where('branch', $branch);
    //             $purchasesQuery->where('branch', $branch);
    //             $expensesQuery->where('branch', $branch);
    //         }
    //     } else {
    //         // Non-admins can only filter within their allowed branches
    //         if ($branch) {
    //             $invoicesQuery->where('branch', $branch);
    //             $purchasesQuery->where('branch', $branch);
    //             $expensesQuery->where('branch', $branch);
    //         } else {
    //             $invoicesQuery->whereIn('branch', $warehousePermission);
    //             $purchasesQuery->whereIn('branch', $warehousePermission);
    //             $expensesQuery->whereIn('branch', $warehousePermission);
    //         }
    //     }

    //     // Fetch the data
    //     $invoices = $invoicesQuery->get();
    //     $purchases = $purchasesQuery->get();
    //     $expenses = $expensesQuery->get();

    //     // Calculate totals
    //     $totalSum = $invoices->sum('deposit');
    //     $totalPurchase = $purchases->sum('deposit');
    //     $totalExpense = $expenses->sum('amount');

    //     $branchs = Warehouse::all();
    //     $branchNames = $branchs->pluck('name', 'id');
    //     $currentBranchName = $branch ? $branchNames[$branch] : 'All Profits';

    //     return view('profit.profit', compact('invoices', 'totalSum', 'totalPurchase', 'totalExpense', 'branchs', 'currentBranchName'));
    // }

    // public function index(Request $request)
    // {
    //     $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];
    //     $branch = $request->query('branch');
    //     $currentMonth = Carbon::now()->month;
    //     $currentYear = Carbon::now()->year;

    //     if (auth()->user()->is_admin == '1') {
    //         $branchs = Warehouse::all();
    //     } else {
    //         $branchs = Warehouse::whereIn('id', $warehousePermission)->get();
    //     }

    //     // Invoices, Purchases, Expenses Queries
    //     $invoicesQuery = Invoice::selectRaw('DATE(invoice_date) as date, SUM(deposit) as daily_total')
    //         ->whereMonth('invoice_date', $currentMonth)
    //         ->whereYear('invoice_date', $currentYear)
    //         ->groupBy('date');

    //     $purchasesQuery = PurchaseOrder::selectRaw('DATE(po_date) as date, SUM(deposit) as daily_total')
    //         ->whereMonth('po_date', $currentMonth)
    //         ->whereYear('po_date', $currentYear)
    //         ->groupBy('date');

    //     $expensesQuery = Expense::selectRaw('DATE(date) as date, SUM(amount) as daily_total')
    //         ->whereMonth('date', $currentMonth)
    //         ->whereYear('date', $currentYear)
    //         ->groupBy('date');

    //     if ($branch) {
    //         $invoicesQuery->where('branch', $branch);
    //         $purchasesQuery->where('branch', $branch);
    //         $expensesQuery->where('branch', $branch);
    //     } else {
    //         $invoicesQuery->whereIn('branch', $branchs->pluck('id')->toArray());
    //         $purchasesQuery->whereIn('branch', $branchs->pluck('id')->toArray());
    //         $expensesQuery->whereIn('branch', $branchs->pluck('id')->toArray());
    //     }

    //     // Get the final results
    //     $invoices = $invoicesQuery->get();
    //     $purchases = $purchasesQuery->get();
    //     $expenses = $expensesQuery->get();

    //     // Stock and OutSource Sells filtering
    //     $filteredStockSells = Invoice::with('sells')
    //         ->whereMonth('invoice_date', $currentMonth)
    //         ->whereYear('invoice_date', $currentYear);

    //     $filteredOutSourceSells = Invoice::with('sells')
    //         ->whereMonth('invoice_date', $currentMonth)
    //         ->whereYear('invoice_date', $currentYear);

    //     // Apply branch filter for Stock and OutSource sells
    //     if ($branch) {
    //         $filteredStockSells->where('branch', $branch);
    //         $filteredOutSourceSells->where('branch', $branch);
    //     } else {
    //         $filteredStockSells->whereIn('branch', $branchs->pluck('id')->toArray());
    //         $filteredOutSourceSells->whereIn('branch', $branchs->pluck('id')->toArray());
    //     }

    //     $filteredStockSells = $filteredStockSells->get()
    //         ->filter(function ($invoice) {
    //             return $invoice->sells->where('status', 0)->isNotEmpty();
    //         });

    //     $filteredOutSourceSells = $filteredOutSourceSells->get()
    //         ->filter(function ($invoice) {
    //             return $invoice->sells->where('status', 1)->isNotEmpty();
    //         });

    //     $filteredStockSellsByDay = $filteredStockSells->groupBy(function ($invoice) {
    //         return Carbon::parse($invoice->invoice_date)->format('Y-m-d');
    //     })->map(function ($group) {
    //         return $group->flatMap(function ($invoice) {
    //             return $invoice->sells->where('status', 0)->map(function ($sell) {
    //                 return $sell->retail_price * $sell->product_qty;
    //             });
    //         })->sum();
    //     });

    //     $filteredOutSourceSellsByDay = $filteredOutSourceSells->groupBy(function ($invoice) {
    //         return Carbon::parse($invoice->invoice_date)->format('Y-m-d');
    //     })->map(function ($group) {
    //         return $group->flatMap(function ($invoice) {
    //             return $invoice->sells->where('status', 1)->map(function ($sell) {
    //                 return $sell->retail_price * $sell->product_qty;
    //             });
    //         })->sum();
    //     });

    //     $totalStockDeposit = $filteredStockSells->flatMap(function ($invoice) {
    //         return $invoice->sells->where('status', 0)->map(function ($sell) {
    //             return $sell->retail_price * $sell->product_qty;
    //         });
    //     })->sum();

    //     $totalOutSourceDeposit = $filteredOutSourceSells->flatMap(function ($invoice) {
    //         return $invoice->sells->where('status', 1)->map(function ($sell) {
    //             return $sell->retail_price * $sell->product_qty;
    //         });
    //     })->sum();


    //     // Group invoices, purchases, and expenses by day
    //     $invoicesByDay = $invoices->keyBy('date')->map->daily_total;
    //     $purchasesByDay = $purchases->keyBy('date')->map->daily_total;
    //     $expensesByDay = $expenses->keyBy('date')->map->daily_total;

    //     // Calculate overall totals
    //     $totalSum = $invoices->sum('daily_total');
    //     $totalPurchase = $purchases->sum('daily_total');
    //     $totalExpense = $expenses->sum('daily_total');

    //     // Get branch names and current branch name for display
    //     $branchNames = $branchs->pluck('name', 'id');
    //     $currentBranchName = $branch ? $branchNames[$branch] : 'All Profits';

    //     return view('profit.profit', compact(
    //         'invoices',
    //         'purchases',
    //         'expenses',
    //         'invoicesByDay',
    //         'purchasesByDay',
    //         'expensesByDay',
    //         'totalSum',
    //         'totalPurchase',
    //         'totalExpense',
    //         'branchs',
    //         'currentBranchName',
    //         'totalStockDeposit',
    //         'totalOutSourceDeposit',
    //         'filteredOutSourceSellsByDay',
    //         'filteredStockSellsByDay'
    //     ));
    // }

    public function index(Request $request)
    {
        // Get user permissions and current date details
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];
        $branch = $request->query('branch'); // Branch filter
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Get branches based on user type
        if (auth()->user()->is_admin == '1') {
            $branchs = Warehouse::all();
        } else {
            $branchs = Warehouse::whereIn('id', $warehousePermission)->get();
        }

        // Invoices, Purchases, Expenses Queries
        $invoicesQuery = Invoice::selectRaw('DATE(invoice_date) as date, SUM(deposit) as daily_total')
            ->whereMonth('invoice_date', $currentMonth)
            ->whereYear('invoice_date', $currentYear)
            ->groupBy('date');

        $purchasesQuery = PurchaseOrder::selectRaw('DATE(po_date) as date, SUM(deposit) as daily_total')
            ->whereMonth('po_date', $currentMonth)
            ->whereYear('po_date', $currentYear)
            ->groupBy('date');

        $expensesQuery = Expense::selectRaw('DATE(date) as date, SUM(amount_mmk) as daily_total')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->groupBy('date');

        // Apply branch filter if selected
        if ($branch) {
            $invoicesQuery->where('branch', $branch);
            $purchasesQuery->where('branch', $branch);
            $expensesQuery->where('branch', $branch);
        } else {
            $invoicesQuery->whereIn('branch', $branchs->pluck('id')->toArray());
            $purchasesQuery->whereIn('branch', $branchs->pluck('id')->toArray());
            $expensesQuery->whereIn('branch', $branchs->pluck('id')->toArray());
        }

        // Get results for invoices, purchases, expenses
        $invoices = $invoicesQuery->get();
        $purchases = $purchasesQuery->get();
        $expenses = $expensesQuery->get();

        // Stock Sells Query
        $filteredStockSells = Invoice::with('sells')
            ->whereMonth('invoice_date', $currentMonth)
            ->whereYear('invoice_date', $currentYear);


        // Apply branch filter for Stock and OutSource sells
        if ($branch) {
            $filteredStockSells->where('branch', $branch);
        } else {
            $filteredStockSells->whereIn('branch', $branchs->pluck('id')->toArray());
        }

        $filteredStockSells = $filteredStockSells->get()->filter(function ($invoice) {
            return $invoice->sells->isNotEmpty();
        });


        // Group by day
        $filteredStockSellsByDay = $filteredStockSells->groupBy(function ($invoice) {
            return Carbon::parse($invoice->invoice_date)->format('Y-m-d');
        })->map(function ($group) {
            return $group->flatMap(function ($invoice) {
                return $invoice->sells->map(function ($sell) {
                    return $sell->retail_price * $sell->product_qty;
                });
            })->sum();
        });

        $totalStockDeposit = $filteredStockSells->flatMap(function ($invoice) {
            return $invoice->sells->map(function ($sell) {
                return $sell->retail_price * $sell->product_qty;
            });
        })->sum();

        // Group invoices, purchases, and expenses by day
        $invoicesByDay = $invoices->keyBy('date')->map->daily_total;
        $purchasesByDay = $purchases->keyBy('date')->map->daily_total;
        $expensesByDay = $expenses->keyBy('date')->map->daily_total;

        // Calculate overall totals
        $totalSum = $invoices->sum('daily_total');
        $totalPurchase = $purchases->sum('daily_total');
        $totalExpense = $expenses->sum('daily_total');

        // Get branch names and current branch name for display
        $branchNames = $branchs->pluck('name', 'id');
        $currentBranchName = $branch ? $branchNames[$branch] : 'All Profits';

        return view('profit.profit', compact(
            'invoices',
            'purchases',
            'expenses',
            'invoicesByDay',
            'purchasesByDay',
            'expensesByDay',
            'totalSum',
            'totalPurchase',
            'totalExpense',
            'branchs',
            'currentBranchName',
            'totalStockDeposit',
            'filteredStockSellsByDay'
        ));
    }


    public function search(Request $request)
    {
        // Get user permissions and current date details
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];
        $branch = $request->query('branch'); // Branch filter
        $startDate = $request->query('start_date'); // Start date filter
        $endDate = $request->query('end_date'); // End date filter

        // Get branches based on user type
        if (auth()->user()->is_admin == '1') {
            $branchs = Warehouse::all();
        } else {
            $branchs = Warehouse::whereIn('id', $warehousePermission)->get();
        }

        // Invoices, Purchases, Expenses Queries
        $invoicesQuery = Invoice::selectRaw('DATE(invoice_date) as date, SUM(deposit) as daily_total')
            ->when($startDate, function ($query) use ($startDate) {
                return $query->whereDate('invoice_date', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->whereDate('invoice_date', '<=', $endDate);
            })
            ->groupBy('date');

        $purchasesQuery = PurchaseOrder::selectRaw('DATE(po_date) as date, SUM(deposit) as daily_total')
            ->when($startDate, function ($query) use ($startDate) {
                return $query->whereDate('po_date', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->whereDate('po_date', '<=', $endDate);
            })
            ->groupBy('date');

        $expensesQuery = Expense::selectRaw('DATE(date) as date, SUM(amount_mmk) as daily_total')
            ->when($startDate, function ($query) use ($startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->groupBy('date');

        // Apply branch filter if selected
        if ($branch) {
            $invoicesQuery->where('branch', $branch);
            $purchasesQuery->where('branch', $branch);
            $expensesQuery->where('branch', $branch);
        } else {
            $invoicesQuery->whereIn('branch', $branchs->pluck('id')->toArray());
            $purchasesQuery->whereIn('branch', $branchs->pluck('id')->toArray());
            $expensesQuery->whereIn('branch', $branchs->pluck('id')->toArray());
        }

        // Get results for invoices, purchases, expenses
        $invoices = $invoicesQuery->get();
        $purchases = $purchasesQuery->get();
        $expenses = $expensesQuery->get();

        // Stock Sells Query
        $filteredStockSells = Invoice::with('sells')
            ->when($startDate, function ($query) use ($startDate) {
                return $query->whereDate('invoice_date', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->whereDate('invoice_date', '<=', $endDate);
            });

        // Apply branch filter for Stock and OutSource sells
        if ($branch) {
            $filteredStockSells->where('branch', $branch);
        } else {
            $filteredStockSells->whereIn('branch', $branchs->pluck('id')->toArray());
        }

        $filteredStockSells = $filteredStockSells->get()->filter(function ($invoice) {
            return $invoice->sells->isNotEmpty();
        });

        // Group by day
        $filteredStockSellsByDay = $filteredStockSells->groupBy(function ($invoice) {
            return Carbon::parse($invoice->invoice_date)->format('Y-m-d');
        })->map(function ($group) {
            return $group->flatMap(function ($invoice) {
                return $invoice->sells->map(function ($sell) {
                    return $sell->retail_price * $sell->product_qty;
                });
            })->sum();
        });

        $totalStockDeposit = $filteredStockSells->flatMap(function ($invoice) {
            return $invoice->sells->map(function ($sell) {
                return $sell->retail_price * $sell->product_qty;
            });
        })->sum();

        // Group invoices, purchases, and expenses by day
        $invoicesByDay = $invoices->keyBy('date')->map->daily_total;
        $purchasesByDay = $purchases->keyBy('date')->map->daily_total;
        $expensesByDay = $expenses->keyBy('date')->map->daily_total;

        // Calculate overall totals
        $totalSum = $invoices->sum('daily_total');
        $totalPurchase = $purchases->sum('daily_total');
        $totalExpense = $expenses->sum('daily_total');

        // Get branch names and current branch name for display
        $branchNames = $branchs->pluck('name', 'id');
        $currentBranchName = $branch ? $branchNames[$branch] : 'All Profits';

        return view('profit.profit', compact(
            'invoices',
            'purchases',
            'expenses',
            'invoicesByDay',
            'purchasesByDay',
            'expensesByDay',
            'totalSum',
            'totalPurchase',
            'totalExpense',
            'branchs',
            'currentBranchName',
            'totalStockDeposit',
            'filteredStockSellsByDay'
        ));
    }
}
