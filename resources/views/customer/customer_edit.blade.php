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

                <li class="nav-item text-white">
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
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Customer Edit</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">Customer Edit
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>


                <div class="container-fluid mt-3">
                    <div class="row  justify-content-center d-flex">
                        <!-- left column -->
                        <div class="col-md-8">
                            <!-- general form elements -->
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title  " style="font-weight: bold;">Customer Edit</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body">
                                    <form action="{{ url('customer_update', $showCustomer->id) }}" method="POST">
                                        @csrf
                                        <div class="card-body">

                                            {{-- <div class="form-group">
                                                <label for="customer_id">Customer Id</label>
                                                <input type="text" class="form-control" id="customer_id" value="{{$showCustomer->customer_id}}"
                                                    placeholder="Enter Customer id" autofocus name="customer_id">
                                            </div> --}}

                                            <div class="form-group">
                                                <label for="name">Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="name" required
                                                    autofocus name="name" value="{{ $showCustomer->name }}">

                                            </div>

                                            <div class="form-group">
                                                <label for="phno">Phone Number</label>
                                                <input type="text" class="form-control" id="phone number"
                                                    name="phno" value="{{ $showCustomer->phno }}">

                                            </div>


                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <input type="text" class="form-control" id="email"
                                                    placeholder="Enter Email" name="email"
                                                    value="{{ $showCustomer->email }}">
                                            </div>

                                            <div class="form-group" style="display: none;">
                                                <label for="crc">Customer Type </label>
                                                <select name="type" id="type" class="form-control">
                                                    <option value="{{ $showCustomer->type }}">
                                                        {{ $showCustomer->type }}
                                                    </option>

                                                </select>
                                            </div>

                                            
                                            <div class="form-group">
                                                <label for="address">Address</label>
                                                <input type="text" class="form-control" id="phone number"
                                                    name="address" value="{{ $showCustomer->address }}">
                                            </div>


                                        </div>
                                        <div class="modal-footer justify-content-end">
                                            <a href="{{ url('customer') }}" class="btn btn-danger">Back</a>
                                            <button type="submit" class="btn btn-primary">Update </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>


        </section>

    </div>



    </div>


    @include('layouts.footer')
