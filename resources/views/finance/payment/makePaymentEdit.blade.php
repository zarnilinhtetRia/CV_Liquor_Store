@include('layouts.header')

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        @include('layouts.nav')
        @include('layouts.sidebar')
        <div class="content-wrapper">


            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            @if ($show->opening_balance == null)
                                <h1>Transaction Payment Edit</h1>
                            @else
                                <h1>Opening Balance Edit</h1>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                                @if ($show->opening_balance == null)
                                    <li class="breadcrumb-item active">Transaction Payment Edit</li>
                                @else
                                    <li class="breadcrumb-item active">Opening Balance Edit</li>
                                @endif
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            @if (session('updateStatus'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('updateStatus') }}
                </div>
            @endif
            <!-- Main content -->
            <section class="content">
                <div class="container">
                    <div class="row justify-content-center d-flex">
                        <div class="col-md-10 my-3 p-3">
                            <div class="card card-primary p-4">

                                <!-- /.card-header -->
                                <!-- form start -->



                                <form action="{{ url('transaction_payment_update', $show->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" value="" name="opening_balance"
                                        value="{{ $show->opening_balance }}">
                                    <input type="hidden" value="" name="transaction_id">
                                    <input type="hidden" value="" name="account_id">

                                    @if ($show->opening_balance == null)
                                        <div class="form-group">
                                            <label for="debit_note">Voucher No. <span
                                                    style="color: red;">*</span></label>
                                            <input type="text" class="form-control" id="debit_note" name="debit_note"
                                                value="{{ $show->debit_note }}" readonly>
                                            @error('debit_note')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @else
                                        <div class="form-group">
                                            <label for="debit_note">Voucher No. <span
                                                    style="color: red;">*</span></label>
                                            <input type="text" class="form-control" name="debit_note"
                                                value="{{ $show->debit_note }}" readonly>
                                            @error('debit_note')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif

                                    <div class="form-group">
                                        <label for="status">Status <span style="color: red;">*</span></label>
                                        <select name="payment_status" class="form-control" id="status" required>
                                            <option value="{{ strtoupper($show->payment_status) }}">
                                                {{ strtoupper($show->payment_status) }}
                                            </option>
                                            <option value="IN">IN</option>
                                            <option value="OUT">OUT</option>
                                        </select>
                                        @error('status')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    @if ($show->opening_balance == null)
                                        <div class="form-group">
                                            <label for="status">Account</label>
                                            <select name="transfer_account_id" class="form-control"
                                                id="transfer_account_id">
                                                <option value="">Choose Select Account</option>
                                                @foreach ($accounts as $account)
                                                    <option value="{{ $account->id }}"
                                                        {{ old('transfer_account_id', $show->transfer_account_id ?? '') == $account->id ? 'selected' : '' }}>
                                                        {{ $account->account_name }}
                                                    </option>
                                                @endforeach
                                            </select>

                                        </div>
                                    @endif

                                    <div class="form-group">
                                        <label for="receiver_name">Receiver Name <span
                                                style="color: red;">*</span></label>
                                        <input type="text" class="form-control" id="receiver_name"
                                            name="receiver_name" value="{{ $show->receiver_name }}">
                                        @error('receiver_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="reference_no">Reference No. <span
                                                style="color: red;">*</span></label>
                                        <input type="text" class="form-control" id="reference_no" name="reference_no"
                                            value="{{ $show->reference_no }}">
                                        @error('reference_no')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="transaction_code">Amount <span style="color: red;">*</span></label>
                                        <input type="number" class="form-control" id="transaction_code" name="amount"
                                            placeholder="Enter Amount" required value="{{ $show->amount }}">
                                        @error('transaction_code')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="payment_date">Payment Date <span
                                                style="color: red;">*</span></label>
                                        <input type="date" class="form-control" id="payment_date"
                                            name="payment_date" value="{{ $show->payment_date }}" required>
                                        @error('payment_date')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea class="form-control" id="description" name="note" rows="3" placeholder="Enter ...">{{ $show->note }}</textarea>
                                        @error('description')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="modal-footer justify-content-end">

                                        <button type="submit" class="btn btn-primary"
                                            style="background-color: #007BFF">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; SSE Web Solutions</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>

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

</body>

</html>
