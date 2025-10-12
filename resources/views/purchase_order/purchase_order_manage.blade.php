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
                                <h1>Purchase Order list</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>

                                    </li>
                                    <li class="breadcrumb-item">Purchase Order List</li>
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
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>{{ session('delete') }}</strong>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <div class="card ">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title">Purchase Order List</h3>
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
                                                <a href="{{ url('purchase_order_manage') }}" class="dropdown-item">All
                                                    Purchase
                                                    Order</a>
                                                @foreach ($branchs as $drop)
                                                    <a class="dropdown-item"
                                                        href="{{ route('purchase_order', $drop->id) }}">{{ $drop->name }}</a>
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
                                                            href="{{ route('purchase_order', $drop->id) }}">{{ $drop->name }}</a>
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
                                            <th>PO Number</th>

                                            <th>Location</th>
                                            <th>Supplier</th>
                                            {{-- <th>Receiving Mode</th> --}}
                                            <th>Date</th>
                                            <th>Internal Reg Number</th>
                                            <th>Supplier Reg Number</th>
                                            <th>Container Reg Number</th>
                                            <th>Total</th>
                                            {{-- <th>Transit Date</th> --}}
                                            {{-- <th>Status Change</th>
                                            <th>Status</th> --}}
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


                                        @foreach ($po as $pos)
                                            <tr>

                                                <td>{{ $no }}</td>
                                                <td>{{ $pos->quote_no }}</td>
                                                <td>{{ $pos->warehouse->name ?? 'N/A' }}</td>

                                                <td>{{ $pos->supplier->name ?? 'N/A' }}</td>
                                                {{-- <td>{{ $pos->balance_due }}</td> --}}
                                                <td>{{ \Carbon\Carbon::parse($pos->po_date)->format('d / m / Y') }}
                                                </td>
                                                <td>{{ $pos->internal_register_number }}</td>
                                                <td>{{ $pos->supplier_register_number }}</td>
                                                <td>{{ $pos->container_register_number }}</td>

                                                <td>
                                                    @if ($pos->currency_method == 'MMK')
                                                        {{ number_format($pos->total) }}
                                                    @else
                                                        {{ number_format($pos->total) }}
                                                    @endif
                                                </td>
                                                {{-- <td>{{ $pos->transit_date ? \Carbon\Carbon::parse($pos->transit_date)->format('d/m/Y') : 'N/A' }}
                                                </td> --}}
                                                {{-- <td>
                                                    @if ($pos->transit_status == '1')
                                                        <span class="text-success">Transit Done</span>
                                                    @else
                                                        @if ($pos->balance_due == 'PO')
                                                            <select name="po_status" id="po_status"
                                                                data-val="{{ $pos->id }}" class="form-control">
                                                                <option value="" selected disabled>Select Status
                                                                </option>
                                                                <option value="PO"
                                                                    @if ($pos->po_status == 'PO') selected @endif>PO
                                                                </option>
                                                                <option value="PI PENDING"
                                                                    @if ($pos->po_status == 'PI PENDING') selected @endif>PI
                                                                    PENDING</option>
                                                                <option value="PI CONFIRM"
                                                                    @if ($pos->po_status == 'PI CONFIRM') selected @endif>PI
                                                                    CONFIRM</option>
                                                            </select>
                                                        @else
                                                            {{ 'N/A' }}
                                                        @endif
                                                    @endif
                                                </td> --}}

                                                {{-- @if ($pos->balance_due == 'PO')
                                                    @if ($pos->po_status == 'PO')
                                                        <td id="{{ 'postatus-' . $pos->id }}"
                                                            class="bg-danger text-center">
                                                            {{ $pos->po_status }}
                                                        </td>
                                                    @elseif($pos->po_status == 'PI PENDING')
                                                        <td id="{{ 'postatus-' . $pos->id }}"
                                                            class="bg-warning text-center">
                                                            {{ $pos->po_status }}
                                                        </td>
                                                    @elseif($pos->po_status == 'PI CONFIRM')
                                                        <td id="{{ 'postatus-' . $pos->id }}"
                                                            class="bg-success text-center">
                                                            {{ $pos->po_status }}

                                                            @if ($pos->transit_status == '1')
                                                            @else
                                                                <form action="{{ url('transit', $pos->id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="transit_status"
                                                                        value="1">
                                                                    <button class="btn btn-primary"
                                                                        type="submit">Transit</button>
                                                                </form>
                                                            @endif


                                                        </td>
                                                    @endif
                                                @else
                                                    <td>
                                                        {{ 'N/A' }}
                                                    </td>
                                                @endif --}}

                                                <td>

                                                    @if (in_array('PO Payment', $choosePermission) || auth()->user()->is_admin == '1')
                                                        {{-- @if ($pos->balance_due == 'PO') --}}
                                                        <a href="{{ url('po_make_payment', $pos->id) }}"
                                                            class="btn btn-warning btn-sm text-white mb-1"><i
                                                                class="fa-solid fa-money-check-dollar"></i></a>
                                                        {{-- @endif --}}
                                                    @endif

                                                    @if (in_array('Purchase Order Details', $choosePermission) || auth()->user()->is_admin == '1')
                                                        <a href="{{ route('purchase_order_details', $pos->id) }}"
                                                            class="btn btn-primary btn-sm"><i
                                                                class="fa-solid fa-eye"></i></a>
                                                    @endif

                                                    @if (in_array('Purchase Order Edit', $choosePermission) || auth()->user()->is_admin == '1')
                                                        <a href="{{ route('purchase_order_edit', $pos->id) }}"
                                                            class="btn btn-success btn-sm"><i
                                                                class="fa-solid fa-pen-to-square"></i></a>
                                                    @endif

                                                    @if (in_array('Purchase Order Delete', $choosePermission) || auth()->user()->is_admin == '1')
                                                        <a href="{{ url('purchase_order_delete', $pos->id) }}"
                                                            class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Are you sure you want to delete this Purchase Order ?')"><i
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
                "pageLength": 50,
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
                "pageLength": 50,
                "buttons": ["excel", "pdf", "print"]
            }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
        });
    </script>

    <script>
        $(document).on('change', '#po_status', function() {
            let status_val = $(this).val();
            let po_id = $(this).data('val');
            let postatus = $('#postatus-' + po_id);
            console.log(po_id);
            $.ajax({
                url: "{{ route('po-status-change') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    status: status_val,
                    id: po_id
                },
                // dataType: 'json',
                success: function(data) {
                    let resultHtml = '';
                    let resultClass = '';

                    if (data.po_status == 'PO') {
                        resultHtml = data.po_status;
                        resultClass = 'bg-danger text-center';
                    } else if (data.po_status == 'PI PENDING') {
                        resultHtml = data.po_status;
                        resultClass = 'bg-warning text-center';
                    } else {
                        resultHtml = data.po_status;
                        resultClass = 'bg-success text-center';
                        location.reload();
                    }

                    const postatus = $('#postatus-' + data
                        .id);
                    postatus.html(resultHtml).removeClass().addClass(resultClass);

                    alert('PO Status successfully changed!');
                }

            });
        });
    </script>
</body>

</html>
