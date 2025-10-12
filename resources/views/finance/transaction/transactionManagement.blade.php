@include('layouts.header')

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        @include('layouts.nav')
        @include('layouts.sidebar')
        <div class="content-wrapper">
            {{-- <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>DataTables</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">DataTables</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section> --}}

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">


                            <!-- Content Header (Page header) -->
                            <section class="content-header">
                                <div class="container-fluid">
                                    <div class="row mb-2">
                                        <div class="col-sm-6">
                                            <h1>Transactions</h1>
                                        </div>
                                        <div class="col-sm-6">
                                            <ol class="breadcrumb float-sm-right">
                                                <li class="breadcrumb-item"><a
                                                        href="{{ url('/dashboard') }}">Dashboard</a></li>
                                                <li class="breadcrumb-item active">Transaction</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div><!-- /.container-fluid -->
                            </section>

                            <div class="container-fluid mb-4 mr-auto">
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

                                @if (in_array('Transaction Register', $choosePermission) || auth()->user()->is_admin == '1')
                                    <div class="row d-flex justify-content-end mr-2">
                                        <div class="">
                                            <button type="button" class="mr-auto btn btn-default text-white"
                                                data-toggle="modal" data-target="#modal-lg"
                                                style="background-color: #007BFF">
                                                <i class="fa-solid fa-circle-plus"></i> Add Transaction
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Modal Content --}}
                            <div class="modal fade" id="modal-lg">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Add Transaction </h4>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ url('/transaction_register') }}" method="POST">
                                                @csrf
                                                <div class="form-group">
                                                    <label for="phno">Location <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-control" name="location" required
                                                        id="location">
                                                        <option value="" selected disabled>Choose Location
                                                        </option>
                                                        @foreach ($branches as $branch)
                                                            <option value="{{ $branch->id }}">{{ $branch->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="accounts_id">Account Name <span
                                                            style="color: red;">&nbsp;*</span></label>
                                                    <select name="account_id" class="form-control" id="account_id"
                                                        required>
                                                        <option value="">Select Account</option>

                                                        @foreach ($account as $accounts)
                                                            <option value="{{ $accounts->id }}">
                                                                {{ $accounts->account_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('account_id')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="form-group" style="display: none">
                                                    <label for="status">Status<span
                                                            style="color: red;">&nbsp;*</span></label>
                                                    <select name="status" class="form-control" id="status" required>
                                                        {{-- <option value="">Choose One</option> --}}

                                                        <option value="in">IN

                                                        </option>
                                                        <option value="out">OUT

                                                        </option>
                                                    </select>
                                                    @error('status')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="form-group" style="display: none;">
                                                    <label for="transaction_code">Opening Amount<span
                                                            style="color: red;">&nbsp;*</span></label>
                                                    <input type="text" class="form-control" id="transaction_code"
                                                        name="transaction_code" value="0" required>
                                                    @error('transaction_code')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="transaction_name">Name<span
                                                            style="color: red;">&nbsp;*</span></label>
                                                    <input type="text" class="form-control" id="transaction_name"
                                                        name="transaction_name" placeholder="Enter Transaction Name"
                                                        required value="">
                                                    @error('transaction_name')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="form-group" style="display:none">
                                                    <label>Descriptions</label>
                                                    <textarea class="form-control" rows="3" placeholder="Enter ..." style="border-color:#6B7280" name="description"
                                                        value="no need"></textarea>
                                                </div>

                                                <!-- /.card-body -->
                                                <div class="modal-footer justify-content-between">
                                                    <button type="button" class="btn btn-default"
                                                        data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary"
                                                        style="background-color: #007BFF">Register</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <!-- /.modal-content -->
                                </div>
                                <!-- /.modal-dialog -->
                            </div>


                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                </div>
                            @endif
                            @if (session('deleteStatus'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('deleteStatus') }}
                                </div>
                            @endif
                            @if (session('updateStatus'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('updateStatus') }}
                                </div>
                            @endif
                            <div class="card">
                                <div class="card-header d-flex justify-content-start align-items-center">

                                    <h3 class="card-title">Transaction List</h3>
                                    <div class="dropdown ml-auto mr-2">
                                        <div id="branchDropdown" class="dropdown ml-auto"
                                            style="display:inline-block; margin-left: 10px;">
                                            <button class="btn btn-primary dropdown-toggle" type="button"
                                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                                {{ $currentBranchName }}
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a href="{{ url('transactionManagement') }}"
                                                    class="dropdown-item">All </a>
                                                @if (auth()->user()->is_admin == '1')

                                                    @foreach ($branches as $drop)
                                                        <a class="dropdown-item"
                                                            href="{{ route('branch_transaction', $drop->id) }}">{{ $drop->name }}</a>
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
                                                                href="{{ route('branch_transaction', $drop->id) }}">{{ $drop->name }}</a>
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
                                                <th>No</th>
                                                <th>Account Name</th>
                                                <th>Location</th>
                                                <th>Name</th>
                                                <th>Amount</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $no = 1;
                                                $totalAmount = 0; // Initialize total amount
                                            @endphp
                                            @foreach ($transaction as $transactions)
                                                <tr>
                                                    <td>{{ $no }}</td>
                                                    <td>{{ $transactions->account->account_name }}</td>
                                                    <td>{{ $transactions->warehouse->name }}</td>
                                                    <td>{{ $transactions->transaction_name }}</td>
                                                    <td>
                                                        {{ number_format(isset($diff[$transactions->id]) ? abs($diff[$transactions->id]) : 0) }}
                                                        @php
                                                            $totalAmount += isset($diff[$transactions->id])
                                                                ? abs($diff[$transactions->id])
                                                                : 0;
                                                        @endphp


                                                    </td>
                                                    <td>
                                                        <div class="row">
                                                            @if (in_array('Transaction Edit', $choosePermission) || auth()->user()->is_admin == '1')
                                                                <a href="{{ route('finance#transactionManagementEdit', $transactions->id) }}"
                                                                    title="Transaction Edit"
                                                                    class="mx-2 btn btn-success"><i
                                                                        class="fa-solid fa-pen-to-square"></i></a>
                                                            @endif

                                                            @if (in_array('Transaction Delete', $choosePermission) || auth()->user()->is_admin == '1')
                                                                <a href="{{ url('transaction_delete', $transactions->id) }}"
                                                                    class="btn btn-danger"
                                                                    onclick="return confirm('Are you sure want to delete this transaction ?')"><i
                                                                        class="fa-solid fa-trash"></i></a>
                                                            @endif

                                                            @if (in_array('Add Payment', $choosePermission) || auth()->user()->is_admin == '1')
                                                                <a href="{{ url('payment', $transactions->id) }}"
                                                                    class="btn btn-warning ml-2">Detail & Payment</a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                                @php
                                                    $no++;
                                                @endphp
                                            @endforeach
                                        </tbody>
                                        {{-- <tfoot>
                                            <tr>
                                                <th colspan="4" class="text-right">Total</th>
                                                <th>{{ number_format($totalAmount) }}</th>
                                                <th></th>
                                            </tr>
                                        </tfoot> --}}
                                    </table>
                                </div>

                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>

            </section>


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
            $('#location').on('change', function() {
                var locationId = $(this).val();
                $('#account_id').html('<option value="">Loading...</option>');
                console.log(locationId);

                if (locationId) {
                    $.ajax({
                        url: '{{ route('get_accounts') }}',
                        type: 'GET',
                        data: {
                            locationId: locationId,
                        },
                        success: function(data) {
                            $('#account_id').empty().append(
                                '<option value="">Select Account</option>');

                            if (data && data.length > 0) {
                                $.each(data, function(index, account) {
                                    $('#account_id').append('<option value="' + account
                                        .id + '">' + account.account_name +
                                        '</option>');
                                });
                            } else {
                                $('#account_id').append(
                                    '<option value="">No accounts available</option>');
                            }
                        },
                        error: function() {
                            $('#account_id').empty().append(
                                '<option value="">Error loading accounts</option>');
                        }
                    });
                } else {
                    $('#account_id').empty().append('<option value="">Select Account</option>');
                }
            });
        });
    </script>


    <script>
        $(function() {
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "pageLength": 30,
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        });
    </script>


</body>

</html>
