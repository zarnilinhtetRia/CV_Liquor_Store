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
                                <h1>Invoices List</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">Invoices List
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
                                    <i class="fa-solid fa-circle-plus"></i> Add Invoices </button>

                            </div>
                        @endif


                    </div>



                    <div class="modal fade" id="modal-lg">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title"> Add Invoices</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ url('customer_register') }}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="form-group col-md-3">
                                                <label for="pos">POS</label>
                                                <input type="text" class="form-control" id="pos" placeholder="Enter POS" name="pos">
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="date">Date</label>
                                                <input type="text" class="form-control" id="date" placeholder="Enter Date" name="date">
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="time">Time</label>
                                                <input type="text" class="form-control" id="time" placeholder="Enter Time" name="time">
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="cashier_id">Cashier ID</label>
                                                <input type="text" class="form-control" id="cashier_id" placeholder="Enter Cashier ID" name="cashier_id">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-3">
                                                <label for="cashier_name">Cashier Name</label>
                                                <input type="text" class="form-control" id="cashier_name" placeholder="Enter Cashier Name" name="cashier_name">
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="receipt_no">Receipt No</label>
                                                <input type="text" class="form-control" id="receipt_no" placeholder="Enter Receipt No" name="receipt_no">
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="transaction_no">Transaction No</label>
                                                <input type="text" class="form-control" id="transaction_no" placeholder="Enter Transaction No" name="transaction_no">
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="reprinted_by">Reprinted By</label>
                                                <input type="text" class="form-control" id="reprinted_by" placeholder="Enter Reprinted By" name="reprinted_by">
                                            </div>
                                        </div>

                                        <hr>

                                        <div class="row">
                                            <div class="form-group col-md-3">
                                                <label for="reprinted_datetime">Reprinted Date & Time</label>
                                                <input type="text" class="form-control" id="reprinted_datetime" placeholder="Enter Reprinted Date & Time" name="reprinted_datetime">
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="passport_no">Passport No</label>
                                                <input type="text" class="form-control" id="passport_no" placeholder="Enter Passport No" name="passport_no">
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="nationality">Nationality</label>
                                                <input type="text" class="form-control" id="nationality" placeholder="Enter Nationality" name="nationality">
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="flight_code">Flight Code</label>
                                                <input type="text" class="form-control" id="flight_code" placeholder="Enter Flight Code" name="flight_code">
                                            </div>
                                        </div>


  <hr>


  <div class="row">
    <div class="form-group col-12">
        <label for="item_name">Item Name</label>
        <input type="text" class="form-control" id="item_name" placeholder="Enter Item Name" name="item_name">
    </div>
</div>

<div class="row">
    <div class="form-group col-md-3">
        <label for="qty">Qty</label>
        <input type="number" class="form-control" id="qty" name="qty" min="0" step="1" oninput="calculateAll()">
    </div>

    <div class="form-group col-md-3">
        <label for="price">Price</label>
        <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" oninput="calculateAll()">
    </div>

    <div class="form-group col-md-3">
        <label for="discount">Discount (Amount)</label>
        <input type="number" class="form-control" id="discount" name="discount" min="0" step="0.01" oninput="calculateAll()">
    </div>

    <div class="form-group col-md-3">
        <label for="total">Item Total</label>
        <input type="text" class="form-control" id="total" name="total" readonly>
    </div>
</div>

<hr>

<!-- Summary Section -->
<div class="row">
    <div class="form-group col-md-4">
        <label>Sub Total</label>
        <input type="text" class="form-control" id="sub_total" readonly name="sub_total">
    </div>

    <div class="form-group col-md-4">
        <label>GST @ 0.00%</label>
        <input type="text" class="form-control" id="gst" value="0.00" readonly  name="gst">
    </div>

    <div class="form-group col-md-4">
        <label>Total Number of Items</label>
        <input type="text" class="form-control" id="total_items" readonly name="total_items">
    </div>
</div>

<div class="row">
    <div class="form-group col-md-4">
        <label>Summary of Discounts</label>
        <input type="text" class="form-control" id="total_discount" readonly name="total_discount">
    </div>

    <div class="form-group col-md-4">
        <label>Other Disc</label>
        <input type="number" name="other_disc" class="form-control" id="other_disc" value="0" step="0.01" oninput="calculateAll()">
    </div>

    <div class="form-group col-md-4">
        <label>Final Total</label>
        <input type="text" class="form-control" id="final_total" readonly name="final_total">
    </div>
</div>

<hr>

