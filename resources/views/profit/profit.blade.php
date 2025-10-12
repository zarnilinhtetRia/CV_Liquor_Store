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
                    <a class="nav-link text-white " href="#">Date -
                        <?= $currentDate = date('d-m-y') ?></a>
                </li>


                <li class="nav-item ml-auto">
                    <a class="nav-link" href="#">


                    </a>
                </li>


            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">


                <div class="btn-group">
                    <button type="button" class="btn text-white dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        {{ auth()->user()->name }}
                    </button>
                    <div class="dropdown-menu ">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn  p-1 changelogout " style="width: 157px">
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
                                <h1>Monthly Net Profit</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">Monthly Net Profit
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <div class="my-5 container-fluid">
                    <div class="row">
                        <div class="col-md-10">
                            <form action="{{ url('search') }}" method="get">
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label for="start_date">Date From:</label>
                                        <input type="date" name="start_date" class="form-control" required>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="end_date">Date To:</label>
                                        <input type="date" name="end_date" class="form-control" required>
                                    </div>
                                    @if (auth()->user()->is_admin == '1')
                                        <div class="form-group col-md-3">
                                            <label for="branch">Location<span class="text-danger">*</span></label>

                                            <select name="branch" id="branch" class="form-control" required>

                                                @foreach ($branchs as $branch)
                                                    <option value="{{ $branch->id }}">{{ $branch->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @else
                                        <div class="form-group col-md-3">
                                            <label for="branch">Location<span class="text-danger">*</span></label>

                                            <select name="branch" id="branch" class="form-control" required>
                                                @php
                                                    $userPermissions = auth()->user()->level
                                                        ? json_decode(auth()->user()->level)
                                                        : [];
                                                @endphp
                                                <option value="" selected disabled>Select Location
                                                </option>
                                                @foreach ($branchs as $branch)
                                                    @if (in_array($branch->id, $userPermissions))
                                                        <option value="{{ $branch->id }}">
                                                            {{ $branch->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    <div class="col-md-2 form-group">
                                        <label for="">&nbsp;</label>
                                        <input type="submit" class="btn btn-primary form-control" value="Search"
                                            style="background-color: #218838">
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

                <div class="ml-2 container-fluid">
                    <div class="mt-3 col-md-12">
                        <div class="card ">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title">Monthly Net Profit Table</h3>
                                <div class="dropdown ml-auto mr-5">
                                    <div id="branchDropdown" class="dropdown ml-auto"
                                        style="display:inline-block; margin-left: 10px;">
                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            {{ $currentBranchName }}
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                                            @if (auth()->user()->is_admin == '1')
                                                <a href="{{ url('index') }}"
                                                    class="dropdown-item {{ request('branch') ? '' : 'active' }}">All
                                                    Invoices</a>
                                                @foreach ($branchs as $drop)
                                                    <a class="dropdown-item {{ request('branch') == $drop->id ? 'active' : '' }}"
                                                        href="{{ url('index?branch=' . $drop->id) }}">{{ $drop->name }}</a>
                                                @endforeach
                                            @else
                                                @php
                                                    $userPermissions = auth()->user()->level
                                                        ? json_decode(auth()->user()->level)
                                                        : [];
                                                @endphp
                                                @foreach ($branchs as $drop)
                                                    @if (in_array($drop->id, $userPermissions))
                                                        <a class="dropdown-item {{ request('branch') == $drop->id ? 'active' : '' }}"
                                                            href="{{ url('index?branch=' . $drop->id) }}">{{ $drop->name }}</a>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- /.card-header -->
                            <div class="card-body">
                                @php
                                    use Carbon\Carbon;
                                @endphp
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Date</th>
                                            <th>Stock</th>
                                            <th>Total Sale</th>
                                            <th>Total Purchase</th>
                                            <th>Total Expense</th>
                                            <th>Profit</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @foreach ($invoicesByDay as $date => $totalSale)
                                            @php
                                                $stock = $filteredStockSellsByDay->get($date, 0);
                                                $purchase = $purchasesByDay->get($date, 0);
                                                $expense = $expensesByDay->get($date, 0);
                                                $profit = $totalSale - ($purchase + $expense);
                                            @endphp

                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $date }}</td>
                                                <td>{{ number_format($stock) }}</td>
                                                <td>{{ number_format($totalSale) }}</td>
                                                <td>{{ number_format($purchase) }}</td>
                                                <td>{{ number_format($expense) }}</td>
                                                <td>{{ number_format($profit) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                    <tfoot style="background-color: #d7f0d9">
                                        <tr>
                                            <td colspan="2"><strong>Total</strong></td>
                                            <td>{{ number_format($filteredStockSellsByDay->sum()) }}</td>
                                            <td>{{ number_format($invoicesByDay->sum()) }}</td>
                                            <td>{{ number_format($purchasesByDay->sum()) }}</td>
                                            <td>{{ number_format($expensesByDay->sum()) }}</td>
                                            <td>{{ number_format($invoicesByDay->sum() - ($purchasesByDay->sum() + $expensesByDay->sum())) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>


                            </div>
                            <!-- /.card-body -->
                        </div>

                        <div class="card ">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title">Monthly Net Profit Table</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">

                                <table id="" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Stock</th>
                                            <th>Total Sale</th>
                                            <th>Total Purchase</th>
                                            <th>Total Expense</th>
                                            <th>Profit</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <tr>
                                            <td>
                                                @if ($invoices->isNotEmpty() && $invoices->first()->date)
                                                    {{ Carbon::parse($invoices->first()->date)->format('F / Y') }}
                                                @elseif($purchases->isNotEmpty() && $purchases->first()->date)
                                                    {{ Carbon::parse($purchases->first()->date)->format('F / Y') }}
                                                @elseif($expenses->isNotEmpty() && $expenses->first()->date)
                                                    {{ Carbon::parse($expenses->first()->date)->format('F / Y') }}
                                                @else
                                                    No Data
                                                @endif
                                            </td>
                                            <td>{{ number_format($totalStockDeposit) }}</td>
                                            <td>{{ number_format($totalSum) }}</td>
                                            <td>{{ number_format($totalPurchase) }}</td>
                                            <td>{{ number_format($totalExpense) }}</td>
                                            <td>{{ number_format($totalSum - ($totalPurchase + $totalExpense)) }}

                                            </td>

                                        </tr>


                                    </tbody>

                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    </div>
                </div>

            </section>

        </div>



    </div>


    @include('layouts.footer')
