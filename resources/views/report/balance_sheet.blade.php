@include('layouts.header')

<style>
    .nav-tabs .nav-link {
        color: #007FFF;
        transition: color 0.3s, background-color 0.3s;
    }

    .nav-tabs .nav-link:hover {
        color: #0056b3;
        background-color: #e6f7ff;
    }

    .nav-tabs .nav-link.active {
        color: #ffffff;
        background-color: #007FFF;
        border-color: #007FFF;
    }
</style>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav col-md-6">
                <li class="nav-item">
                    <a class="text-white nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>

                <li class="nav-item">
                    <a class="text-white nav-link" href="#">Date -
                        <?= $currentDate = date('d-m-y') ?></a>
                </li>


            </ul>

            <!-- Right navbar links -->
            <ul class="ml-auto navbar-nav">


                <div class="btn-group">
                    <button type="button" class="text-white btn dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        {{ auth()->user()->name }}
                    </button>
                    <div class="dropdown-menu ">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="p-1 btn changelogout " style="width: 157px">
                                <i class="fa-solid fa-right-from-bracket "></i> Logout</button>

                        </form>


                    </div>
                </div>



            </ul>
        </nav>
        @include('layouts.sidebar') <div class="content-wrapper">
            <!-- Main content -->
            <section class="content">
                {{-- Permission Php --}}
                @php
                    $choosePermission = [];
                    if (auth()->user()->permission) {
                        $decodedPermissions = json_decode(auth()->user()->permission, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $choosePermission = $decodedPermissions;
                        }
                    }
                @endphp
                {{-- End Php --}}

                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Balance Sheet</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Balance Sheet</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </section>


                <div class="ml-2 container-fluid">

                    {{-- <div class="my-5 container-fluid">
                        <div class="row">
                            <div class="col-md-10">
                                <form action="{{ url('profit_loss_search') }}" method="get">
                                    <div class="row">
                                        <div class="col-md-2 form-group">
                                            <label for="start_date">Date From:</label>
                                            <input type="date" name="start_date" class="form-control" required>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <label for="end_date">Date To:</label>
                                            <input type="date" name="end_date" class="form-control" required>
                                        </div>


                                        <div class="col-md-2 form-group">
                                            <label for="">&nbsp;</label>
                                            <input type="submit" class="btn btn-primary form-control" value="Search"
                                                style="background-color: #218838">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div> --}}

                    <div class="mt-3 col-md-12">
                        <div class="card ">
                            {{-- <h3 class="card-title  mt-3">Profit & Loss Table</h3> --}}
                            <div class="card-header d-flex justify-content-between align-items-center">

                                <div class="dropdown mr-auto ">
                                    <div id="branchDropdown" class="dropdown ml-auto"
                                        style="display:inline-block; margin-left: 10px;">
                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            {{ $currentBranchName }}
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a href="{{ url('balance_sheet') }}" class="dropdown-item">All </a>
                                            @if (auth()->user()->is_admin == '1')

                                                @foreach ($branches as $drop)
                                                    <a class="dropdown-item"
                                                        href="{{ route('branch_balance_sheet', $drop->id) }}">{{ $drop->name }}</a>
                                                @endforeach
                                            @else
                                                @php
                                                    $userPermissions = auth()->user()->level
                                                        ? json_decode(auth()->user()->level)
                                                        : [];
                                                @endphp
                                                @foreach ($branches as $drop)
                                                    @if (in_array($drop->id, $userPermissions))
                                                        <a class="dropdown-item"
                                                            href="{{ route('branch_profit_loss', $drop->id) }}">{{ $drop->name }}</a>
                                                    @endif
                                                @endforeach
                                            @endif

                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="table-responsive">

                                    <table id="example1" class="table table-bordered table-striped ">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">No.</th>
                                                <th rowspan="
                                                2">
                                                    Account
                                                    Number</th>
                                                <th rowspan="2">Account Name</th>
                                                <th rowspan="2">Transaction Name</th>
                                                <th colspan="2" class="text-center">Opening</th>
                                                <th colspan="2" class="text-center">Current</th>
                                                <th colspan="2" class="text-center">Total</th>
                                            </tr>
                                            <tr>
                                                <th style="background-color: #C5F0C7">IN</th>
                                                <th style="background-color: #FBCCCC">OUT</th>
                                                <th style="background-color: #C5F0C7">IN</th>
                                                <th style="background-color: #FBCCCC">OUT</th>
                                                <th style="background-color: #C5F0C7">IN</th>
                                                <th style="background-color: #FBCCCC">OUT</th>
                                            </tr>
                                        </thead>



                                        {{-- <tbody>
                                            @foreach ($accounts as $key => $account)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td><a
                                                            href="{{ url('/report_account_transaction_payment', $account->id) }}">
                                                            {{ $account->account_number }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $account->account_name }}</td>
                                                    <td style="background-color: #C5F0C7">
                                                        {{ number_format($accountDepositSums[$account->id]['depositInvoiceSumPrevious'] + $accountDepositSums[$account->id]['depositPurchaseReturnSumPrevious'] + $accountDepositSums[$account->id]['depositPaymentInSumPrevious']) }}


                                                    </td>
                                                    <td style="background-color: #FBCCCC">
                                                        {{ number_format($accountDepositSums[$account->id]['depositPurchaseOrderSumPrevious'] + $accountDepositSums[$account->id]['depositSaleReturnSumPrevious'] + $accountDepositSums[$account->id]['depositPaymentOutSumPrevious']) }}


                                                    </td>
                                                    <td style="background-color: #C5F0C7">
                                                        {{ number_format($accountDepositSums[$account->id]['depositInvoiceSumCurrent'] + $accountDepositSums[$account->id]['depositPurchaseReturnSumCurrent'] + $accountDepositSums[$account->id]['depositPaymentInSumCurrent']) }}
                                                    </td>
                                                    <td style="background-color: #FBCCCC">
                                                        {{ number_format($accountDepositSums[$account->id]['depositPurchaseOrderSumCurrent'] + $accountDepositSums[$account->id]['depositSaleReturnSumCurrent'] + $accountDepositSums[$account->id]['depositPaymentOutSumCurrent']) }}
                                                    </td>
                                                    <td style="background-color: #C5F0C7">
                                                        {{ number_format($accountDepositSums[$account->id]['depositInvoiceSumPrevious'] + $accountDepositSums[$account->id]['depositInvoiceSumCurrent'] + $accountDepositSums[$account->id]['depositPurchaseReturnSumCurrent'] + $accountDepositSums[$account->id]['depositPurchaseReturnSumPrevious'] + $accountDepositSums[$account->id]['depositPaymentInSumCurrent'] + $accountDepositSums[$account->id]['depositPaymentInSumPrevious']) }}
                                                    </td>


                                                    <td style="background-color: #FBCCCC">
                                                        {{ number_format(
                                                            $accountDepositSums[$account->id]['depositPurchaseOrderSumPrevious'] +
                                                                $accountDepositSums[$account->id]['depositPurchaseOrderSumCurrent'] +
                                                                $accountDepositSums[$account->id]['depositSaleReturnSumCurrent'] +
                                                                $accountDepositSums[$account->id]['depositSaleReturnSumPrevious'] +
                                                                $accountDepositSums[$account->id]['depositPaymentOutSumCurrent'] +
                                                                $accountDepositSums[$account->id]['depositPaymentOutSumPrevious'],
                                                        ) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody> --}}
                                        <tbody>
                                            @php
                                                $totalOpeningIn = 0;
                                                $totalOpeningOut = 0;
                                                $totalCurrentIn = 0;
                                                $totalCurrentOut = 0;
                                                $totalIn = 0;
                                                $totalOut = 0;
                                            @endphp
                                            @foreach ($transaction as $tran)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td><a
                                                            href="{{ url('/report_account_transaction_payment', [$tran->account->id, $tran->id]) }}">{{ $tran->account->account_number }}</a>
                                                    </td>
                                                    <td>{{ $tran->account->account_name }}</td>
                                                    <td>{{ $tran->transaction_name }}</td>
                                                    <td style="background-color: #C5F0C7">
                                                        {{ number_format($accountDepositSums[$tran->id]['depositprevious']) }}
                                                    </td>
                                                    <td style="background-color: #FBCCCC">
                                                        {{ number_format($accountDepositSums[$tran->id]['expenseprevious']) }}
                                                    </td>
                                                    <td style="background-color: #C5F0C7">
                                                        {{ number_format($accountDepositSums[$tran->id]['depositcurrent']) }}
                                                    </td>
                                                    <td style="background-color: #FBCCCC">
                                                        {{ number_format($accountDepositSums[$tran->id]['expensecurrent']) }}
                                                    </td>
                                                    <td style="background-color: #C5F0C7">
                                                        {{ number_format($accountDepositSums[$tran->id]['depositprevious'] + $accountDepositSums[$tran->id]['depositcurrent']) }}
                                                    </td>
                                                    <td style="background-color: #FBCCCC">
                                                        {{ number_format($accountDepositSums[$tran->id]['expenseprevious'] + $accountDepositSums[$tran->id]['expensecurrent']) }}
                                                    </td>

                                                    @php
                                                        $totalOpeningIn +=
                                                            $accountDepositSums[$tran->id]['depositprevious'];
                                                        $totalOpeningOut +=
                                                            $accountDepositSums[$tran->id]['expenseprevious'];
                                                        $totalCurrentIn +=
                                                            $accountDepositSums[$tran->id]['depositcurrent'];
                                                        $totalCurrentOut +=
                                                            $accountDepositSums[$tran->id]['expensecurrent'];
                                                        $totalIn +=
                                                            $accountDepositSums[$tran->id]['depositprevious'] +
                                                            $accountDepositSums[$tran->id]['depositcurrent'];
                                                        $totalOut +=
                                                            $accountDepositSums[$tran->id]['expenseprevious'] +
                                                            $accountDepositSums[$tran->id]['expensecurrent'];
                                                    @endphp
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="4" class="text-right">Total</th>
                                                <th>{{ number_format($totalOpeningIn) }}</th>
                                                <th>{{ number_format($totalOpeningOut) }}</th>
                                                <th>{{ number_format($totalCurrentIn) }}</th>
                                                <th>{{ number_format($totalCurrentOut) }}</th>
                                                <th>{{ number_format($totalIn) }}</th>
                                                <th>{{ number_format($totalOut) }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>

                    </div>
                </div>
        </div>

        </section>

    </div>



    </div>
    <script src="{{ asset('plugins/jquery/jquery.min.js ') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js ') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js ') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/vfs_fonts.js ') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>



    <script>
        $(function() {
            $("#example1").DataTable({
                "responsive": false,
                "lengthChange": false,
                "autoWidth": false,
                "pageLength": 30,
                "buttons": [{
                        extend: 'excelHtml5',
                        text: 'Excel',
                        filename: 'report_invoices', // Set filename here
                    },
                    {
                        extend: 'pdfHtml5',
                        text: ' PDF'
                    }
                ]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        });
    </script>


</body>

</html>
