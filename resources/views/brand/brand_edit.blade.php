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

                <li class="nav-item">
                    <a class="nav-link text-white" href="#">Date -
                        <?= $currentDate = date('d-m-y') ?></a>
                </li>


            </ul>

            <!-- Right navbar links -->
            <ul class="ml-auto navbar-nav">
                <div class="btn-group">
                    <button type="button" class=" text-white btn dropdown-toggle" data-toggle="dropdown"
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
                            <div class="col-sm-6 mt-3">
                                <h1>Brand Edit</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">Brand Edit
                                    </li>
                                </ol>
                            </div>

                        </div>
                    </div><!-- /.container-fluid -->
                </section>
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>{{ session('success') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('delete_success'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>{{ session('delete_success') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <section class="content container-fluid">

                    <div class="row">
                        <div class="col-6 offset-3">
                            <div class="card">
                                <div class="cade-header mt-2 mb-2">
                                    <h4 class="cade-title text-center">Brand Edit</h4>
                                </div>
                                <div class="card-body">
                                    <form action="{{ url('brand_update', $brand->id) }}" method="POST">
                                        @csrf
                                        <div class="card-body">

                                            <div class="form-group">
                                                <label for="name">Brand Name<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="name"
                                                    placeholder="Enter Brand Name" required autofocus name="name"
                                                    value="{{ $brand->name }}">
                                            </div>



                                            @if (auth()->user()->is_admin == '1')
                                                <div class="form-group">
                                                    <label for="warehouse_id">Location<span
                                                            class="text-danger">*</span></label>
                                                    <select name="warehouse_id" id="warehouse_id" class="form-control"
                                                        required>
                                                        @foreach ($branches as $branch)
                                                            <option value="{{ $branch->id }}"
                                                                {{ $branch->id == $brand->warehouse_id ? 'selected' : '' }}>
                                                                {{ $branch->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @else
                                                <div class="form-group">
                                                    <label for="warehouse_id">Location<span
                                                            class="text-danger">*</span></label>
                                                    <select name="warehouse_id" id="warehouse_id" class="form-control"
                                                        required>
                                                        @php
                                                            $userPermissions = auth()->user()->level
                                                                ? json_decode(auth()->user()->level)
                                                                : [];
                                                        @endphp
                                                        @foreach ($branches as $branch)
                                                            @if (in_array($branch->id, $userPermissions))
                                                                <option value="{{ $branch->id }}"
                                                                    {{ $branch->id == $brand->warehouse_id ? 'selected' : '' }}>
                                                                    {{ $branch->name }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif

                                            <div class="form-group">
                                                <label for="category">Brand<span class="text-danger">*</span></label>
                                                <select class="form-control" id="category" required autofocus
                                                    name="category_id">
                                                    @foreach ($categories as $key => $category)
                                                        <option value="{{ $category->id }}"
                                                            data-branch-id="{{ $category->branch }}"
                                                            @if ($category->id == $brand->category_id) selected @endif>
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="remark">Remark</label>
                                                <textarea class="form-control" id="remark" placeholder="Enter Remark" autofocus name="remark" rows="5">{{ $brand->remark }}</textarea>
                                            </div>

                                            <button type="button" class="btn btn-default"
                                                onclick="window.history.back()">Close</button>
                                            <button type="submit" class="btn btn-primary">Save </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                    {{-- Modal End --}}
                </section>


            </section>

        </div>



    </div>




    @include('layouts.footer')


    <script>
        $(document).ready(function() {
            const selectedBranchId = $('#warehouse_id').val();
            const itemCategoryDropdown = $('#category');

            itemCategoryDropdown.find('option').each(function() {
                const branchId = $(this).data('branch-id');

                if (!branchId || branchId == selectedBranchId) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });

            $(document).on('change', '#warehouse_id', function() {
                const newSelectedBranchId = $(this).val();
                itemCategoryDropdown.val('');

                itemCategoryDropdown.find('option').each(function() {
                    const branchId = $(this).data('branch-id');

                    if (!branchId || branchId == newSelectedBranchId) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        });
    </script>
