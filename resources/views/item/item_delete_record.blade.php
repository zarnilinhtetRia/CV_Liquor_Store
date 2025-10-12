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
                                <h1>Product Delete Record</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>

                                    </li>
                                    <li class="breadcrumb-item">Product Delete Record</li>
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



                    <div class="container-fluid">

                        <!-- /.modal -->
                        <div class="mt-3 col-md-12">
                            <div class="bg-white">
                                {{-- @if (session('success'))
                                    <p style="color: green;">{{ session('success') }}</p>
                                @endif

                                <form action="{{ url('reset-deleted-at') }}" method="POST">
                                    @csrf
                                    <button type="submit">Migration</button>
                                </form> --}}

                                <div class="d-flex card-header">
                                    <h3 class="card-title">Product Delete Record List</h3>
                                </div>

                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-3">
                                        <!-- Delete Selected Button -->




                                    </div>
                                    <div class="table-responsive">
                                        <form id="delete-form" method="POST" action="{{ route('restore_items') }}">
                                            @csrf
                                            <button id="delete-selected" class="btn btn-success btn-sm shadow">
                                                <i class="bi bi-trash-fill"></i> Restore Selected
                                            </button>
                                            <table id="example1"
                                                class="table table-bordered table-striped items-table">
                                                <thead>
                                                    <tr>
                                                        <th><input type="checkbox" id="select-all"></th>
                                                        <th>No.</th>
                                                        <th>Product Name</th>
                                                        <th>Location</th>
                                                        {{-- <th>Category</th>
                                                    <th>Brand</th> --}}

                                                        <th>Product Register Type</th>
                                                        <th>Description</th>
                                                        <th style="width: 15%">Restore</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="items-data">
                                                    @foreach ($items as $key => $item)
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" class="row-checkbox"
                                                                    name="ids[]" value="{{ $item->id }}">
                                                            </td>
                                                            {{-- <td>{{ $item->id }}</td> --}}
                                                            <td>{{ $key + 1 }}</td>
                                                            <td>{{ $item->item_name }}</td>
                                                            <td>{{ $item->warehouse->name ?? 'N/A' }}</td>
                                                            {{-- <td>{{ $item->item_category }}</td>
                                                        <td>{{ $item->brand }}</td> --}}
                                                            <td>{{ $item->item_type }}</td>
                                                            <td>{{ $item->item_descriptions }}</td>
                                                            <td>
                                                                <a href="{{ url('restore_items', $item->id) }}"
                                                                    class="btn btn-success"><i
                                                                        class="fa-solid fa-trash-can-arrow-up"></i></a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </form>
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
    <!-- AdminLTE for demo purposes -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('select-all');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');

            selectAllCheckbox.addEventListener('change', function() {
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });
        });
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
