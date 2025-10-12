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
                                <h1>Profit & Loss</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Profit & Loss</li>
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
                            <div class="card-header d-flex justify-content-start align-items-center">
                                <div class="dropdown mx-2">
                                    <div id="branchDropdown" class="dropdown ml-auto"
                                        style="display:inline-block; margin-left: 10px;">
                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            {{ $currentBranchName }}
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a href="{{ url('profit_loss') }}" class="dropdown-item">All </a>
                                            @if (auth()->user()->is_admin == '1')

                                                @foreach ($branches as $drop)
                                                    <a class="dropdown-item"
                                                        href="{{ route('branch_profit_loss', $drop->id) }}">{{ $drop->name }}</a>
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

                                <a href="javascript:void(0);" class="btn btn-secondary" id="export-excel">Excel
                                    Export</a>
                            </div>

                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="d-flex justify-content-end mb-3">
                                    <div class="input-group col-3">
                                        <input type="seaa" id="search" class="form-control"
                                            placeholder="Search for...">
                                    </div>
                                </div>

                                <div class="table-responsive">

                                    @php
                                        $totalOpeningBalance = 0;
                                        $totalCurrentBalance = 0;
                                        $no = 1;
                                    @endphp

                                    <table id="transaction-table" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">Particular</th>
                                                <th rowspan="2">Note No.</th>
                                                <th class="text-center">Previous Month</th>
                                                <th class="text-center">Current Month</th>
                                                <th rowspan="2">Remark</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center">MMK</th>
                                                <th class="text-center">MMK</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $groupedTransactions = $transaction->groupBy('account.account_type');
                                                $grandTotalOpeningBalance = 0;
                                                $grandTotalCurrentBalance = 0;
                                            @endphp

                                            @foreach ($groupedTransactions as $accountType => $transactions)
                                                @php
                                                    $hasNonZeroBalance = false;
                                                    $openingBalanceTotal = 0;
                                                    $currentBalanceTotal = 0;
                                                @endphp

                                                @foreach ($transactions as $tran)
                                                    @php
                                                        $previousDeposit =
                                                            $accountDepositSums[$tran->id]['depositprevious'] ?? 0;
                                                        $previousExpense =
                                                            $accountDepositSums[$tran->id]['expenseprevious'] ?? 0;
                                                        $currentDeposit =
                                                            $accountDepositSums[$tran->id]['depositcurrent'] ?? 0;
                                                        $currentExpense =
                                                            $accountDepositSums[$tran->id]['expensecurrent'] ?? 0;

                                                        $openingBalance = $previousDeposit - $previousExpense;
                                                        $currentBalance = $currentDeposit - $currentExpense;

                                                        if ($openingBalance != 0 || $currentBalance != 0) {
                                                            $hasNonZeroBalance = true;
                                                        }

                                                        $openingBalanceTotal += $openingBalance;
                                                        $currentBalanceTotal += $currentBalance;
                                                    @endphp
                                                @endforeach

                                                @if ($hasNonZeroBalance)
                                                    <!-- Account Type Group Total Row -->
                                                    <tr id="title" class="title"
                                                        style="background-color: #a2f2b388">
                                                        <td><strong>{{ $accountType }}</strong></td>
                                                        <td><strong>Total</strong></td>
                                                        <td class="text-center">
                                                            <strong>{{ number_format($openingBalanceTotal) }}</strong>
                                                        </td>
                                                        <td class="text-center">
                                                            <strong>{{ number_format($currentBalanceTotal) }}</strong>
                                                        </td>
                                                        <td></td>
                                                    </tr>

                                                    <!-- Individual Transactions -->
                                                    @foreach ($transactions as $tran)
                                                        @php
                                                            $previousDeposit =
                                                                $accountDepositSums[$tran->id]['depositprevious'] ?? 0;
                                                            $previousExpense =
                                                                $accountDepositSums[$tran->id]['expenseprevious'] ?? 0;
                                                            $currentDeposit =
                                                                $accountDepositSums[$tran->id]['depositcurrent'] ?? 0;
                                                            $currentExpense =
                                                                $accountDepositSums[$tran->id]['expensecurrent'] ?? 0;

                                                            $openingBalance = $previousDeposit - $previousExpense;
                                                            $currentBalance = $currentDeposit - $currentExpense;
                                                        @endphp

                                                        @if ($openingBalance != 0 || $currentBalance != 0)
                                                            <tr>
                                                                <td>
                                                                    <a
                                                                        href="{{ url('/report_account_transaction_payment', [$tran->account->id, $tran->id]) }}">
                                                                        {{ $tran->account->account_name }}
                                                                    </a>
                                                                </td>
                                                                <td></td>
                                                                <td class="text-center">
                                                                    {{ number_format($openingBalance) }}</td>
                                                                <td class="text-center">
                                                                    {{ number_format($currentBalance) }}</td>
                                                                <td></td>
                                                            </tr>
                                                        @endif
                                                    @endforeach

                                                    <!-- Accumulate into Grand Totals -->
                                                    @php
                                                        $grandTotalOpeningBalance += $openingBalanceTotal;
                                                        $grandTotalCurrentBalance += $currentBalanceTotal;
                                                    @endphp
                                                @endif
                                            @endforeach
                                        </tbody>

                                        <!-- Grand Total in Footer -->
                                        <tfoot>
                                            <tr>
                                                <th colspan="2" class="text-right">Grand Total</th>
                                                <th class="text-center">
                                                    <strong>{{ number_format($grandTotalOpeningBalance) }}</strong>
                                                </th>
                                                <th class="text-center">
                                                    <strong>{{ number_format($grandTotalCurrentBalance) }}</strong>
                                                </th>
                                                <th></th>
                                            </tr>
                                        </tfoot>


                                    </table>



                                </div>
                            </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>



    <script>
        $(function() {
            $("#example1").DataTable({
                "responsive": false,
                "lengthChange": false,
                "autoWidth": false,
                "pageLength": 100,
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

        document.getElementById('search').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase(); // Get search term
            const rows = document.querySelectorAll('#transaction-table tbody tr'); // Get all table rows

            rows.forEach(row => {
                // Skip rows with .table-success class
                if (row.classList.contains('title')) {
                    return; // Do not process this row
                }

                const columns = row.getElementsByTagName('td'); // Get all columns in the current row
                let matchFound = false;

                // Check each column in the row for the search term
                for (let i = 0; i < columns.length; i++) {
                    const text = columns[i].textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        matchFound = true;
                        break;
                    }
                }

                // Show or hide the row based on the match
                if (matchFound) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Excel Export
        document.getElementById('export-excel').addEventListener('click', function() {
            var wb = XLSX.utils.table_to_book(document.getElementById('transaction-table'), {
                sheet: "Profit & Loss"
            });
            XLSX.writeFile(wb, "profit_loss.xlsx");
        });
    </script>


</body>

</html>
