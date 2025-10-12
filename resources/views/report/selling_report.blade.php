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
                                <h1>Sale Product Report</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Sale Product Reports</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </section>



                <div class="ml-2 container-fluid">
                    <form action="{{ url('selling_report') }}" method="get">
                        <div class="my-5 container-fluid">
                            <div class="row">
                                <div class="col-md-6">
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
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 col-md-12">
                            <div class="card ">
                                <div class="card-header d-flex justify-content-between">
                                    <h3 class="card-title">Sale Product Reports</h3>
                                    <div class="dropdown ml-auto mr-2"><select name="branch"
                                            class="form-control  shadow rounded bg-primary text-light border-secondary"
                                            id="branch" onchange="this.form.submit()">
                                            <option value="" {{ request('branch') == '' ? 'selected' : '' }}>All
                                                Locations
                                            </option>
                                            @foreach ($warehouses as $warehouse)
                                                <option value="{{ $warehouse->id }}"
                                                    {{ request('branch') == $warehouse->id ? 'selected' : '' }}>
                                                    {{ $warehouse->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Product Name</th>
                                                {{-- <th>Brand</th> --}}
                                                <th>Modal</th>
                                                <th>Colour</th>
                                                {{-- <th>Size</th> --}}
                                                <th>Qty</th>
                                                <th>Price</th>
                                                <th>Location</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- @foreach ($sells as $key => $sell)
                                                @foreach ($sell->variations as $variation)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $sell->product_name }} ( {{ $sell->description }} )</td>

                                                        <td>{{ $variation->model }}</td>
                                                        <td>{{ $variation->colour }}</td>

                                                        <td>{{ $sell->product_qty }}
                                                        </td>
                                                        <td>{{ $sell->retail_price }}</td>
                                                        <td>{{ $sell->branch ? $sell->branch->name : '' }}</td>
                                                    </tr>
                                                @endforeach
                                            @endforeach --}}


                                            {{-- change for group by product name and  description (sum qty and price) start  --}}
                                            @php
                                                $groupedSells = $sells->groupBy(function ($item) {
                                                    return $item->product_name . '|' . $item->description;
                                                });
                                            @endphp
                                            @php
                                                $row = 1;
                                                $final_total_price = 0;
                                            @endphp
                                            @foreach ($groupedSells as $groupKey => $group)
                                                @php
                                                    $first = $group->first();
                                                    $total_qty = $group->sum('product_qty');
                                                    $total_price = $group->sum('retail_price');
                                                @endphp

                                                @foreach ($first->variations as $variation)
                                                    <tr>
                                                        <td>{{ $row++ }}</td>
                                                        <td>{{ $first->product_name }} ( {{ $first->description }} )
                                                        </td>
                                                        <td>{{ $variation->model }}</td>
                                                        <td>{{ $variation->colour }}</td>
                                                        <td>{{ $total_qty }}</td>
                                                        <td>{{ number_format($total_price) }}</td>
                                                        <td>{{ $first->branch ? $first->branch->name : '' }}</td>
                                                    </tr>
                                                    @php
                                                        $final_total_price += $total_price;
                                                    @endphp
                                                @endforeach
                                            @endforeach



                                            {{-- change for group by product name and  description (sum qty and price) end  --}}
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4"></td>
                                                <td class="text-right">Total</td>
                                                <td>{{ number_format($final_total_price) }}</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>



                                </div>
                                <!-- /.card-body -->
                            </div>
                        </div>
                    </form>
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

                "buttons": [{
                    extend: 'excelHtml5',
                    text: 'Excel',
                    titleAttr: 'Export to Excel',
                    className: 'btn btn-secondary',
                    footer: true,

                }, {
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
                }]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        });
    </script>


</body>

</html>
