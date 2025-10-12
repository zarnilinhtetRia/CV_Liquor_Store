<!DOCTYPE html>
<HTML>

<head>

    <link rel="stylesheet" href="{{ asset('locallink/css/bootstrap.min.css') }}">
    <script src="{{ asset('locallink/js/ajax_jquery.js') }}"></script>
    <script src="{{ asset('locallink/js/typehead.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <script src="{{ asset('locallink/js/moment.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>POS</title>
    {{-- <style>
        input {
            position: relative;
            width: 150px;
            height: 40px;
            color: white;
        }

        input:before {
            position: absolute;
            top: 6px;
            left: 6px;
            content: attr(data-date);
            display: inline-block;
            color: black;
        }

        input::-webkit-datetime-edit,
        input::-webkit-inner-spin-button,
        input::-webkit-clear-button {
            display: none;
        }

        input::-webkit-calendar-picker-indicator {
            position: absolute;
            top: 6px;
            right: 0;
            color: black;
            opacity: 1;
        }
    </style> --}}
    <style>
        .alert {
            border-radius: 10px;
            padding: 15px 20px;
            font-size: 16px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .alert .close {
            font-size: 18px;
            color: #000;
            opacity: 0.7;
        }

        .alert .close:hover {
            opacity: 1;
        }
    </style>
</head>


<body>
    <!-- As a link -->
    <nav class="navbar navbar-light justify-content-between">
        <h1 class="mx-4">POS</h1>
        @if (session('success'))
            <h4 class="text-success">{{ session('success') }}</h4>
        @endif
        @if (session('delete'))
            <h4 class="text-danger">{{ session('delete') }}</h4>
        @endif
        <form class="form-inline">

            {{-- Permission Php --}}
            @php
                $choosePermission = [];
                if (auth()->user()->permission) {
                    $decodedPermissions = json_decode(auth()->user()->permission, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $choosePermission = $decodedPermissions;
                    }
                }
            @endphp
            {{-- End Php --}}

            <div class="row">
                {{-- <div class="col">
                    <a href="{{ url('sale_return_register') }}" type="button" class="mr-auto btn btn-primary ">
                        Sale Return</a>
                </div> --}}
                @if (in_array('Suspend', $choosePermission) || auth()->user()->is_admin == '1')
                    <div class="col">
                        <button type="button" class="mx-2 btn btn-primary" data-toggle="modal" data-target="#modal-xl">
                            Suspended
                        </button>
                    </div>
                @endif
            </div>
        </form>
    </nav>

    <div class="container-fluid " id="content">
        <hr>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert"
                style="background-color: #d4edda; border: 1px solid #c3e6cb;">
                <i class="fas fa-check-circle mr-2" style="font-size: 20px; color: #28a745;"></i>
                <strong>{{ session('success') }}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"
                    style="outline: none; border: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center" role="alert"
                style="background-color: #fff3cd; border: 1px solid #ffeeba;">
                <i class="fas fa-exclamation-circle mr-2" style="font-size: 20px; color: #ffc107;"></i>
                <strong>{{ session('error') }}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"
                    style="outline: none; border: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif


        @if ($errors->has('phno'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong> {{ $errors->first('phno') }}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif


        <div class="modal fade" id="modal-xl">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Suspended List</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Your HTML code -->
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>POS No.</th>
                                        <th>Customer Name</th>
                                        <th>Total</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = ' 1';
                                    @endphp
                                    @foreach ($suspends as $suspend)
                                        <tr>
                                            <td>{{ $no }}</td>
                                            <td>{{ $suspend->invoice_no }}</td>
                                            <td>{{ $suspend->customer_name ?? 'N/A' }}</td>
                                            <td>{{ $suspend->total }}</td>
                                            <td>{{ date('Y-m-d', strtotime($suspend->created_at)) }}
                                            </td>
                                            <td>
                                                <a href="{{ url('suspend_delete', $suspend->id) }}"
                                                    class="btn btn-danger"
                                                    onclick="return confirm('Are you sure you want to delete this Suspend ?')">
                                                    Delete</a>
                                                <a href="{{ url('invoice_edit', $suspend->id) }}"
                                                    class="btn btn-primary">Unsuspend</a>
                                            </td>
                                        </tr>
                                        @php
                                            $no++;
                                        @endphp
                                    @endforeach
                                </tbody>
                            </table>

                        </div>

                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="modal-lg">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #007bff26 !important;">
                        <h4 class="modal-title"> Register New Customer</h4>
                        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button> -->
                    </div>
                    <div class="modal-body" style="background-color: #007bff26 !important;">
                        <form action="{{ url('customer_register') }}" method="POST">
                            @csrf
                            <div class="card-body">

                                {{-- <div class="form-group">
                                    <label for="">Customer Id</label>
                                    <input type="text" class="form-control" id=""
                                        placeholder="Enter Customer id" autofocus name="customer_id">
                                </div> --}}


                                <div class="form-group mt-3">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="" placeholder="Enter Name"
                                        required autofocus name="name">
                                </div>


                                <div class="form-group mt-3">
                                    <label for="phno">Phone Number</label>
                                    <input type="text" class="form-control" id=""
                                        placeholder="Enter Phone Number" name="phno">
                                </div>


                                <div class="form-group mt-3">
                                    <label for="email">Email</label>
                                    <input type="text" class="form-control" id=""
                                        placeholder="Enter Customer Email" name="email">
                                </div>

                                <div class="form-group mt-3">
                                    <label for="crc">Customer Type </label>
                                    <select name="type" id="" class="form-control">
                                        <option selected disabled>Select Customer Type</option>
                                        <option value="Retail">Retail</option>
                                        <option value="Whole Sale">Whole Sale</option>
                                    </select>
                                </div>

                                @if (auth()->user()->is_admin == '1')
                                    <div class="form-group">
                                        <label for="branch">Location<span class="text-danger">*</span></label>

                                        <select name="branch" id="" class="form-control" required>
                                            <option value="" selected disabled>Select Location
                                            </option>
                                            @foreach ($warehouses as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <div class="form-group">
                                        <label for="branch">Location<span class="text-danger">*</span></label>

                                        <select name="branch" id="" class="form-control" required>
                                            @php
                                                $userPermissions = auth()->user()->level
                                                    ? json_decode(auth()->user()->level)
                                                    : [];
                                            @endphp
                                            <option value="" selected disabled>Select Location
                                            </option>
                                            @foreach ($warehouses as $branch)
                                                @if (in_array($branch->id, $userPermissions))
                                                    <option value="{{ $branch->id }}">
                                                        {{ $branch->name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                @endif




                                <div class="form-group mt-3">
                                    <label for="address">Address</label>
                                    <input type="text" class="form-control" id="phone number"
                                        placeholder="Enter Address" name="address">
                                </div>
                            </div>



                    </div>
                    <div class="modal-footer justify-content-between" style="background-color: #007bff26 !important;">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save </button>
                    </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>


        <form method="post" id="myForm" action="{{ url('invoice_register') }}" enctype="multipart/form-data">
            @csrf

            <div class="mx-3 row ">

                <div class="mt-4 row">
                    <div class="col-md-3">
                        <label for="invoice_no" style="font-weight:bolder">POS Number</label>
                        <input type="text" id="invoice_no" class="form-control" name="invoice_no"
                            value="{{ $invoice_no }}" readonly>
                    </div>
                    {{-- <div class="col-md-3">
                        <label for="branch_invoice_no" style="font-weight:bolder">Branch POS Number</label>
                        <input type="text" id="branch_invoice_no" class="form-control" name="branch_invoice_no"
                            value="" readonly>
                    </div> --}}
                    <div class="col-md-3">
                        <label for="invoice_date" style="font-weight:bolder">Date</label>
                        <input type="date" name="invoice_date" class="form-control" max="<?php echo date('Y-m-d'); ?>"
                            value="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="col-md-3" style="">
                        <label for="overdue_date" class=" caption"
                            style="font-weight:bolder">{{ trans('Payment OverDue Date') }}</label>
                        <input type="date" name="overdue_date" id="overdue_date" class="form-control round"
                            autocomplete="off" min="<?= date('Y-m-d') ?>">
                    </div>
                    @if (auth()->user()->is_admin == '1')
                        <div class="frmSearch col-md-3">
                            <div class="frmSearch col-sm-12">
                                <span style="font-weight:bolder">
                                    <label for="cst" class="caption">{{ trans('Location') }}&nbsp;</label>
                                </span>
                                <select name="branch" id="location" class="mb-4 form-control location" required>

                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">
                                            {{ $warehouse->name }}
                                        </option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                    @else
                        <div class="frmSearch col-md-3">
                            <div class="frmSearch col-sm-12">
                                <span style="font-weight:bolder">
                                    <label for="cst" class="caption">{{ trans('Location') }}&nbsp;</label>
                                </span>
                                <select name="branch" id="location" class="mb-4 form-control location" required>

                                    @php
                                        $userPermissions = auth()->user()->level
                                            ? json_decode(auth()->user()->level)
                                            : [];
                                    @endphp

                                    @foreach ($warehouses as $branch)
                                        @if (in_array($branch->id, $userPermissions))
                                            <option value="{{ $branch->id }}">
                                                {{ $branch->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>

                            </div>
                        </div>


                    @endif
                    <div class="col-md-3 mt-2" style="display: none">
                        <label for="cst" class="caption" style="font-weight:bolder">Register
                            Mode</label>
                        <select name="balance_due" id="balance_due" class="mb-4 form-control balance_due" required>

                            <option value="Invoice">Invoice</option>


                        </select>
                    </div>

                    <div class="mt-4 frmSearch col-md-3" hidden> <label for="payment"
                            style="font-weight:bolder">{{ trans('Sale Price Category') }}
                        </label>
                        <select class="mb-4 form-control round " aria-label="Default select example"
                            name="sale_price_category" id="sale_price_category" required>

                            <option value="Default" selected>Default</option>
                            <option value="Whole Sale">Whole Sale</option>
                            <option value="Retail">Retail</option>
                        </select>
                    </div>
                    {{-- <div class="col-md-3 ">
                        <label for="payment_method" style="font-weight:bolder">{{ trans('Payment Methods') }}</label>
                        <select class="mb-4 form-control round" aria-label="Default select example"
                            name="payment_method" required>

                            <option value="Cash">Cash</option>
                            <option value="K Pay">K Pay</option>
                            <option value="Wave">Wave</option>
                            <option value="Others">Others</option>
                        </select>
                    </div> --}}

                    <input type="hidden" name="quote_category" id="quote_category" value="POS">
                </div>
                <div class="content-wrapper">
                    <div class="content-body">
                        <div class="">
                            <div class="card-content">

                                <div class="card-body">

                                    <div class="row">
                                        <div class="col-sm-12 cmp-pnl">
                                            <div id="customerpanel" class="inner-cmp-pnl">
                                                <div class="mt-3 form-group row">
                                                    <div class="frmSearch col-sm-3">
                                                        <div class="frmSearch col-sm-12">
                                                            <span style="font-weight:bolder">
                                                                <label for="cst"
                                                                    class="caption">{{ trans('Search  Customer Name & Phone No.') }}</label>
                                                            </span>
                                                            <div class="form-group d-flex">
                                                                <input type="text" id="customer" name="customer"
                                                                    class="mr-2 form-control round" autocomplete="off"
                                                                    placeholder="Search.....">
                                                                &nbsp;&nbsp;&nbsp; <button type="submit"
                                                                    class="btn btn-primary"
                                                                    id="customer_search">Add</button>
                                                            </div>

                                                            <div id="customer-box-result"></div>
                                                        </div>
                                                    </div>


                                                    <div class="col-sm-2 mt-4">
                                                        <button type="button" data-toggle="modal"
                                                            data-target="#modal-lg"
                                                            class="btn btn-primary text-white">Customer
                                                            Register</button>
                                                    </div>

                                                    <input type="hidden" id="service_id" name="service_id"
                                                        value="0">


                                                    <input type="hidden" name="manager_type"
                                                        value="{{ Auth::user()->type }}">


                                                </div>
                                            </div>
                                            <div class="col-sm-3 cmp-pnl">

                                                <div class="inner-cmp-pnl">

                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-4 row table-responsive" style="margin-top:1vh;">
                                            <table class="table table-bordered">
                                                <thead
                                                    style="background: radial-gradient(circle, rgb(255, 100, 100), rgb(52, 52, 52));
color:white;">
                                                    <tr class="item_header bg-gradient-directional-blue white">
                                                        <th class="text-center" style="width: 13%;">
                                                            {{ trans('Name') }}
                                                        </th>
                                                        <th class="text-center" style="width: 14%;">
                                                            {{ trans('Phone Number') }}
                                                        </th>
                                                        <th class="text-center" style="width: 18%;">
                                                            {{ trans('Customer Type') }}
                                                        </th>
                                                        <th class="text-center" style="width: 13%;">
                                                            {{ trans('Address') }}
                                                        </th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="item_header bg-gradient-directional-blue white">
                                                        <td class="text-center"><input type='text'
                                                                name='customer_name' id="name"
                                                                class="form-control"></td>
                                                        <input type='hidden' name='customer_id' id="customer_id"
                                                            class="form-control">
                                                        <input type='hidden' name='status' id="status"
                                                            class="form-control" value="pos">
                                                        <td class="text-center"><input type='text' name='phno'
                                                                id="phone_no" class="form-control"></td>
                                                        <td class="text-center"><input type='text' name='type'
                                                                id="type" class="form-control"></td>
                                                        <td class="text-center"><input type='text' name='address'
                                                                class="form-control" id="address"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <hr>


                                        <div class="mt-4 frmSearch col-md-3">
                                            <div class="frmSearch col-sm-12">
                                                <span style="font-weight:bolder">
                                                    <label for="cst"
                                                        class="caption">{{ trans('Search Item/Model Name ') }}&nbsp;</label>
                                                </span>
                                                <input type="text" class="form-control productname typeahead"
                                                    name="itemname" id='productname' autocomplete="off"
                                                    placeholder="Search Item Name ">
                                                <input type="hidden"
                                                    class="form-control result_product_name typeahead result_product_name"
                                                    name="result_product_name[]" id="result_product_name-0"
                                                    autocomplete="off">
                                                <input type="hidden"
                                                    class="form-control result_descriptions typeahead descriptions"
                                                    name="result_descriptions[]" id="result_descriptions-0"
                                                    autocomplete="off">
                                                <input type="hidden"
                                                    class="form-control result_product_code typeahead result_product_code"
                                                    name="result_product_code[]" id="result_product_code-0"
                                                    autocomplete="off">


                                                <div id="customer-box-result"></div>
                                            </div>


                                        </div>

                                        <div class="mt-4 frmSearch col-md-3">
                                            <div class="frmSearch col-sm-12">
                                                <span style="font-weight:bolder">
                                                    <label for="cst"
                                                        class="caption">{{ trans('Search Item Barcode') }}&nbsp;</label>
                                                </span>
                                                <input type="text"
                                                    class="form-control productname typeahead barcode-input"
                                                    name="barcode" id="barcode" autocomplete="off"
                                                    placeholder="Search Item Barcode ">
                                                <div id="customer-box-result"></div>
                                            </div>



                                        </div>

                                        {{-- <div class="mt-4 frmSearch col-md-3">
                                            <label for="payment"
                                                style="font-weight:bolder">{{ trans('Sale Price Category') }}
                                            </label>
                                            <select class="mb-4 form-control round "
                                                aria-label="Default select example" name="sale_price_category"
                                                id="sale_price_category" required>

                                                <option value="Default" selected>Default</option>
                                                <option value="Whole Sale">Whole Sale</option>
                                                <option value="Retail">Retail</option>
                                            </select>
                                        </div> --}}

                                        <div class="row table-responsive " style="margin-top:1vh;">
                                            <!-- <table class="table-responsive tfr my_stripe"> -->
                                            <table class="table table-bordered">
                                                <!-- Fixed width -->
                                                <thead
                                                    style="background: radial-gradient(circle, rgb(255, 100, 100), rgb(52, 52, 52));
;color:white;">
                                                    <tr class="item_header bg-gradient-directional-blue white"
                                                        style="margin-bottom:10px;">
                                                        <th width="3%" class="text-center">
                                                            {{ trans('No') }}
                                                        </th>
                                                        <th width="10%" class="text-center wholesale_th">
                                                            {{ trans('Price Category') }}
                                                        </th>
                                                        <th width="23%" class="text-center">
                                                            {{ trans('Item / Model') }}
                                                        </th>
                                                        <th width="21%" class="text-center">
                                                            {{ trans('Descriptions') }}
                                                        </th>
                                                        <th width="6%" class="text-center">
                                                            {{ trans('Qty') }}
                                                        </th>
                                                        <th width="7%" class="text-center">{{ trans('Unit') }}
                                                        </th>
                                                        <!-- <th width="9%" class="text-center wholesale_th">
                                                            {{ trans('လက်ကားစျေး') }}
                                                        </th> -->
                                                        <th width="7%" class="text-center wholesale_th">
                                                            {{ trans('FOC') }}
                                                        </th>
                                                        <th width="8%" class="text-center retail_th">
                                                            {{ trans('Price') }}
                                                        </th>
                                                        <th width="8%" class="text-center">
                                                            {{ trans('Discounts') }}
                                                        </th>
                                                        <th width="" class="text-center"
                                                            style="display: none;">
                                                            {{ trans('Expiry') }}
                                                        </th>
                                                        <th width="9%" class="text-center">
                                                            {{ trans('Amount') }}
                                                            ({{ config('currency.symbol') }})
                                                        </th>
                                                    </tr>
                                                </thead>

                                                <tbody id="showitem123">
                                                    <tr>


                                                        <td class="text-center" id="count">1</td>
                                                        <td class="price_td">
                                                            <select
                                                                class="form-control round auto_calculate price_category"
                                                                aria-label="Default select example"
                                                                name="price_category[]" id="price_category-0">
                                                                <option value="retail_unit_price" selected>Retail Unit
                                                                    Price (1)</option>
                                                                <option value="retail_set_price">Retail Unit Price (2)
                                                                </option>
                                                                <option value="promotion_retail_unit">Retail Unit Price
                                                                    (3)</option>
                                                                <option value="promotion_retail_set">Retail Unit Price
                                                                    (4)</option>
                                                            </select>
                                                        </td>

                                                        <td>

                                                            {{-- <div class="row align-items-center"> --}}
                                                            {{-- <div class="col-auto">
                                                                    <input type="checkbox" id="sell_status-0"
                                                                        value="1"
                                                                        class="form-check-input sell_status" />

                                                                    <input type="hidden" name="sell_status[]"
                                                                        id="sell_status_input-0"
                                                                        class="form-control sell_status_input"
                                                                        value="0" />
                                                                </div> --}}

                                                            {{-- <div class="col"> --}}
                                                            <input type="text"
                                                                class="form-control productname typeahead barcode auto_calculate"
                                                                name="barcode[]" value="{{ old('barcode') }}"
                                                                id="barcode-0" autocomplete="off" hidden>
                                                            <input type="text"
                                                                class="form-control productname typeahead item_name auto_calculate"
                                                                name="part_number[]" value="{{ old('part_number') }}"
                                                                placeholder="{{ trans('Enter Part Number') }}"
                                                                id="item_name-0" autocomplete="off">
                                                            <input type="hidden"
                                                                class="form-control result_item_name typeahead"
                                                                name="result_item_name[]" id="result_item_name-0"
                                                                autocomplete="off">
                                                            <input type="hidden"
                                                                class="form-control result_descriptions typeahead descriptions"
                                                                name="result_descriptions[]"
                                                                id="result_descriptions-0" autocomplete="off">
                                                            <input type="hidden"
                                                                class="form-control result_product_code typeahead "
                                                                name="result_product_code[]"
                                                                id="result_product_code-0" autocomplete="off">
                                                            <input type="hidden"
                                                                class="form-control result_id typeahead result_id"
                                                                name="result_id[]" id="result_id-0"
                                                                autocomplete="off">

                                                            <input type="hidden"
                                                                class="form-control item_id typeahead"
                                                                name="item_id[]" id="item_id-0" autocomplete="off">

                                                            <input type="hidden" class="form-control model typeahead"
                                                                name="model[]" id="model-0" autocomplete="off">

                                                            <input type="hidden"
                                                                class="form-control colour typeahead" name="colour[]"
                                                                id="colour-0" autocomplete="off">
                                                            <input type="hidden" class="form-control size typeahead"
                                                                name="size[]" id="size-0" autocomplete="off">

                                                            <input type="hidden"
                                                                class="form-control seater typeahead" name="seater[]"
                                                                id="seater-0" autocomplete="off">

                                                            {{-- </div> --}}
                                                            {{-- </div> --}}

                                                        </td>

                                                        <td><input type="text"
                                                                class="form-control description typeahead"
                                                                value="{{ old('part_description') }}"
                                                                name="part_description[]"
                                                                placeholder="{{ trans('') }}" id='description-0'
                                                                autocomplete="off"></td>
                                                        <td><input type="text"
                                                                class="form-control req amnt auto_calculate"
                                                                name="product_qty[]" id="amount-0"
                                                                autocomplete="off" value="1"><input
                                                                type="hidden" id="alert-0" value=""
                                                                name="alert[]"></td>
                                                        <td><input type="text" class="form-control item_unit"
                                                                name="item_unit[]" id="item_unit-0">

                                                        </td>
                                                        <!-- <td class="wholesale_td"><input type="text"
                                                                class="form-control price" name="product_price[]"
                                                                id="price-0" autocomplete="off" value="0">
                                                        </td> -->
                                                        <td class="foc_td">
                                                            <select class="form-control round auto_calculate"
                                                                aria-label="Default select example" name="foc[]"
                                                                id="foc-0">
                                                                <option value="No" selected>No</option>
                                                                <option value="Yes">Yes</option>
                                                            </select>
                                                        </td>

                                                        <td class="retail_td">
                                                            <input type="text"
                                                                class="form-control retail_price auto_calculate"
                                                                name="retail_price[]" id="retail_price-0"
                                                                autocomplete="off" value="0" readonly>
                                                            <input type="hidden" name="retail_unit_price[]"
                                                                class="form-control retail_unit_price auto_calculate"
                                                                id="retail_unit_price-0" value="0">
                                                            <input type="hidden" name="retail_set_price[]"
                                                                class="form-control retail_set_price"
                                                                id="retail_set_price-0" value="0">
                                                            <input type="hidden" name="promotion_retail_unit[]"
                                                                class="form-control promotion_retail_unit"
                                                                id="promotion_retail_unit-0" value="0">
                                                            <input type="hidden" name="promotion_retail_set[]"
                                                                class="form-control promotion_retail_set"
                                                                id="promotion_retail_set-0" value="0">
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <input type="hidden" name="discount_category[]"
                                                                    value="percent" class="discount_category"
                                                                    id="discount_category-0">
                                                                <input type="hidden" name="discount_amt[]"
                                                                    value="0" class="discount_amt"
                                                                    id="discount_amt-0">
                                                                <input type="text"
                                                                    class="form-control vat auto_calculate"
                                                                    name="discount[]" id="vat-0" value="0"
                                                                    autocomplete="off">
                                                                <div class="input-group-append">
                                                                    <button type="button"
                                                                        class="btn btn-success discount_cat"
                                                                        data-val="0">
                                                                        %
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td style="display: none;"><input type="text"
                                                                class="form-control exp_date " name="exp_date[]"
                                                                id="exp_date-0" autocomplete="off">
                                                        </td>

                                                        <td style="display: none;"><input type="text"
                                                                class="form-control warehouse " name="warehouse[]"
                                                                id="warehouse-0" autocomplete="off">
                                                        </td>

                                                        <td style="text-align:center">
                                                            <span class='ttlText' id="foc-0"></span>
                                                            <span
                                                                class="currenty">{{ config('currency.symbol') }}</span>
                                                            <strong>
                                                                <span class='ttlText' id="result-0"></span>
                                                            </strong>
                                                        </td>


                                                        {{-- <td></td> --}}
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <table class="mt-3">
                                                <tbody id="showitem">




                                                    <tr style="display: table-row;">
                                                        <td></td>
                                                        <td colspan="">

                                                        </td>



                                                    </tr>


                                                    <tr class="last-item-row sub_c">
                                                        <td></td>
                                                        <td class="add-row">
                                                            <button type="button" class="btn btn-success"
                                                                id="addproduct"
                                                                style="margin-top:20px;margin-bottom:20px;display:none;">
                                                                <i class="fa fa-plus-square"></i>
                                                                {{ trans('Add row') }}
                                                            </button>
                                                            {{-- <button type="button" class="btn btn-primary"
                                                                id="calculate">
                                                                Calculate
                                                            </button> --}}

                                                            @if (in_array('Item', $choosePermission) || auth()->user()->is_admin == '1')
                                                                <a href="{{ URL('items') }}" target="_blank"
                                                                    id="item_search">
                                                                    <button type="button" class="btn btn-danger">
                                                                        <i class="fa fa-plus-square"></i> Item Search
                                                                    </button></a>
                                                            @endif
                                                        </td>
                                                        <td colspan="6"></td>
                                                        <br><br>

                                                    </tr>


                                                    <tr class="sub_c" style="display: table-row;">
                                                        <td colspan="2">
                                                            @if (isset($employees[0]))
                                                                {{ trans('general.employee') }}
                                                                <select name="user_id"
                                                                    class="selectpicker form-control">
                                                                    <option value="{{ $logged_in_user->id }}">
                                                                        {{ $logged_in_user->first_name }}
                                                                    </option>
                                                                    @foreach ($employees as $employee)
                                                                        <option value="{{ $employee->id }}">
                                                                            {{ $employee->first_name }}
                                                                            {{ $employee->last_name }}
                                                                        </option>
                                                                    @endforeach

                                                                </select>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr class="sub_c" style="display: table-row;">
                                                        <td>

                                                        </td>
                                                    </tr>
                                                    <tr style="display: none;" class="sub_c"
                                                        style="display: table-row;">
                                                        <td colspan="2">

                                                        </td>
                                                        <td colspan="3" align="right"><strong>Total Purchase
                                                            </strong>
                                                        </td>
                                                        <td align="left" colspan="2" class="col-md-4"><input
                                                                type="text" name="total_buy_price"
                                                                class="form-control" id="total_buy_price" readonly
                                                                style="background-color: #E9ECEF">

                                                        </td>

                                                    </tr>
                                                    <tr class="" hidden>
                                                        <td colspan="2"></td>
                                                        <td colspan="3" align="right"><strong>Choose Currency
                                                                Method</strong></td>
                                                        <td align="left" colspan="2" class="col-md-2">
                                                            <select name="currency_method" id="currency_method"
                                                                class="form-control">
                                                                <option value="MMK" selected>MMK</option>
                                                                <option value="USD">USD</option>
                                                                {{-- <option value="YUAN">YUAN</option> --}}
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr class="sub_c" style="display: table-row;">
                                                        <td colspan="2">

                                                        </td>
                                                        <td colspan="3" align="right"><strong>Sub Total
                                                            </strong>
                                                        </td>
                                                        <td align="left" colspan="2" class="col-md-4"><input
                                                                type="text" name="sub_total" class="form-control"
                                                                id="invoiceyoghtml" readonly
                                                                style="background-color: #E9ECEF">

                                                        </td>

                                                    </tr>
                                                    <tr class="sub_c" style="display: table-row;">
                                                        <td colspan="2">

                                                        </td>
                                                        <td colspan="3" align="right"><strong>Discount
                                                            </strong>
                                                        </td>
                                                        <td align="left" colspan="2" class="col-md-4"><input
                                                                type="text" name="total_discount"
                                                                class="form-control" id="total_discount">

                                                        </td>

                                                    </tr>

                                                    <tr class="sub_c" style="display: table-row;">
                                                        <td colspan="2">

                                                        </td>
                                                        <td colspan="3" align="right"><strong>Item Discount
                                                            </strong>
                                                        </td>
                                                        <td align="left" colspan="2" class="col-md-4"><input
                                                                type="text" class="form-control"
                                                                id="item_discount" readonly>

                                                        </td>

                                                    </tr>
                                                    <tr class="sub_c" style="display: table-row;">
                                                        <td colspan="2">

                                                        </td>
                                                        <td colspan="3" align="right"><strong>Total
                                                            </strong>
                                                        </td>
                                                        <td align="left" colspan="2" class="col-md-4"><input
                                                                type="text" name="total" class="form-control"
                                                                id="total_total" readonly
                                                                style="background-color: #E9ECEF">

                                                        </td>

                                                    </tr>

                                                <tbody id="trContainer">
                                                    <tr class="sub_c">
                                                        <td colspan="2"></td>
                                                        <td colspan="3" align="right"><strong>Payment
                                                                Method</strong></td>
                                                        <td align="left" colspan="1" class="col-md-2">
                                                            <input type="text" name="payment_amount[]"
                                                                class="form-control payment_amount"
                                                                id="payment_amount" required>
                                                        </td>
                                                        <td align="left" colspan="1"
                                                            class="col-md-2 payment_method">
                                                            <div class="input-group">
                                                                <select name="payment_method[]" id="payment_method-0"
                                                                    class="form-control" required>

                                                                </select>
                                                                <div class="input-group-append">
                                                                    <button type="button" id="addRow"
                                                                        class="btn btn-primary">
                                                                        <i class="fa-solid fa-plus"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </td>



                                                    </tr>
                                                </tbody>


                                                <tr class="sub_c">
                                                    <td colspan="2">

                                                    </td>
                                                    <td colspan="3" align="right"><strong>Cash
                                                        </strong>
                                                    </td>
                                                    <td align="left" colspan="2"><input type="text"
                                                            name="paid" class="form-control" id="paid"
                                                            onchange="paidFunction()">

                                                    </td>

                                                </tr>
                                                <tr class="sub_c">
                                                    <td colspan="2">

                                                    </td>
                                                    <td colspan="3" align="right"><strong>Change Due
                                                        </strong>
                                                    </td>
                                                    <td align="left" colspan="2"><input type="text"
                                                            name="balance" class="form-control" id="balance"
                                                            readonly="">

                                                    </td>
                                                </tr>

                                                <tr class="sub_c " style="display: table-row;">
                                                    <td colspan="12"> <label for="remark">Remark</label>
                                                        <textarea name="remark" id="remark" class="form-control" rows="2"></textarea>

                                                    </td>
                                                </tr>
                                                <tr class="sub_c " style="display: table-row;">


                                                    <td align="right" colspan="9">
                                                        @if (in_array('Suspend', $choosePermission) || auth()->user()->is_admin == '1')
                                                            <button id="suspend" class="mt-3 btn btn-primary"
                                                                type="submit">Suspend</button>
                                                        @endif
                                                        <button id="submitButton" class="mt-3 btn btn-primary"
                                                            type="submit">Save</button>


                                                        <a href="{{ url('pos') }}" type="submit"
                                                            class="mt-3 btn btn-danger">Cancel
                                                        </a>

                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>


                </div>
            </div>

        </form>
        <script>
            $(document).ready(function() {
                getAccount(0);
            });
            $('#location').on('change', function() {
                getAccount(0);
            });
            $('#balance_due').on('change', function() {
                getAccount(0);
            });

            function getAccount(payment_count) {
                var locationId = $("#location").val();
                var register_mode = $("#balance_due").val();

                console.log(register_mode);
                $('#payment_method-' + payment_count).html(
                    '<option value="">Loading...</option>');
                console.log(register_mode);
                if (locationId) {
                    $.ajax({

                        url: "{{ route('get_accounts_transaction') }}",
                        method: 'GET',
                        data: {
                            locationId: locationId,
                            register_mode: register_mode,
                        },
                        success: function(data) {
                            console.log(data);
                            $('#payment_method-' + payment_count)
                                .empty().append(
                                    '<option value="">Select Transaction</option>'
                                );
                            if (data && data.length > 0) {
                                $.each(data, function(index,
                                    transaction) {
                                    $('#payment_method-' +
                                            payment_count)
                                        .append(
                                            '<option value="' +
                                            transaction
                                            .id + '">' +
                                            transaction
                                            .transaction_name +
                                            '</option>');
                                });
                            } else {
                                $('#payment_method-' +
                                    payment_count).append(
                                    '<option value="">No Transaction available</option>'
                                );
                            }
                        },
                        error: function() {
                            $('#payment_method-' + payment_count)
                                .empty().append(
                                    '<option value="">Error loading transactions</option>'
                                );
                        }
                    });
                } else {
                    $('#payment_method-' + payment_count).empty().append(
                        '<option value="">Select Transaction</option>');
                }
                $('#location').on('change', function() {
                    getAccount(payment_count);
                });
            }
        </script>
        <script>
            $(document).ready(function() {
                // getBranchInvoiceNo();
                fetchInvoiceUpdates();
            });
            // $(document).on('change', '#location', function() {
            //     getBranchInvoiceNo();
            // });
            // $(document).on('change', '#balance_due', function() {
            //     getBranchInvoiceNo();
            // });

            // function getBranchInvoiceNo() {
            //     let warehouse_id = $('#location').val();


            //     if (warehouse_id) {
            //         $.ajax({
            //             type: 'POST',
            //             url: "{{ route('get_branch_pos_no') }}",
            //             data: {
            //                 _token: "{{ csrf_token() }}",
            //                 warehouse: warehouse_id,
            //                 // reg_mode: reg_mode
            //             },
            //             success: function(data) {
            //                 $('#branch_invoice_no').val(data.no);
            //             },
            //             error: function(xhr, status, error) {
            //                 console.error(xhr.responseText);
            //             }
            //         });
            //     } else {
            //         alert('Choose Warehouse Location!');
            //     }
            // }

            // setInterval(getBranchInvoiceNo, 3000);


            //invoice number update
            function fetchInvoiceUpdates() {
                fetch("{{ url('pos_no_updates') }}")
                    .then(response => response.json())
                    .then(data => {
                        const invoiceNoInput = document.getElementById("invoice_no");

                        if (data.invoice_no && invoiceNoInput.value !== data.invoice_no) {
                            invoiceNoInput.value = data.invoice_no;
                        }
                    })
                    .catch(error => console.error("Failed to fetch invoice updates:", error));
            }

            setInterval(fetchInvoiceUpdates, 3000);
        </script>
        <script>
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document).ready(function() {
                let count = 0;




                function initializeTypeahead() {
                    $('#productname').typeahead({
                        source: function(query, process) {

                            var Selectedlocation = $('#location').val();
                            return $.ajax({
                                url: "{{ route('autocomplete-part-code-invoice') }}",
                                method: 'POST',
                                data: {
                                    query: query,
                                    location: Selectedlocation,
                                },
                                dataType: 'json',
                                success: function(data) {
                                    console.log(data);
                                    const formattedData = data.map(function(item) {
                                        return {
                                            item_name: item.item_name || '',
                                            description: item.description || '',
                                            product_code: item.product_code || '',
                                            model: item.model || '',
                                            colour: item.colour || '',
                                            size: item.size || '',
                                            seater: item.seater || '',
                                            id: item.id,
                                            item_id: item.item_id,
                                            variation_desc: item.variation_desc,
                                            display: item.item_name + ' (' + (item
                                                    .product_code ? item
                                                    .product_code + ' - ' : '') +
                                                (item.model ? item.model + ' - ' :
                                                    '') +
                                                (item.colour ? item.colour : '') +
                                                (item.size ? ' - ' + item.size :
                                                    '') +
                                                (item.seater ? ' - ' + item.seater :
                                                    '') + ' )' +
                                                ' (' + (item.variation_desc ? item
                                                    .variation_desc : '') + ')',
                                        };
                                    });
                                    process(formattedData);
                                }
                            });

                        },
                        displayText: function(item) {
                            return item.display;
                        },
                        afterSelect: function(item) {
                            $('#result_descriptions-0').val(item.variation_desc);
                            $('#result_product_code-0').val(item.product_code);
                            $('#result_item_name-0').val(item.item_name);

                        },
                        autoSelect: true,
                        items: 'all'
                    });


                    //close for barcode suggestion
                    $('.barcode-input').typeahead({
                        source: function(query, process) {
                            var Selectedlocation = $('#location').val();
                            return $.ajax({
                                url: "{{ route('autocomplete.barcode-invoice') }}",
                                method: 'POST',
                                data: {
                                    query: query,
                                    location: Selectedlocation,
                                },
                                dataType: 'json',
                                success: function(data) {
                                    const formattedBarcodes = data.map(function(barcode) {
                                        return String(
                                            barcode);
                                    });
                                    process(formattedBarcodes);
                                },
                                error: function(error) {
                                    console.error(error);
                                }
                            });
                        },
                        displayText: function(item) {
                            return item;
                        },
                        afterSelect: function(item) {

                            $('.barcode_input').val(
                                item);
                        },
                        autoSelect: true
                    });
                }



                function updateItemName(item_name, row, description, product_code, cuz_name, productname) {
                    var $location = $('#location');
                    var $itemName0 = $("#item_name-0");
                    var $item_id = $("#item_id-0");
                    var $variation_id = $("#result_id-0");
                    var $result_item_name = $("#result_item_name-0");
                    var $description0 = $("#description-0");
                    var $expDate0 = $("#exp_date-0");
                    var $barcode0 = $("#barcode-0");
                    var $price0 = $("#price-0");
                    var $itemUnit0 = $("#item_unit-0");
                    var $retailPrice0 = $("#retail_price-0");


                    var $buyPrice0 = $("#buy_price-0");
                    var $warehouse0 = $("#warehouse-0");
                    var $productname = $("#productname");
                    var $productName0 = $("#result_product_name-0");
                    var $model = $("#model-0");
                    var $color = $("#colour-0");
                    var $size = $("#size-0");
                    var $seater = $("#seater-0");

                    var $barcode = $("#barcode");
                    var $amount0 = $("#amount-0");
                    var $retail_set_price = $("#retail_set_price-0");
                    var $promotion_retail_unit = $("#promotion_retail_unit-0");
                    var $promotion_retail_set = $("#promotion_retail_set-0");
                    var $retail_unit_price = $("#retail_unit_price-0");
                    var $promotion_retail_set = $("#promotion_retail_set-0");
                    // var $retail_unit_price = $("#retail_unit_price-0");
                    var $retail_price = $("#retail_price-0");

                    let price_category_val = $('#price_category-0').val();

                    var selectedLocation = $location.val();
                    var cuzName = $("#type").val();

                    function handleSuccess(data) {
                        // console.log(price_category_val);
                        var item = data['item'];
                        var variation = data['variations'][0];
                        let retailprice = parseFloat(variation.retail_price) || 0;
                        // console.log(retailprice);
                        let retail_set_price = parseFloat(variation.retail_set_price) || 0;
                        let promotion_retail_unit = parseFloat(variation.promotion_retail_unit) || 0;
                        let promotion_retail_set = parseFloat(variation.promotion_retail_set) || 0;

                        $result_item_name.val(item['item_name']);
                        // $itemName0.val(productname ? productname : item.item_name + ' (' + (variation.descriptions ||
                        //         'No description') + ' - ' +
                        //     (variation.product_code || 'No code') + ')');

                        $itemName0.val(productname ? productname : item.item_name + ' (' + (variation.product_code ?
                                variation
                                .product_code + ' - ' : '') + (variation.model ? variation.model + ' - ' :
                                '') + (variation.colour ? variation.colour : '') + (variation.size ? ' - ' +
                                variation.size : '') + (variation.seater ? ' - ' + variation.seater : '') + ' )' +
                            ' (' + (variation.descriptions ? variation.descriptions : '') + ')');
                        // console.log($("#item_name-0").val());
                        // $itemName0.val(productname);
                        $item_id.val(item['id']);
                        $variation_id.val(variation['id']);
                        $warehouse0.val(item['warehouse_id']);
                        $itemUnit0.val(variation['item_unit']);
                        $description0.val(variation['descriptions']);
                        $expDate0.val(variation['expired_date']);
                        $barcode0.val(variation['variations_barcode']);
                        $price0.val(variation['wholesale_price']);
                        $retailPrice0.val(variation['retail_price']);
                        $buyPrice0.val(variation['buy_price']);
                        $model.val(variation['model']);
                        $color.val(variation['colour']);
                        $seater.val(variation['seater']);
                        $size.val(variation['size']);

                        let new_retail = 0;
                        if (price_category_val == 'retail_set_price') {
                            new_retail = Math.round(retail_set_price);
                            $retailPrice0.val(new_retail);
                        } else if (price_category_val == 'promotion_retail_unit') {
                            new_retail = Math.round(promotion_retail_unit);
                            $retailPrice0.val(new_retail);
                        } else if (price_category_val == 'promotion_retail_set') {
                            new_retail = Math.round(promotion_retail_set);
                            $retailPrice0.val(new_retail);
                        } else {
                            new_retail = Math.round(retailprice);
                            $retailPrice0.val(new_retail);
                        }

                        $retail_set_price.val(variation['retail_set_price']);
                        $promotion_retail_unit.val(variation['promotion_retail_unit']);
                        $promotion_retail_set.val(variation['promotion_retail_set']);
                        $retail_unit_price.val(variation['retail_price']);
                        $retail_price.val(new_retail);
                        $productname.val('');
                        $barcode.val('');
                        autoCalculate();
                        if (parseFloat(variation.reorder_level_stock) >= parseFloat(variation.quantity)) {
                            alert(variation.quantity + " quantity!");
                        }
                    }

                    function handleError(xhr, status, error) {
                        console.error(xhr.responseText);
                    }

                    if ($itemName0.val() === "") {


                        var $barcodeInput = $('.barcode-input');
                        var item_barcode = $barcodeInput.val();

                        if (item_barcode.length > 1) {
                            $.ajax({
                                type: 'POST',
                                url: "{{ route('get.barcode.data-invoice') }}",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    barcode: item_name,
                                    location: selectedLocation,

                                },
                                success: handleSuccess,
                                error: handleError
                            });
                            // console.log(item_name);

                        } else {
                            $.ajax({
                                type: 'POST',
                                url: "{{ route('get.part.data-invoice') }}",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    itemname: item_name,
                                    location: selectedLocation,
                                    description: description,
                                    product_code: product_code
                                },

                                success: handleSuccess,
                                error: handleError
                            });

                        }


                    } else {

                        if (
                            ($itemName0.val() === $("#productname").val() ||
                                $("#barcode-0").val() === $("#barcode").val() &&
                                $("#barcode").val().length > 0)) {

                            var existingRow = $("#amount-0");
                            var currentQuantity = parseInt(existingRow.val());
                            existingRow.val(currentQuantity + 1);
                            $("#barcode").val('');
                            $("#productname").val('');
                            autoCalculate();

                        } else {

                            var $barcodeInput = $('.barcode-input');
                            var item_barcode = $barcodeInput.val();
                            if (item_barcode.length > 1) {
                                $.ajax({
                                    type: 'POST',
                                    url: "{{ route('get.barcode.data-invoice') }}",
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        barcode: item_name,
                                        location: selectedLocation,
                                    },
                                    success: function(data) {
                                        var barcode = $("#barcode")
                                            .val();
                                        if (parseFloat(data.reorder_level_stock) >= parseFloat(data
                                                .quantity)) {
                                            alert(data.quantity + " quantity!");
                                        }
                                        data.variations.forEach(function(variation) {
                                            addNewRow(data.item, variation, "",
                                                barcode);
                                        });
                                        $("#barcode").val('');
                                        $("#productname").val('');
                                    },
                                    error: handleError
                                });
                            } else {
                                $.ajax({
                                    type: 'POST',
                                    url: "{{ route('get.part.data-invoice') }}",
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        itemname: item_name,
                                        location: selectedLocation,
                                        description: description,
                                        product_code: product_code
                                    },
                                    success: function(data) {
                                        console.log(data);
                                        if (parseFloat(data.reorder_level_stock) >= parseFloat(data
                                                .quantity)) {
                                            alert(data.quantity + " quantity!");
                                        }
                                        data.variations.forEach(function(variation) {
                                            addNewRow(data.item, variation, productname);
                                        });

                                        $("#barcode").val();
                                        $("#productname").val();
                                    },
                                    error: handleError
                                });
                            }

                        }
                    }

                    $("#barcode").val();
                    $("#productname").val();
                }


                initializeTypeahead();




                function addNewRow(item, variation, productname, barcode) {
                    let cuz_name = $("#type").val();
                    if (barcode && barcode.length > 1) {
                        existingRow = $("#showitem123 input.barcode[value='" + barcode + "']").closest('tr');
                    } else {
                        existingRow = $("#showitem123 input.productname[value='" + productname + "']").closest('tr');
                    }


                    if (existingRow.length > 0) {
                        let qtyInput = existingRow.find('.req.amnt');
                        let currentQty = parseInt(qtyInput.val()) || 0;
                        qtyInput.val(currentQty + 1);
                        $("#barcode").val('');
                        $("#productname").val('');
                        autoCalculate();
                    } else {
                        count++;
                        let rowCount = $("#showitem123 tr").length;
                        var $barcodeInput = $('.barcode-input');
                        var item_barcode = $barcodeInput.val();

                        if (item_barcode.length > 1) {
                            displayName = item.item_name + ' (' + (variation.product_code ? variation
                                    .product_code + ' - ' : '') + (variation.model ? variation.model + ' - ' :
                                    '') + (variation.colour ? variation.colour : '') + (variation.size ? ' - ' +
                                    variation.size : '') + (variation.seater ? ' - ' + variation.seater : '') + ' )' +
                                ' (' + (variation.descriptions ? variation.descriptions : '') + ')';
                        } else {
                            var displayName = productname || "";
                        }
                        // console.log(variation);
                        let newRow = '<tr>' +
                            '<td class="text-center">' + (rowCount + 1) + '</td>' +
                            '<td class="price_td"><select class="form-control round auto_calculate price_category" aria-label="Default select example" name="price_category[]" id="price_category-' +
                            count +
                            '"> <option value="retail_unit_price" selected>Retail Unit Price (1)</option> <option value="retail_set_price">Retail Unit Price (2)</option> <option value="promotion_retail_unit">Retail Unit Price (3)</option> <option value="promotion_retail_set">Retail Unit Price (4)</option></select></td>' +
                            '<td style="display: none"><input type="text" class="form-control barcode typeahead" name="barcode[]" id="barcode-' +
                            count + '" autocomplete="off" value="' + variation['variations_barcode'] + '"></td>' +
                            '<td><input type="text" class="form-control productname typeahead" name="part_number[]" id="item_name-' +
                            count + '" autocomplete="off" value="' + displayName +
                            '"><input type="hidden" class="form-control result_item_name typeahead" name="result_item_name[]" id="result_item_name-' +
                            count + '" autocomplete="off" value="' + item['item_name'] +
                            '"><input type="hidden" class="form-control item_id typeahead" name="item_id[]" id="item_id-' +
                            count + '" autocomplete="off" value="' + item['id'] +
                            '"><input type="hidden" class="form-control result_id typeahead" name="result_id[]" id="result_id-' +
                            count + '" autocomplete="off" value="' + variation['id'] + '"></td>' +

                            '<input type="hidden" class="form-control model typeahead" name="model[]" id="model-' +
                            count + '" autocomplete="off" value="' + (variation['model'] ? variation['model'] : '') +
                            '">' +
                            '<input type="hidden" class="form-control colour typeahead" name="colour[]" id="colour-' +
                            count + '" autocomplete="off" value="' + (variation['colour'] ? variation['colour'] : '') +
                            '">' +
                            '<input type="hidden" class="form-control size typeahead" name="size[]" id="size-' +
                            count + '" autocomplete="off" value="' + (variation['size'] ? variation['size'] : '') +
                            '">' +
                            '<input type="hidden" class="form-control seater typeahead" name="seater[]" id="seater-' +
                            count + '" autocomplete="off" value="' + (variation['seater'] ? variation['seater'] : '') +
                            '">' +
                            '<td><input type="text" class="form-control description typeahead" name="part_description[]" id="description-' +
                            count + '" autocomplete="off" value="' + (variation['descriptions'] ? variation[
                                'descriptions'] : '') + '"></td>' +

                            '<td><input type="text" class="form-control req amnt auto_calculate" name="product_qty[]" id="amount-' +
                            count + '" autocomplete="off" value="1"></td>' +
                            '<td><input type="text" class="form-control unit" name="item_unit[]" id="item_unit-' +
                            count + '" autocomplete="off" value="' + (variation['item_unit'] ? variation['item_unit'] :
                                '') +
                            '" required></td>' +
                            ' <td class="foc_td"><select class="form-control round auto_calculate" aria-label="Default select example" name="foc[]" id="foc-' +
                            count + '"><option value="No" selected>No</option>' +
                            '<option value="Yes">Yes</option></select></td>' +

                            '<td class="retail_td"><input type="text" class="form-control retail_price auto_calculate" name="retail_price[]" value="' +
                            (variation['retail_price'] ? (variation[
                                'retail_price']) : '0') + '" id="retail_price-' +
                            count + '"   autocomplete="off" readonly>' +
                            '<input type="hidden" class="form-control retail_set_price" name="retail_set_price[]" id="retail_set_price-' +
                            count + '" value="' + (variation['retail_set_price'] ? (variation['retail_set_price']) :
                                '0') + '">' +
                            '<input type="hidden" class="form-control promotion_retail_unit" name="promotion_retail_unit[]" id="promotion_retail_unit-' +
                            count + '" value="' + (variation['promotion_retail_unit'] ? (variation[
                                'promotion_retail_unit']) : '0') + '">' +
                            '<input type="hidden" class="form-control promotion_retail_set" name="promotion_retail_set[]" id="promotion_retail_set-' +
                            count + '" value="' + (variation['promotion_retail_set'] ? (variation[
                                'promotion_retail_set']) : '0') + '">' +
                            '<input type="hidden" class="form-control retail_unit_price" name="retail_unit_price[]" id="retail_unit_price-' +
                            count + '" value="' + (variation['retail_price'] ? (variation[
                                'retail_price']) : '0') + '"></td>' +
                            '<td><div class="input-group"><input type="hidden" name="discount_category[]" value="percent" class="discount_category" id="discount_category-' +
                            count + '">' +
                            '<input type="hidden" name="discount_amt[]" value="0" class="discount_amt" id="discount_amt-' +
                            count + '">' +
                            '<input type="text" class="form-control vat auto_calculate"' +
                            'name="discount[]" id="vat-' + count +
                            '" value="0" autocomplete="off"><div class="input-group-append">' +
                            '<button type="button" class="btn btn-success discount_cat" data-val="' + count +
                            '">%</button></div></div></td>' +
                            '<td style="display: none;"><input type="text" class="form-control exp_date" name="exp_date[]" id="exp_date-' +
                            count + '" autocomplete="off" value="' + variation['expired_date'] + '"></td>' +
                            '<td style="display: none;"><input type="text" class="form-control warehouse" name="warehouse[]" id="warehouse-' +
                            count + '" autocomplete="off" value="' + item['warehouse_id'] + '"></td>' +
                            '<td style="text-align:center"><span class="currenty"></span><strong><span id="result-' +
                            count + '">0</span></strong></td>' +
                            '<td style="width: 5%"><button type="submit" class="btn btn-danger remove_item_btn" id="removebutton"><i class="fa-solid fa-minus"></i></button></td>' +
                            '</tr>';
                        $("#showitem123").append(newRow);
                        initializeTypeahead(count);
                        autoCalculate();
                    }
                }




                $(document).on('click', '.remove_item_btn', function(e) {
                    e.preventDefault();
                    let row_item = $(this).parent().parent();
                    $(row_item).remove();

                    // Update row numbers
                    $('#showitem123 tr').each(function(index) {
                        $(this).find('td:first').text(index + 1);
                    });

                    initializeTypeahead();
                    autoCalculate();
                });


                $(document).ready(function() {
                    function calculatePayment() {
                        let total = 0;
                        $('.payment_amount').each(function() {
                            let value = parseFloat($(this).val()) || 0;
                            total += value;
                        });
                        total = Math.round(total);
                        $('#paid').val(total);
                        paidFunction();
                    }

                    function paidFunction() {
                        let paid = parseFloat($('#paid').val()) || 0;
                        let total_p = parseFloat($('#total_total').val()) || 0;
                        let balance = total_p - paid;
                        balance = Math.round(balance);
                        $('#balance').val(balance);
                    }

                    $(document).on('input', '.payment_amount', function() {
                        calculatePayment();
                    });

                    $('#paid').on('input', function() {
                        paidFunction();
                    });

                    // Function to add a new row
                    let payment_count = 1;
                    $('#addRow').click(function() {
                        if ($('#trContainer tr.sub_c').length < 4) {
                            var newRow = `<tr class="sub_c">
                <td colspan="2"></td>
                <td colspan="3" align="right"><strong></strong></td>
                <td align="left" colspan="1" class="col-md-2">
                    <input type="text" name="payment_amount[]" class="form-control payment_amount">
                </td>
                <td align="left" colspan="1" class="col-md-2">
                   <div class="input-group">
            <select name="payment_method[]" class="form-control" id="payment_method-${payment_count}"  required>


            </select>
            <div class="input-group-append">
                <button type="button" class="removeRow btn btn-danger">
                    <i class="fa-solid fa-minus"></i>
                </button>
            </div>
        </div>
                </td>

            </tr>`;

                            $('#trContainer').append(newRow);
                            getAccount(payment_count)
                            payment_count++;
                        } else {
                            alert('You can only add a maximum of 4 payment rows.');
                        }
                    });
                    $(document).on('click', '.removeRow', function() {
                        $(this).closest('tr').remove();
                        calculatePayment();
                    });

                    calculatePayment();
                });

                $(document).on('keydown', '#barcode', function(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();

                        let item_name = $(this).val();
                        let row = $(this).closest('tr');
                        let cuz_name = $("#type").val();
                        updateItemName(item_name, row, "", "", cuz_name, "");
                    }
                });


                $(document).on('click', '.typeahead .dropdown-item', function(e) {
                    e.preventDefault();

                    if ($("#customer").val()) {} else {
                        const row = $(this).closest('tr');

                        const item_name = $('#result_item_name-0').val();
                        const productname = $('#productname').val();
                        const description = $('#result_descriptions-0').val();
                        const product_code = $('#result_product_code-0').val();
                        console.log(product_code);
                        // console.log(description);
                        // console.log(productname);
                        console.log(item_name);
                        let cuz_name = $("#type").val();
                        updateItemName(item_name, row, description, product_code, cuz_name, productname);
                        $('#productname').val('');
                    }
                });


                $(document).on('change', '.price_category', function(e) {
                    let row_val = $(this).closest('tr');
                    let p_category = $(this).val();
                    let r_set_price = row_val.find('.retail_set_price').val() || 0;
                    let promotion_r_unit = row_val.find('.promotion_retail_unit').val() || 0;
                    let promotion_r_set = row_val.find('.promotion_retail_set').val() || 0;
                    let r_price = row_val.find('.retail_unit_price').val() || 0;
                    row_val.find('.retail_price').val(r_price);

                    if (p_category == 'retail_set_price') {
                        let new_r = Math.round(r_set_price);
                        row_val.find('.retail_price').val(new_r);
                    } else if (p_category == 'promotion_retail_unit') {
                        let new_r = Math.round(promotion_r_unit);
                        row_val.find('.retail_price').val(new_r);
                    } else if (p_category == 'promotion_retail_set') {
                        let new_r = Math.round(promotion_r_set);
                        row_val.find('.retail_price').val(new_r);
                    } else {
                        let new_r = Math.round(r_price);
                        console.log(r_price);

                    }


                });


                // Initialize typeahead for the first row
                initializeTypeahead(count);

                function autoCalculate() {

                    let total = 0;
                    let total_purchase = 0;
                    let totalTax = 0;
                    let salePriceCategory = $('#sale_price_category').val();
                    let total_discount = 0;


                    let total_foc = 0;

                    console.log(count);
                    for (let i = 0; i < (count + 1); i++) {
                        var qty = parseInt($('#amount-' + i).val() || 0);
                        var item_name = $('#productname-' + i).val() || 0;
                        var sel = $('#focsel-' + i).val() || 0;
                        var foc = $('#foc-' + i).val() || 0;
                        var buy_price = parseFloat($('#buy_price-' + i).val() || 0);
                        let price;
                        let discount = 0;

                        if (salePriceCategory === 'Default') {
                            let cuz_name = $("#type").val();
                            price = cuz_name === "Whole Sale" ? parseFloat($('#price-' + i).val() || 0) :
                                parseFloat($('#retail_price-' + i).val() || 0);
                        } else if (salePriceCategory === 'Whole Sale') {
                            price = parseFloat($('#price-' + i).val() || 0);
                        } else if (salePriceCategory === 'Retail') {
                            price = parseFloat($('#retail_price-' + i).val() || 0);
                        }

                        let taxRate = parseFloat($('#vat-' + i).val() || 0);

                        if (!isNaN(taxRate) && taxRate > 0) {
                            let discount_cat = $('#discount_category-' + i).val();
                            console.log(discount_cat);

                            if (discount_cat == 'percent') {
                                discount = Math.round(price * qty * (taxRate / 100));
                            } else {
                                discount = taxRate;
                            }
                            // $("#result-" + i).text(total_amount - discount);
                            $('#discount_amt-' + i).val(discount);
                            // discount = taxRate;
                            $("#result-" + i).text((price * qty) - discount);
                        } else {
                            $("#result-" + i).text(price * qty);
                            $('#discount_amt-' + i).val(taxRate);
                        }
                        if (foc == 'Yes') {
                            total_foc += price * qty;
                            //substract item discount if foc is no
                        } else {
                            total_discount += discount;
                            totalTax += discount;
                        }
                        total += price * qty;
                        total -= total_foc;
                        total_purchase += buy_price * qty;
                        // total_discount += discount;
                        // totalTax += discount;

                    }
                    // console.log(total_dis);

                    let taxt = total
                    taxt = Math.ceil(taxt);
                    let total_total = total - total_discount;
                    let total_dis = parseFloat(total_total - $('#total_discount').val());

                    $("#invoiceyoghtml").val(total);
                    $("#item_discount").val(totalTax);
                    $("#total_buy_price").val(total_purchase);
                    $("#commercial_text").val(taxt);
                    $("#total").val(total_total);
                    $('#total_total').val(total_dis);



                }



                $(document).on("input", '.auto_calculate', function(e) {
                    e.preventDefault();
                    autoCalculate();
                });

                $(document).on("change", '.auto_calculate', function(e) {
                    e.preventDefault();
                    autoCalculate();
                });

                $(document).on("click", '.auto_calculate', function(e) {
                    e.preventDefault();
                    autoCalculate();
                });
                $(document).on('click', '.discount_cat', function() {
                    let id_val = $(this).data('val');
                    console.log(id_val);
                    if ($(this).text() == 'Ks') {
                        $('#discount_category-' + id_val).val('percent');
                        // $('#vat-'+id_val).data('val','percent');
                        $(this).text('%');
                        autoCalculate();
                    } else {
                        $(this).text('Ks');
                        // $('#vat-'+id_val).data('val','kyat');
                        $('#discount_category-' + id_val).val('kyat');
                        autoCalculate();
                    }
                });
            });


            function paidFunction() {

                let paid = document.getElementById("paid").value;
                let total_p = document.getElementById("total_total").value;
                let balance = total_p - paid;
                $("#balance").val(balance);
            }

            $(document).ready(function() {
                var path = "{{ route('customer_service_search') }}";
                $('#customer').typeahead({
                    source: function(query, process) {
                        var Selectedlocation = $('#location').val();

                        return $.get(path, {
                            query: query,
                            location: Selectedlocation,
                        }, function(data) {
                            // Format the data for Typeahead
                            var formattedData = [];
                            $.each(data, function(index, customer) {
                                // Check if the query matches the name or phone number
                                if (customer.name.toLowerCase().indexOf(query
                                        .toLowerCase()) !== -1) {
                                    // If the query matches the name, show the name
                                    formattedData.push(customer.name);
                                } else if (customer.phno.indexOf(query) !== -1) {
                                    // If the query matches the phone number, show the phone number
                                    formattedData.push(customer.phno);
                                }
                            });
                            return process(formattedData);
                        });
                    }
                });



                $(document).on('click', '#customer_search', function(e) {
                    e.preventDefault();
                    let serialNumber = $("#customer").val();

                    $.ajax({
                        type: 'POST',
                        url: "{{ route('customer_service_search_fill') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            model: serialNumber,
                            location: $('#location').val()
                        },
                        success: function(data) {
                            console.log(data);

                            if (data.customer) {
                                $("#name").val(data.customer.name);
                                $("#customer_id").val(data.customer.id);
                                $("#phone_no").val(data.customer.phno);
                                $("#type").val(data.customer
                                    .type);
                                $("#address").val(data.customer.address);
                                $("#customer").val('');
                            } else {
                                console.error("Customer not found");
                                $("#customer").val('');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                });
            });



            // $("input").on("change", function() {
            //     if (this.value && moment(this.value, "YYYY-MM-DD").isValid()) {
            //         this.setAttribute(
            //             "data-date",
            //             moment(this.value, "YYYY-MM-DD").format("DD/MM/YYYY")
            //         );
            //     } else {
            //         this.setAttribute("data-date", "dd/mm/yyyy");
            //     }
            // }).trigger("change");

            $(document).on("input", "#total_discount", function() {
                let subtotal = parseFloat($("#invoiceyoghtml").val()) || 0;
                let totalDiscount = parseFloat($("#total_discount").val()) || 0;
                let totalVAT = 0;
                $(".vat").each(function() {
                    totalVAT += parseFloat($(this).val()) || 0;
                });
                let total = subtotal - totalDiscount - totalVAT;
                $("#total_total").val(total);
            });


            document.getElementById("suspend").addEventListener("click", function() {
                setStatus("suspend");
            });

            document.getElementById("submitButton").addEventListener("click", function() {
                setStatus("pos");
            });

            function setStatus(status) {
                document.getElementById("status").value = status;
                document.getElementById("myForm").submit();
            }
        </script>


        <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
</body>

</HTML>
