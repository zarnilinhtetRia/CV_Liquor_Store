@include('layouts.header')

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
                                <h1>Items</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>

                                    </li>
                                    <li class="breadcrumb-item">Items</li>
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

                <div class="container-fluid">
                    <div class="row">
                        <div class="ml-2 col row d-flex">

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
                        {{-- End Permission --}}

                    </div>


                    @if (in_array('Item Register', $choosePermission) || auth()->user()->is_admin == '1')
                        <div class="mt-5 mr-auto col">
                            <a href="{{ url('items_register') }}" type="button" class="mr-auto btn btn-primary ">
                                Item Register</a>
                        </div>
                    @endif


                    <div class="container-fluid">

                        <!-- /.modal -->
                        <div class="mt-3 col-md-12">
                            <div class="bg-white">

                                <div class="card-header">
                                    <h3 class="card-title">Items List</h3>
                                </div>

                                <!-- /.card-header -->
                                <form id="delete-form" method="POST" action="{{ route('delete.items') }}">
                                    @csrf
                                    <div class="card-body">
                                        <div class="d-flex justify-content-end mb-3">
                                            <!-- <button id="delete-selected" class="btn btn-danger mx-2">Delete
                                                Selected</button> -->

                                            <input type="text" id="search" class="form-control col-2 ms-auto"
                                                placeholder="Search items">
                                        </div>
                                        <div class="table-responsive">
                                            <table id="example1"
                                                class="table table-bordered table-striped items-table">
                                                <thead>
                                                    <tr>
                                                        <!-- <th><input type="checkbox" id="select-all"></th> -->
                                                        <th>No.</th>
                                                        <th>Item Name</th>
                                                        <th>Category</th>
                                                        <th>Brand</th>
                                                        <th>Model</th>
                                                        <th>Colour</th>
                                                        <th>Size</th>
                                                        <th>Seater</th>
                                                        <th>Item Register Type</th>
                                                        <th>Warehouse Qty</th>
                                                        <th>Available Qty</th>
                                                        <th>Deliver Qty</th>
                                                        <th>Location</th>
                                                        <!-- <th style="width: 15%">Action</th> -->
                                                    </tr>
                                                </thead>
                                                <tbody class="items-data">
                                                    @php
                                                        $no = 1;
                                                    @endphp

                                                    @foreach ($items as $item)
                                                        <tr>
                                                            <!-- <td>
                                                                <input type="checkbox" class="row-checkbox"
                                                                    name="ids[]" value="{{ $item->id }}">
                                                            </td> -->
                                                            <td>{{ ($items->currentPage() - 1) * $items->perPage() + $loop->iteration }}
                                                            </td>
                                                            <td>{{ $item->item->item_name }}</td>
                                                            <td>{{ $item->item->item_category }}</td>
                                                            <td>{{ $item->item->brand }}</td>
                                                            <td>{{ $item->variations->model }}</td>
                                                            <td>{{ $item->variations->colour }}</td>
                                                            <td>{{ $item->variations->size }}</td>
                                                            <td>{{ $item->variations->seater }}</td>
                                                            <td>{{ $item->item->item_type }}</td>
                                                            <td class="text-center">{{ $item->warehouse_qty }}</td>
                                                            @if( $item->available_qty < 0)
                                                                <td class="bg-danger rounded text-center">{{ $item->available_qty }}</td>
                                                            @else
                                                                <td class="bg-success rounded text-center">{{ $item->available_qty }}</td>
                                                            @endif
                                                            <td class="text-center">{{ $item->deliver_qty }}</td>
                                                            <td>{{ $item->item->warehouse->name ?? 'N/A' }}</td>
                                                            <!-- <td>
                                                                @if (in_array('Item Details', $choosePermission) || auth()->user()->is_admin == '1')
                                                                    <a href="{{ url('item_details', $item->item->id) }}"
                                                                        class="btn btn-primary btn-sm"><i
                                                                            class="fa-solid fa-eye"></i></a>
                                                                @endif
                                                                @if (in_array('Item Edit', $choosePermission) || auth()->user()->is_admin == '1')
                                                                    <a href="{{ url('item_edit', $item->item->id) }}"
                                                                        class="btn btn-success btn-sm"><i
                                                                            class="fa-solid fa-pen-to-square"></i></a>
                                                                @endif
                                                                @if (in_array('Item Delete', $choosePermission) || auth()->user()->is_admin == '1')
                                                                    <a href="{{ url('item_delete', $item->item->id) }}"
                                                                        class="btn btn-danger btn-sm"
                                                                        onclick="return confirm('Are you sure you want to delete this Item ?')"><i
                                                                            class="fa-solid fa-trash"></i></a>
                                                                @endif
                                                                {{-- @if (in_array('Inout History', $choosePermission) || auth()->user()->is_admin == '1')
                                                                <a href="{{ url('in_out', $item->item->id) }}"
                                                                    class="mt-1 btn btn-info btn-sm">In/Out History</a>
                                                            @endif --}}
                                                            </td> -->
                                                        </tr>
                                                        @php
                                                            $no++;
                                                        @endphp
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
            $('#search').on('keyup', function() {
                let searchTerm = $(this).val();

                $.ajax({
                    url: '{{ route('item.itemqty') }}',
                    method: 'GET',
                    data: {
                        search: searchTerm
                    },
                    success: function(response) {
                        // $('#example1 tbody').html(rows);
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

        // $(document).ready(function() {
        //     new DataTable('#example1', {
        //         "lengthChange": false,
        //         "paging": false,
        //         "pageLength": 100,
        //         "searching": false,
        //         "language": {
        //             "info": "",
        //             "infoEmpty": "",
        //             "infoFiltered": ""
        //         }
        //     });
        // });
    </script>
</body>

</html>
