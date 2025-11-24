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
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Customer Edit</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">Invoices Edit
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
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title  " style="font-weight: bold;">Invoices Edit</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body">
                                    <form action="{{ url('customer_update', $data->id) }}" method="POST">


        @csrf


        <!-- POS & Transaction Details -->
        <div class="row">
            <div class="form-group col-md-3">
                <label for="pos">POS</label>
                <input type="text" class="form-control" id="pos" name="pos" value="{{ $data->pos }}">
            </div>

            <div class="form-group col-md-3">
                <label for="date">Date</label>
                <input type="text" class="form-control" id="date" name="date" value="{{ $data->date }}">
            </div>

            <div class="form-group col-md-3">
                <label for="time">Time</label>
                <input type="text" class="form-control" id="time" name="time" value="{{ $data->time }}">
            </div>

            <div class="form-group col-md-3">
                <label for="cashier_id">Cashier ID</label>
                <input type="text" class="form-control" id="cashier_id" name="cashier_id" value="{{ $data->cashier_id }}">
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-3">
                <label for="cashier_name">Cashier Name</label>
                <input type="text" class="form-control" id="cashier_name" name="cashier_name" value="{{ $data->cashier_name }}">
            </div>

            <div class="form-group col-md-3">
                <label for="receipt_no">Receipt No</label>
                <input type="text" class="form-control" id="receipt_no" name="receipt_no" value="{{ $data->receipt_no }}">
            </div>

            <div class="form-group col-md-3">
                <label for="transaction_no">Transaction No</label>
                <input type="text" class="form-control" id="transaction_no" name="transaction_no" value="{{ $data->transaction_no }}">
            </div>

            <div class="form-group col-md-3">
                <label for="reprinted_by">Reprinted By</label>
                <input type="text" class="form-control" id="reprinted_by" name="reprinted_by" value="{{ $data->reprinted_by }}">
            </div>
        </div>

        <hr>

        <!-- Passenger / Travel Info -->
        <div class="row">
            <div class="form-group col-md-3">
                <label for="reprinted_datetime">Reprinted Date & Time</label>
                <input type="text" class="form-control" id="reprinted_datetime" name="reprinted_datetime" value="{{ $data->reprinted_datetime }}">
            </div>

            <div class="form-group col-md-3">
                <label for="passport_no">Passport No</label>
                <input type="text" class="form-control" id="passport_no" name="passport_no" value="{{ $data->passport_no }}">
            </div>

            <div class="form-group col-md-3">
                <label for="nationality">Nationality</label>
                <input type="text" class="form-control" id="nationality" name="nationality" value="{{ $data->nationality }}">
            </div>

            <div class="form-group col-md-3">
                <label for="flight_code">Flight Code</label>
                <input type="text" class="form-control" id="flight_code" name="flight_code" value="{{ $data->flight_code }}">
            </div>
        </div>

        <hr>

        <!-- Item / Purchase Details -->
        <div class="row">
            <div class="form-group col-12">
                <label for="item_name">Item Name</label>
                <input type="text" class="form-control" id="item_name" name="item_name" value="{{ $data->item_name }}">
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-3">
                <label for="qty">Qty</label>
                <input type="number" class="form-control" id="qty" name="qty" value="{{ $data->qty }}" min="0" step="1" oninput="calculateAll()">
            </div>

            <div class="form-group col-md-3">
                <label for="price">Price</label>
                <input type="number" class="form-control" id="price" name="price" value="{{ $data->price }}" min="0" step="0.01" oninput="calculateAll()">
            </div>

            <div class="form-group col-md-3">
                <label for="discount">Discount</label>
                <input type="number" class="form-control" id="discount" name="discount" value="{{ $data->discount }}" min="0" step="0.01" oninput="calculateAll()">
            </div>

            <div class="form-group col-md-3">
                <label for="total">Item Total</label>
                <input type="text" class="form-control" id="total" name="total" value="{{ $data->total }}" readonly>
            </div>
        </div>

        <hr>

        <!-- Summary Section -->
        <div class="row">
            <div class="form-group col-md-4">
                <label>Sub Total</label>
                <input type="text" class="form-control" id="sub_total" value="{{ $data->sub_total }}" readonly>
            </div>

            <div class="form-group col-md-4">
                <label>GST @ 0.00%</label>
                <input type="text" class="form-control" id="gst" value="{{ $data->gst }}" readonly>
            </div>

            <div class="form-group col-md-4">
                <label>Total Number of Items</label>
                <input type="text" class="form-control" id="total_items" value="{{ $data->total_items }}" readonly>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-4">
                <label>Summary of Discounts</label>
                <input type="text" class="form-control" id="total_discount" value="{{ $data->total_discount }}" readonly>
            </div>

            <div class="form-group col-md-4">
                <label>Other Disc</label>
                <input type="number" class="form-control" id="other_disc" name="other_disc" value="{{ $data->other_disc }}" step="0.01" oninput="calculateAll()">
            </div>

            <div class="form-group col-md-4">
                <label>Final Total</label>
                <input type="text" class="form-control" id="final_total" value="{{ $data->final_total }}" readonly>
            </div>
        </div>

        <hr>

        <!-- Payment Section -->
        <div class="row">
            <div class="form-group col-md-4">
                <label>Cash</label>
                <input type="number" class="form-control" id="cash" name="cash" value="{{ $data->cash }}" step="0.01" oninput="calculateAll()">
            </div>

            <div class="form-group col-md-4">
                <label>Change Back (Cash)</label>
                <input type="text" class="form-control" id="change_back" value="{{ $data->change_back }}" readonly>
            </div>

            <div class="form-group col-md-4">
                <label>Adjust</label>
                <input type="text" class="form-control" id="adjust" value="{{ $data->adjust }}" readonly>
            </div>
        </div>

        <hr>

        <!-- Member Section -->
        <div class="row">
            <div class="form-group col-md-3">
                <label for="member_tier">Member Tier</label>
                <input type="text" class="form-control" id="member_tier" name="member_tier" value="{{ $data->member_tier }}">
            </div>

            <div class="form-group col-md-3">
                <label for="tier_validity">Tier Validity</label>
                <input type="text" class="form-control" id="tier_validity" name="tier_validity" value="{{ $data->tier_validity }}">
            </div>

            <div class="form-group col-md-3">
                <label for="nett_spend">Accumulated Nett Spend</label>
                <input type="text" class="form-control" id="nett_spend" name="nett_spend" value="{{ $data->nett_spend }}">
            </div>

            <div class="form-group col-md-3">
                <label for="issued_points">Issued Points</label>
                <input type="text" class="form-control" id="issued_points" name="issued_points" value="{{ $data->issued_points }}">
            </div>
            <div class="form-group col-md-3">
        <label for="py2024_points">Barcode Number</label>
        <input type="text" class="form-control" id="py2024_points"
               placeholder="0 by 30 Jun 25" name="barcode" value="{{ $data->barcode }}">
    </div>
        </div>
                                        <div class="modal-footer justify-content-end">
                                            <a href="{{ url('customer') }}" class="btn btn-danger">Back</a>
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
<script>
function calculateAll() {
    // Get input values
    const qty = parseFloat(document.getElementById('qty').value) || 0;
    const price = parseFloat(document.getElementById('price').value) || 0;
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const otherDisc = parseFloat(document.getElementById('other_disc').value) || 0;
    const cash = parseFloat(document.getElementById('cash').value) || 0;

    // Calculate Item Total
    let itemTotal = (qty * price) - discount;
    if (itemTotal < 0) itemTotal = 0;
    document.getElementById('total').value = itemTotal.toFixed(2);

    // Calculate Sub Total
    let subTotal = qty * price;
    document.getElementById('sub_total').value = subTotal.toFixed(2);

    // Total number of items
    document.getElementById('total_items').value = qty;

    // Summary of Discounts
    const totalDiscount = discount + otherDisc;
    document.getElementById('total_discount').value = totalDiscount.toFixed(2);

    // Final Total
    let finalTotal = subTotal - totalDiscount;
    if (finalTotal < 0) finalTotal = 0;
    document.getElementById('final_total').value = finalTotal.toFixed(2);

    // Change Back
    const change = cash - finalTotal;
    document.getElementById('change_back').value = change.toFixed(2);

    // Adjust (like rounding differences)
    let adjust = 0;
    if (change !== 0) {
        adjust = (change - Math.floor(change)).toFixed(2);
    }
    document.getElementById('adjust').value = adjust;

    // GST calculation if needed (currently 0%)
    const gstRate = 0; // Change this if needed
    const gstAmount = finalTotal * gstRate / 100;
    document.getElementById('gst').value = gstAmount.toFixed(2);
}

// Attach calculateAll to inputs (optional if not using inline oninput)
document.querySelectorAll('#qty, #price, #discount, #other_disc, #cash').forEach(el => {
    el.addEventListener('input', calculateAll);
});
</script>


    @include('layouts.footer')
