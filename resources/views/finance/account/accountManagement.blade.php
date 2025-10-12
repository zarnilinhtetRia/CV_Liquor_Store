@include('layouts.header')

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        @include('layouts.nav')
        @include('layouts.sidebar')
        <div class="content-wrapper">
            <!-- Main content -->
            <section class="content">

                <section class="content-header">
                    <div class="container-fluid">
                        <div class="mb-2 row">
                            <div class="col-sm-6">
                                <h1>Account Management</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">Account Management
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

                @if ($errors->has('account_number'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong> {{ $errors->first('account_number') }}</strong>
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

                    @if (in_array('Account Register', $choosePermission) || auth()->user()->is_admin == '1')
                        <div class="row d-flex justify-content-end mr-2">
                            <div class=""> <button type="button" class="mr-auto btn btn-primary "
                                    data-toggle="modal" data-target="#modal-lg">
                                    <i class="fa-solid fa-circle-plus"></i> Add Account
                            </div>
                        </div>
                    @endif
                    <div class="modal fade" id="modal-lg">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Add Account </h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ url('account_register') }}" method="POST">
                                        @csrf
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="phno">Location <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" name="location" required id="location">
                                                    <option value="" selected disabled>Choose Location
                                                    </option>
                                                    @foreach ($branches as $branch)
                                                        <option value="{{ $branch->id }}">{{ $branch->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="account_code">Code<span
                                                        style="color: red;">&nbsp;*</span></label>
                                                <input type="text" class="form-control" id="account_code"
                                                    name="account_number" placeholder="Enter Account Code" required>
                                                @error('account_number')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="account_name">Name <span
                                                        style="color: red;">&nbsp;*</span></label>
                                                <input type="text" class="form-control" id="account_name"
                                                    name="account_name" placeholder="Enter Account Name" required>
                                                @error('account_name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="account_name">Type <span
                                                        style="color: red;">&nbsp;*</span></label>
                                                <select name="account_type" id="type" class="form-control">
                                                    <option value="" selected disabled>Select Account Type
                                                    </option>
                                                    <option value="Non Current Assets">Non Current Assets (BL)
                                                    </option>
                                                    <option value="Current Assets">Current Assets (BL)</option>
                                                    <option value="Long Term Liability">Long Term Liability (BL)
                                                    </option>
                                                    <option value="Current Liability">Current Liability (BL)
                                                    </option>
                                                    <option value="Non Current Liability">Non Current Liability (BL)
                                                    </option>
                                                    <option value="Equity">Equity (BL)</option>
                                                    <option value="Revenue">Revenue (PL)</option>
                                                    <option value="Cost of Sale">Cost of Sale (PL)</option>

                                                    <option value="Other income">Other income (PL)</option>
                                                    <option value="Admin Expenses">Admin Expenses (PL)</option>
                                                    </option>
                                                    <option value="Depreciation">Depreciation (PL)
                                                    </option>
                                                    <option value="Mainteance Expenses">Mainteance Expenses (PL)
                                                    </option>
                                                    <option value="S & D Expenses">S & D Expenses (PL)
                                                    </option>
                                                    <option value="Other Expenses">Other Expenses (PL)
                                                    </option>
                                                    <option value="Marketing Expenses">Marketing Expenses (PL)
                                                    </option>
                                                    <option value="Finance Expenses">Finance Expenses (PL)
                                                    </option>
                                                </select>
                                                <input type="hidden" name="account_bl_pl" id="bl_pl">
                                                @error('type')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="modal-footer d-flex justify-content-between"> <button
                                                    type="button" class="btn btn-default"
                                                    data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Save </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>
                <!-- /.modal -->
                <div class="mt-3 col-md-12">
                    <div class="card ">
                        <div class="card-header d-flex justify-content-start align-items-center">

                            <h3 class="card-title">Account List</h3>
                            <div class="dropdown  ml-auto mr-2">
                                <div id="branchDropdown" class="dropdown ml-auto"
                                    style="display:inline-block; margin-left: 10px;">
                                    <button class="btn btn-primary dropdown-toggle" type="button"
                                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        {{ $currentBranchName }}
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a href="{{ url('accountManagement') }}" class="dropdown-item">All </a>
                                        @if (auth()->user()->is_admin == '1')

                                            @foreach ($branches as $drop)
                                                <a class="dropdown-item"
                                                    href="{{ route('branch_account', $drop->id) }}">{{ $drop->name }}</a>
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
                                                        href="{{ route('branch_account', $drop->id) }}">{{ $drop->name }}</a>
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
                                        <th>Account Number</th>
                                        <th>Account Name</th>
                                        <th>Location</th>
                                        <th>Type</th>
                                        <!-- <th>BL/PL</th> -->
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @foreach ($accounts as $account)
                                        <tr>
                                            <td>{{ $no }}</td>
                                            <td>{{ $account->account_number }}</td>
                                            <td>{{ $account->account_name }}</td>
                                            <td>{{ $account->warehouse->name ?? '' }}</td>
                                            <td>{{ $account->account_type }}</td>
                                            {{-- <td>{{ $account->account_bl_pl }}</td> --}}
                                            <td>
                                                <div class="row">
                                                    @if (in_array('Account Edit', $choosePermission) || auth()->user()->is_admin == '1')
                                                        <a href="{{ route('account_edit', $account->id) }}"
                                                            title="Account Edit" class="mx-2 btn btn-success"><i
                                                                class="fa-solid fa-pen-to-square"></i></a>
                                                    @endif

                                                    @if (in_array('Account Delete', $choosePermission) || auth()->user()->is_admin == '1')
                                                        <a href="{{ url('account_delete', $account->id) }}"
                                                            class="btn btn-danger"
                                                            onclick="return confirm('Are you sure want to delete this account ?')"><i
                                                                class="fa-solid fa-trash"></i></a>
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
    </script>
    <script>
        document.getElementById('type').addEventListener('change', function() {
            var selectedValue = this.value;
            var hiddenInput = document.getElementById('bl_pl');

            var blCategories = [
                'Non Current Assets',
                'Current Assets',
                'Long Term Liability',
                'Current Liability',
                'Non Current Liability',
                'Equity'
            ];

            var plCategories = [
                'Revenue',
                'Cost of Sale',
                'Other income',
                'Admin Expenses',
                'Depreciation',
                'Mainteance Expenses',
                'S & D Expenses',
                'Other Expenses',
                'Marketing Expenses',
                'Finance Expenses'
            ];

            if (blCategories.includes(selectedValue)) {
                hiddenInput.value = 'BL';
            } else if (plCategories.includes(selectedValue)) {
                hiddenInput.value = 'PL';
            } else {
                hiddenInput.value = '';
            }
        });
    </script>

</body>

</html>
