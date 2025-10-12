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

                <li class="nav-item ">
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
        <div class="content-wrapper ">
            <!-- Main content -->
            <section class="content ">
                <section class="content-header">
                    <div class="container-fluid ">
                        <div class="mb-2 row">
                            <div class="col-sm-6">
                                <h1>Dashboard</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">Dashboard
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>
            </section>
            @php
                $userPermissions = [];
                if (auth()->user()->permission) {
                    $decodedPermissions = json_decode(auth()->user()->permission, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $userPermissions = $decodedPermissions;
                    }
                }
            @endphp

            @if (in_array('Dashboard', $userPermissions) || auth()->user()->is_admin == '1')
                <section class="content">
                    <div class="container-fluid">
                        <div class="my-3 d-flex justify-content-end">
                            <div class="dropdown ml-auto mr-4">
                                <div id="branchDropdown" class="dropdown ml-auto"
                                    style="display:inline-block; margin-left: 5px;">
                                    <button class="btn btn-primary dropdown-toggle" type="button"
                                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        {{ $currentBranchName }}
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                                        @if (auth()->user()->is_admin == '1')
                                            <a href="{{ url('dashboard') }}" class="dropdown-item">All Location</a>
                                            @foreach ($branchs as $drop)
                                                <a class="dropdown-item"
                                                    href="{{ route('dashboard', $drop->id) }}">{{ $drop->name }}</a>
                                            @endforeach
                                        @else
                                            @php
                                                $userPermissions = auth()->user()->level
                                                    ? json_decode(auth()->user()->level)
                                                    : [];
                                            @endphp
                                            @foreach ($branchs as $drop)
                                                @if (in_array($drop->id, $userPermissions))
                                                    <a class="dropdown-item"
                                                        href="{{ route('dashboard', $drop->id) }}">{{ $drop->name }}</a>
                                                @endif
                                            @endforeach
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Small boxes (Stat box) -->
                        <div class="row container-fluid">

                            <div class="col ">
                                <!-- small box -->
                                <div class="small-box bg-primary">
                                    <div class="inner">
                                        <h3>{{ $invoiceCount }}</h3>
                                        <p>Invoice</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa-solid fa-file-invoice-dollar"></i>
                                    </div>
                                    {{-- <a href="{{ url('invoice') }}" class="small-box-footer">More info
                                        <i class="fas fa-arrow-circle-right"></i>
                                    </a> --}}
                                </div>
                            </div>
                            <!-- ./col -->

                            <!-- ./col -->
                            <div class="col ">
                                <!-- small box -->
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>{{ $quotationCount }}</h3>
                                        <p>Quotation</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa-solid fa-file-lines"></i>
                                    </div>
                                    {{-- <a href="{{ url('quotation') }}" class="small-box-footer">More info <i
                                            class="fas fa-arrow-circle-right"></i></a> --}}
                                </div>
                            </div>
                            <!-- ./col -->
                            <div class="col ">
                                <!-- small box -->
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3>{{ $purchaseOrderCount }}</h3>

                                        <p class="d-block d-sm-none">{{ Str::limit('Purchase Order', 8) }}</p>
                                        <p class="d-none d-sm-block">{{ 'Purchase Order' }}</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa-solid fa-store"></i>
                                    </div>
                                    {{-- <a href="{{ url('purchase_order_manage') }}" class="small-box-footer">More info <i
                                            class="fas fa-arrow-circle-right"></i></a> --}}
                                </div>
                            </div>

                        </div>


                    </div>
                </section>
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="mt-3 bg-white card-header col-12 col-md-6 col-lg-6">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h4 class="card-title text-center">
                                            <i class="fas fa-award me-2"></i>
                                            Top 8 Product for {{ \Carbon\Carbon::now()->format('F, Y') }}
                                        </h4>
                                    </div>
                                    <div class="card-body">
                                        <ol class="list-group list-group-numbered">
                                            @forelse ($warrantyClaimCounts as $claim)
                                                <li
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span style="font-size: 13px">
                                                        <strong>{{ $claim->product_name }}</strong>
                                                    </span>
                                                    <span style="font-size: 13px" class="fw-bold">
                                                        {{ $claim->count }}
                                                    </span>
                                                </li>
                                            @empty
                                                <li class="list-group-item text-center">
                                                    <em>No Product found for this month.</em>
                                                </li>
                                            @endforelse
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="mt-3 bg-white card-header col-12 col-md-6 col-lg-6">

                                <h3 class="mx-3 my-2 mt-3 card-title">
                                    <i class="mr-1 fa-regular fa-file-lines font-weight-bold"></i>
                                    Invoices
                                </h3>

                                <canvas id="invoiceChart" width="600" height="300"></canvas>

                            </div> --}}
                            <div class="mt-3 bg-white card-header col-12 col-md-6 col-lg-6">
                                <h3 class="mx-3 my-2 mt-3 card-title">
                                    <i class="mr-1 fa-regular fa-file-lines font-weight-bold"></i> Invoices
                                </h3>
                                <canvas id="invoiceChart" width="300" height="300"></canvas>
                            </div>

                            <!-- Include Chart.js -->

                            {{-- <div class="mt-3 bg-white  card-header col-12 col-md-6 col-lg-6">

                                <h3 class="mx-3 my-2 mt-3 card-title">
                                    <i class="mr-1 fa-regular fa-file-lines font-weight-bold"></i>
                                    POS
                                </h3>


                                <canvas id="posChart" width="600" height="300"></canvas>

                            </div> --}}

                            <div class="mt-3 bg-white  card-header col-12 col-md-12 col-lg-12">

                                <h3 class="mx-3 my-2 mt-3 card-title">
                                    <i class="mr-1 fa-regular fa-file-lines font-weight-bold"></i>
                                    Purchase Order
                                </h3>


                                <canvas id="poChart" width="600" height="200"></canvas>

                            </div>
                        </div>
                    </div>
                </section>
            @endif



