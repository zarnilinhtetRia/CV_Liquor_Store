<?php

namespace App\Http\Controllers;

use App\Models\Sell;
use App\Models\Invoice;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index($branch = null)
    {
        $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];

        if (auth()->user()->is_admin == '1') {
            if ($branch) {
                $invoiceCount = Invoice::where('status', 'invoice')
                    ->where('invoice_category', 'Invoice')->where('branch', $branch)
                    ->whereMonth('created_at', now()->month) // Current month
                    ->whereYear('created_at', now()->year)   // Current year
                    ->count();

                $posCount = Invoice::where('status', 'pos')
                    ->where('invoice_category', 'POS')->where('branch', $branch)
                    ->whereMonth('created_at', now()->month) // Current month
                    ->whereYear('created_at', now()->year)   // Current year
                    ->count();


                $quotationCount = Invoice::where('status', 'quotation')
                    ->where('invoice_category', 'quotation')->where('branch', $branch)
                    ->whereMonth('created_at', now()->month) // Current month
                    ->whereYear('created_at', now()->year)   // Current year
                    ->count();

                $purchaseOrderCount = PurchaseOrder::where('balance_due', 'PO')
                    ->where('status', 'invoice')->where('branch', $branch)
                    ->whereMonth('created_at', now()->month) // Current month
                    ->whereYear('created_at', now()->year)   // Current year
                    ->count();

                $monthNames = [
                    1 => 'Jan',
                    2 => 'Feb',
                    3 => 'Mar',
                    4 => 'Apr',
                    5 => 'May',
                    6 => 'Jun',
                    7 => 'Jul',
                    8 => 'Aug',
                    9 => 'Sep',
                    10 => 'Oct',
                    11 => 'Nov',
                    12 => 'Dec'
                ];

                $chart = [];
                $posChart = [];
                $poChart = [];

                // Fetch invoices for the current year
                $invoices = Invoice::where('status', 'invoice')
                    ->where('invoice_category', 'Invoice')->where('branch', $branch)
                    ->whereYear('created_at', now()->year) // Get all months of this year
                    ->get();

                // Loop through invoices and sum totals per month
                foreach ($invoices as $invoice) {
                    $monthIndex = $invoice->created_at->month; // Get numeric month (1-12)
                    $month = $monthNames[$monthIndex] ?? $monthIndex; // Get month name

                    // Sum total amounts per month
                    if (!isset($chart[$month])) {
                        $chart[$month] = 0; // Initialize if not set
                    }
                    $chart[$month] += $invoice->total;
                }

                // dd($chart);

                // Fetch invoices for the current year
                $point_of_sales = Invoice::where('status', 'pos')
                    ->where('invoice_category', 'POS')->where('branch', $branch)
                    ->whereYear('created_at', now()->year) // Get all months of this year
                    ->get();

                // Loop through invoices and sum totals per month
                foreach ($point_of_sales as $pos) {
                    $monthIndex = $pos->created_at->month; // Get numeric month (1-12)
                    $month = $monthNames[$monthIndex] ?? $monthIndex; // Get month name

                    // Sum total amounts per month
                    if (!isset($posChart[$month])) {
                        $posChart[$month] = 0; // Initialize if not set
                    }
                    $posChart[$month] += $pos->total;
                }

                // Fetch invoices for the current year
                $purchase_orders = PurchaseOrder::where('balance_due', 'PO')
                    ->where('status', 'invoice')->where('branch', $branch)
                    ->whereYear('created_at', now()->year) // Get all months of this year
                    ->get();

                // Loop through invoices and sum totals per month
                foreach ($purchase_orders as $po) {
                    $monthIndex = $po->created_at->month; // Get numeric month (1-12)
                    $month = $monthNames[$monthIndex] ?? $monthIndex; // Get month name

                    // Sum total amounts per month
                    if (!isset($poChart[$month])) {
                        $poChart[$month] = 0; // Initialize if not set
                    }
                    $poChart[$month] += $po->total;
                }

                $warrantyClaimCounts = Sell::select('product_name', DB::raw('count(*) as count'))
                    ->whereMonth('created_at', now()->month)->where('warehouse', $branch)
                    ->groupBy('product_name')
                    ->orderBy('count', 'desc')
                    ->take(8)
                    ->get();
            } else {
                $invoiceCount = Invoice::where('status', 'invoice')
                    ->where('invoice_category', 'Invoice')
                    ->whereMonth('created_at', now()->month) // Current month
                    ->whereYear('created_at', now()->year)   // Current year
                    ->count();

                $posCount = Invoice::where('status', 'pos')
                    ->where('invoice_category', 'POS')
                    ->whereMonth('created_at', now()->month) // Current month
                    ->whereYear('created_at', now()->year)   // Current year
                    ->count();


                $quotationCount = Invoice::where('status', 'quotation')
                    ->where('invoice_category', 'quotation')
                    ->whereMonth('created_at', now()->month) // Current month
                    ->whereYear('created_at', now()->year)   // Current year
                    ->count();

                $purchaseOrderCount = PurchaseOrder::where('balance_due', 'PO')
                    ->where('status', 'invoice')
                    ->whereMonth('created_at', now()->month) // Current month
                    ->whereYear('created_at', now()->year)   // Current year
                    ->count();

                $monthNames = [
                    1 => 'Jan',
                    2 => 'Feb',
                    3 => 'Mar',
                    4 => 'Apr',
                    5 => 'May',
                    6 => 'Jun',
                    7 => 'Jul',
                    8 => 'Aug',
                    9 => 'Sep',
                    10 => 'Oct',
                    11 => 'Nov',
                    12 => 'Dec'
                ];

                $chart = [];
                $posChart = [];
                $poChart = [];

                // Fetch invoices for the current year
                $invoices = Invoice::where('status', 'invoice')
                    ->where('invoice_category', 'Invoice')
                    ->whereYear('created_at', now()->year) // Get all months of this year
                    ->get();

                // Loop through invoices and sum totals per month
                foreach ($invoices as $invoice) {
                    $monthIndex = $invoice->created_at->month; // Get numeric month (1-12)
                    $month = $monthNames[$monthIndex] ?? $monthIndex; // Get month name

                    // Sum total amounts per month
                    if (!isset($chart[$month])) {
                        $chart[$month] = 0; // Initialize if not set
                    }
                    $chart[$month] += $invoice->total;
                }

                // dd($chart);

                // Fetch invoices for the current year
                $point_of_sales = Invoice::where('status', 'pos')
                    ->where('invoice_category', 'POS')
                    ->whereYear('created_at', now()->year) // Get all months of this year
                    ->get();

                // Loop through invoices and sum totals per month
                foreach ($point_of_sales as $pos) {
                    $monthIndex = $pos->created_at->month; // Get numeric month (1-12)
                    $month = $monthNames[$monthIndex] ?? $monthIndex; // Get month name

                    // Sum total amounts per month
                    if (!isset($posChart[$month])) {
                        $posChart[$month] = 0; // Initialize if not set
                    }
                    $posChart[$month] += $pos->total;
                }

                // Fetch invoices for the current year
                $purchase_orders = PurchaseOrder::where('balance_due', 'PO')
                    ->where('status', 'invoice')
                    ->whereYear('created_at', now()->year) // Get all months of this year
                    ->get();

                // Loop through invoices and sum totals per month
                foreach ($purchase_orders as $po) {
                    $monthIndex = $po->created_at->month; // Get numeric month (1-12)
                    $month = $monthNames[$monthIndex] ?? $monthIndex; // Get month name

                    // Sum total amounts per month
                    if (!isset($poChart[$month])) {
                        $poChart[$month] = 0; // Initialize if not set
                    }
                    $poChart[$month] += $po->total;
                }

                $warrantyClaimCounts = Sell::select('product_name', DB::raw('count(*) as count'))
                    ->whereMonth('created_at', now()->month)
                    ->groupBy('product_name')
                    ->orderBy('count', 'desc')
                    ->take(8)
                    ->get();
            }
        } else {
            $invoiceCount = Invoice::where('status', 'invoice')
                ->where('invoice_category', 'Invoice')
                ->whereMonth('created_at', now()->month) // Current month
                ->whereYear('created_at', now()->year)   // Current year
                ->whereIn('branch', $warehousePermission)
                ->count();


            $posCount = Invoice::where('status', 'pos')
                ->where('invoice_category', 'POS')
                ->whereMonth('created_at', now()->month) // Current month
                ->whereYear('created_at', now()->year)   // Current year
                ->whereIn('branch', $warehousePermission)
                ->count();

            $quotationCount = Invoice::where('status', 'quotation')
                ->where('invoice_category', 'quotation')
                ->whereMonth('created_at', now()->month) // Current month
                ->whereYear('created_at', now()->year)   // Current year
                ->whereIn('branch', $warehousePermission)
                ->count();

            $purchaseOrderCount = PurchaseOrder::where('balance_due', 'PO')
                ->where('status', 'invoice')
                ->whereMonth('created_at', now()->month) // Current month
                ->whereYear('created_at', now()->year)   // Current year
                ->whereIn('branch', $warehousePermission)
                ->count();


            $monthNames = [
                1 => 'Jan',
                2 => 'Feb',
                3 => 'Mar',
                4 => 'Apr',
                5 => 'May',
                6 => 'Jun',
                7 => 'Jul',
                8 => 'Aug',
                9 => 'Sep',
                10 => 'Oct',
                11 => 'Nov',
                12 => 'Dec',
            ];

            $chart = [];
            $posChart = [];
            $poChart = [];

            // Fetch invoices for the current year
            $invoices = Invoice::where('status', 'invoice')
                ->where('invoice_category', 'Invoice')
                ->whereMonth('created_at', now()->month) // Current month
                ->whereYear('created_at', now()->year)   // Current year
                ->where('branch', $warehousePermission)
                ->get();

            // Loop through invoices and sum totals per month
            foreach ($invoices as $invoice) {
                $monthIndex = $invoice->created_at->month; // Get numeric month (1-12)
                $month = $monthNames[$monthIndex] ?? $monthIndex; // Get month name

                // Sum total amounts per month
                if (!isset($chart[$month])) {
                    $chart[$month] = 0; // Initialize if not set
                }
                $chart[$month] += $invoice->total;
            }

            // Fetch point of sales for the current year
            $point_of_sales = Invoice::where('status', 'pos')
                ->where('invoice_category', 'POS')
                ->whereMonth('created_at', now()->month) // Current month
                ->whereYear('created_at', now()->year)   // Current year
                ->where('branch', $warehousePermission)
                ->get();

            // Loop through point of sales and sum totals per month
            foreach ($point_of_sales as $pos) {
                $monthIndex = $pos->created_at->month; // Get numeric month (1-12)
                $month = $monthNames[$monthIndex] ?? $monthIndex; // Get month name

                // Sum total amounts per month
                if (!isset($posChart[$month])) {
                    $posChart[$month] = 0; // Initialize if not set
                }
                $posChart[$month] += $pos->total;
            }

            // Fetch purchase orders for the current year
            $purchase_orders = PurchaseOrder::where('balance_due', 'PO')
                ->where('status', 'invoice')
                ->whereMonth('created_at', now()->month) // Current month
                ->whereYear('created_at', now()->year)   // Current year
                ->whereIn('branch', $warehousePermission)
                ->get();

            // Loop through purchase orders and sum totals per month
            foreach ($purchase_orders as $po) {
                $monthIndex = $po->created_at->month; // Get numeric month (1-12)
                $month = $monthNames[$monthIndex] ?? $monthIndex; // Get month name

                // Sum total amounts per month
                if (!isset($poChart[$month])) {
                    $poChart[$month] = 0; // Initialize if not set
                }
                $poChart[$month] += $po->total;
            }
            $warrantyClaimCounts = Sell::select('product_name', DB::raw('count(*) as count'))->whereIn('warehouse', $warehousePermission)
                ->whereMonth('created_at', now()->month)
                ->groupBy('product_name')
                ->orderBy('count', 'desc')
                ->take(8)
                ->get();
        }


        // dd($chart);

        $branchs = Warehouse::all();
        $branchNames = $branchs->pluck('name', 'id');

        $currentBranchName = $branch ? $branchNames[$branch] : 'All Locations';
        return view('dashboard', compact('invoiceCount', 'posCount', 'quotationCount', 'purchaseOrderCount', 'purchaseOrderCount', 'chart', 'posChart', 'poChart', 'warrantyClaimCounts', 'branchs', 'currentBranchName', 'branchNames'));
    }

    public function home()
    {

        return view('home');
    }
}
