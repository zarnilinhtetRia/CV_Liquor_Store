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
                                <h1>Customer List</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">Customer List
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <div id="successMessage"></div>

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



                    <div class="row d-flex justify-content-end mr-2">
                        @if (in_array('Customer Register', $choosePermission) || auth()->user()->is_admin == '1')
                            <div> <button type="button" class=" btn btn-primary " data-toggle="modal"
                                    data-target="#modal-lg">
                                    <i class="fa-solid fa-circle-plus"></i> Add Customer </button>

                            </div>
                        @endif


                    </div>



                    <div class="modal fade" id="modal-lg">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title"> Add Customer</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ url('customer_register') }}" method="POST">
                                        @csrf
                                        <div class="card-body">



                                            <div class="form-group">
                                                <label for="name">Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="name"
                                                    placeholder="Enter Name" required autofocus name="name">
                                            </div>


                                            <div class="form-group">
                                                <label for="phno">Phone Number</label>
                                                <input type="text" class="form-control" id="phone number"
                                                    placeholder="Enter Phone Number" name="phno">
                                            </div>

                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <input type="text" class="form-control" id="email"
                                                    placeholder="Enter Email" name="email">
                                            </div>

                                            <div class="form-group" style="display: none;">
                                                <label for="crc">Customer Type </label>
                                                <!-- <input type="text" class="form-control"
                                                    placeholder="Enter Customer Type" name="type"> -->
                                                <select name="type" id="type" class="form-control">

                                                    <option value="Retail">Retail</option>

                                                </select>
                                            </div>

                                            @if (auth()->user()->is_admin == '1')
                                                <div class="form-group">
                                                    <label for="branch">Location<span
                                                            class="text-danger">*</span></label>

                                                    <select name="branch" id="branch" class="form-control"
                                                        required>
                                                        <option value="" selected disabled>Select Location
                                                        </option>
                                                        @foreach ($branchs as $branch)
                                                            <option value="{{ $branch->id }}">{{ $branch->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @else
                                                <div class="form-group">
                                                    <label for="branch">Location<span
                                                            class="text-danger">*</span></label>

                                                    <select name="branch" id="branch" class="form-control"
                                                        required>
                                                        @php
                                                            $userPermissions = auth()->user()->level
                                                                ? json_decode(auth()->user()->level)
                                                                : [];
                                                        @endphp
                                                        <option value="" selected disabled>Select Location
                                                        </option>
                                                        @foreach ($branchs as $branch)
                                                            @if (in_array($branch->id, $userPermissions))
                                                                <option value="{{ $branch->id }}">
                                                                    {{ $branch->name }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif

                                            <div class="form-group">
                                                <label for="address">Address</label>
                                                <input type="text" class="form-control" id="phone number"
                                                    placeholder="Enter Address" name="address">
                                            </div>
                                        </div>
                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-default"
                                        data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save </button>
                                </div>
                                </form>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>
                    <!-- /.modal -->
                    <div class="mt-3 col-md-12">
                        <div class="ml-2 col row d-flex">
                            <form id="fileImportForm" action="{{ route('customer_file_export') }}" method="GET"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4 form-group" style="max-width: 500px; margin: 0 auto;">
                                    <div class="text-left custom-file">

                                        <label for="warehouse">Choose Location</label>
                                        <select name="warehouse_id" id="warehouse" class="form-control warehouse"
                                            required>
                                            <option value="All Location" selected>All Location</option>
                                            @foreach ($branchs as $warehouse)
                                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}
                                                </option>
                                            @endforeach
                                        </select>



                                        <div class="p-1 mt-2 text-left custom-file col"
                                            style="border:#d0d0db 1px solid; background-color: white">
                                            <input type="file" name="file" class="" id="customFile">
                                        </div>

                                        <button type="button" class="mt-3 btn btn-primary"
                                            id="importBtn">Import</button>
                                        <button type="submit" class="mt-3 btn btn-success"
                                            id="exportBtn">Export</button>
                                    </div>
                                </div>
                                <a href="{{ route('customer_file_import_template') }}">Download Import CSV
                                    Template</a>
                            </form>
                        </div>
                        <div class="card ">
                            <div class="card-header">
                                <h3 class="card-title">Customer List</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">

                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            {{-- <th>Customer Id</th> --}}
                                            <th>Name</th>
                                            <th>Phone Number</th>
                                            <th>Email</th>
                                            {{-- <th>Customer Type</th> --}}
                                            <th>Location</th>
                                            <th>Address</th>
                                            <th>Total</th>
                                            <th>Delivered Qty</th>
                                            <th>Remain Qty</th>
                                            <th>Action</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $no = '1';

                                        @endphp
                                        @foreach ($customers as $customer)
                                            @php
                                                $sells = $customer->invoices->flatMap->sells;
                                                $inv = $customer->invoices;

                                            @endphp
                                            <tr>
                                                <td>{{ $no }}</td>
                                                {{-- <td>{{ $customer->id }}</td> --}}
                                                <td><a
                                                        href="{{ url('customer_invoice', $customer->id) }}">{{ $customer->name }}</a>
                                                </td>
                                                <td>{{ $customer->phno }}</td>
                                                <td>{{ $customer->email }}</td>
                                                {{-- <td>{{ $customer->type }}</td> --}}
                                                <td>
                                                    @foreach ($branchs as $branch)
                                                        @if ($branch->id == $customer->branch)
                                                            {{ $branch->name }}
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td>{{ $customer->address }}</td>
                                                <td>{{ number_format($inv->sum('total')) }}</td>
                                                <td>{{ $sells->sum('delivered_qty') }}</td>
                                                <td>{{ $sells->sum('product_qty') - $sells->sum('delivered_qty') }}
                                                </td>
                                                <td>
                                                    <div class="row">
                                                        @if (in_array('Customer Edit', $choosePermission) || auth()->user()->is_admin == '1')
                                                            <a href="{{ url('customer_edit', $customer->id) }}"
                                                                title="Customer Edit" class="mx-2 btn btn-success"><i
                                                                    class="fa-solid fa-pen-to-square"></i></a>
                                                        @endif

                                                        @if (in_array('Customer Delete', $choosePermission) || auth()->user()->is_admin == '1')
                                                            <a href="{{ url('customer_delete', $customer->id) }}"
                                                                title="Customer Delete" class=" btn btn-danger"><i
                                                                    class="fa-solid fa-trash"></i></a>
                                                        @endif

                                                        @if (in_array('Customer Credit', $choosePermission) || auth()->user()->is_admin == '1')
                                                            <a href="{{ url('customer_credit', $customer->id) }}"
                                                                type="button"
                                                                class="mx-2 btn btn-warning text-white">
                                                                Credit</a>
                                                        @endif


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
        $(function() {
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "pageLength": 30,
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        });

        //Excel Import
        $(document).ready(function() {
            $('#importBtn').on('click', function(e) {
                e.preventDefault();
                var formData = new FormData($('#fileImportForm')[
                    0]);
                $('#importBtn').prop('disabled', true);
                $('#successMessage').html(
                    '<div class="alert alert-info">Importing file... Please wait.</div>'
                );

                $.ajax({
                    url: '{{ route('customer_file_import') }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content')
                    },
                    success: function(response) {
                        $('#successMessage').html(
                            '<div class="alert alert-success">File imported successfully!</div>'
                        );
                        window.location.reload();
                    },
                    error: function(xhr, status, error) {
                        $('#successMessage').html(
                            '<div class="alert alert-danger">Please Choose File!</div>'
                        );
                        $('#importBtn').prop('disabled', false);
                        window.location.reload();

                    }
                });
            });
        });
    </script>


</body>

</html>
