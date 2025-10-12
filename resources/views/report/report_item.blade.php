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
                                <h1>Product Report</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Item Report</li>
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


                                {{--
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Item Name</th>
                                            @foreach ($warehouses as $warehouse)
                                                <th>{{ $warehouse->name }}</th>
                                            @endforeach
                                            <th>Total Quantity</th>
                                            <th> Price</th>
                                            <th>Wholesale Price</th>
                                            <th>Total Amount (Retail)</th>
                                            <th>Total Amount (Wholesale)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalRetailPrice = 0;
                                            $totalWholesalePrice = 0;
                                            $totalRetailAmount = 0;
                                            $totalWholesaleAmount = 0;
                                        @endphp
                                        @foreach ($items as $item)
                                            @php
                                                $totalRetailPrice += (float) $item['variation_retail_price'];
                                                $totalWholesalePrice += (float) $item['variation_wholesale_price'];

                                                if ($item['item_type'] == 'Service') {
                                                    $totalRetailAmount +=
                                                        (float) 1 * (float) $item['variation_retail_price'];
                                                } else {
                                                    $totalRetailAmount +=
                                                        (float) $item['total_quantity'] *
                                                        (float) $item['variation_retail_price'];
                                                }

                                                if ($item['item_type'] == 'Service') {
                                                    $totalWholesaleAmount +=
                                                        (float) 1 * (float) $item['variation_wholesale_price'];
                                                } else {
                                                    $totalWholesaleAmount +=
                                                        (float) $item['total_quantity'] *
                                                        (float) $item['variation_wholesale_price'];
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{ $item['item_name'] }}</td>
                                                @foreach ($warehouses as $warehouse)
                                                    <td>
                                                        @if ($item['item_type'] == 'Service')
                                                            0
                                                        @else
                                                            {{ $item['warehouse_quantities'][$warehouse->id] ?? 0 }}
                                                        @endif
                                                    </td>
                                                @endforeach
                                                <td>
                                                    @if ($item['item_type'] == 'Service')
                                                        0
                                                    @else
                                                        {{ $item['total_quantity'] }}
                                                    @endif
                                                </td>
                                                <td>{{ number_format((float) $item['variation_retail_price']) }}</td>
                                                <td>{{ number_format((float) $item['variation_wholesale_price']) }}</td>
                                                <td>
                                                    @if ($item['item_type'] == 'Service')
                                                        {{ number_format((float) 1 * (float) $item['variation_retail_price']) }}
                                                    @else
                                                        {{ number_format((float) $item['total_quantity'] * (float) $item['variation_retail_price']) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($item['item_type'] == 'Service')
                                                        {{ number_format((float) 1 * (float) $item['variation_wholesale_price']) }}
                                                    @else
                                                        {{ number_format((float) $item['total_quantity'] * (float) $item['variation_wholesale_price']) }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="{{ count($warehouses) + 2 }}" class="text-right">Total</td>
                                            <td>{{ number_format($totalRetailPrice) }}</td>
                                            <td>{{ number_format($totalWholesalePrice) }}</td>
                                            <td>{{ number_format($totalRetailAmount) }}</td>
                                            <td>{{ number_format($totalWholesaleAmount) }}</td>
                                        </tr>
                                    </tfoot>
                                </table> --}}



                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Product Name</th>
                                                {{-- <th>Variation Descriptions</th> --}}
                                                @foreach ($warehouses as $warehouse)
                                                    <th>{{ $warehouse->name }}</th>
                                                @endforeach
                                                <th style="background-color: #7ed3fa">Total Quantity</th>
                                                <th>Purchase Price</th>
                                                <th>Retail Price</th>
                                                <th>Wholesale Price</th>
                                                <th>Total Amount(Purchase)</th>
                                                <th>Total Amount (Retail)</th>
                                                <th>Total Amount (Wholesale)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalRetailPrice = 0;
                                                $totalWholesalePrice = 0;
                                                $totalPurchasePrice = 0;
                                                $totalRetailAmount = 0;
                                                $totalWholesaleAmount = 0;
                                                $totalPurchaseAmount = 0;
                                                $no = 1;
                                            @endphp
                                            @foreach ($variations as $variation)
                                                @php
                                                    $totalRetailPrice += (float) $variation['retail_price'];
                                                    $totalPurchasePrice += (float) $variation['buy_price'];
                                                    $totalWholesalePrice += (float) $variation['wholesale_price'];
                                                    $totalRetailAmount +=
                                                        (float) $variation['total_quantity'] *
                                                        (float) $variation['retail_price'];
                                                    $totalWholesaleAmount +=
                                                        (float) $variation['total_quantity'] *
                                                        (float) $variation['wholesale_price'];
                                                    $totalPurchaseAmount +=
                                                        (float) $variation['total_quantity'] *
                                                        (float) $variation['buy_price'];
                                                @endphp
                                                <tr>
                                                    <td>{{ $no }}</td>
                                                    <td>{{ $variation['item_name'] }} (
                                                        {{ $variation['variation_desc'] }} )</td>

                                                    @foreach ($warehouses as $warehouse)
                                                        <td>{{ $variation['warehouse_quantities'][$warehouse->id] ?? 0 }}
                                                        </td>
                                                    @endforeach
                                                    <td style="background-color: #7ed3fa">
                                                        {{ $variation['total_quantity'] }}</td>
                                                    <td>{{ number_format($variation['buy_price']) }}</td>
                                                    <td>{{ number_format($variation['retail_price']) }}</td>

                                                    <td>{{ number_format($variation['wholesale_price']) }}</td>
                                                    <td>{{ number_format($variation['total_quantity'] * $variation['buy_price']) }}
                                                    </td>
                                                    <td>{{ number_format($variation['total_quantity'] * $variation['retail_price']) }}
                                                    </td>
                                                    <td>{{ number_format($variation['total_quantity'] * $variation['wholesale_price']) }}
                                                    </td>
                                                </tr>
                                                @php
                                                    $no++;
                                                @endphp
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="{{ count($warehouses) + 2 }}" class="text-right">
                                                </td>
                                                <td class="text-right">Total
                                                </td>
                                                <td>{{ number_format($totalPurchasePrice) }}</td>
                                                <td>{{ number_format($totalRetailPrice) }}</td>
                                                <td>{{ number_format($totalWholesalePrice) }}</td>
                                                <td>{{ number_format($totalPurchaseAmount) }}</td>
                                                <td>{{ number_format($totalRetailAmount) }}</td>
                                                <td>{{ number_format($totalWholesaleAmount) }}</td>
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
