@include('layouts.header')

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav col-md-6">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars text-white"></i></a>
                </li>

                <li class="nav-item text-white">
                    <a class="nav-link text-white" href="#">Date -
                        <?= $currentDate = date('d-m-y') ?></a>
                </li>


            </ul>

            <!-- Right navbar links -->
            <ul class="ml-auto navbar-nav">


                <div class="btn-group">
                    <button type="button" class="btn dropdown-toggle text-white" data-toggle="dropdown"
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
                                <h1>Transfer History </h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">Transfer History
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
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>{{ session('error') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @if (session('delete'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>{{ session('delete') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                {{-- Permission --}}
                @php
                    $choosePermission = [];
                    if (auth()->user()->permission) {
                        $decodedPermissions = json_decode(auth()->user()->permission, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $choosePermission = $decodedPermissions;
                        }
                    }
                @endphp
                {{-- End Permission --}}

                <div class="ml-2 container-fluid">


                    <!-- /.modal -->
                    <div class="mt-3 col-md-12">
                        <div class="card ">
                            <div class="card-header">
                                <h3 class="card-title">Transfer History Table</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Date</th>
                                            <th>Transfer Number</th>
                                            <th> Location (From)</th>
                                            <th> Location (To)</th>
                                            <th>Remark</th>
                                            <th>Quantity</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $no = '1';
                                        @endphp
                                        @foreach ($histories as $history)
                                            <tr>
                                                <td>{{ $no }}</td>
                                                <td>{{ \Carbon\Carbon::parse($history->date)->format('d/m/ Y') }}</td>
                                                <td>{{ $history->transfer_no }}</td>
                                                <td>
                                                    {{-- isset($location_names[$history->id]['from']) ? $location_names[$history->id]['from'] : 'N/A' --}}
                                                    {{ $history->from_warehouse->name }}
                                                </td>
                                                <td>
                                                    {{-- isset($location_names[$history->id]['to']) ? $location_names[$history->id]['to'] : 'N/A' --}}
                                                    {{ $history->to_warehouse->name }}
                                                </td>
                                                <td>{{ $history->remark }}</td>
                                                <td>{{ $history->where('transfer_no', $history->transfer_no)->sum('quantity') }}
                                                </td>

                                                <td>
                                                    <div class="d-flex justify-content-start align-items-center">

                                                        {{-- <a href="{{ url('/good_received', $history->transfer_no) }}"
                                                            class="btn btn-info btn-sm me-2 mb-0 mx-1">
                                                            Good Received
                                                        </a> --}}
                                                        <a href="{{ url('/transfer_history_detail', $history->transfer_no) }}"
                                                            class="btn btn-primary btn-sm me-2 mb-0">
                                                            <i class="fa-solid fa-eye"></i>
                                                        </a>
                                                        @if (in_array('Transfer Item Delete', $choosePermission) || auth()->user()->is_admin == '1')
                                                            <a href="{{ url('transfer_history_delete', $history->transfer_no) }}"
                                                                title="Transfer History Delete"
                                                                class="btn btn-danger btn-sm me-2 mb-0 mx-1"
                                                                onclick="return confirm('Are you sure you want to delete this Transfer?')">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </a>
                                                        @endif
                                                        {{-- <a href="{{ url('transfer_history_edit', $history->transfer_no) }}"
                                                            title="Transfer History Edit"
                                                            class="btn btn-success btn-sm mb-0">
                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                        </a> --}}
                                                    </div>
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



    <script>
        $(document).ready(function() {
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "pageLength": 30,
                "dom": 'Bfrtip', // Enable Buttons
                "buttons": [{
                    extend: 'excelHtml5',
                    text: 'Export to Excel',
                    title: 'Transfer History', // Custom file name
                    className: 'btn btn-secondary',
                    exportOptions: {
                        columns: ':not(:last-child)' // Exclude the last column (Action)
                    }
                }]
            });
        });
    </script>


</body>

</html>
