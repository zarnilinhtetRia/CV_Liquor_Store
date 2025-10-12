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

                            <form action="{{ url('make_payment_store', $make_payments->id) }}" method="POST"
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


                                    <div class="form-group col-md-4">
                                        <label for="">Invoice No</label>
                                        <input type="text" class="form-control" id=""
                                            value="{{ $make_payments->invoice_no }}" name="" readonly>
                                        {{-- <input type="text" class="form-control" id="payment_id"
                                            value="{{ $make_payments->branch }}" name="" hidden> --}}
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="invoice_no">Cash Voucher No<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="invoice_no" value=""
                                            name="invoice_no" required readonly>
                                    </div>


                                    <div class="form-group col-md-4">
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
                                        <button type="submit" class="btn btn-primary px-2"
                                            onclick="return confirm('Are you sure payment?');">
                                            Make Payment
                                        </button>
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
                                            <th>Cash Voucher No</th>
                                            <th>Payment Method</th>
                                            <th>Amount</th>
                                            <th>Note</th>
                                            <th>Payment Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($payments as $key => $payment)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $payment->invoice_no }}</td>
                                                <td>{{ $payment->transaction->transaction_name }}</td>
                                                <td>{{ number_format($payment->amount) }}</td>
                                                <td>{{ $payment->note }}</td>
                                                <td>{{ $payment->payment_date }}</td>
                                                <td>

                                                    <a href="{{ url('cash_voucher_delete', $payment->id) }}"
                                                        class="btn btn-danger"
                                                        onclick="return confirm('Are you sure you want to delete this voucher?');">Delete</a>

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

        function getAccount(payment_count) {

            var locationId = $("#location").val();
            var register_mode = @json($make_payments->balance_due);
            $('#payment_method-' + payment_count).html(
                '<option value="">Loading...</option>');

            if (locationId) {
                $.ajax({
                    url: "{{ route('get_accounts_transaction') }}",
                    method: 'GET',
                    data: {
                        locationId: locationId,
                        register_mode: register_mode,
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



        //invoice number update
        // function fetchInvoiceUpdates() {
        //     $.ajax({
        //         url: "{{ url('payment_no_updates') }}",
        //         method: "GET",
        //         dataType: "json",
        //         data: [
        //             'payment_id': payment_id
        //         ]
        //         success: function(data) {
        //             const invoiceNoInput = $("#invoice_no");
        //             if (data.invoice_no && invoiceNoInput.val() !== data.invoice_no) {
        //                 invoiceNoInput.val(data.invoice_no);
        //             }
        //         },
        //         error: function(xhr, status, error) {
        //             console.error("Failed to fetch invoice updates:", error);
        //         }
        //     });
        // }

        // setInterval(fetchInvoiceUpdates, 3000);

        $(document).ready(function() {
            function fetchInvoiceUpdates() {
                const paymentId = $("#payment_id").val();

                $.ajax({
                    url: "{{ url('payment_no_updates') }}",
                    method: "GET",
                    dataType: "json",
                    // data: {
                    //     payment_id: paymentId
                    // },
                    success: function(data) {
                        const invoiceNoInput = $("#invoice_no");
                        if (data.invoice_no && invoiceNoInput.val() !== data.invoice_no) {
                            invoiceNoInput.val(data.invoice_no);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Failed to fetch invoice updates:", error);
                    }
                });
            }
            // Fetch updates every 3 seconds

            fetchInvoiceUpdates();
            setInterval(fetchInvoiceUpdates, 3000);
        });
    </script>
