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
        @include('layouts.sidebar')
        <div class="content-wrapper">
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
                                <h1>General Ledger</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item active">General Ledger</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </section>


                <div class="ml-2 container-fluid">

                    <div class="my-5 container-fluid">
                        <div class="row">
                            <div class="col-md-10">
                                <form action="{{ url('general_ledger') }}" method="get" id="date_search_form">
                                    <input type="hidden" name="branch_id" id="branch_id"
                                        value="{{ request('branch') }}">
                                    <div class="row">
                                        <div class="col-md-3 form-group">
                                            <label for="start_date">Date From:</label>
                                            <input type="date" name="start_date" id="start_date" class="form-control"
                                                value="{{ request('start_date') }}" required>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label for="end_date">Date To:</label>
                                            <input type="date" name="end_date" id="end_date" class="form-control"
                                                value="{{ request('end_date') }}" required>
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="branch">Location:</label>
                                            <select name="branch" id="branch" class="form-control">
                                                <option value="">All</option>
                                                @if (auth()->user()->is_admin == '1')
                                                    @foreach ($branches as $drop)
                                                        <option value="{{ $drop->id }}"
                                                            {{ request('branch') == $drop->id ? 'selected' : '' }}>
                                                            {{ $drop->name }}
                                                        </option>
                                                    @endforeach
                                                @else
                                                    @php
                                                        $userPermissions = auth()->user()->level
                                                            ? json_decode(auth()->user()->level)
                                                            : [];
                                                    @endphp
                                                    @foreach ($branches as $drop)
                                                        @if (in_array($drop->id, $userPermissions))
                                                            <option value="{{ $drop->id }}"
                                                                {{ request('branch') == $drop->id ? 'selected' : '' }}>
                                                                {{ $drop->name }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </select>
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
                    </div>





                    <div class="mt-3 col-md-12">
                        <div class="card ">
                            <div class="card-header d-flex justify-content-start align-items-center">
                                <!-- <h3 class="card-title">General Ledger</h3> -->
                                <div class="dropdown ml-2 mr-5">
                                    <div id="branchDropdown" class="dropdown ml-auto"
                                        style="display:inline-block; margin-left: 10px;">
                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            {{ $currentBranchName }}
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a href="{{ url('general_ledger') }}" class="dropdown-item">All </a>
                                            @if (auth()->user()->is_admin == '1')

                                                @foreach ($branches as $drop)
                                                    <a class="dropdown-item branch_select"
                                                        href="{{ url('general_ledger') }}"
                                                        data-val="{{ $drop->id }}">{{ $drop->name }}</a>
                                                @endforeach
                                            @else
                                                @php
                                                    $userPermissions = auth()->user()->level
                                                        ? json_decode(auth()->user()->level)
                                                        : [];
                                                @endphp
                                                @foreach ($branches as $drop)
                                                    @if (in_array($drop->id, $userPermissions))
                                                        <a class="dropdown-item branch_select"
                                                            href="{{ url('general_ledger') }}"
                                                            data-val="{{ $drop->id }}">{{ $drop->name }}</a>
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
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Account Name</th>
                                                <th>Location</th>
                                                <th>Name</th>
                                                <th style="background-color: #C5F0C7">IN</th>
                                                <th style="background-color: #FBCCCC">OUT</th>
                                                <th>Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- @foreach ($accounts as $key => $account)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>
                                                        @if (request('start_date') && request('end_date'))
                                                            <a
                                                                href="{{ url('/report_account_transaction_payment_search/' . $account->id . '?start_date=' . request('start_date') . '&end_date=' . request('end_date')) }}">
                                                                {{ $account->account_number }}
                                                            </a>
                                                        @else
                                                            <a
                                                                href="{{ url('/report_account_transaction_payment', $account->id) }}">
                                                                {{ $account->account_number }}
                                                            </a>
                                                        @endif
                                                    </td>
                                                    <td>{{ $account->account_name }}</td>
                                                    <td>{{ $account->account_type }}</td>
                                                    <!-- <td>{{ $account->account_bl_pl }}</td> -->
                                                    <td style="background-color: #C5F0C7">
                                                        {{ number_format($accountDepositSums[$account->id]['depositInvoiceSum'] + $accountDepositSums[$account->id]['depositPurchaseReturnSum'] + $accountDepositSums[$account->id]['depositPaymentInSum']) }}
                                                    </td>
                                                    <td style="background-color: #FBCCCC">
                                                        {{ number_format($accountDepositSums[$account->id]['depositPurchaseOrderSum'] + $accountDepositSums[$account->id]['depositSaleReturnSum'] + $accountDepositSums[$account->id]['depositPaymentOutSum']) }}
                                                    </td>
                                                </tr>
                                            @endforeach --}}

                                            @php
                                                $no = 1;
                                                $totalAmount = 0; // Initialize total amount
                                                $totalDeposit = 0; // Initialize total amount
                                                $totalExpense = 0; // Initialize total amount
                                            @endphp
                                            @foreach ($transaction as $transactions)
                                                <tr>
                                                    <td>{{ $no }}</td>
                                                    <!-- <td><a href="{{ url('general_ledger_detail', $transactions->id) }}">{{-- $transactions->account->account_name --}}</a></td> -->
                                                    <td>{{ $transactions->account->account_name }}</td>
                                                    <td>{{ $transactions->warehouse->name }}</td>
                                                    <td>{{ $transactions->transaction_name }}</td>
                                                    <td style="background-color: #C5F0C7">
                                                        {{ number_format($accountDepositSums[$transactions->id]['depositcurrent']) }}
                                                    </td>
                                                    <td style="background-color: #FBCCCC">
                                                        {{ number_format($accountDepositSums[$transactions->id]['expensecurrent']) }}
                                                    </td>
                                                    @php
                                                        $totalAmount +=
                                                            $accountDepositSums[$transactions->id]['depositcurrent'] -
                                                            $accountDepositSums[$transactions->id]['expensecurrent'];
                                                        $totalDeposit +=
                                                            $accountDepositSums[$transactions->id]['depositcurrent'];
                                                        $totalExpense +=
                                                            $accountDepositSums[$transactions->id]['expensecurrent'];
                                                    @endphp
                                                    <td>
                                                        {{ number_format($accountDepositSums[$transactions->id]['depositcurrent'] - $accountDepositSums[$transactions->id]['expensecurrent']) }}
                                                    </td>

                                                </tr>
                                                @php
                                                    $no++;
                                                @endphp
                                            @endforeach

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th class="text-right">Total</th>
                                                <th>{{ number_format($totalDeposit) }}</th>
                                                <th>{{ number_format($totalExpense) }}</th>
                                                <th>{{ number_format($totalAmount) }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>

                    </div>
                </div>
            </section>
        </div>

    </div>



    <!-- </div> -->
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
                        filename: 'report_general_ledger', // Set filename here
                        footer: true, // Ensure the footer is included in the export
                        customize: function(xlsx) {
                            var sheet = xlsx.xl.worksheets['sheet1.xml'];
                            var lastRow = $('row:last', sheet); // Get last row in the sheet

                            // Clone last row (which is the total row) and append it as the Grand Total row
                            var grandTotalRow = lastRow.clone();
                            $('row:last', sheet).after(grandTotalRow);

                            // Modify the first cell to indicate "Grand Total"
                            $('c[r^="A"]', grandTotalRow).attr('t', 'inlineStr').find('is t').text(
                                'Grand Total');
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: ' PDF'
                    }
                ]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        });
    </script>

    {{-- <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).on('click', '.branch_select', function(e) {
            e.preventDefault(); // Prevent the default link behavior

            // Get the href attribute of the clicked element
            let path = $(this).attr('href');

            // Get the start_date and end_date values
            let start_date = $('#start_date').val() ?? null;
            let end_date = $('#end_date').val() ?? null;
            let branch_id = $(this).attr('data-val');
            $('#branch_id').val(branch_id);

            // Log the start and end dates for debugging
            console.log(start_date + '-' + end_date);

            // Check if both dates are provided
            if (start_date && end_date) {
                path += `/?branch=${branch_id}&start_date=${start_date}&end_date=${end_date}`;
            } else {
                path += `/?branch=${branch_id}`;
            }

            console.log('Request URL:', path);

            window.location.href = path;
        });

        $(document).on('submit', '#date_search_form', function(e) {
            e.preventDefault(); // Prevent the default link behavior

            // Get the href attribute of the clicked element
            let path = $(this).attr('action');

            // Get the start_date and end_date values
            let start_date = $('#start_date').val() ?? null;
            let end_date = $('#end_date').val() ?? null;
            let branch_id = $('#branch_id').val();

            // Log the start and end dates for debugging
            console.log(start_date + '-' + end_date);

            // Check if both dates are provided
            if (start_date && end_date) {
                path += `/?branch=${branch_id}&start_date=${start_date}&end_date=${end_date}`;
            } else {
                path += `/?branch=${branch_id}`;
            }

            console.log('Request URL:', path);

            window.location.href = path;
            alert('Search Result Updated!');

        });
    </script> --}}


</body>

</html>
