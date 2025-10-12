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
    <title>CodeVerse POS</title>
    @include('layouts.invoice_style')
    <style>
        .typeahead.dropdown-menu {
            max-height: 200px;
            overflow-y: auto;
        }
    </style>

</head>

<body>

    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mt-3">
            <h1>Purchase Order Edit</h1>

        </div>
        <form method="post" id="data_form" action=" {{ URL('purchase_order_update', $purchase_orders->id) }}"
            enctype="multipart/form-data">
            @csrf

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

                <div class="mt-3 col-md-3">
                    <label for="invocieno" class="mt-1 caption"
                        style="font-weight:bolder">{{ trans('Purchase Order Number') }} <span
                            class="text-danger">*</span></label>

                    <div class="input-group">
                        <div class="input-group-addon"><span class="icon-file-text-o" aria-hidden="true"></span>
                        </div>
                        <input type="text" id="invoice_number" name="po_number" class="form-control round"
                            value="{{ $purchase_orders->quote_no }}">


                    </div>
                </div>

                <div class="mt-3 col-md-3">
                    <label for="invociedate" class="mt-1 caption"
                        style="font-weight:bolder">{{ trans('Purchase Order Date') }}</label>

                    <div class="mb-2 input-group">
                        <div class="input-group-addon"><span class="icon-calendar4" aria-hidden="true"></span>
                        </div>
                        <!-- <input type="date" name="invoice_date" id="invoice_date" class="form-control round required" placeholder="{{ trans('invoicedate') }}" data-toggle="datepicker" autocomplete="off" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>"> -->
                        <input type="date" name="po_date" id="invoice_date" class="form-control round required"
                            autocomplete="off" max="<?= date('Y-m-d') ?> " value="{{ $purchase_orders->po_date }}"
                            required>
                    </div>
                </div>

                <div class="mt-3 col-md-3">
                    <label for="overdue" class="mt-1 caption"
                        style="font-weight:bolder">{{ trans('Payment OverDue Date') }}</label>
                    <div class="mb-2 input-group">
                        <div class="input-group-addon"><span class="icon-calendar4" aria-hidden="true"></span>
                        </div>

                        <input type="date" name="overdue_date" id="overdue_date" class="form-control round "
                            autocomplete="off" min="<?= date('Y-m-d') ?>" value="{{ $purchase_orders->overdue_date }}">
                    </div>
                </div>

                {{-- <div class="mt-3 col-md-3">
                    <label for="remark" style="font-weight:bolder">{{ trans('Payment Method') }}
                    </label>

                    <select class="mt-1 mb-3 form-control round" aria-label="Default select example"
                        name="payment_method" required>
                        <option value="Cash" {{ $purchase_orders->payment_method == 'Cash' ? 'selected' : '' }}>Cash
                        </option>
                        <option value="K Pay" {{ $purchase_orders->payment_method == 'K Pay' ? 'selected' : '' }}>K
                            Pay</option>
                        <option value="Wave" {{ $purchase_orders->payment_method == 'Wave' ? 'selected' : '' }}>Wave
                        </option>
                        <option value="Others" {{ $purchase_orders->payment_method == 'Others' ? 'selected' : '' }}>
                            Others</option>
                    </select>

                </div> --}}


                <div class="frmSearch col-md-3 col-sm-6 mt-3" style="display: none">
                    <div class="frmSearch col-sm-12">
                        <div class="frmSearch col-sm-12">
                            <span style="font-weight:bolder">
                                <label for="cst" class="caption mt-1"> Receiving Mode
                                </label>
                            </span>
                            <select name="balance_due" id="balance_due" class="mb-4 form-control balance_due">

                                <option value="PO" @if ($purchase_orders->balance_due == 'PO') selected @endif>PO</option>
                                {{-- <option value="Sale Return Invoice" @if ($purchase_orders->balance_due == 'Sale Return Invoice') selected @endif>
                                    Sale
                                    Return</option> --}}

                            </select>

                            <div id="customer-box-result"></div>
                        </div>


                    </div>

                </div>

                <div class="frmSearch mt-3 col-md-3 mb-3" id="supplier_box">
                    <span style="font-weight:bolder">
                        <label for="cst" class="caption mt-1">{{ trans('Supplier Name') }}</label>
                    </span>

                    <select name="supplier_id" id="supplier_id" class="form-control">
                        <option value="">Choose Supplier</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" data-branch="{{ $supplier->branch }}"
                                {{ $purchase_orders->supplier_id == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="frmSearch col-md-3 mb-3" id="">
                    <span style="font-weight:bolder">
                        <label for="internal_register_number"
                            class="caption">{{ trans('Internal Register Number') }}</label>
                    </span>
                    <input type="text" id="internal_register_number" name="internal_register_number"
                        class="form-control round" value="{{ $purchase_orders->internal_register_number }}">
                </div>

                <div class="frmSearch col-md-3 mb-3" id="">
                    <span style="font-weight:bolder">
                        <label for="supplier_register_number"
                            class="caption">{{ trans('Supplier Register Number') }}</label>
                    </span>
                    <input type="text" id="supplier_register_number" name="supplier_register_number"
                        class="form-control round" value="{{ $purchase_orders->supplier_register_number }}">
                </div>

                <div class="frmSearch col-md-3 mb-3" id="">
                    <span style="font-weight:bolder">
                        <label for="container_register_number"
                            class="caption">{{ trans('Container Register Number') }}</label>
                    </span>
                    <input type="text" id="container_register_number" name="container_register_number"
                        class="form-control round" value="{{ $purchase_orders->container_register_number }}">
                </div>

            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>{{ session('success') }}</strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>{{ session('error') }}</strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="content-wrapper" style="background-color:aqua">
                <div class="content-body">
                    <div class="card">
                        <div class="card-content">

                            <div class="card-body">


                                <input type="hidden" value="invoice" name="status">
                                <div class="row">
                                    <div class="col-md-6 col-lg-4 cmp-pnl">
                                        <div id="customerpanel" class="inner-cmp-pnl">

                                            <div class="form-group row">
                                                <div class="frmSearch col-sm-12">
                                                    <span style="font-weight:bolder">
                                                        <label for="cst"
                                                            class="caption">{{ trans('Search Product Name') }}&nbsp;</label>
                                                    </span>
                                                    <input type="text"
                                                        class="form-control productname typeahead auto_calculate"
                                                        name="itemname" id='productname' autocomplete="off"
                                                        placeholder="Search Product Name ">
                                                    <input type="hidden" id="result_item_id">
                                                    <input type="hidden" id="result_variation_id">
                                                    <input type="hidden"
                                                        class="form-control result_product_name typeahead result_product_name"
                                                        name="result_product_name[]" id="result_product_name-0"
                                                        autocomplete="off">
                                                    <!-- <input type="hidden"
                                                    class="form-control result_product_code typeahead result_product_code"
                                                    name="result_product_code[]" id="result_product_code-0"
                                                    autocomplete="off">  -->

                                                    <div id="customer-box-result"></div>
                                                </div>
                                            </div>
                                            <input type="hidden" id="service_id" name="service_id" value="0">
                                            <input type="hidden" name="advisor_name"
                                                value="{{ Auth::user()->name }}">
                                            <input type="hidden" name="manager_type"
                                                value="{{ Auth::user()->type }}">

                                        </div>
                                    </div>
                                    @if (auth()->user()->is_admin == '1')
                                        <div class="frmSearch col-md-3">
                                            <span style="font-weight: bolder;">
                                                <label for="cst"
                                                    class="caption">{{ trans('Location') }}&nbsp;</label>
                                            </span>
                                            <select name="location" id="location" class="mb-4 form-control location"
                                                required>
                                                @foreach ($warehouses as $warehouse)
                                                    <option value="{{ $warehouse->id }}"
                                                        {{ $purchase_orders->branch == $warehouse->id ? 'selected' : '' }}>
                                                        {{ $warehouse->name }}
                                                    </option>
                                                @endforeach

                                                {{-- @foreach ($warehouses as $warehouse)
                                                    @if ($warehouse->id == 8)
                                                        <option value="{{ $warehouse->id }}">
                                                            {{ $warehouse->name }}
                                                        </option>
                                                    @endif
                                                @endforeach --}}
                                            </select>
                                        </div>
                                    @else
                                        <div class="frmSearch col-md-3">
                                            <span style="font-weight:bolder">
                                                <label for="cst"
                                                    class="caption">{{ trans('Location') }}&nbsp;</label>
                                            </span>
                                            <select name="location" id="location" class="mb-4 form-control location"
                                                required>

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

                                                {{-- @foreach ($warehouses as $warehouse)
                                                    @if ($warehouse->id == 8)
                                                        <option value="{{ $warehouse->id }}">
                                                            {{ $warehouse->name }}
                                                        </option>
                                                    @endif
                                                @endforeach --}}
                                            </select>
                                        </div>
                                    @endif





                                </div>

                                <div class="row " style="margin-top:1vh;">
                                    <!-- <table class="table-responsive tfr my_stripe"> -->


                                    <table class="table">
                                        <thead>
                                            <tr class="item_header bg-gradient-directional-blue white"
                                                style="margin-bottom:10px;">
                                                <th width="5%" class="text-center">{{ trans('No') }}</th>
                                                <th width="25%" class="text-center">{{ trans('Product Name') }}
                                                </th>

                                                <th width="20%" class="text-center" style="display: none;">
                                                    {{ trans('Descriptions') }}
                                                </th>

                                                <th width="7%" class="text-center">{{ trans('Quantity') }}
                                                </th>
                                                <th width="7%" class="text-center">{{ trans('Unit') }}
                                                </th>

                                                <th width="8%" class="text-center">{{ trans('Unit Price') }}
                                                </th>

                                                <th width="8%" class="text-center">
                                                    {{ trans('Discounts') }}
                                                </th>

                                                <th width="8%" class="text-center">
                                                    {{ trans('Expired Date') }}
                                                </th>

                                                <th width="14%" class="text-center">{{ trans('Amount') }}
                                                    {{-- ({{ config('currency.symbol') }}) --}}
                                                </th>

                                            </tr>
                                        </thead>
                                        <tbody id="showitem123">
                                            @php
                                                $item_dis = 0;
                                                $amount = 0;
                                                $subtotal = 0;
                                                $overall_dis = 0;
                                                $item_discount = 0;
                                                $final_total = 0;
                                            @endphp
                                            @foreach ($purchase_sells as $key => $po)
                                                <tr>
                                                    <td class="text-center" id="count">{{ $key + 1 }}</td>

                                                    <td>

                                                        <input type="text"
                                                            class="form-control productname typeahead auto_calculate"
                                                            name="part_number[]" value="{{ $po->part_number }}"
                                                            placeholder="{{ trans('Enter Product Name') }}"
                                                            id='productname-0' autocomplete="off">
                                                        <input type="hidden"
                                                            class="form-control result_item_name typeahead result_item_name"
                                                            name="result_item_name[]" id="result_item_name-0"
                                                            autocomplete="off" value="{{ $po->product_name }}">
                                                        <!-- <input type="hidden"
                                                            class="form-control result_descriptions typeahead descriptions"
                                                            name="result_descriptions[]" id="result_descriptions-0"
                                                            autocomplete="off"> -->
                                                        <input type="hidden"
                                                            class="form-control result_product_code typeahead "
                                                            name="result_product_code[]" id="result_product_code-0"
                                                            autocomplete="off" value="{{ $po->product_code }}">
                                                        <input type="hidden"
                                                            class="form-control result_id typeahead result_id"
                                                            name="result_id[]" id="result_id-0"
                                                            value="{{ $po->variation_id }}" autocomplete="off">
                                                        <input type="hidden"
                                                            class="form-control item_id typeahead item_id"
                                                            name="item_id[]" value="{{ $po->item_id }}"
                                                            id="item_id-0" autocomplete="off">
                                                        <input type="hidden"
                                                            class="form-control product_list_id typeahead"
                                                            name="product_list_id[]" id="product_list_id-0"
                                                            value="{{ $po->product_list_id }}">

                                                        <input type="hidden" class="form-control model typeahead"
                                                            name="model[]" id="model-0"
                                                            value="{{ $po->model }}">

                                                        <input type="hidden" class="form-control colour typeahead"
                                                            name="colour[]" id="colour-0"
                                                            value="{{ $po->colour }}">

                                                        <input type="hidden" class="form-control size typeahead"
                                                            name="size[]" id="size-0"
                                                            value="{{ $po->size }}">

                                                        <input type="hidden" class="form-control seater typeahead"
                                                            name="seater[]" id="seater-0"
                                                            value="{{ $po->seater }}">


                                                    </td>

                                                    <td hidden><input type="text"
                                                            class="form-control description typeahead"
                                                            value="{{ $po->description }}" name="part_description[]"
                                                            placeholder="{{ trans('') }}" id='description-0'
                                                            autocomplete="off"></td>

                                                    <td><input type="number"
                                                            class="form-control req amnt auto_calculate"
                                                            name="product_qty[]" id="amount-{{ $key }}"
                                                            autocomplete="off" value="{{ $po->product_qty }}"><input
                                                            type="hidden" id="alert-{{ $key }}"
                                                            name="alert[]">
                                                    </td>
                                                    <td><input type="text" class="form-control item_unit "
                                                            name="item_unit[]" id="item_unit-0" autocomplete="off"
                                                            value="{{ $po->unit }}">
                                                    </td>


                                                    <td><input type="text" class="form-control product_price "
                                                            name="product_price[]" id="price-{{ $key }}"
                                                            autocomplete="off" value="{{ $po->product_price }}">
                                                    </td>

                                                    <td>

                                                        <div class="input-group">
                                                            <input type="hidden" name="discount_category[]"
                                                                value="{{ $po->discount_category }}"
                                                                class="discount_category" id="discount_category-0">
                                                            <input type="number" name="discount_amt[]"
                                                                value="{{ $po->discount_amt }}" class="discount_amt"
                                                                id="discount_amt-0" hidden>
                                                            <input type="number"
                                                                class="form-control vat auto_calculate"
                                                                name="discount[]" id="vat-0"
                                                                value="{{ $po->discount }}" autocomplete="off">
                                                            <div class="input-group-append">
                                                                <button type="button"
                                                                    class="btn btn-success discount_cat"
                                                                    data-val="0">
                                                                    @if ($po->discount_category == 'percent')
                                                                        %
                                                                    @else
                                                                        $
                                                                    @endif
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    @php
                                                        $item_dis += $po->discount_amt;
                                                    @endphp
                                                    <td><input type="text" class="form-control exp_date "
                                                            name="exp_date[]" id="exp_date-0"
                                                            value="{{ $po->exp_date }}" autocomplete="off">
                                                    </td>


                                                    <td style="display: none;"><input type="text"
                                                            class="form-control warehouse " name="warehouse[]"
                                                            id="warehouse-0" autocomplete="off"
                                                            value="{{ $po->warehouse }}">
                                                    </td>

                                                    @php
                                                        $amount =
                                                            round($po->product_qty * $po->product_price) -
                                                            $po->discount_amt;
                                                        $subtotal += round($po->product_qty * $po->product_price);
                                                    @endphp
                                                    <td style="text-align:center" class='ttlText1' id="result-0">
                                                        <strong>
                                                            {{ $amount }}
                                                        </strong>
                                                    </td>


                                                    <td style="width: 5%;"><button type="submit"
                                                            class="btn btn-danger remove_item_btn auto_calculate"
                                                            id="removebutton">Remove</button></td>
                                                </tr>
                                            @endforeach
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
                                                    <!-- <button type="button" class="btn btn-success" id="addproduct"
                                                        style="margin-top:20px;margin-bottom:20px;">
                                                        <i class="fa fa-plus-square"></i>
                                                        {{ trans('Add row') }}
                                                    </button> -->
                                                    <!-- <button type="button" class="btn btn-primary" id="calculate">
                                                        Calculate
                                                    </button> -->


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
                                                <td>

                                                </td>
                                            </tr>

                                            <!-- <tr class="">
                                                <td colspan="2"></td>
                                                <td colspan="3" align="right"><strong>Choose Currency
                                                        Method</strong></td>
                                                <td align="left" colspan="2" class="col-md-2">
                                                        <select name="currency_method" id="currency_method"
                                                            class="form-control">
                                                            <option value="MMK" @if ($purchase_orders->currency_method == 'MMK') selected @endif>MMK</option>
                                                            <option value="USD" @if ($purchase_orders->currency_method == 'USD') selected @endif>USD</option>
                                                            <option value="YUAN" @if ($purchase_orders->currency_method == 'YUAN') selected @endif>YUAN</option>
                                                        </select>
                                                </td>
                                            </tr> -->

                                            {{-- @php
                                                if($purchase_orders->currency_method == 'MMK'){
                                                    $subtotal = $purchase_orders->sub_total;
                                                    $overall_dis = $purchase_orders->overall_discount_mmk;
                                                    $item_discount = $item_dis;
                                                    $final_total = $purchase_orders->total;
                                                }else{
                                                    $overall_dis = $purchase_orders->overall_discount_mmk;
                                                    $item_discount = number_format($item_dis / $purchase_orders->exchange_rate, 2);
                                                    $final_total = $purchase_orders->total * $purchase_orders->exchange_rate;
                                                }
                                            @endphp --}}
                                            <tr class="sub_c" style="display: table-row;">
                                                <td colspan="2">
                                                </td>
                                                <td colspan="3" align="right"><strong>Sub Total
                                                    </strong>
                                                </td>
                                                <td align="left" colspan="2" class="col-md-4"><input
                                                        type="text" name="sub_total" class="form-control"
                                                        data-val="{{ $subtotal }}" id="invoiceyoghtml" readonly
                                                        style="background-color: #E9ECEF"
                                                        value="{{ $purchase_orders->sub_total }}">

                                                </td>

                                            </tr>
                                            <tr class="sub_c" style="display: table-row;">
                                                <td colspan="2">

                                                </td>
                                                <td colspan="3" align="right"><strong>Overall Discount
                                                    </strong>
                                                </td>
                                                <td align="left" colspan="2" class="col-md-4">
                                                    <input type="number" name="total_discount" class="form-control"
                                                        id="total_discount"
                                                        value="{{ $purchase_orders->discount_total }}"
                                                        data-val="{{ $overall_dis }}">
                                                    <input type="hidden" name="overall_discount_mmk"
                                                        value="{{ $purchase_orders->overall_discount_mmk }}"
                                                        data-val="{{ $overall_dis }}" class="form-control"
                                                        id="overall_discount_mmk">
                                                </td>

                                            </tr>
                                            <tr class="sub_c" style="display: table-row;">
                                                <td colspan="2">

                                                </td>
                                                <td colspan="3" align="right"><strong>Item Discount
                                                    </strong>
                                                </td>
                                                <td align="left" colspan="2" class="col-md-4">
                                                    <input type="text" class="form-control" id="item_discount"
                                                        data-val="{{ $item_dis }}" value="{{ $item_dis }}"
                                                        readonly>
                                                </td>

                                            </tr>
                                            <tr class="sub_c" style="display: table-row;">
                                                <td colspan="2">

                                                </td>
                                                <td colspan="3" align="right"><strong>Total
                                                    </strong>
                                                </td>
                                                <td align="left" colspan="2" class="col-md-4">
                                                    <input type="text" name="total" class="form-control"
                                                        id="total_total" readonly style="background-color: #E9ECEF"
                                                        data-val="{{ $final_total }}"
                                                        value="{{ $purchase_orders->total }}">
                                                </td>

                                            </tr>

                                        <tbody id="trContainer">
                                            @forelse ($payment_method as $index => $payment)
                                                <tr class="sub_c">
                                                    <td colspan="2"></td>
                                                    <td colspan="3" align="right">
                                                        @if ($index === 0)
                                                            <strong>Payment Method</strong>
                                                        @endif
                                                    </td>
                                                    <td align="left" colspan="1" class="col-md-2">
                                                        <input type="number" name="payment_amount[]"
                                                            class="form-control payment_amount" id="payment_amount"
                                                            value="{{ $payment->payment_amount }}" readonly>
                                                    </td>
                                                    <td align="left" colspan="1"
                                                        class="col-md-2 payment_method">
                                                        <div class="input-group">
                                                            <select name="payment_method[]"
                                                                id="payment_method-{{ $index }}"
                                                                class="form-control"
                                                                onmousedown="event.preventDefault();"
                                                                onkeydown="event.preventDefault();">
                                                                @foreach ($transactions as $transactionCollection)
                                                                    @foreach ($transactionCollection as $transaction)
                                                                        <option value="{{ $transaction->id }}"
                                                                            @if ($transaction->id == $payment->payment_method) selected @endif>
                                                                            {{ $transaction->transaction_name }}
                                                                        </option>
                                                                    @endforeach
                                                                @endforeach
                                                            </select>
                                                            <div class="input-group-append">
                                                                @if ($index === 0)
                                                                    <button type="button" id="addRow"
                                                                        class="btn btn-primary" disabled>
                                                                        <i class="fa-solid fa-plus"></i>
                                                                    </button>
                                                                @else
                                                                    <button class="removeRow btn btn-danger"
                                                                        disabled><i
                                                                            class="fa-solid fa-minus"></i></button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr class="sub_c">
                                                    <td colspan="2"></td>
                                                    <td colspan="3" align="right"><strong>Payment
                                                            Method</strong></td>
                                                    <td align="left" colspan="1" class="col-md-2">
                                                        <input type="number" name="payment_amount[]"
                                                            class="form-control payment_amount" id="payment_amount"
                                                            required>
                                                    </td>
                                                    <td align="left" colspan="1"
                                                        class="col-md-2 payment_method">
                                                        <div class="input-group">
                                                            <select name="payment_method[]" id="payment_method"
                                                                class="form-control" required>
                                                                <option value="Cash">Cash</option>
                                                                <option value="K Pay">K Pay</option>
                                                                <option value="Wave">Wave</option>
                                                                <option value="Others">Others</option>
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
                                            @endforelse

                                        </tbody>



                                        <tr class="sub_c" style="display: table-row;">
                                            <td colspan="2">

                                            </td>
                                            <td colspan="3" align="right"><strong>Deposit
                                                </strong>
                                            </td>
                                            <td align="left" colspan="2"><input type="number" name="paid"
                                                    class="form-control" id="paid" onchange="paidFunction()"
                                                    value="{{ $purchase_orders->deposit }}">
                                            </td>

                                        </tr>
                                        <tr class="sub_c" style="display: table-row;">
                                            <td colspan="2">

                                            </td>
                                            <td colspan="3" align="right"><strong>Remaining Balance
                                                </strong>
                                            </td>
                                            <td align="left" colspan="2"><input type="text" name="balance"
                                                    class="form-control" id="balance" readonly
                                                    value="{{ $purchase_orders->remain_balance }}">

                                            </td>
                                        </tr>

                                        <tr class="sub_c " style="display: table-row;">
                                            <td colspan="12"> <label
                                                    for="remark"style="font-size: 14px;font-weight:bold">Remark</label>
                                                <textarea name="remark" id="remark" class="form-control" rows="2">{{ $purchase_orders->remark }}</textarea>

                                            </td>
                                        </tr>
                                        <tr class="sub_c " style="display: table-row;">
                                            <td align="right" colspan="9">
                                                <a href="{{ url('purchase_order_manage') }}"
                                                    class="btn btn-danger mt-3">Back</a>
                                                <button id="submitButton" class="mt-3 mx-2 btn btn-primary"
                                                    type="submit"
                                                    onclick="return confirm('Are you sure you want to Update?');">
                                                    Update
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>


                            </div>

                        </div>
                    </div>
                </div>

        </form>
    </div>

    </div>
    <script>
        function handleKeyUp(event) {
            console.log(`Key pressed: ${event.key}`);
            // You can add more logic here to respond to the event
        }
    </script>

    <script>
        function getAccount(payment_count) {

            var locationId = $("#location").val();
            var register_mode = $("#balance_due").val();
            $('#payment_method-' + payment_count).html(
                '<option value="">Loading...</option>');

            if (locationId) {
                $.ajax({
                    url: 'get_accounts_transaction_po/' +
                        locationId + '/' + register_mode, // Update this URL to match your route
                    type: 'GET',
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

        }
    </script>



    <script>
        $(document).ready(function() {
            let count = 0;

            function initializeTypeahead(count) {
                $('#productname').typeahead({
                    source: function(query, process) {
                        var Selectedlocation = $('#location').val();
                        return $.ajax({
                            url: "{{ route('autocomplete-part-code-po') }}",
                            method: 'POST',
                            data: {
                                query: query,
                                location: Selectedlocation,
                            },
                            dataType: 'json',
                            success: function(data) {
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
                                        display: item.item_name + ' (' +
                                            (item.product_code ? item.product_code +
                                                ' - ' : '') +
                                            (item.model ? item.model + ' - ' : '') +
                                            (item.colour ? item.colour : '') +
                                            (item.size ? ' - ' + item.size : '') +
                                            (item.seater ? ' - ' + item.seater :
                                                '') +
                                            ') (' + (item.description ? item
                                                .description : '') + ')'
                                    };
                                });
                                process(formattedData);
                            }
                        });
                    },
                    displayText: function(item) {
                        return item.display; // No longer checks `product_list`
                    },
                    afterSelect: function(item) {
                        $('#result_product_name-0').val(item.item_name);
                        $('#result_item_id').val(item.item_id);
                        $('#result_variation_id').val(item.id);
                    },
                    autoSelect: true,
                    items: 'all'
                });
            }

            function initializeTypeaheads() {
                for (let i = 0; i <= count; i++) {
                    initializeTypeahead(i);
                }
            }


            function updateItemName(item_name, row, item_id, variation_id, cuz_name, productname) {
                var $location = $('#location');
                var $itemName0 = $("#item_name-0");
                var $item_id = $("#item_id-0");
                var $variation_id = $("#result_id-0");
                var $result_item_name = $("#result_item_name-0");
                var $price0 = $("#price-0");
                var $itemUnit0 = $("#item_unit-0");
                var $retailPrice0 = $("#retail_price-0");
                var $buyPrice0 = $("#buy_price-0");
                var $warehouse0 = $("#warehouse-0");
                var $productname = $("#productname");
                var $productName0 = $("#result_product_name-0");
                var $amount0 = $("#amount-0");
                var $unitm3 = $("#unit_m3-0");
                var $unitkg = $("#unit_kg-0");
                var $totalunitm3 = $("#total_unit_m3-0");
                var $totalunitkg = $("#total_unit_kg-0");

                var selectedLocation = $location.val();
                var cuzName = $("#type").val();

                function handleSuccess(data) {
                    var item = data['item'];
                    // var variation = data['variations'][0];
                    var variation = data['variation'];
                    var item_qty = data['item_qty'];

                    $result_item_name.val(item['item_name']);
                    $itemName0.val(productname ? productname : item.item_name + ' (' + (variation.product_code ?
                            variation.product_code + ' - ' : '') +
                        (variation.model ? variation.model + ' - ' : '') +
                        (variation.colour ? variation.colour : '') + (variation.size ? ' - ' + variation.size :
                            '') +
                        (variation.seater ? ' - ' + variation.seater : '') + ' )');
                    $item_id.val(item['id']);
                    $variation_id.val(variation['id']);
                    $warehouse0.val(item['warehouse_id']);
                    $itemUnit0.val(variation['item_unit']);
                    $expDate0.val(variation['expired_date']);
                    // $description0.val(variation['descriptions']);
                    // let rate_exchange = parseFloat($('#exchange_rate').val() || 1);
                    let unitprice = parseFloat(variation['buy_price']) || 0;
                    // Calculate new retail price and round to the nearest integer
                    // let new_unitprice = Math.round(unitprice * rate_exchange);
                    // let rounded_retail = Math.round(number / 10) * 10; //to round the integer
                    $price0.val(unitprice);
                    // $price0.val(new_unitprice);

                    $('#result_product_code-0').val(variation['product_code']);
                    $('#model-0').val(variation['model']);
                    $('#colour-0').val(variation['colour']);



                    $retailPrice0.val(variation['retail_price']);
                    $buyPrice0.val(variation['buy_price']);
                    $partDesc0.val(variation['variations_desc']);


                    $productname.val('');
                    // $barcode.val('');


                }

                function handleError(xhr, status, error) {
                    console.error(xhr.responseText);
                }

                if ($itemName0.val() === "") {
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('get-part-po-data') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            item_id: item_id,
                            variation_id: variation_id,
                            location: selectedLocation,
                        },
                        success: function(data) {
                            handleSuccess(data);
                            autoCalculate();
                        },
                        error: handleError
                    });


                } else {
                    if ($("#item_name-0").val() === $("#productname").val()) {

                        var existingRow = $("#amount-0");

                        var currentQuantity = parseInt(existingRow.val());
                        existingRow.val(currentQuantity + 1);

                        // $("#barcode").val('');
                        $("#productname").val('');
                        autoCalculate();

                    } else {
                        $.ajax({
                            type: 'POST',
                            url: "{{ route('get-part-po-data') }}",
                            data: {
                                _token: "{{ csrf_token() }}",
                                item_id: item_id,
                                variation_id: variation_id,
                                location: selectedLocation,
                            },
                            success: function(data) {

                                // data.variations.forEach(function(variation) {
                                //     addNewRow(data.item, variation, productname);
                                // });
                                addNewRow(data.item, data.variation, data.item_qty,
                                    productname);
                                autoCalculate();

                                // $("#barcode").val();
                                $("#productname").val();
                            },
                            error: handleError
                        });


                    }
                }

                // $("#barcode").val();
                $("#productname").val();
            }


            function addNewRow(item, variation, item_qty, productname) {
                let cuz_name = $("#type").val();

                existingRow = $("#showitem123 input.productname[value='" + productname + "']").closest('tr');

                if (existingRow.length > 0) {
                    let qtyInput = existingRow.find('.req.amnt');
                    let currentQty = parseInt(qtyInput.val()) || 0;
                    qtyInput.val(currentQty + 1);
                    // $("#barcode").val('');
                    $("#productname").val('');

                } else {
                    count++;
                    let rowCount = $("#showitem123 tr").length;
                    // let rate_exchange = parseFloat($('#exchange_rate').val() || 1);
                    let unitprice = parseFloat(variation['buy_price']) || 0;
                    // Calculate new retail price and round to the nearest integer
                    // let new_unitprice = Math.round(unitprice * rate_exchange);
                    // let rounded_retail = Math.round(number / 10) * 10; //to round the integer

                    // if (item_barcode.length > 1) {
                    // displayName = item.item_name + ' (' + (variation
                    // .product_code ?? '') + ' - '+ (variation.model ?? '') +' - '+ (variation.colour ?? '') + ' - '+ (variation.size ?? '')  + ' - '+ (variation.seater ?? '') +' )';

                    displayName = item.item_name + ' (' + (variation.product_code ? variation.product_code + ' - ' :
                            '') +
                        (variation.model ? variation.model + ' - ' : '') +
                        (variation.colour ? variation.colour : '') + (variation.size ? ' - ' + variation.size :
                            '') +
                        (variation.seater ? ' - ' + variation.seater : '') + ') (' + (variation.variations_desc ?
                            variation.variations_desc : '') + ')';
                    let newRow = '<tr>' +
                        '<td class="text-center">' + (rowCount + 1) + '</td>' +
                        '<td>' +
                        '<input type="text" class="form-control productname typeahead item_name auto_calculate" name="part_number[]" id="productname-' +
                        count + '" autocomplete="off" value="' + displayName + '">' +
                        '<input type="hidden" class="form-control result_item_name typeahead result_item_name" name="result_item_name[]" id="result_item_name-' +
                        count + '" value="' + item['item_name'] + '" autocomplete="off">' +
                        // '<input type="hidden" class="form-control result_descriptions typeahead result_descriptions" name="result_descriptions[]" id="result_descriptions-' +
                        // count +'" autocomplete="off">'+
                        '<input type="hidden" class="form-control result_product_code typeahead result_product_code" name="result_product_code[]" id="result_product_code-' +
                        count + '" value="' + variation['product_code'] + '">' +
                        '<input type="hidden" class="form-control result_id typeahead result_id" name="result_id[]" id="result_id-' +
                        count + '" value="' + variation['id'] + '" autocomplete="off">' +
                        '<input type="hidden" class="form-control item_id typeahead item_id" name="item_id[]" id="item_id-' +
                        count + '" value="' + item['id'] + '" autocomplete="off">' +

                        '<input type="hidden" class="form-control model typeahead" name="model[]" id="model-' +
                        count + '" value="' + variation['model'] + '">' +
                        '<input type="hidden" class="form-control colour typeahead" name="colour[]" id="colour-' +
                        count + '" value="' + variation['colour'] + '"></td>' +
                        '<td hidden><input type="text" class="form-control description" name="part_description[]" id="description-' +
                        count + '" value="' + variation['variations_desc'] +
                        '" autocomplete="off"></td>' +
                        '<td><input type="number" class="form-control req amnt auto_calculate" name="product_qty[]" id="amount-' +
                        count +
                        '" autocomplete="off" value="1"><input type="hidden" id="alert-0" value="" name="alert[]"></td>' +

                        '<td><input type="text" class="form-control item_unit" name="item_unit[]" id="item_unit-' +
                        count + '" value="' + (variation['item_unit'] ? variation['item_unit'] : '') +
                        '" autocomplete="off"></td>' +

                        '<td><input type="text" class="form-control price product_price auto_calculate" name="product_price[]" id="price-' +
                        count + '" value="' + unitprice + '" autocomplete="off"></td>' +

                        // '<td><input type="text" class="form-control vat" name="discount[]" value="0" id="vat-' +
                        // count + '"   autocomplete="off"></td>' +
                        '<td><div class="input-group"><input type="hidden" name="discount_category[]" value="percent" class="discount_category" id="discount_category-' +
                        count + '">' +
                        '<input type="number" name="discount_amt[]" value="0" class="discount_amt" id="discount_amt-' +
                        count + '" hidden>' +

                        '<input type="number" class="form-control vat auto_calculate"' +
                        'name="discount[]" id="vat-' + count +
                        '" value="0" autocomplete="off"><div class="input-group-append">' +
                        '<button type="button" class="btn btn-danger discount_cat" data-val="' + count +
                        '">%</button></div></div></td>' +
                        '<td><input type="text" class="form-control exp_date" name="exp_date[]" id="exp_date-' +
                        count + '" value="' + (variation['expired_date'] ?? '') + '" autocomplete="off"></td>' +
                        '<td style="display : none;"><input type="text" class="form-control warehouse " name="warehouse[]" id="warehouse-' +
                        count + '" value="' + item['warehouse_id'] + '" "autocomplete="off"></td>' +
                        '<td style="text-align:center"><strong><span class="ttlText1" id="result-' + count +
                        '">0</span></strong></td>' +
                        '<td style="width: 5%;"><button type="submit" class="btn btn-danger remove_item_btn auto_calculate" id="removebutton">Remove</button></td>' +
                        '</tr>';

                    $("#showitem123").append(newRow);
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

                initializeTypeaheads();
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
                    // if($('#currency_method').val() == 'MMK'){
                    //     balance = Math.round(balance);
                    // }else{
                    //     balance = balance.toFixed(2);
                    // }
                    $('#balance').val(balance);
                }

                $(document).on('input', '.payment_amount', function() {
                    calculatePayment();
                });

                $('#paid').on('input', function() {
                    paidFunction();
                });

                var payment_count = @json($payment_method).length + 1;
                $('#addRow').click(function() {
                    // Check the number of rows
                    if ($('#trContainer tr.sub_c').length < 4) {
                        var newRow = `<tr class="sub_c">
                                        <td colspan="2"></td>
                                        <td colspan="3" align="right"><strong></strong></td>
                                        <td align="left" colspan="1" class="col-md-2">
                                            <input type="text" name="payment_amount[]" class="form-control payment_amount">
                                        </td>
                                        <td align="left" colspan="1" class="col-md-2">
                                            <div class="input-group">
                                                <select name="payment_method[]" id="payment_method-${payment_count}" class="form-control" required>
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
                    payment_count--;
                    calculatePayment();
                    autoCalculate();
                });

                calculatePayment();



                //change discount category
                $(document).on('click', '.discount_cat', function() {
                    let row = $(this).closest('tr');
                    let currentText = $(this).text().trim();

                    // console.log(id_val);
                    if (currentText == '$') {
                        // $('#discount_category-'+id_val).val('percent');
                        $(this).text('%');
                        row.find('.discount_category').val('percent');
                        autoCalculate();
                    } else {
                        $(this).text('$');
                        row.find('.discount_category').val('kyat');
                        autoCalculate();
                    }
                });

            });



            $(document).on('click', '.typeahead .dropdown-item', function(e) {
                e.preventDefault();

                if ($("#customer").val()) {} else {
                    const row = $(this).closest('tr');

                    const item_name = $('#result_product_name-0').val();
                    const productname = $('#productname').val();
                    const product_code = $('#result_product_code-0').val();
                    let item_id = $('#result_item_id').val();
                    let variation_id = $('#result_variation_id').val();
                    let cuz_name = $("#type").val();
                    // updateItemName(item_name, row, product_code, cuz_name, productname);
                    updateItemName(item_name, row, item_id, variation_id, cuz_name, productname);

                    $('#productname').val('');
                }
            });

            // Initialize typeahead for the first row
            initializeTypeahead(count);

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

            $(document).on("keyup", '.product_price', function() {
                autoCalculate();
            });

            function autoCalculate() {
                let subtotal = 0;
                // let totalDis = 0;
                let total = 0;
                let itemDiscount = 0;
                let foc_total = 0;

                $('#showitem123 tr').each(function() {
                    let row = $(this);
                    let qty = parseInt(row.find('.req.amnt').val()) || 0;
                    let unit_m3 = parseFloat(row.find('.unit_m3').val()) || 0;
                    let unit_kg = parseFloat(row.find('.unit_kg').val()) || 0;
                    let price;

                    price = parseFloat(row.find('.product_price').val()) || 0;

                    let discount = parseFloat(row.find('.vat').val()) || 0;

                    let discount_cat = row.find('.discount_category').val();
                    console.log(discount_cat);

                    let total_unit_m3 = Math.round(unit_m3 * qty);
                    let total_unit_kg = Math.round(unit_kg * qty);
                    let itemTotal = Math.round(qty * price);

                    row.find('.total_unit_m3').val(total_unit_m3);
                    row.find('.total_unit_kg').val(total_unit_kg);

                    if (discount_cat == 'percent') {
                        discount = Math.round(itemTotal * (discount / 100));
                    }

                    row.find('.discount_amt').val(discount);

                    // totalTotal += itemTotal;

                    // if(foc == 'Yes'){
                    //     foc_total += itemTotal;
                    // }else{
                    subtotal += itemTotal;

                    if (!isNaN(discount) && discount > 0) {
                        itemTotal -= discount;
                    }

                    itemDiscount += discount;
                    // }

                    total += itemTotal;

                    console.log(row.find('.ttlText1'));

                    row.find('.ttlText1').text(itemTotal);

                });

                //substract foc total
                // total -= foc_total;

                let paid = parseFloat(document.getElementById("paid").value) ||
                    0;
                // let total_p = parseFloat(document.getElementById("total_total").value) ||
                //     0;
                // let total_discount = parseFloat(document.getElementById("total_discount").value) ||
                //     0;
                // let total_discount = $('#total_discount').attr('data-val') ?? $('#total_discount').val();
                let total_discount = $('#total_discount').val();

                let totalDiscount = total - total_discount;
                // let balance = total - paid - total_discount;
                let balance = totalDiscount - paid;

                // $("#invoiceyoghtml").attr('data-val',subtotal);
                // // $("#balance").attr('data-val',balance);
                // $("#item_discount").attr('data-val',itemDiscount);
                // $('#total_total').attr('data-val',totalDiscount);

                // $("#balance").val(balance);
                // $("#item_discount").val(itemDiscount);
                // // $('#invoiceyoghtml').val(totalTotal);
                // $('#invoiceyoghtml').val(subtotal);
                // $('#total_total').val(totalDiscount);
                // console.log($('#currency_method').val());
                // if($('#currency_method').val() != 'MMK'){
                //     let ex_rate = parseFloat($('#exchange_rate').val());
                //     calculateOtherCurrency(ex_rate);
                // }else{
                $("#balance").val(balance);
                $("#item_discount").val(itemDiscount);
                // $('#invoiceyoghtml').val(totalTotal);
                $('#invoiceyoghtml').val(subtotal);
                $('#total_total').val(totalDiscount);
                // }
            }

            function paidFunction() {
                let paid = document.getElementById("paid").value;
                let total_p = document.getElementById("invoiceyoghtml").value;
                let balance = total_p - paid;
                $("#balance").val(balance);
            }
        });
    </script>
    <script>
        // $(document).on('click', '.remove_item_btn', function(e) {
        //     e.preventDefault();
        //     let row_item = $(this).parent().parent();
        //     $(row_item).remove();
        //     count--;
        // });



        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        function paidFunction() {

            let paid = document.getElementById("paid").value;
            let total_p = document.getElementById("total_total").value;
            let balance = total_p - paid;
            $("#balance").val(balance); //update balance
        }
    </script>

    <script>
        $(document).ready(function() {

            var path = "{{ route('po_search') }}";
            $('#customer').typeahead({
                source: function(query, process) {
                    return $.get(path, {
                        query: query
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

            $(document).one('click', '#customer_search', function(e) {
                e.preventDefault();
                let serialNumber = $("#customer").val();

                $.ajax({
                    type: 'POST',
                    url: "{{ route('po_search_fill') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        model: serialNumber // Adjusted to match server-side parameter name
                    },
                    success: function(data) {
                        console.log(data);
                        $("#supplier_id").val(data['product']['id']);

                        $("#name").val(data['product']['name']);
                        $("#phno").val(data['product']['phno']);
                        $("#address").val(data['product']['address']);
                        // Adjusted to match server-side data
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>


    <script>
        $(document).ready(function() {

            $('#extra_discount').on('change', function() {

                let subtotal = parseFloat($('#invoiceyoghtml').val());
                let total_discount = parseFloat($('#commercial_text').val());
                let new_total = subtotal - total_discount;
                let extra_discount = parseFloat($(this).val());

                if (isNaN(extra_discount)) {
                    extra_discount = 0;
                }

                let newTotal = new_total - extra_discount;

                $('#total').val(newTotal.toFixed(2));
            });
        });
    </script>

    <script>
        // document.getElementById('submit-data').addEventListener('click', function() {
        //     var serviceType = document.getElementById('service_type').value;
        //     var serviceTypeError = document.getElementById('serviceTypeError');

        //     if (serviceType === '') {
        //         serviceTypeError.style.display = 'block';
        //     } else {
        //         serviceTypeError.style.display = 'none';
        //         // Add your code to handle the submission without a form
        //     }
        // });
    </script>
    <script>
        // $("input[type='date']").on("change", function() {
        //     if (this.value && moment(this.value, "YYYY-MM-DD").isValid()) {
        //         this.setAttribute(
        //             "data-date",
        //             moment(this.value, "YYYY-MM-DD").format("DD/MM/YYYY")
        //         );
        //     } else {
        //         this.setAttribute("data-date", "dd/mm/yyyy");
        //     }
        // }).trigger("change");
    </script>

    <script>
        //Enter Key click add row
        $(document).on('keydown', '.form-control', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                $('#addproduct').click();
            }
        });
    </script>
    <script>
        $(document).on("input", "#total_discount", function() {
            let subtotal = parseFloat($("#invoiceyoghtml").val()) || 0;
            let totalDiscount = parseFloat($("#total_discount").val()) || 0;
            let totalVAT = parseFloat($('#item_discount').val()) || 0;

            // $(".vat").each(function() {
            //     totalVAT += parseFloat($(this).val()) || 0;
            // });
            let total = subtotal - (totalDiscount + totalVAT);
            $("#total_total").val(total);

            // if($('#currency_method').val() != 'MMK'){
            //     $("#total_total").val(total.toFixed(2));
            //     let rate = $('#exchange_rate').val();
            //     $('#total_discount').attr('data-val',(totalDiscount * rate));
            //     $('#overall_discount_mmk').val((totalDiscount * rate));
            //     $('#total_total').attr('data-val',(total * rate));

            // }else{
            //     $("#total_total").val(total);
            //     $('#total_discount').attr('data-val',totalDiscount);
            //     $('#overall_discount_mmk').val(totalDiscount);
            //     $('#total_total').attr('data-val',total);
            // }
        });
    </script>
    <script>
        $(document).ready(function() {

            var purchase_orders = <?php echo json_encode($purchase_orders->balance_due); ?>;
            if (purchase_orders == "Sale Return") {

                $("#supplier_box").hide();
            }

            $(document).on("change", "#balance_due", function() {
                if ($(this).val() == "PO") { // Check if balance_due is empty
                    $("#supplier_box").show();

                } else {

                    $("#supplier_box").hide();

                }
            });
        });

        $(document).ready(function() {
            function filterSuppliers() {
                var selectedLocation = $('#location').val();
                var defaultSupplier = $('#supplier_id').data('default');

                $('#supplier_id option').each(function() {
                    var supplierBranch = $(this).data('branch');
                    if (supplierBranch == selectedLocation || selectedLocation === '') {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });

                if (defaultSupplier) {
                    var isDefaultVisible = $('#supplier_id option').filter(function() {
                        return $(this).val() === defaultSupplier && $(this).is(':visible');
                    }).length > 0;

                    if (isDefaultVisible) {
                        $('#supplier_id').val(defaultSupplier);
                    } else {
                        $('#supplier_id').val('');
                    }
                }
            }
            $('#location').change(function() {
                filterSuppliers();
            }).change();
            $('#supplier_id').data('default', "{{ $purchase_orders->supplier_id }}");
        });


        document.addEventListener('DOMContentLoaded', function() {
            function updateHiddenInput(checkbox) {
                const input = checkbox.closest('tr').querySelector('.sell_status_input');
                if (input) {
                    input.value = checkbox.checked ? '1' : '0';
                }
            }

            document.addEventListener('change', function(e) {
                if (e.target && e.target.classList.contains('sell_status')) {
                    updateHiddenInput(e.target);
                }
            });

            document.querySelectorAll('.sell_status').forEach(function(checkbox) {
                updateHiddenInput(checkbox);
            });
        });
    </script>


</body>

</HTML>
