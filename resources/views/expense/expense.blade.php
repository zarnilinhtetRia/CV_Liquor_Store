@include('layouts.header')

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        {{-- @include('layouts.nav') --}}
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

                <li class="ml-auto nav-item">

                    @php
                        use App\Models\Warehouse;
                        $branchs = Warehouse::all();
                    @endphp
                    <a class="nav-link text-white" href="#">
                        @foreach ($branchs as $branch)
                            @if ($branch->id == auth()->user()->level)
                                {{ $branch->name }}
                            @endif
                        @endforeach
                        {{-- {{ auth()->user()->level }} --}}
                    </a>

                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="ml-auto navbar-nav">
                <div class="btn-group">
                    <button type="button" class=" text-white btn dropdown-toggle" data-toggle="dropdown"
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
                                <h1>Expense</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">Expense
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
                @if (session('delete'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>{{ session('delete') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="ml-2 container-fluid">

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
                        <div class="">
                            @if (in_array('Expenses Register', $choosePermission) || auth()->user()->is_admin == '1')
                                <button type="button" class="mr-auto btn btn-primary " data-toggle="modal"
                                    data-target="#modal-lg">
                                    <i class="fa-solid fa-circle-plus"></i> Add Expense
                                </button>
                            @endif
                            <a href="{{ url('expense_delete_record') }}" class="mr-auto btn btn-danger">
                                <i class="fa-solid fa-clock-rotate-left"></i> Delete Record
                            </a>
                        </div>
                    </div>

                    <div class="modal fade" id="modal-lg">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Add Expense</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ url('expense_store') }}" method="POST">
                                        @csrf
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="name">Name<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="name"
                                                        placeholder="Enter name" required autofocus name="name">
                                                </div>

                                                @if (auth()->user()->is_admin == '1')
                                                    <div class="form-group col-md-6">
                                                        <label for="branch">Location<span
                                                                class="text-danger">*</span></label>

                                                        <select name="branch" id="branch" class="form-control"
                                                            required>
                                                            <option value="" selected disabled>Select Location
                                                            </option>
                                                            @foreach ($branches as $branch)
                                                                <option value="{{ $branch->id }}">{{ $branch->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @else
                                                    <div class="form-group col-md-6">
                                                        <label for="branch">Location<span
                                                                class="text-danger">*</span></label>

                                                        <select name="branch" id="branch" class="form-control"
                                                            required>
                                                            @php
                                                                $userPermissions = auth()->user()->level
                                                                    ? json_decode(auth()->user()->level)
                                                                    : [];
                                                            @endphp
                                                            @foreach ($branches as $branch)
                                                                @if (in_array($branch->id, $userPermissions))
                                                                    <option value="{{ $branch->id }}">
                                                                        {{ $branch->name }}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="row">
                                                @if (auth()->user()->is_admin == '1')
                                                    <div class="form-group col-md-6">
                                                        <label for="category">Category<span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-control" id="category" required autofocus
                                                            name="category">
                                                            <option disabled selected>Select Category</option>
                                                            @foreach ($categories as $key => $category)
                                                                <option value="{{ $category['id'] }}"
                                                                    data-branch="{{ $category->branch }}">
                                                                    {{ $category['name'] }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @else
                                                    <div class="form-group col-md-6">
                                                        <label for="category">Category<span
                                                                class="text-danger">*</span></label>
                                                        @php
                                                            $userPermissions = auth()->user()->level
                                                                ? json_decode(auth()->user()->level)
                                                                : [];
                                                        @endphp
                                                        <select class="form-control" id="category" required autofocus
                                                            name="category">
                                                            <option disabled selected>Select Category</option>
                                                            @foreach ($categories as $key => $category)
                                                                @if (in_array($category->id, $userPermissions))
                                                                    <option value="{{ $category['id'] }}"
                                                                        data-branch="{{ $category->branch }}">
                                                                        {{ $category['name'] }}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @endif
                                                <div class="form-group col-md-6">
                                                    <label for="date">Date<span
                                                            class="text-danger">*</span></label>
                                                    <input type="date" class="form-control" id="date"
                                                        required autofocus name="date">
                                                </div>

                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="amount_mmk">Amount(MMK)<span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" id="amount_mmk"
                                                        value="0" placeholder="Enter amount(Kyat)" required
                                                        autofocus name="amount_mmk">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="transaciton">Transaction<span
                                                            class="text-danger">*</span></label>
                                                    <select name="transaction_id" id="transaction"
                                                        class="form-control">

                                                    </select>
                                                </div>

                                            </div>

                                            <div class="form-group">
                                                <label for="amount">Description<span
                                                        class="text-danger">*</span></label>
                                                <textarea class="form-control" id="description" placeholder="Enter description" autofocus name="description"
                                                    rows="5"></textarea>
                                            </div>
                                        </div>




                                        <button type="button" class="btn btn-default"
                                            data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save </button>
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
                        <div class="card-header">
                            <h3 class="card-title">Expense List</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">

                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Branch</th>
                                        <th>Description</th>
                                        <th>Date</th>
                                        <th>Amount(Kyat)</th>
                                        {{-- <th>Amount(USD)</th> --}}
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($expenses as $key => $expense)
                                        @php
                                            $date = new DateTime($expense->date);
                                        @endphp
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $expense->name }}</td>
                                            <td>
                                                @foreach ($categories as $cat)
                                                    @if ($cat->id == $expense->category)
                                                        {{ $cat->name }}
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach ($branches as $branch)
                                                    @if ($branch->id == $expense->branch)
                                                        {{ $branch->name }}
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>{{ $expense->description }}</td>
                                            <td>{{ $date->format('d M Y') }}
                                            </td>
                                            <td>{{ number_format($expense->amount_mmk) }}</td>
                                            {{-- <td>{{ number_format($expense->amount_usd, 2) }}</td> --}}
                                            <td>

                                                @if (in_array('Expenses Edit', $choosePermission) || auth()->user()->is_admin == '1')
                                                    <a href="{{ url('expense_edit', $expense->id) }}"
                                                        class="btn btn-success btn-sm"><i
                                                            class="fa-solid fa-pen-to-square"></i></a>
                                                @endif



                                                @if (in_array('Expenses Delete', $choosePermission) || auth()->user()->is_admin == '1')
                                                    <a href="{{ url('expense_delete', $expense->id) }}"
                                                        class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Are you sure you want to delete this Expense?')"><i
                                                            class="fa-solid fa-trash"></i></a>
                                                @endif
                                            </td>
                                        </tr>
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
    <script src="{{ asset('locallink/js/ajax_jquery.js') }}"></script>
    <script>
        $("#branch").on("change", function() {
            var locationId = $("#branch").val();
            console.log(locationId);
            $('#transaction').html(
                '<option value="">Loading...</option>');

            if (locationId) {
                $.ajax({
                    url: 'get_accounts_transaction_expense/' +
                        locationId, // Update this URL to match your route
                    type: 'GET',
                    success: function(data) {
                        console.log(data);
                        $('#transaction')
                            .empty().append(
                                '<option value="">Select Transaction</option>'
                            );

                        if (data && data.length > 0) {
                            $.each(data, function(index,
                                transaction) {
                                $('#transaction')
                                    .append(
                                        '<option value="' +
                                        transaction
                                        .id + '">' +
                                        transaction
                                        .transaction_name +
                                        '</option>');
                            });
                        } else {
                            $('#transaction').append(
                                '<option value="">No Transaction available</option>'
                            );
                        }
                    },
                    error: function() {
                        $('#transaction')
                            .empty().append(
                                '<option value="">Error loading transactions</option>'
                            );
                    }
                });
            } else {
                $('#transaction').empty().append(
                    '<option value="">Select Transaction</option>');
            }
        })
    </script>
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
        $(document).ready(function() {
            $('#branch').on('change', function() {
                var selectedBranch = $(this).val();

                $('#category option').hide();

                $('#category option[value=""]').show();

                $('#category option[data-branch="' + selectedBranch + '"]').show();

                $('#category').val('');
            });
        });
    </script>

</body>

</html>
