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

                @if (Auth::user()->type === '1')
                    <li class="nav-item ml-auto">
                        <a class="nav-link" href="#">
                            @if (Auth::user()->branch_id)
                                {{ Auth::user()->branch->branch_name }}
                            @else
                                Admin
                            @endif

                        </a>
                    </li>
                @else
                    <li class="nav-item ml-auto">
                        <a class="nav-link" href="#">
                            @if (Auth::user()->branch_id)
                                {{ Auth::user()->branch->branch_name }}
                            @else
                                N/A
                            @endif
                        </a>
                    </li>
                @endif
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
            <!-- Main content -->
            <section class="content">

                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Setting Edit</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">Setting Edit
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>


                <div class="container-fluid mt-3">
                    <div class="row  justify-content-center d-flex">
                        <!-- left column -->
                        <div class="col-md-8">
                            <!-- general form elements -->
                            <div class="card card-color">
                                <div class="card-header">
                                    <h3 class="card-title  " style="font-weight: bold;">Setting Edit</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body">
                                    <form action="{{ url('setting_update', $setting->id) }}" method="POST">
                                        @csrf
                                        <div class="card-body">
                                            <div class="card-body">

                                                <div class="form-group">
                                                    <label for="name">Category Name <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-control" name="category">
                                                        <option value="" selected disabled>Choose Category
                                                        </option>
                                                        <option value="invoice"
                                                            @if ($setting->category == 'invoice') selected @endif>Invoice
                                                        </option>
                                                        <option value="invoice return"
                                                            @if ($setting->category == 'invoice return') selected @endif>Invoice
                                                            Return</option>
                                                        <option value="pos"
                                                            @if ($setting->category == 'pos') selected @endif>POS
                                                        </option>
                                                        <option value="purchase order"
                                                            @if ($setting->category == 'purchase order') selected @endif>Purchase
                                                            Order</option>
                                                        <option value="expense"
                                                            @if ($setting->category == 'expense') selected @endif>Expense
                                                        </option>

                                                        <option value="purchase order return"
                                                            @if ($setting->category == 'purchase order return') selected @endif>Purchase
                                                            Order Return
                                                        </option>
                                                        <option value="receivable"
                                                            @if ($setting->category == 'receivable') selected @endif>
                                                            Receivable</option>
                                                        <option value="payable"
                                                            @if ($setting->category == 'payable') selected @endif>Payable
                                                        </option>
                                                        <option value="saleaccount"
                                                            @if ($setting->category == 'saleaccount') selected @endif>Sale
                                                            Account (Invoice)
                                                        </option>
                                                        <option value="buyaccount"
                                                            @if ($setting->category == 'buyaccount') selected @endif>Buy
                                                            Account (Purchase Order)
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
                                                            <option value="{{ $branch->id }}"
                                                                @if ($setting->location == $branch->id) selected @endif>
                                                                {{ $branch->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="phno">Account Name <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-control" name="transaction_id" id="account_id">
                                                        <option value="" selected disabled>Choose Choose
                                                        </option>
                                                        @foreach ($transactions as $transaction)
                                                            <option value="{{ $transaction->id }}"
                                                                @if ($setting->transaction_id == $transaction->id) selected @endif>
                                                                {{ $transaction->account_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>



                                            </div>
                                            <div class="modal-footer justify-content-end">

                                                <button type="submit" class="btn btn-primary">Update </button>
                                            </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>


        </section>

    </div>



    </div>
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
