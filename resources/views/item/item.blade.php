@include('layouts.header')
<style>
    .img-thumbnail {
        border: 2px solid #ddd;
        border-radius: 4px;
        padding: 5px;
        transition: transform 0.2s;
    }

    .img-thumbnail:hover {
        transform: scale(1.1);
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
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
                    <a class="text-white nav-link " href="#">Date -
                        <?= $currentDate = date('d-m-y') ?></a>
                </li>


                <li class="ml-auto nav-item">
                    <a class="nav-link" href="#">


                    </a>
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
        @include('layouts.sidebar')
        <div class="content-wrapper">
            <!-- Main content -->
            <section class="content">

                <section class="content-header">
                    <div class="container-fluid">
                        <div class="mb-2 row">
                            <div class="col-sm-6">
                                <h1>Product List</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>

                                    </li>
                                    <li class="breadcrumb-item">Product List</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                @if (session('delete'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>{{ session('delete') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif


                {{-- @if (session('excelimport'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>{{ session('excelimport') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span dangeraria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif --}}
                <div id="successMessage"></div>


                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>{{ session('error') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @elseif (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>{{ session('success') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @if ($errors->has('file'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error:</strong> {{ $errors->first('file') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <div class="container-fluid">
                    <div class="row">
                        <div class="ml-2 col row d-flex">
                            <form id="fileImportForm" action="{{ route('file-export') }}" method="GET"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4 form-group" style="max-width: 500px; margin: 0 auto;">
                                    <div class="text-left custom-file">
                                        @if (Auth::user()->is_admin == '1')
                                            <label for="warehouse">Choose Location</label>
                                            <select name="warehouse_id" id="warehouse" class="form-control warehouse"
                                                required>
                                                <option value="All Location" selected>All Location</option>
                                                @foreach ($warehouses as $warehouse)
                                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <label for="warehouse">Choose Location</label>
                                            <select name="warehouse_id" id="warehouse" class="form-control warehouse"
                                                required>
                                                @php
                                                    $userPermissions = auth()->user()->level
                                                        ? json_decode(auth()->user()->level)
                                                        : [];
                                                @endphp
                                                @foreach ($warehouses as $warehouse)
                                                    @if (in_array($warehouse->id, $userPermissions))
                                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        @endif

                                        <div class="p-1 mt-2 text-left custom-file col"
                                            style="border:#d0d0db 1px solid; background-color: white">
                                            <input type="file" name="file" class="" id="customFile">
                                        </div>

                                        <button type="button" class="mt-3 btn btn-primary"
                                            id="importBtn">Import</button>
                                        <button type="submit" class="mt-3 btn btn-success"
                                            id="exportBtn">Export</button>
                                    </div>
                                </div>
                                <a href="{{ route('file-import-template') }}">Download Import CSV Template</a>
                            </form>
                        </div>

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




                    </div>

                    {{-- <div class="mb-2 row d-flex justify-content-end mr-3">
                        @if (in_array('Item Register', $choosePermission) || auth()->user()->is_admin == '1')
                            <div><a href="{{ url('items_register') }}" type="button"
                                    class="mr-auto btn btn-primary ">
                                    Product Register</a></div>
                        @endif

                    </div> --}}






                    <div class="container-fluid">

                        <!-- /.modal -->
                        <div class="mt-3 col-md-12">
                            <div class="bg-white">

                                <div class="card-header d-flex justify-content-between">
                                    <h3 class="card-title">Product List</h3>
                                    <div class="dropdown ml-auto mr-2">
                                        <form method="GET" action="{{ url('items') }}"
                                            class="d-flex align-items-center">
                                            <select name="branch"
                                                class="form-control  shadow rounded bg-primary text-light border-secondary"
                                                id="branch" onchange="this.form.submit()">
                                                @if (Auth::user()->is_admin == '1')
                                                    <option value=""
                                                        {{ request('branch') == '' ? 'selected' : '' }}>All
                                                        Locations
                                                    </option>
                                                    @foreach ($warehouses as $warehouse)
                                                        <option value="{{ $warehouse->id }}"
                                                            {{ request('branch') == $warehouse->id ? 'selected' : '' }}>
                                                            {{ $warehouse->name }}
                                                        </option>
                                                    @endforeach
                                                @else
                                                    @foreach ($warehouses as $warehouse)
                                                        @if (in_array($warehouse->id, $userPermissions))
                                                            <option value="{{ $warehouse->id }}"
                                                                {{ request('branch') == $warehouse->id ? 'selected' : '' }}>
                                                                {{ $warehouse->name }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </select>
                                        </form>
                                    </div>
                                </div>

                                <!-- /.card-header -->

                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-3">

                                        <div class="d-flex align-items-center gap-3">
                                            <!-- Delete Selected Button -->



                                            <form id="delete-form" method="POST"
                                                action="{{ route('delete.items') }}">
                                                @csrf
                                                <button id="delete-selected" class="btn btn-danger btn-sm shadow">
                                                    <i class="bi bi-trash-fill"></i> Delete Selected
                                                </button>
                                        </div>

                                        <div class="input-group col-3">
                                            <input type="text" id="search" class="form-control"
                                                placeholder="Search Products">
                                            <button class="btn btn-primary ml-1" type="button" id="search_button">
                                                Search
                                            </button>
                                        </div>

                                    </div>
                                    <div class="table-responsive">

                                        <table id="example1" class="table table-bordered table-striped items-table">
                                            <thead>
                                                <tr>
                                                    <th><input type="checkbox" id="select-all"></th>
                                                    <th>No.</th>
                                                    <th>Image</th>
                                                    <th>Product Name</th>
                                                    <th>Product Type</th>
                                                    <th>Location</th>
                                                    {{-- <th>Category</th>
                                                    <th>Brand</th> --}}
                                                    <th>Total Warehouse Qty</th>
                                                    <th>Total Available Qty</th>
                                                    <th>Total Qty To Deliver</th>
                                                    {{-- <th>Product Register Type</th> --}}
                                                    <th>Description</th>
                                                    <th style="width: 15%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="items-data">
                                                @foreach ($items as $item)
                                                    @php
                                                        //can access permission
                                                        $isAdmin = auth()->user()->is_admin == 1;
                                                        $warehousePermission = auth()->user()->level
                                                            ? json_decode(auth()->user()->level)
                                                            : [];
                                                        $canAccess =
                                                            $isAdmin ||
                                                            in_array($item->warehouse_id, $warehousePermission);
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <input type="checkbox" class="row-checkbox"
                                                                name="ids[]" value="{{ $item->id }}">
                                                        </td>


                                                        <td>{{ ($items->currentPage() - 1) * $items->perPage() + $loop->iteration }}
                                                        </td>
                                                        <td>
                                                            @php
                                                                $imagePath = $item->image
                                                                    ? asset('item_images/' . $item->image)
                                                                    : asset('img/default.png');
                                                            @endphp
                                                            <a href="{{ $imagePath }}" target="_blank">
                                                                <img src="{{ $imagePath }}" alt="Item Image"
                                                                    width="50" height="50"
                                                                    class="img-thumbnail">
                                                            </a>
                                                        </td>

                                                        <td>{{ $item->item_name }}</td>
                                                        <td>{{ $item->item_type }}</td>
                                                        <td>{{ $item->warehouse->name ?? 'N/A' }}</td>
                                                        {{-- <td>{{ $item->item_category }}</td>
                                                        <td>{{ $item->brand }}</td> --}}
                                                        @php
                                                            $warehouseqty = 0;
                                                            $availableqty = 0;
                                                            $deliverqty = 0;

                                                            if ($item->variations->isNotEmpty()) {
                                                                $warehouseqty = $item->variations->sum(function (
                                                                    $variation,
                                                                ) {
                                                                    return (int) optional($variation->item_quantity)
                                                                        ->warehouse_qty;
                                                                });

                                                                $availableqty = $item->variations->sum(function (
                                                                    $variation,
                                                                ) {
                                                                    return (int) optional($variation->item_quantity)
                                                                        ->available_qty;
                                                                });

                                                                $deliverqty = $item->variations->sum(function (
                                                                    $variation,
                                                                ) {
                                                                    return (int) optional($variation->item_quantity)
                                                                        ->deliver_qty;
                                                                });
                                                            }
                                                        @endphp


                                                        <td
                                                            class="@if ($warehouseqty < 5) text-danger @else text-success @endif text-center rounded">
                                                            @if ($item->stock_type == 'Stock')
                                                                <span
                                                                    style="font-weight: bold !important;font-size:14px !important;">{{ $warehouseqty }}</span>
                                                            @else
                                                                <span
                                                                    style="font-weight: bold !important;font-size:18px !important;">{{ abs($warehouseqty) }}</span>
                                                            @endif
                                                        </td>
                                                        <td
                                                            class="@if ($availableqty < 5) text-danger @else text-success @endif text-center rounded">
                                                            @if ($item->stock_type == 'Stock')
                                                                <span
                                                                    style="font-weight: bold !important;font-size:14px !important;">{{ $availableqty }}</span>
                                                            @else
                                                                <span
                                                                    style="font-weight: bold !important;font-size:14px !important;">{{ abs($availableqty) }}</span>
                                                            @endif
                                                        </td>
                                                        <td
                                                            class="@if ($deliverqty > 10) text-danger @else text-success @endif text-center rounded">
                                                            @if ($item->stock_type == 'Stock')
                                                                <span
                                                                    style="font-weight: bold !important;font-size:14px !important;">{{ $deliverqty }}</span>
                                                            @else
                                                                <span
                                                                    style="font-weight: bold !important;font-size:14px !important;">{{ abs($deliverqty) }}</span>
                                                            @endif
                                                        </td>
                                                        {{-- <td>{{ $item->item_type }}</td> --}}
                                                        <td>{{ $item->item_descriptions }}</td>
                                                        <td>
                                                            @if (in_array('Item Details', $choosePermission) || auth()->user()->is_admin == '1')
                                                                <a href="{{ url('item_details', $item->id) }}"
                                                                    class="btn btn-primary btn-sm ">
                                                                    <i class="fa-solid fa-eye"></i>
                                                                </a>
                                                            @endif

                                                            @if (in_array('Item Edit', $choosePermission) || auth()->user()->is_admin == '1')
                                                                <a href="{{ $canAccess ? url('item_edit', $item->id) : '#' }}"
                                                                    class="btn btn-success btn-sm {{ $canAccess ? '' : 'disabled' }}"
                                                                    title="{{ $canAccess ? 'Edit Item' : 'You do not have permission to edit this item' }}">
                                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                                </a>
                                                            @endif


                                                            @if (in_array('Item Delete', $choosePermission) || auth()->user()->is_admin == '1')
                                                                <a href="{{ $canAccess ? url('item_delete', $item->id) : '#' }}"
                                                                    class="btn btn-danger btn-sm {{ $canAccess ? '' : 'disabled' }}"
                                                                    title="{{ $canAccess ? 'Edit Item' : 'You do not have permission to edit this item' }}"
                                                                    onclick="return confirm('Are you sure you want to delete this Item?')">
                                                                    <i class="fa-solid fa-trash"></i>
                                                                </a>
                                                            @endif




                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>



                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <div class="pagination-info text-start">
                                                Showing {{ $items->firstItem() }} to {{ $items->lastItem() }} of
                                                {{ $items->total() }} results
                                            </div>
                                            <div class="pagination">
                                                {{ $items->links('pagination::bootstrap-4') }}
                                            </div>
                                        </div>
                                    </div>




                                </div>
                                </form>


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
    <!-- AdminLTE for demo purposes -->

    <script>
        $(document).ready(function() {
            $('#search_button').on('click', function() {
                let searchTerm = $('#search').val();
                let branch = $('#branch').val() || null;

                $.ajax({
                    url: '{{ route('item.index') }}',
                    method: 'GET',
                    data: {
                        search: searchTerm,
                        branch: branch,
                    },
                    success: function(response) {
                        $('.items-data').html($(response).find('.items-data').html());
                        $('.pagination-info').html($(response).find('.pagination-info').html());
                        $('.pagination').html($(response).find('.pagination').html());
                    },
                });
            });
        });


        $(document).on('click', '.pagination a', function(event) {
            event.preventDefault();
            let url = $(this).attr('href');
            let searchQuery = $('#search').val();
            $.ajax({
                url: url,
                method: 'GET',
                data: {
                    search: searchQuery,
                },
                success: function(response) {
                    $('.items-data').html($(response).find('.items-data').html());
                    $('.pagination-info').html($(response).find('.pagination-info').html());
                    $('.pagination').html($(response).find('.pagination').html());
                }
            });
        });


        // "Select All" functionality
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('select-all');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');

            selectAllCheckbox.addEventListener('change', function() {
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });
        });


        //Excel Import
        $(document).ready(function() {
            $('#importBtn').on('click', function(e) {
                e.preventDefault();
                var formData = new FormData($('#fileImportForm')[
                    0]);
                $('#importBtn').prop('disabled', true);
                $('#successMessage').html(
                    '<div class="alert alert-info">Importing file... Please wait.</div>'
                );

                $.ajax({
                    url: '{{ route('file-import') }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content')
                    },
                    success: function(response) {
                        $('#successMessage').html(
                            '<div class="alert alert-success">File imported successfully!</div>'
                        );
                        window.location.reload();
                    },
                    error: function(xhr, status, error) {
                        $('#successMessage').html(
                            '<div class="alert alert-danger">Please Choose File!</div>'
                        );
                        $('#importBtn').prop('disabled', false);
                        window.location.reload();

                    }
                });
            });
        });
    </script>





</body>

</html>