<!-- Payment Section -->
<div class="row">
    <div class="form-group col-md-4">
        <label>Cash</label>
        <input type="number" class="form-control" id="cash" step="0.01" oninput="calculateAll()" name="cash">
    </div>

    <div class="form-group col-md-4">
        <label>Change Back (Cash)</label>
        <input type="text" class="form-control" id="change_back" readonly name="change_back">
    </div>

    <div class="form-group col-md-4">
        <label>Adjust</label>
        <input type="text" class="form-control" id="adjust" readonly name="adjust">
    </div>
</div>
<div class="row">

    <div class="form-group col-md-3">
        <label for="member_tier">Member Tier</label>
        <input type="text" class="form-control" id="member_tier"
               placeholder="Platinum" name="member_tier">
    </div>

    <div class="form-group col-md-3">
        <label for="tier_validity">Tier Validity</label>
        <input type="text" class="form-control" id="tier_validity"
               placeholder="15 Nov 25" name="tier_validity">
    </div>

    <div class="form-group col-md-3">
        <label for="nett_spend">Accumulated Nett Spend</label>
        <input type="text" class="form-control" id="nett_spend"
               placeholder="$52643" name="nett_spend">
    </div>

    <div class="form-group col-md-3">
        <label for="issued_points">Issued Points</label>
        <input type="text" class="form-control" id="issued_points"
               placeholder="5100" name="issued_points">
    </div>

</div>

<div class="row">

    <div class="form-group col-md-3">
        <label for="py2025_bal">PY2025 Bal Points</label>
        <input type="text" class="form-control" id="py2025_bal"
               placeholder="20620" name="py2025_bal">
    </div>

    <div class="form-group col-md-3">
        <label for="py2025_redeem">PY2025 Redeemable Points</label>
        <input type="text" class="form-control" id="py2025_redeem"
               placeholder="4600" name="py2025_redeem">
    </div>

    <div class="form-group col-md-3">
        <label for="py2024_points">PY2024 Points</label>
        <input type="text" class="form-control" id="py2024_points"
               placeholder="0 by 30 Jun 25" name="py2024_points">
    </div>
 <div class="form-group col-md-3">
        <label for="py2024_points">Barcode Number</label>
        <input type="text" class="form-control" id="py2024_points"
               placeholder="0 by 30 Jun 25" name="barcode">
    </div>
</div>





                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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

                        <div class="card ">
                            <div class="card-header">
                                <h3 class="card-title">Invoices List</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">

                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Cashier</th>
                                            <th>Cashier Name</th>
                                            <th>Receipt No</th>
                                            <th>Transaction</th>
                                            <th>Action</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $no = '1';

                                        @endphp
                                        @foreach ($customers as $customer)

                                            <tr>
                                                <td>{{ $no }}</td>
                                                {{-- <td>{{ $customer->id }}</td> --}}
                                                <td><a
                                                        href="">{{ $customer->cashier_id }}</a>
                                                </td>
                                                <td>{{ $customer->cashier_name }}</td>
                                                <td>{{ $customer->receipt_no }}</td>

                                                <td>{{ $customer->transaction_no }}</td>

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

                                                        @endif
 <a href="{{ url('view', $customer->id) }}"
                                                                title="Customer Delete" class=" btn btn-primary mx-2">Print</a>

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
function calculateAll() {
    const qty = parseFloat(document.getElementById('qty').value) || 0;
    const price = parseFloat(document.getElementById('price').value) || 0;
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const otherDisc = parseFloat(document.getElementById('other_disc').value) || 0;
    const cash = parseFloat(document.getElementById('cash').value) || 0;

    // Item Total
    let itemTotal = (qty * price) - discount;
    if (itemTotal < 0) itemTotal = 0;
    document.getElementById('total').value = itemTotal.toFixed(2);

    // Sub Total
    let subTotal = qty * price;
    document.getElementById('sub_total').value = subTotal.toFixed(2);

    // Total items
    document.getElementById('total_items').value = qty;

    // Summary of discounts
    const totalDiscount = discount + otherDisc;
    document.getElementById('total_discount').value = totalDiscount.toFixed(2);

    // Final Total
    let finalTotal = subTotal - totalDiscount;
    if (finalTotal < 0) finalTotal = 0;
    document.getElementById('final_total').value = finalTotal.toFixed(2);

    // Change Back
    const change = cash - finalTotal;
    document.getElementById('change_back').value = change.toFixed(2);

    // Adjust (like the receipt)
    let adjust = 0;
    if (change !== 0) adjust = (change - Math.floor(change)).toFixed(2);
    document.getElementById('adjust').value = adjust;
}
</script>

</body>

</html>
