@include('layouts.header')



<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav col-md-6">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">Date -
                        <?= $currentDate = date('d-m-y') ?></a>
                </li>


            </ul>

            <!-- Right navbar links -->
            <ul class="ml-auto navbar-nav">


                <div class="btn-group">
                    <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
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

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">



                            <section class="content-header">
                                <div class="container-fluid">
                                    <div class="row mb-2">
                                        <div class="col-sm-6">
                                            <h1>Settings</h1>
                                        </div>
                                        <div class="col-sm-6">
                                            <ol class="breadcrumb float-sm-right">
                                                <li class="breadcrumb-item"><a
                                                        href="{{ url('/dashboard') }}">Dashboard</a></li>
                                                <li class="breadcrumb-item active">Settings</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>{{ session('success') }}</strong>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="mb-3 row d-flex justify-content-end mr-2">
                                <div class=""> <button type="button" class="mr-auto btn btn-primary "
                                        data-toggle="modal" data-target="#modal-lg">
                                        <i class="fa-solid fa-circle-plus"></i> Add Setting </button>

                                </div>
                            </div>
                            <div class="modal fade" id="modal-lg">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Add Setting</h4>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ url('setting_store') }}" method="POST">
                                                @csrf
                                                <div class="card-body">

                                                    <div class="form-group">
                                                        <label for="name">Category Name <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-control" name="category">
                                                            <option value="" selected disabled>Choose Category
                                                            </option>
                                                            <option value="invoice">Invoice</option>
                                                            <option value="invoice return">Invoice Return</option>

                                                            <option value="purchase order">Purchase Order</option>
                                                            <option value="purchase order return">Purchase Order Return
                                                            </option>
                                                            <option value="receivable">Receivable
                                                            </option>
                                                            <option value="expense">Expense
                                                            </option>
                                                            <option value="payable">Payable
                                                            </option>
                                                            <option value="saleaccount">Sale Account (Invoice)
                                                            </option>
                                                            <option value="buyaccount">Buy Account (Purchase Order)
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="phno">Location <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-control" name="location" id="location">
                                                            <option value="" selected disabled>Choose Location
                                                            </option>
                                                            @foreach ($branches as $branch)
                                                                <option value="{{ $branch->id }}">{{ $branch->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="phno">Account Name <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-control" name="transaction_id"
                                                            id="account_id">
                                                            <option value="" selected disabled>Choose Choose
                                                            </option>
                                                            {{-- @foreach ($transactions as $transaction)
                                                                <option value="{{ $transaction->id }}">
                                                                    {{ $transaction->transaction_name }}
                                                                </option>
                                                            @endforeach --}}
                                                        </select>
                                                    </div>




                                                </div>



                                        </div>
                                        <div class="modal-body justify-content-between">
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
                            <div class="card">
                                <div class="card-header d-flex justify-content-start align-items-center">

                                    <h3 class="card-title">Setting List</h3>
                                    <div class="dropdown ml-auto mr-2">
                                        <div id="branchDropdown" class="dropdown ml-auto"
                                            style="display:inline-block; margin-left: 10px;">
                                            <button class="btn btn-primary dropdown-toggle" type="button"
                                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                                {{ $currentBranchName }}
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a href="{{ url('setting') }}" class="dropdown-item">All
                                                </a>
                                                @if (auth()->user()->is_admin == '1')

                                                    @foreach ($branches as $drop)
                                                        <a class="dropdown-item"
                                                            href="{{ route('branch_setting', $drop->id) }}">{{ $drop->name }}</a>
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
                                                                href="{{ route('branch_setting', $drop->id) }}">{{ $drop->name }}</a>
                                                        @endif
                                                    @endforeach
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">

                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No.</th>

                                                <th> Category Name</th>
                                                <th>Location</th>
                                                <th>Account Name</th>


                                                <th>Action</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $no = '1';
                                            @endphp
                                            @foreach ($settings as $setting)
                                                <tr>
                                                    <td>{{ $no }}</td>


                                                    <td>{{ \Illuminate\Support\Str::title($setting->category) }}</td>


                                                    <td>{{ $setting->warehouse->name }}</td>
                                                    <td>{{ $setting->account->account_name ?? '' }}</td>
                                                    <td>
                                                        <div class="row"><a
                                                                href="{{ url('setting_edit/' . $setting->id) }}"
                                                                class="btn btn-success mx-2"><i
                                                                    class="fa-solid fa-pen-to-square"></i></a>
                                                            <a href="{{ url('setting_delete/' . $setting->id) }}"
                                                                class="btn btn-danger"><i
                                                                    class="fa-solid fa-trash"></i></a>
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
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        </section>

    </div>
    </div>
    {{-- <script src="{{ asset('backend/js/jquery-3.6.0.js') }}"></script> --}}
    <script src="{{ asset('plugins/jquery/jquery.min.js ') }}"></script>

    <script>
        $(document).ready(function() {
            $('#location').on('change', function() {
                var locationId = $(this).val();
                $('#account_id').html('<option value="">Loading...</option>');

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

    @include('layouts.footer')
