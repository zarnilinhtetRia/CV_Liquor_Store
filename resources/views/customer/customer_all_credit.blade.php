@include('layouts.header')

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav col-md-6">
                <li class="nav-item">
                    <a class="nav-link text-white" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white" href="#">Date -
                        <?= $currentDate = date('d-m-y') ?></a>
                </li>


            </ul>

            <!-- Right navbar links -->
            <ul class="ml-auto navbar-nav">


                <div class="btn-group">
                    <button type="button" class="btn text-white dropdown-toggle" data-toggle="dropdown"
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

                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Customer's Credit</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">Customer's Credit
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>{{ session('success') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>{{ session('error') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if ($errors->has('phno'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong> {{ $errors->first('phno') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif


                <div class="ml-2 container-fluid">

                    <!-- left column -->

                    <!-- general form elements -->



                    <div class="row">
                        <div class="mr-auto col">
                            <form action="{{ url('all_customer_credit') }}" method="get">
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label for="start_date">Date From:</label>
                                        <input type="date" name="start_date" class="form-control" required>
                                    </div>
                                    <div class="col-md-3 form-group">
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
                    <!-- /.modal -->
                    <div class="mt-3 col-md-12">
                        <div class="card ">
                            <div class="card-header">

                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>

                                        <tr>
                                            <th>No.</th>
                                            <th>Invoice Number</th>
                                            {{-- <th>Branch Invoice Number</th> --}}
                                            <th>Customer Name</th>
                                            <th>Status</th>
                                            <th>Deposit</th>
                                            <th>Remain Balance</th>
                                            <th>Amount</th>
                                            <th>Invoice Date</th>
                                            <th>Payment</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($invoices as $key => $invoice)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $invoice->invoice_no }}</td>
                                                {{-- <td>{{ $invoice->branch_invoice_no }}</td> --}}
                                                <td>{{ $invoice->customer_name }}</td>
                                                <td>
                                                    @if ($invoice->deposit <= 0)
                                                        @if ($invoice->deposit == null)
                                                        @else
                                                            <span class="px-4 py-1"
                                                                style="background-color:#FF6B6B; border-radius:5px;">Unpaid</span>
                                                        @endif
                                                    @endif

                                                    @if ($invoice->deposit !== $invoice->total)
                                                        @if ($invoice->deposit >= 1)
                                                            <span class="px-4 py-1"
                                                                style="background-color:#FFFD8D; border-radius:5px;">Patial</span>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($invoice->deposit !== null)
                                                        {{ $invoice->deposit }}
                                                    @else
                                                        0
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($invoice->remain_balance)
                                                        {{ $invoice->remain_balance }}
                                                    @else
                                                        0
                                                    @endif
                                                </td>
                                                <td>{{ $invoice->total }}</td>
                                                <td>{{ $invoice->invoice_date }}</td>
                                                <td>
                                                    <a href="{{ url('make_payment', $invoice->id) }}"
                                                        class="btn btn-success">
                                                        <i class="fa-solid fa-file-invoice-dollar"></i>
                                                    </a>
                                                </td>

                                            </tr>
                                        @endforeach


                                    </tbody>
                                    <tfoot>
                                        <tr>

                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-right">Total</td>
                                            <td>{{ $deposit }}</td>
                                            <td>{{ $balance }}</td>
                                            <td> {{ $total_amount }}</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>

                                </table>
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
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "pageLength": 100,
                "buttons": [{
                        extend: 'excelHtml5',
                        text: 'Excel Export',
                        filename: 'customer_credit', // Set filename here
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

                ]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        });
    </script>

</body>

</html>
