@include('layouts.header')

<style>
    .nav-tabs .nav-link {
        color: #007FFF;
        transition: color 0.3s, background-color 0.3s;
    }

    .nav-tabs .nav-link:hover {
        color: #0056b3;
        background-color: #e6f7ff;
    }

    .nav-tabs .nav-link.active {
        color: #ffffff;
        background-color: #007FFF;
        border-color: #007FFF;
    }
</style>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav col-md-6">
                <li class="nav-item">
                    <a class="text-white nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>

                <li class="nav-item">
                    <a class="text-white nav-link" href="#">Date -
                        <?= $currentDate = date('d-m-y') ?></a>
                </li>


            </ul>

            <!-- Right navbar links -->
            <ul class="ml-auto navbar-nav">


                <div class="btn-group">
                    <button type="button" class="text-white btn dropdown-toggle" data-toggle="dropdown"
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
        @include('layouts.sidebar') <div class="content-wrapper">
            <!-- Main content -->
            <section class="content">
                {{-- Permission Php --}}
                @php
                    $choosePermission = [];
                    if (auth()->user()->permission) {
                        $decodedPermissions = json_decode(auth()->user()->permission, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $choosePermission = $decodedPermissions;
                        }
                    }
                @endphp
                {{-- End Php --}}

                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Purchase Product Report</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Purchase Product Report</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </section>



                <div class="ml-2 container-fluid">

                    {{-- <div class="my-5 container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                <form action="{{ url('monthly_item_search') }}" method="get">
                                    <div class="row">
                                        <div class="col-md-5 form-group">
                                            <label for="">Date From :</label>
                                            <input type="date" name="start_date" class="form-control" required>
                                        </div>
                                        <div class="col-md-5 form-group">
                                            <label for="">Date To :</label>
                                            <input type="date" name="end_date" class="form-control" required>
                                        </div>
                                        <div class="mt-3 col-md-3 form-group">
                                            <input type="submit" class="btn btn-primary form-control" value="Search"
                                                style="background-color: #218838">
                                        </div>

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div> --}}
                    <div class="mt-3 col-md-12">
                        <div class="card ">
                            <div class="card-header">
                                <h3 class="card-title">Product Reports</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">




@php
use Carbon\Carbon;
@endphp

                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                               <th>Product Name</th>
                                               <th>Date</th>


                                               <th>Qty</th>
                                               <th>Purchase Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php

                                                $no = 1;
                                            @endphp
                                            {{-- @foreach ($purchases as $purchase)

                                               @foreach($purchase->po_sells->filter(function ($item) {
                                                    return \Carbon\Carbon::parse($item->po_date)->month == now()->month
                                                        && \Carbon\Carbon::parse($item->po_date)->year == now()->year;
                                                }) as $item)
                                                    <tr>
                                                        <td>{{ $no++ }}</td>
                                                        <td> {{ $item->product_name }} </td>
                                                        <td> {{ $item->product_qty }} </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach --}}
                                            @php $no = 1; @endphp
                                        @php


                                            $no = 1;
                                            $allItems = collect();

                                            // Combine all po_sells from all purchases
                                            foreach ($purchases as $purchase) {
                                                $filtered = $purchase->po_sells->filter(function ($item) {
                                                    return Carbon::parse($item->po_date)->month == now()->month
                                                        && Carbon::parse($item->po_date)->year == now()->year;
                                                });

                                                // Attach purchase po_date to each po_sell item
                                                $filtered->each(function ($item) use ($purchase) {
                                                    $item->purchase_po_date = $purchase->po_date; // from purchase order
                                                });

                                                $allItems = $allItems->merge($filtered);
                                            }

                                           $grouped = $allItems->groupBy('product_name')->map(function ($items) {
                                                // Collect all unique PO dates (formatted)
                                                $dates = $items->pluck('purchase_po_date')
                                                    ->unique()
                                                    ->map(fn($d) => Carbon::parse($d)->format('d M Y'))
                                                    ->join(', '); // use <br> for line break

                                                return [
                                                    'total_qty' => $items->sum('product_qty'),
                                                    'price' => $items->sum(function ($item) {
                                                        return $item->product_price * $item->product_qty;
                                                    }),
                                                    'po_dates' => $dates ,// all po dates combined
                                                ];
                                            });

                                        @endphp

                                        @foreach ($grouped as $product_name => $data)
                                            <tr>
                                                <td>{{ $no++ }}</td>
                                                <td>{{ $product_name }}</td>
                                                <td>{{ $data['po_dates'] }}</td>
                                                <td>{{ $data['total_qty'] }}</td>
                                                <td>{{ number_format($data['price'], 2) }}</td>

                                            </tr>
                                        @endforeach





                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="4" style="text-align:right">Total:</th>

                                                <th>
                                                    {{
                                                        number_format($grouped->sum('price'))
                                                    }}
                                                </th>
                                            </tr>
                                        </tfoot>

                                    </table>
                                </div>

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
                "responsive": false,
                "lengthChange": false,
                "autoWidth": false,
                "pageLength": 100,
                "buttons": [{
                        extend: 'excelHtml5',
                        text: 'Excel',
                        filename: 'report_products', // Set filename here
                        footer: true
                    },
                    {
                        extend: 'pdfHtml5',
                        text: ' PDF',
                        footer: true,
                        exportOptions: {
                            columns: ':visible' // export only visible columns
                        },
                        customize: function(doc) {

                            doc.pageMargins = [10, 10, 10, 10]; // [left, top, right, bottom]


                            doc.defaultStyle.fontSize = 9; // Smaller font for more

                            doc.styles.tableHeader.fontSize = 9;
                            doc.styles.tableFooter.fontSize = 9;


                            doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                .length + 1).join('*').split('');
                        }
                    }
                ]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        });
    </script>


</body>

</html>
