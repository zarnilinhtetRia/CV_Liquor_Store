<style>
    .main-sidebar {
        /* background: radial-gradient(circle, #4549aa, #d1413d); */
        background: #092366;
        font-family:
            "Times New Roman", serif;

    }

    .nav-link {
        background-color: transparent;
        /* Default background */
        transition: background-color 0.3s, color 0.3s;
        /* Smooth transition */
    }

    .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.2);

    }
</style>
<aside class="main-sidebar sidebar-primary elevation-4">
    <!-- Brand Logo -->
    <span class="text-center brand-link ">
        <span class="text-white brand-text font-weight-bold">POS</span>
    </span>


    <!-- Sidebar -->
    <div class="sidebar ">


        <!-- Sidebar Menu -->
        <nav class="my-2">
            <ul class="nav nav-pills nav-sidebar flex-column " data-widget="treeview" role="menu" data-accordion="false">
                @php
                    $userPermissions = [];
                    if (auth()->user()->permission) {
                        $decodedPermissions = json_decode(auth()->user()->permission, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $userPermissions = $decodedPermissions;
                        }
                    }
                @endphp

                <li class="nav-item">
                    <a href="{{ url('/home') }}" class="nav-link">
                        <i class="text-white fa-solid fa-house nav-icon "></i>
                        <p class="pl-3 text-white">
                            Home </p>
                    </a>
                </li>

                @if (in_array('Dashboard', $userPermissions) || auth()->user()->is_admin == '1')
                    <li class="nav-item">
                        <a href="{{ url('/dashboard') }}" class="nav-link">
                            <i class="fa-solid fa-chart-line nav-icon text-white"></i>
                            <p class="pl-3 text-white">
                                Dashboard </p>
                        </a>
                    </li>
                @endif

                @if (in_array('Item', $userPermissions) || auth()->user()->is_admin == '1')
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="text-white nav-icon fas fa-table"></i>
                            <p class="pl-3 text-white">
                                Product
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('items') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">Product List</p>
                                </a>
                            </li>
                            @if (in_array('Item Register', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('items_register') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon "></i>
                                        <p class="text-white">Add Product</p>
                                    </a>
                                </li>
                            @endif
                            <!-- <li class="nav-item">
                                <a href="{{ url('items_qty') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">Items</p>
                                </a>
                            </li> -->
                            {{-- <li class="nav-item">
                                <a href="{{ url('new_order_items') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">New Order Product</p>
                                </a>
                            </li> --}}
                            <li class="nav-item">
                                <a href="{{ url('item_delete_record') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">Delete Product Record</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if (in_array('Customer', $userPermissions) || auth()->user()->is_admin == '1')
                    <li class="nav-item">
                        <a href="{{ url('/customer') }}" class="nav-link">
                            <i class="text-white fa-solid fa-user-plus nav-icon"></i>
                            <p class="pl-3 text-white">
                                Customer</p>
                            </p><i class="text-white right fas fa-angle-left"></i>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('customer') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">Customer List</p>
                                </a>
                            </li>
                            @if (in_array('All Customer Credit', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('all_customer_credit') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">Customer Credit</p>
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </li>

                @endif


                {{-- @if (in_array('POS', $userPermissions) || auth()->user()->is_admin == '1')
                    <li class="nav-item">
                        <a href="{{ url('/pos') }}" class="nav-link">
                            <i class="text-white fa-solid fa-cart-plus nav-icon"></i>
                            <p class="pl-3 text-white">
                                POS
                            </p><i class="text-white right fas fa-angle-left"></i>
                        </a>


                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('pos') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">POS Management</p>
                                </a>
                            </li>
                            @if (in_array('POS Register', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('pos_register') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">Issue POS</p>
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </li>
                @endif --}}

                @if (in_array('Invoice', $userPermissions) || auth()->user()->is_admin == '1')
                    <li class="nav-item">
                        <a href="{{ url('/invoice') }}" class="nav-link">
                            <i class="text-white nav-icon fas fa-copy"></i>
                            <p class="pl-3 text-white">
                                Invoice
                            </p><i class="text-white right fas fa-angle-left"></i>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('invoice') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">Invoice List</p>
                                </a>
                            </li>
                            @if (in_array('Invoice Register', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('invoice_reg') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">Add Invoice</p>
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </li>
                @endif

                @if (in_array('Customer Return', $userPermissions) || auth()->user()->is_admin == '1')
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fa-solid fa-arrow-rotate-left text-white nav-icon"></i>
                            <p class="pl-3 text-white">
                                Customer Return
                            </p><i class="text-white right fas fa-angle-left"></i>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('customer_return_manage') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">Return List</p>
                                </a>
                            </li>
                            @if (in_array('Issue Customer Return', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('customer_return') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">Add Return</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if (in_array('Quotation', $userPermissions) || auth()->user()->is_admin == '1')
                    <li class="nav-item">
                        <a href="{{ url('/quotation') }}" class="nav-link">

                            <i class="text-white fa-solid fa-file-invoice-dollar nav-icon"></i>
                            <p class="pl-3 text-white">
                                Quotation
                            </p><i class="text-white right fas fa-angle-left"></i>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('quotation') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">Quotation List</p>
                                </a>
                            </li>
                            @if (in_array('Quotation Register', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('quotation_register') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">Add Quotation</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif



                @if (in_array('Purchase Order', $userPermissions) || auth()->user()->is_admin == '1')
                    <li class="nav-item">
                        <a href="" class="nav-link">
                            <i class="text-white fa-solid fa-receipt nav-icon"></i>
                            <p class="pl-3 text-white">
                                Purchase Order
                            </p><i class="text-white right fas fa-angle-left"></i>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('purchase_order_manage') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">Purchase Order List</p>
                                </a>
                            </li>
                            @if (in_array('Purchase Order Register', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('purchase_order_register') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">Add Purchase Order</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if (in_array('Daily Delivered', $userPermissions) || auth()->user()->is_admin == '1')
                    <li class="nav-item">
                        <a href="{{ url('/daily_delivery') }}" class="nav-link">
                            <i class="fa-solid fa-truck nav-icon text-white"></i>
                            <p class="pl-3 text-white">
                                Daily Delivered </p>
                        </a>
                    </li>
                @endif


                @if (in_array('Transfer', $userPermissions) || auth()->user()->is_admin == '1')
                    <li class="nav-item">
                        <a href="{{ url('/warehouse') }}" class="nav-link">
                            <i class="fa-solid fa-arrow-right-arrow-left text-white nav-icon"></i>
                            <p class="pl-3 text-white">
                                Transfer Product
                            </p><i class="text-white right fas fa-angle-left"></i>
                        </a>
                        <ul class="nav nav-treeview">

                            @if (in_array('Transfer Item', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('transfer_item') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">Transfer Product</p>
                                    </a>
                                </li>
                            @endif

                            @if (in_array('Transfer Item', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('show_transfer_history') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">Transfer List</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if (in_array('Expenses', $userPermissions) || auth()->user()->is_admin == '1')
                    <li class="nav-item">
                        <a href="{{ url('expense') }}" class="nav-link">
                            <i class="text-white fa-solid fa-money-check-dollar nav-icon"></i>
                            <p class="pl-3 text-white">
                                Expenses </p><i class="text-white right fas fa-angle-left"></i>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('/expense') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">Expense</p>
                                </a>
                            </li>
                            @if (in_array('Expense Category', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('expense_category') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">Expense Category</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                {{-- @if (in_array('Brand', $userPermissions) || auth()->user()->is_admin == '1')
                    <li class="nav-item">
                        <a href="{{ url('brand') }}" class="nav-link">
                            <i class="text-white fa-solid fa-tags nav-icon"></i>
                            <p class="pl-3 text-white">
                                Brands </p><i class="text-white right fas fa-angle-left"></i>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('/brand') }}" class="nav-link">
                                    <i class="text-white far fa-circle nav-icon"></i>
                                    <p class="text-white">Brand</p>
                                </a>
                            </li>
                            @if (in_array('Brand Category', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('brand_category') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">Brand Category</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif --}}





                {{-- @if (in_array('Profit', $userPermissions) || auth()->user()->is_admin == '1')
                    <li class="nav-item">
                        <a href="{{ url('/profit') }}" class="nav-link">
                            <i class="text-white fa-solid fa-book nav-icon"></i>
                            <p class="pl-3 text-white">
                                Net Profit
                            </p>
                        </a>
                    </li>
                @endif --}}

                @if (in_array('Report', $userPermissions) || auth()->user()->is_admin == '1')
                    <li class="nav-item">
                        <a href="{{ url('report') }}" class="nav-link">
                            <i class="text-white fa-solid fa-list-ul nav-icon"></i>
                            <p class="pl-3 text-white">
                                Report </p><i class="text-white right fas fa-angle-left"></i>
                        </a>
                        <ul class="nav nav-treeview">

                            @if (in_array('General Ledger', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('general_ledger') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">General Ledger</p>
                                    </a>
                                </li>
                            @endif
                            {{-- @if (in_array('Balance Sheet', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('balance_sheet') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">Balance Sheet</p>
                                    </a>
                                </li>
                            @endif --}}
                            @if (in_array('Profit&Loss', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('profit_loss') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">Profit & Loss</p>
                                    </a>
                                </li>
                            @endif



                            @if (in_array('Invoice Report', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('report') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">Invoices</p>
                                    </a>
                                </li>
                            @endif

                            @if (in_array('Quotation Report', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('report_quotation') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">Quotations</p>
                                    </a>
                                </li>
                            @endif

                            {{-- @if (in_array('POS Report', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('report_pos') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">POS</p>
                                    </a>
                                </li>
                            @endif --}}

                            @if (in_array('Purchase Order Report', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('report_po') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">Purchase Orders</p>
                                    </a>
                                </li>
                            @endif

                            @if (in_array('Purchase Return', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('report_purchase_return') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">Purchase Return</p>
                                    </a>
                                </li>
                            @endif
                            {{--
                            @if (in_array('Sale Return (Invoice)', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('report_sale_return') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">Sale Return (Invoice)</p>
                                    </a>
                                </li>
                            @endif --}}

                            @if (in_array('Item Report', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('report_item') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">Product</p>
                                    </a>
                                </li>
                            @endif

                            @if (in_array('Selling Report', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('selling_report') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">Selling Report</p>
                                    </a>
                                </li>
                            @endif
                              @if (in_array('Purchase Product Report', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('purchase_items') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">Purchase Product</p>
                                    </a>
                                </li>
                            @endif
                            @if (in_array('Receivable', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('receivable') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">Receivable</p>
                                    </a>
                                </li>
                            @endif
                            @if (in_array('Payable', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('payable') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">Payable</p>
                                    </a>
                                </li>
                            @endif

                            @if (in_array('Supplier Report', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('supplier_report') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">Supplier Report</p>
                                    </a>
                                </li>
                            @endif


                            {{-- @if (in_array('Sale Return (POS)', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('sale_return') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">Sale Return (POS)</p>
                                    </a>
                                </li>
                            @endif --}}
                        </ul>
                    </li>
                @endif


                @if (in_array('Account', $userPermissions) ||
                        in_array('Transaction', $userPermissions) ||
                        in_array('Setting', $userPermissions) ||
                        auth()->user()->is_admin == '1')
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fa-solid fa-gears text-white nav-icon"></i>
                            <p class="pl-3 text-white">
                                Accounting Setting </p><i class="text-white right fas fa-angle-left"></i>
                        </a>
                        <ul class="nav nav-treeview">
                            @if (in_array('Account', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ route('finance#accountManagement') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">Account</p>
                                    </a>
                                </li>
                            @endif

                            @if (in_array('Transaction', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ route('finance#transactionManagement') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">Transaction</p>
                                    </a>
                                </li>
                            @endif
                            @if (in_array('Setting', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('setting') }}" class="nav-link">
                                        <i class="text-white far fa-circle nav-icon"></i>
                                        <p class="text-white">Setting</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                @if (in_array('Unit', $userPermissions) ||
                        in_array('Supplier', $userPermissions) ||
                        auth()->user()->is_admin == '1' ||
                        in_array('User', $userPermissions))
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fa-solid text-white fa-screwdriver-wrench nav-icon"></i>
                            <p class="pl-3 text-white">
                                Additional Setting </p><i class="text-white right fas fa-angle-left"></i>
                        </a>
                        <ul class="nav nav-treeview">
                            @if (in_array('Location', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('/warehouse') }}" class="nav-link">
                                        <i class="text-white fa-solid fa-house nav-icon "></i>
                                        <p class="pl-3 text-white">
                                            Location </p>
                                    </a>
                                </li>
                            @endif
                            @if (in_array('Unit', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('unit') }}" class="nav-link">
                                        <i class="text-white fa-solid fa-brands fa-ubuntu nav-icon"></i>
                                        <p class="pl-3 text-white">Unit</p>
                                    </a>
                                </li>
                            @endif

                            @if (in_array('Supplier', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('/supplier') }}" class="nav-link">
                                        <i class="text-white fa-solid fa-boxes-packing nav-icon"></i>
                                        <p class="pl-3 text-white">Supplier</p>
                                    </a>
                                </li>
                            @endif
                            @if (in_array('User', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a class="nav-link">
                                        <i class="text-white fa-solid fa-users nav-icon"></i>
                                        <p class="pl-3 text-white">
                                            User </p><i class="text-white right fas fa-angle-left"></i>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="{{ url('/user') }}" class="nav-link">
                                                <i class="text-white far fa-circle nav-icon"></i>
                                                <p class="text-white">User</p>
                                            </a>
                                        </li>
                                        @if (in_array('User Type', $userPermissions) || auth()->user()->is_admin == '1')
                                            <li class="nav-item">
                                                <a href="{{ url('/user_type') }}" class="nav-link">
                                                    <i class="text-white far fa-circle nav-icon"></i>
                                                    <p class="text-white">User Type</p>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                            @if (in_array('Configuration', $userPermissions) || auth()->user()->is_admin == '1')
                                <li class="nav-item">
                                    <a href="{{ url('/config_manage') }}" class="nav-link">
                                        <i class="fa-solid fa-gear nav-icon text-white"></i>
                                        <p class="pl-3 text-white">
                                            Configuration
                                        </p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif



            </ul>
        </nav>


    </div>
</aside>
