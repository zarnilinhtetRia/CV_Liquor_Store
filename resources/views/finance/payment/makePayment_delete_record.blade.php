@include('layouts.header')
<style>
    #tableSelect {
        background-color: #6c757d;
        color: white;
    }

    #tableSelect option {
        background-color: white;
        color: black;
    }


    #tableSelect:focus {
        background-color: #6c757d;
        color: white;
    }
</style>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        @include('layouts.nav')
        @include('layouts.sidebar')
        <div class="content-wrapper">


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
                                            <h1>Payment Delete Record</h1>
                                        </div>
                                        <div class="col-sm-6">
                                            <ol class="breadcrumb float-sm-right">
                                                <li class="breadcrumb-item"><a
                                                        href="{{ url('/dashboard') }}">Dashboard</a></li>
                                                <li class="breadcrumb-item active">Payment Delete Record</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div><!-- /.container-fluid -->
                            </section>


                            <div class="container-fluid mb-4 mr-auto">
                                <div class="row justify-content-start align-items-start">
                                    <div class="col-auto">
                                        <a href="{{ url('payment', $id) }}" class="btn btn-danger text-white">
                                            Back
                                        </a>
                                    </div>
                                </div>
                            </div>


                            <!-- Tab content -->
                            <div class="card">

                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="example1" class="table table-bordered table-striped mt-4">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Transfer Account</th>
                                                    <th>Account Name</th>
                                                    <th>Status</th>
                                                    <th>Opening Balance</th>
                                                    <th>Credit No</th>
                                                    <th>Receiver Name</th>
                                                    <th>Reference No</th>
                                                    <th>Payment Date</th>
                                                    <th>Description</th>
                                                    <th>Amount</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($payments as $key => $payment)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td> {{ $payment->transferAccount ? $payment->transferAccount->account_name : 'N/A' }}
                                                        </td>
                                                        <td>
                                                            {{ $payment->account->account_name }}
                                                        </td>
                                                        <td>
                                                            {{ $payment->payment_status }}
                                                        </td>
                                                        <td>
                                                            {{ $payment->opening_balance ?? 'N/A' }}
                                                        </td>
                                                        <td>{{ $payment->voucher_no ?? 'N/A' }}</td>
                                                        <td>{{ $payment->receiver_name ?? 'N/A' }}</td>
                                                        <td>{{ $payment->reference_no ?? 'N/A' }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y') ?? 'N/A' }}
                                                        </td>
                                                        <td>{{ $payment->note ?? 'N/A' }}</td>
                                                        <td>{{ number_format($payment->amount) }}</td>
                                                        <td><a href="{{ url('payments_restore', $payment->id) }}"
                                                                class="btn btn-success rounded-pill">Restore</a></td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>


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
        $(function() {
            $("#example1").DataTable({
                "lengthChange": false,
                "autoWidth": false,
                "pageLength": 30,
            });
        });
    </script>

</body>

</html>
