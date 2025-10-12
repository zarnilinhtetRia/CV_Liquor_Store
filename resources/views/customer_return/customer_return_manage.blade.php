@include('layouts.header')

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

                <section class="content-header">
                    <div class="container-fluid">
                        <div class="mb-2 row">
                            <div class="col-sm-6">
                                <h1>Customer Return</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>

                                    </li>
                                    <li class="breadcrumb-item">Customer Return</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <div class="container-fluid">
                    <div class="row justify-content-center d-flex">


                    </div>



                    <!-- /.modal -->
                    <div class="mt-3 col-md-12">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>{{ session('success') }}</strong>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        @if (session('delete'))
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <strong>{{ session('delete') }}</strong>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <div class="card ">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title">Customer Return List</h3>
                                <div class="dropdown ml-auto mr-2">
                                    <div id="branchDropdown" class="dropdown ml-auto"
                                        style="display:inline-block; margin-left: 5px;">
                                        <button class="btn btn-primary dropdown-toggle" type="button"
                                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            {{ $currentBranchName }}
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                                            @if (auth()->user()->is_admin == '1')
                                                <a href="{{ url('customer_return_manage') }}" class="dropdown-item">All
                                                    Locations</a>
                                                @foreach ($branchs as $drop)
                                                    <a class="dropdown-item"
                                                        href="{{ route('customer_return', $drop->id) }}">{{ $drop->name }}</a>
                                                @endforeach
                                            @else
                                                @php
                                                    $userPermissions = auth()->user()->level
                                                        ? json_decode(auth()->user()->level)
                                                        : [];
                                                @endphp
                                                @foreach ($branchs as $drop)
                                                    @if (in_array($drop->id, $userPermissions))
                                                        <a class="dropdown-item"
                                                            href="{{ route('customer_return', $drop->id) }}">{{ $drop->name }}</a>
                                                    @endif
                                                @endforeach
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>CR No.</th>
                                            <th>Location</th>
                                            {{-- <th>Branch CR No</th> --}}
                                            <th>Customer Name</th>
                                            <th>Customer Phone</th>
                                            <th> Date</th>
                                            <th>Total</th>
                                            <th>Remaining Balance</th>
                                            <th>Payment Status</th>
                                            {{-- <th>Register Mode</th> --}}
                                            <th>Action</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $no = '1';
                                        @endphp
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
                                        @foreach ($invoices as $invoice)
                                            <tr>

                                                <td>{{ $no }}</td>
                                                <td>
                                                    @if ($invoice->status == 'customer return')
                                                        <span class="text-danger">{{ $invoice->invoice_no }}</span>
                                                    @else
                                                        {{ $invoice->invoice_no }}
                                                    @endif

                                                </td>
                                                <td>{{ $invoice->warehouse->name ?? '' }}</td>
                                                {{-- <td>
                                                    @if ($invoice->status == 'customer return')
                                                        <span
                                                            class="text-danger">{{ $invoice->branch_invoice_no }}</span>
                                                    @else
                                                        {{ $invoice->branch_invoice_no }}
                                                    @endif
                                                </td> --}}
                                                <td>{{ $invoice->customer_name }}</td>
                                                <td>{{ $invoice->phno }}</td>
                                                <td>{{ $invoice->invoice_date }}</td>


                                                <td>
                                                    @if ($invoice->currency_method == 'MMK')
                                                        {{ number_format($invoice->total) }}
                                                    @else
                                                        {{ number_format($invoice->total) }}
                                                    @endif
                                                </td>

                                                <td>
                                                    {{ number_format($invoice->remain_balance) ?? 'N/A' }}
                                                </td>

                                                @if ($invoice->total == $invoice->deposit || $invoice->total < $invoice->deposit)
                                                    <td>
                                                        <span class="badge badge-success text-white">Paid</span>
                                                    </td>
                                                @elseif($invoice->total > $invoice->deposit && $invoice->deposit > 0)
                                                    <td>
                                                        <span class="badge badge-warning text-white">Partial Paid</span>
                                                    </td>
                                                @else
                                                    <td>
                                                        <span class="badge badge-danger text-white">Unpaid</span>
                                                    </td>
                                                @endif
                                                {{-- <td>
                                                    @if ($invoice->status == 'customer return')
                                                        Customer Return
                                                    @else
                                                        {{ $invoice->balance_due }}
                                                    @endif
                                                </td> --}}

                                                <td>



                                                    <a href="{{ url('/invoice_receipt_print', $invoice->id) }}"
                                                        class="btn btn-info btn-sm mb-1"><i
                                                            class="fa-solid fa-print"></i></a>

                                                    <a href="{{ url('/invoice_detail', $invoice->id) }}"
                                                        class="btn btn-primary btn-sm mb-1"><i
                                                            class="fa-solid fa-eye"></i></a>




                                                    @if (in_array('Customer Return Delete', $choosePermission) || auth()->user()->is_admin == '1')
                                                        <a href="{{ url('invoice_delete', $invoice->id) }}"
                                                            class="btn btn-danger btn-sm mb-1"
                                                            onclick="return confirm('Are you sure you want to delete this Invoice ?')"><i
                                                                class="fa-solid fa-trash"></i></a>
                                                    @endif

                                                </td>


                                            </tr>
                                            @php
                                                $no++;
                                            @endphp
                                        @endforeach

                                    </tbody>

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
    <!-- AdminLTE for demo purposes -->
    {{-- <script src="../../dist/js/demo.js"></script> --}}
    <!-- Page specific script -->
    <script>
        $(function() {
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "pageLength": 30,
                // "buttons": ["excel", "pdf", "print"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "pageLength": 30,
                "buttons": ["excel", "pdf", "print"]
            }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
        });
    </script>
</body>

</html>
