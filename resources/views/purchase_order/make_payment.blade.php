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
                    <a class="nav-link text-white " href="#">Date -
                        <?= $currentDate = date('d-m-y') ?></a>
                </li>


                <li class="nav-item ml-auto">
                    <a class="nav-link" href="#">


                    </a>
                </li>


            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">


                <div class="btn-group">
                    <button type="button" class="btn text-white dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        {{ auth()->user()->name }}
                    </button>
                    <div class="dropdown-menu ">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn  p-1 changelogout " style="width: 157px">
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
                                <h1>Make Payment</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">Make Payment
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

                <div class="container-fluid">
                    <div class="row justify-content-center d-flex">
                        <div class="card col-8">

                            <form action="{{ url('po_make_payment_store', $make_payments->id) }}" method="POST"
                                class="p-5">
                                @csrf
                                <div class="row mb-4">

                                    <div class="col-md-4 form-group">
                                        <button type="button" class="btn btn-primary" style="width: 100%">Total
                                            -
                                            {{ number_format($make_payments->total) }}
                                        </button>

                                    </div>
                                    <input type="hidden" id="location" value="{{ $make_payments->branch }}">



                                    {{-- <div class="frmSearch col-md-3" style="display: none;">
                                        <div class="frmSearch col-sm-12">
                                            <span style="font-weight:bolder">
                                                <label for="cst"
                                                    class="caption">{{ trans('Head Office') }}&nbsp;</label>
                                            </span>
                                            <select id="location" class="mb-4 form-control location" required>
                                                @foreach ($warehouses as $warehouse)
                                                    <option value="{{ $warehouse->id }}">
                                                        {{ $warehouse->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> --}}

                                    <div class="col-md-4 form-group">
                                        <button type="button" class="btn btn-primary" style="width: 100%">Deposit
                                            -
                                            {{ number_format($make_payments->deposit) }}
                                        </button>

                                    </div>

                                    <div class="col-md-4 form-group">
                                        <input type="hidden" name="remain_balance"
                                            value="{{ $make_payments->remain_balance }}">
                                        <button type="button" class="btn btn-primary" style="width: 100%">
                                            Remaining Balance -
                                            {{ number_format($make_payments->remain_balance) }}
                                        </button>
                                    </div>


                                    <div class="form-group col-md-6">
                                        <label for="po_no">Purchase Order Number <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="po_no"
                                            value="{{ $make_payments->quote_no }}" name="po_no" required readonly>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="payment_date">Payment Date<span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="payment_date"
                                            placeholder="Enter payment date" name="payment_date" required
                                            value="{{ date('Y-m-d') }}">

                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="payment_method">Payment Method <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" name="payment_method" id="payment_method-0"
                                            required>
                                            <option disabled>Select payment method</option>
                                            <!-- <option value="Cash">Cash</option>
                                            <option value="K Pay">Kpay</option>
                                            <option value="Wave">Wave</option> -->
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6" id="total_amount">
                                        <label for="amount">Amount <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="amount" name="amount"
                                            placeholder="Enter amount" required>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="note">Note</label>
                                        <textarea rows="3" class="form-control" id="note" name="note" placeholder="Enter note"></textarea>
                                    </div>


                                    <div class="col-md-12 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary px-2">Make Pyament</button>
                                    </div>
                            </form>
                        </div>
                    </div>

                </div>
                <div class="container-fluid mt-5">
                    <div class="row justify-content-center d-flex">
                        <div class="card col-md-11">
                            <div class="card-header">
                                <h5>Payment List</h5>
                            </div>
                            <div class="card-body">
                                <table id="example" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>PO Number</th>
                                            <th>Payment Method</th>
                                            <th>Amount</th>
                                            <th>Note</th>
                                            <th>Payment Date</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($payments as $key => $payment)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $payment->po_no }}</td>
                                                <td>{{ $payment->transaction->transaction_name }}</td>
                                                <td>{{ number_format($payment->amount) }}</td>
                                                <td>{{ $payment->note }}</td>
                                                <td>{{ $payment->payment_date }}</td>
                                                <td>
                                                    <a href="{{ url('po_voucher_view', $payment->id) }}"
                                                        class="btn btn-primary">Print</a>
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </section>

        </div>



    </div>


    @include('layouts.footer')
    <script>
        $(function() {
            $("#example").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "pageLength": 10, // Set the default number of rows to display
                // "buttons": ["excel", "pdf", "print"]
            }).buttons().container().appendTo('#example_wrapper .col-md-6:eq(0)');
        });
    </script>

    <script>
        $(document).ready(function() {
            getAccount(0);
        });
        $('#location').on('change', function() {
            getAccount(0);
        });
        $('#balance_due').on('change', function() {
            getAccount(0);
        });

        function getAccount(payment_count) {
            var locationId = $("#location").val();
            // var balance_due = $("#balance_due").val();
            var balance_due = @json($make_payments->balance_due);
            $('#payment_method-' + payment_count).html(
                '<option value="">Loading...</option>');
            if (locationId) {
                $.ajax({
                    url: '{{ route('get_accounts_transaction_po') }}',
                    type: 'GET',
                    data: {
                        locationId: locationId,
                        balance_due: balance_due
                    },
                    success: function(data) {
                        console.log(data);
                        $('#payment_method-' + payment_count)
                            .empty().append(
                                '<option value="">Select Transaction</option>'
                            );
                        if (data && data.length > 0) {
                            $.each(data, function(index,
                                transaction) {
                                $('#payment_method-' +
                                        payment_count)
                                    .append(
                                        '<option value="' +
                                        transaction
                                        .id + '">' +
                                        transaction
                                        .transaction_name +
                                        '</option>');
                            });
                        } else {
                            $('#payment_method-' +
                                payment_count).append(
                                '<option value="">No Transaction available</option>'
                            );
                        }
                    },
                    error: function() {
                        $('#payment_method-' + payment_count)
                            .empty().append(
                                '<option value="">Error loading transactions</option>'
                            );
                    }
                });
            } else {
                $('#payment_method-' + payment_count).empty().append(
                    '<option value="">Select Transaction</option>');
            }
            $('#location').on('change', function() {
                getAccount(payment_count);
            });
        }
    </script>
