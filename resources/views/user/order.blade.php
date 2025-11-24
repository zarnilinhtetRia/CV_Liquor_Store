@include('layouts.header')

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars text-white"></i></a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">


                <li class="nav-item">



                <li>

                    <div class="btn-group ">
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
                </li>
                </li>
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
                                <h1>User</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">User
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>
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

                <div class="container-fluid">

                    <!-- left column -->
                    <div class="row d-flex justify-content-end mr-2">
                        @if (in_array('User Register', $choosePermission) || auth()->user()->is_admin == '1')
                            <div class=" ">
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#modal-lg">
                                    <i class="fa-solid fa-circle-plus"></i> Add User
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="modal fade" id="modal-lg">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Add User </h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="{{ url('User_Register') }}" method="POST">
                                    @csrf
                                    <div class="modal-body">

                                        <div class="card-body">

                                            <div class="form-group">
                                                <label for="name">Name</label>
                                                <input type="text" class="form-control" id="name"
                                                    placeholder="Enter Name" required autofocus name="name">
                                            </div>

                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Email address</label>
                                                <input type="email" class="form-control" id="email"
                                                    placeholder="Enter email" name="email" required>
                                            </div>


                                            <div class="form-group">
                                                <label for="password">Password</label>
                                                <input type="password" class="form-control" id="password"
                                                    placeholder="Password" name="password" required
                                                    autocomplete="new-password">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-default"
                                            data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                    </div>
                                </form>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>

                    <div class="col-md-12 mt-3">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>{{ session('success') }}</strong>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        @if (session('delete'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>{{ session('delete') }}</strong>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <div class="card ">
                            <div class="card-header">
                                <h3 class="card-title">User List</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                        <th>ID</th>
                                        <th>Order ID</th>
                                        <th>Order Status</th>

                                        <th>Sender ID</th>
                                        <th>Receiver ID</th>
                                        <th>Order Photo 1</th>
                                        <th>Order Photo 2</th>
                                        <th>Order Photo 3</th>
                                        <th>Order Photo 4</th>
                                        <th>Order Photo 5</th>
                                        <th>Order Photo 6</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $no = '1';
                                        @endphp
                                        @foreach ($orders as $order)
                                            <tr>
                                                <td>{{ $order->id }}</td>

                                                <td>{{ $order->order_id }}</td>
                                                <td>{{ $order->order_status }}</td>

                                                <td>{{ $order->sender_id }}</td>
                                                <td>{{ $order->receiver_id }}</td>
                                                
                                                <td> @if($order->order_photo1)
                                                    <img src="{{ asset('images/orders/'.$order->order_photo1) }}" width="50" alt="Order Photo 1">
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($order->order_photo2)
                                                    <img src="{{ asset('images/orders/'.$order->order_photo2) }}" width="50" alt="Order Photo 2">
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($order->order_photo3)
                                                    <img src="{{ asset('images/orders/'.$order->order_photo3) }}" width="50" alt="Order Photo 3">
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($order->order_photo4)
                                                    <img src="{{ asset('images/orders/'.$order->order_photo4) }}" width="50" alt="Order Photo 4">
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($order->order_photo5)
                                                    <img src="{{ asset('images/orders/'.$order->order_photo5) }}" width="50" alt="Order Photo 5">
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($order->order_photo6)
                                                    <img src="{{ asset('images/orders/'.$order->order_photo6) }}" width="50" alt="Order Photo 6">
                                                    @endif
                                                </td>
                                            </tr>
                                            @php
                                                $no++;
                                            @endphp
                                        @endforeach
                                    </tbody>

                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    </div>
                </div>
        </div>

        </section>

    </div>



    </div>


    @include('layouts.footer')
