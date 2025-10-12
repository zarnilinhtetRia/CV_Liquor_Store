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
                                <h1>Stock Adjust</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">Stock Adjust
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








                <div class="mb-2 ml-6 col"> <button type="button" class="mr-auto btn btn-primary " data-toggle="modal"
                        data-target="#modal-lg">
                        Stock Adjust</button>


                </div>



                <div class="modal fade" id="modal-lg">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title"> Stock Adjust</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">

                                <form action="{{ url('in', $id) }}" method="POST">
                                    @csrf
                                    <div class="card-body">
                                        <div class="row">
                                            <input type="text" class="mt-3 form-control" id="item_variation_id"
                                                value="{{ $id }}" name="item_variation_id"
                                                style="display: none"> <input type="text" class="mt-3 form-control"
                                                id="items_id" value="{{ $item_variation->item_id }}" name="items_id"
                                                style="display: none">
                                            <input type="text" class="mt-3 form-control" id="in_out"
                                                value="in" name="in_out" style="display: none">


                                            <div class="form-group col-md-6">
                                                <label for="warehouse_id">Location<span
                                                        class="text-danger">*</span></label>
                                                <select name="warehouse_id" id="warehouse_id" class="form-control"
                                                    required>
                                                    @php

                                                    @endphp
                                                    @if (Auth::user()->is_admin == '1' || auth::user()->type == '0')
                                                        <option value="" selected disabled>Select Branch</option>
                                                        <option value="{{ $items->warehouse->id }}">
                                                            {{ $items->warehouse->name }}</option>
                                                    @else
                                                        <option value="{{ $items->warehouse->id }}">
                                                            {{ $items->warehouse->name }}</option>
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="date">Select Unit</label>
                                                <select name="unit" id="" class="form-control">
                                                    <option value="" selected disabled>Select Unit</option>
                                                    <option value="{{ $item_variation->unit }}">
                                                        {{ $item_variation->unit }}</option>

                                                </select>
                                            </div>


                                            <div class="form-group col-md-6">
                                                <label for="quantity1">Quantity</label>
                                                <input type="number" class="form-control" id="quantity1"
                                                    name="quantity" required>
                                            </div>

                                            <div class="form-group" style="display: none">
                                                <label for="total_quantity1">Total Quantity</label>
                                                <input type="text" class="form-control" id="total_quantity1"
                                                    name="total_quantity">
                                            </div>

                                            <div class="form-group col-md-6" style="display: none;">
                                                <label for="company_price">Company Price</label>
                                                <input type="text" class="form-control" id="company_price"
                                                    name="company_price">
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="date">Date</label>
                                                <input type="date" class="form-control" id="date"
                                                    name="date" max="{{ date('Y-m-d') }}"
                                                    value="{{ date('Y-m-d') }}" required>
                                            </div>

                                            <div class="form-group col-md-12">
                                                <label for="remark">Remark</label>
                                                <textarea rows="4" class="form-control" id="remark" name="remark"></textarea>
                                            </div>


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




                <!-- form start -->

            </div>


            <div class="card col-md-12">
                <div class="card-body">
                    <div class="">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Branch</th>
                                    <th>Quantity</th>
                                    <th>Total Quantity</th>
                                    <th>Date</th>
                                    <th>Remark</th>
                                    {{-- <th>Action</th> --}}
                                </tr>
                            </thead>
                            <tbody>




                                @php
                                    $no = '1';
                                @endphp

                                @foreach ($inouts as $inout)
                                    <tr>
                                        <td>{{ $no }}</td>
                                        <td>
                                            @if ($inout->warehouse_id)
                                                {{ $inout->warehouse->name }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            {{ $inout->quantity }}{{ ' ' }}{{ $inout->unit ?? '' }}
                                        </td>
                                        <td>
                                            {{ $inout->total_quantity }}{{ ' ' }}{{ $inout->unit ?? '' }}
                                        </td>

                                        <td>
                                            {{ $inout->date }}

                                        </td>
                                        <td>{{ $inout->remark }}</td>

                                        {{-- <td><a href="{{ url('display_print/' . $inout->id, $inout->items_id) }}"
                                                class="btn btn-primary">Print</a></td> --}}
                                    </tr>
                                    @php
                                        $no++;
                                    @endphp
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
        "pageLength": 15,

    });
</script>
