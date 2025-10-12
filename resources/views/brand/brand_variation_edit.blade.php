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
                                <h1>{{$variation->brand->name}} Variation Edit</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">{{$variation->brand->name}} Variation Edit
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
                                    <h4 class="cade-title text-center">{{$variation->brand->name}} Variation Edit</h4>
                                </div>
                                <div class="card-body">
                                    <form action="{{ url('brand_variation_update', $variation->id) }}" method="POST">
                                        @csrf
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="model">Model</label>
                                                <input type="text" class="form-control" id="model"
                                                    placeholder="Enter Brand Model" autofocus name="model" value="{{$variation->model}}">
                                            </div>

                                            <div class="form-group">
                                                <label for="colour">Colour</label>
                                                <input type="text" class="form-control" id="colour"
                                                    placeholder="Enter Brand Colour" autofocus name="colour"  value="{{$variation->colour}}">
                                            </div>

                                            <div class="form-group">
                                                <label for="size">Size</label>
                                                <input type="text" class="form-control" id="size"
                                                    placeholder="Enter Brand Size" autofocus name="size"  value="{{$variation->size}}">
                                            </div>

                                            <div class="form-group">
                                                <label for="seater">Seater</label>
                                                <input type="text" class="form-control" id="seater"
                                                    placeholder="Enter Brand Seater" autofocus name="seater" value="{{$variation->seater}}">
                                            </div>

                                            <button type="button" class="btn btn-default" onclick="window.history.back()">Back</button>
                                            <button type="submit" class="btn btn-primary">Update </button>
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
