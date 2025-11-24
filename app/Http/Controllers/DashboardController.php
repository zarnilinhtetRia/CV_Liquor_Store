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

        return view('dashboard');
    }

    public function home()
    {

        return view('home');
    }
}