</body>
<script src="{{ asset('backend/js/chart.js') }}"></script>
{{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}



<script>
    // var ctx = document.getElementById('invoiceChart').getContext('2d');
    // var chart = new Chart(ctx, {
    //     type: 'line',
    //     data: {
    //         labels: {!! json_encode(array_keys($chart)) !!},
    //         datasets: [{
    //             label: 'Invoices Registrations by Month',
    //             data: {!! json_encode(array_values($chart)) !!},
    //             backgroundColor: [
    //                 '#3FB159',
    //                 '#C164D5',
    //                 '#F50003',
    //                 '#1BC9B5',
    //                 '#D8D8D8',
    //                 '#E95721',
    //                 '#F6BB00',
    //             ]
    //         }]
    //     },
    //     options: {
    //         scales: {
    //             yAxes: [{
    //                 ticks: {
    //                     beginAtZero: false, // Start from 0
    //                     min: 0, // Set the minimum value for y-axis
    //                     max: 1000 // Set the maximum value for y-axis
    //                 }
    //             }]
    //         }
    //     }
    // });
    var ctx = document.getElementById('invoiceChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'doughnut', // Change to donut chart
        data: {
            labels: {!! json_encode(array_keys($chart)) !!}, // Labels (months)
            datasets: [{
                label: 'Invoices Registrations by Month',
                data: {!! json_encode(array_values($chart)) !!}, // Values for each month
                backgroundColor: [
                    '#3FB159',
                    '#C164D5',
                    '#F50003',
                    '#1BC9B5',
                    '#D8D8D8',
                    '#E95721',
                    '#F6BB00',
                ],
                hoverOffset: 10
            }]
        },
        options: {
            responsive: false, // Disable auto-resizing
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: "bottom",
                    labels: {
                        boxWidth: 100, // Smaller legend icons
                        font: {
                            size: 10
                        } // Smaller font size
                    }
                }
            }
        }
    });


    // var ctx = document.getElementById('posChart').getContext('2d');
    // var chart = new Chart(ctx, {
    //     type: 'line',
    //     data: {
    //         labels: {!! json_encode(array_keys($posChart)) !!},
    //         datasets: [{
    //             label: 'POS Registrations by Month',
    //             data: {!! json_encode(array_values($posChart)) !!},
    //             backgroundColor: [
    //                 '#3FB159',
    //                 '#C164D5',
    //                 '#F50003',
    //                 '#1BC9B5',
    //                 '#D8D8D8',
    //                 '#E95721',
    //                 '#F6BB00',
    //             ]
    //         }]
    //     },
    //     options: {
    //         scales: {
    //             yAxes: [{
    //                 ticks: {
    //                     beginAtZero: false, // Start from 0
    //                     min: 0, // Set the minimum value for y-axis
    //                     max: 1000 // Set the maximum value for y-axis
    //                 }
    //             }]
    //         }
    //     }
    // });


    var ctx = document.getElementById('poChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($poChart)) !!},
            datasets: [{
                label: 'PurchaseOrder Registrations by Month',
                data: {!! json_encode(array_values($poChart)) !!},
                backgroundColor: [
                    '#3FB159',
                    '#C164D5',
                    '#F50003',
                    '#1BC9B5',
                    '#D8D8D8',
                    '#E95721',
                    '#F6BB00',
                ]
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: false, // Start from 0
                        min: 0, // Set the minimum value for y-axis
                        max: 1000 // Set the maximum value for y-axis
                    }
                }]
            }
        }
    });
</script>




@include('layouts.footer')
