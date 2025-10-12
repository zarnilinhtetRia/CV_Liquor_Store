@include('layouts.header')
<style>
    .changelogout:hover {
        background-color: whitesmoke;
        color: red;
    }
</style>

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
                                <h1>In-Out</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">In-Out
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

            </section>
            <div class="container-fluid">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>{{ session('success') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @if (session('out-success'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>{{ session('out-success') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="row justify-content-center">

                    <div class="mb-0 col-md-11">
                        <div class="mb-0 col-md-11">
                            <div class="row col-md-11" style="width: 80%">
                                <div class="mb-3 mr-2"> <a href="{{ url('stock_adjust', $item_variations->id) }}"
                                        type="button" class="btn btn-primary">
                                        Stock Adjust </a>
                                </div>

                                <div class="mb-3 mr-2"> <a href="{{ url('damage_item', $item_variations->id) }}"
                                        type="button" class="btn btn-primary">
                                        Damage Item</a>
                                </div>

                                <div class="mb-3 mr-2"> <a href="{{ url('invoice_record', $item_variations->id) }}"
                                        type="button" class="btn btn-primary">
                                        Invoice Record</a>
                                </div>

                                <div class="mb-3 mr-2"> <a href="{{ url('transfer_record', $item_variations->id) }}"
                                        type="button" class="btn btn-primary">
                                        Transfer Record</a>
                                </div>


                            </div>


                        </div>

                        <table class="table table-bordered" style="background-color: #0B5ED7">
                            <tr>
                                <th style="width: 33px">
                                    <a href="{{ url('item_details', $items->id) }}">
                                        <div class="text-center text-white " style="font-weight: bold;color: black">
                                            Item Name - {{ $items->item_name }}


                                        </div>
                                    </a>
                                </th>
                                <th style="width: 33px">

                                    <div class="text-center text-white " style="font-weight: bold;color: black">
                                        Product Code - {{ $item_variations->product_code }}


                                    </div>
                                    </a>
                                </th>
                                <th style="width: 33%">
                                    <a href="{{ url('item_details', $items->id) }}">
                                        <div class="text-center text-white " style="font-weight: bold;color :black">
                                            BarCode
                                            - {{ $item_variations->barcode }}
                                        </div>
                                    </a>
                                </th>
                            </tr>
                        </table>

                    </div>




                    <div class="card col-md-11">
                        <div class="card-header">
                            <span class="card-title" style="font-weight: bold">Purchase Order Record</span>
                        </div>
                        <div class="card-body">
                            <div class="">
                                <table id="example2" class="table table-bordered table-striped ">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Branch</th>
                                            <th>Quantity</th>
                                            <th>Total Quantity</th>
                                            <th>Purchase Price</th>
                                            <th>Date</th>

                                            {{-- <th>Action</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($po as $key => $pos)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $pos->branch ? $pos->branch->name : 'N/A' }}</td>

                                                <td>
                                                    {{ $pos->product_qty }}{{ ' ' }}{{ $pos->unit }}
                                                </td>
                                                <td>
                                                    {{ $item_variations->quantity }}{{ ' ' }}{{ $pos->unit }}
                                                </td>
                                                <td>{{ $pos->product_price }}</td>
                                                <td>{{ $pos->created_at->format('Y-m-d') }}</td>
                                                {{-- <td> <a href="{{ url('stock_adjust_print', [$pos->variation_id, $pos->invoiceid]) }}"
                                                        class="btn btn-primary">
                                                        Print
                                                    </a></td> --}}
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>


                </div>


            </div>
        </div>
    </div>
</body>

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
    new DataTable('#example1', {
        // scrollX: true,
        "lengthChange": false,
        "paging": true,
        "pageLength": 5,

    });
</script>
<script>
    $(document).ready(function() {



        $(document).on("keyup", '#exchange_rate', function(e) {
            e.preventDefault();
            let dolar = $('#purchase_price_in_us').val();
            let rate = $('#exchange_rate').val();
            let result = parseFloat(dolar * rate);
            $('#purchase_price_in_mmk').val(result.toFixed(2));
            // $('#retail_price_in_mmk').val(result.toFixed(2));

        });

    });

    $(document).ready(function() {

        $(document).on("keyup", '#profit_margin', function(e) {
            e.preventDefault();
            let profit_margin = $('#profit_margin').val();
            let retail_price = $('#purchase_price_in_mmk').val();
            let result = (profit_margin * retail_price) / 100;
            let total_result = parseInt(retail_price) + parseInt(result);
            // console.log(total_result);
            $('#retail_price_in_mmk').val((total_result).toFixed(2));
        });
    });




    $(document).ready(function() {



        $(document).on("keyup", '#exchange_rate_yuan', function(e) {
            e.preventDefault();
            let yuan = $('#purchase_price_in_yuan').val();
            let rate = $('#exchange_rate_yuan').val();
            let result = parseFloat(yuan * rate);
            $('#purchase_price_in_mmk').val(result.toFixed(2));
            // $('#retail_price_in_mmk').val(result.toFixed(2));
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let exchangeRateInput = document.getElementById('exchange_rate');

        exchangeRateInput.addEventListener('input', function() {
            // Remove leading zeros
            if (this.value.length > 1 && this.value[0] === '0') {
                this.value = this.value.slice(1);
            }
        });

        exchangeRateInput.addEventListener('blur', function() {
            // If the input is empty, set the value back to "0"
            if (this.value === '') {
                this.value = '0';
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        let purchasePriceInput = document.getElementById('purchase_price_in_us');

        purchasePriceInput.addEventListener('input', function() {
            // Remove leading zeros
            if (this.value.length > 1 && this.value[0] === '0') {
                this.value = this.value.slice(1);
            }
        });

        purchasePriceInput.addEventListener('blur', function() {
            // If the input is empty, set the value back to "0"
            if (this.value === '') {
                this.value = '0';
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        let purchasePriceInput = document.getElementById('purchase_price_in_yuan');

        purchasePriceInput.addEventListener('input', function() {
            // Remove leading zeros
            if (this.value.length > 1 && this.value[0] === '0') {
                this.value = this.value.slice(1);
            }
        });

        purchasePriceInput.addEventListener('blur', function() {
            // If the input is empty, set the value back to "0"
            if (this.value === '') {
                this.value = '0';
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        let exchangeRateYuanInput = document.getElementById('exchange_rate_yuan');

        exchangeRateYuanInput.value = '0'; // Set default value to "0"

        exchangeRateYuanInput.addEventListener('input', function() {
            // Remove leading zeros
            if (this.value.length > 1 && this.value[0] === '0') {
                this.value = this.value.slice(1);
            }
        });

        exchangeRateYuanInput.addEventListener('blur', function() {
            // If the input is empty, set the value back to "0"
            if (this.value === '') {
                this.value = '0';
            }
        });
    });
</script>
