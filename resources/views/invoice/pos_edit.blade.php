<!DOCTYPE html>
<HTML>

<head>
    <link rel="stylesheet" href="{{ asset('locallink/css/bootstrap.min.css') }}">
    <script src="{{ asset('locallink/js/ajax_jquery.js') }}"></script>
    <script src="{{ asset('locallink/js/typehead.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <script src="{{ asset('locallink/js/moment.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <title>POS</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <style>
        /* input {
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
        } */
    </style>

</head>

<body>

    <div class="container-fluid " id="content">


        <h1 class="mx-4 mt-3">
            POS Edit
        </h1>

        <form method="post" id="myForm" action="{{ url('/invoice_update', $invoice->id) }}"
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

            <div class="mx-3 row ">
                {{-- <input type='hidden' name='sale_by' id="sale_by" value="{{ auth()->user()->name }}" class="form-control"> --}}
                <div class="mt-4 row">
                    <div class="col-md-3">
                        <label for="invoice_no" style="font-weight:bolder">POS Number</label>
                        <input type="text" id="invoice_no" class="form-control" name="invoice_no"
                            value="{{ $invoice->invoice_no }}" readonly>
                    </div>
                    {{-- <div class="col-md-3">
                        <label for="branch_invoice_no" style="font-weight:bolder">Branch POS Number</label>
                        <input type="text" id="branch_invoice_no" class="form-control" name="branch_invoice_no"
                            value="{{ $invoice->branch_invoice_no }}" readonly>
                    </div> --}}
                    <div class="col-md-3">
                        <label for="invoice_date" style="font-weight:bolder">Date</label>
                        <input type="date" name="invoice_date" class="form-control"
                            value="{{ $invoice->invoice_date }}" max="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-3" style="">
                        <label for="overdue_date" class=" caption"
                            style="font-weight:bolder">{{ trans('Payment OverDue Date') }}</label>
                        <input type="date" name="overdue_date" id="overdue_date" class="form-control round"
                            autocomplete="off" min="<?= date('Y-m-d') ?>" value="{{ $invoice->overdue_date }}">
                    </div>
                    @if (auth()->user()->is_admin == '1')
                        <div class="frmSearch col-md-3">
                            <div class="frmSearch col-sm-12">
                                <span style="font-weight:bolder">
                                    <label for="cst" class="caption">{{ trans('Location') }}&nbsp;</label>
                                </span>
                                <select name="branch" id="location" class="mb-4 form-control location" required>

                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}"
                                            @if ($invoice->branch == $warehouse->id) selected @endif>
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
                                            <option value="{{ $branch->id }}"
                                                @if ($invoice->branch == $warehouse->id) selected @endif>
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
                            <option value="{{ $invoice->sale_price_category }}">{{ $invoice->sale_price_category }}
                            </option>
                            <option value="Default" selected>Default</option>
                            <option value="Whole Sale">Whole Sale</option>
                            <option value="Retail">Retail</option>
                        </select>
                    </div>
                    {{-- <div class="col-md-3 ">
                        <label for="payment_method" style="font-weight:bolder">{{ trans('Payment Methods') }}</label>
                        <select class="mb-4 form-control round" aria-label="Default select example"
                            name="payment_method" required>
                            <option value="Cash" {{ $invoice->payment_method == 'Cash' ? 'selected' : '' }}>Cash
                            </option>
                            <option value="K Pay" {{ $invoice->payment_method == 'K Pay' ? 'selected' : '' }}>K Pay
                            </option>
                            <option value="Wave" {{ $invoice->payment_method == 'Wave' ? 'selected' : '' }}>Wave
                            </option>
                            <option value="Others" {{ $invoice->payment_method == 'Others' ? 'selected' : '' }}>Others
                            </option>
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
                                        <div class="col-sm-6 cmp-pnl">
                                            <div id="customerpanel" class="inner-cmp-pnl">


                                                <div class="mt-3 form-group row">
                                                    <div class="frmSearch col-sm-7">
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

                                                    <input type="hidden" id="service_id" name="service_id"
                                                        value="0">


                                                    <input type="hidden" name="manager_type"
                                                        value="{{ Auth::user()->type }}">



                                                </div>
                                            </div>
                                            <div class="col-sm-6 cmp-pnl">

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
                                                                class="form-control"
                                                                value="{{ $invoice->customer_name }}"></td>
                                                        <input type='hidden' name='customer_id' id="customer_id"
                                                            class="form-control" value="{{ $invoice->customer_id }}">
                                                        <input type='hidden' name='status' id="status"
                                                            class="form-control" value="pos">
                                                        <td class="text-center"><input type='text' name='phno'
                                                                id="phone_no" class="form-control"
                                                                value="{{ $invoice->phno }}"></td>
                                                        <td class="text-center"><input type='text' name='type'
                                                                id="type" class="form-control"
                                                                value="{{ $invoice->type }}"></td>
                                                        <td class="text-center"><input type='text' name='address'
                                                                class="form-control" id="address"
                                                                value="{{ $invoice->address }}"></td>
                                                    </tr>


                                                </tbody>
                                            </table>


                                        </div>
                                        <hr>

                                        <div class="mt-4 frmSearch col-md-3">
                                            <div class="frmSearch col-sm-12">
                                                <span style="font-weight:bolder">
                                                    <label for="cst"
                                                        class="caption">{{ trans('Search Item Name ') }}&nbsp;</label>
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
                                                    name="barcode" id='barcode' autocomplete="off"
                                                    placeholder="Search Item Barcode ">
                                                <div id="customer-box-result"></div>
                                            </div>



                                        </div>




                                        <div class="row table-responsive " style="margin-top:1vh;">
                                            <!-- <table class="table-responsive tfr my_stripe"> -->
                                            <table class="table table-bordered">
                                                <thead
                                                    style="background: radial-gradient(circle, rgb(255, 100, 100), rgb(52, 52, 52));
color:white;">
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
                                                        <th style="display: none;" width="9%"
                                                            class="text-center">
                                                            {{ trans('Expiry') }}
                                                        </th>
                                                        <th width="9%" class="text-center">
                                                            {{ trans('Amount') }}
                                                            ({{ config('currency.symbol') }})
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
                                                    @foreach ($sells as $key => $sell)
                                                        @foreach ($sell->variations as $variation)
                                                            <tr>
                                                                <td class="text-center" id="count">
                                                                    {{ $key + 1 }}</td>
                                                                <td class="price_td">
                                                                    <select
                                                                        class="form-control round auto_calculate price_category"
                                                                        aria-label="Default select example"
                                                                        name="price_category[]"
                                                                        id="price_category-{{ $key }}">
                                                                        <option value="retail_unit_price"
                                                                            @if ($sell->price_category == 'retail_unit_price') selected @endif>
                                                                            Retail Unit Price (1)</option>
                                                                        <option value="retail_set_price"
                                                                            @if ($sell->price_category == 'retail_set_price') selected @endif>
                                                                            Retail Unit Price (2)</option>
                                                                        <option value="promotion_retail_unit"
                                                                            @if ($sell->price_category == 'promotion_retail_unit') selected @endif>
                                                                            Retail Unit Price (3)</option>
                                                                        <option value="promotion_retail_set"
                                                                            @if ($sell->price_category == 'promotion_retail_set') selected @endif>
                                                                            Retail Unit Price (4)</option>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <input type="text"
                                                                        class="form-control productname typeahead barcode auto_calculate"
                                                                        name="barcode[]"
                                                                        value="{{ $variation->barcode }}"
                                                                        id="barcode-0" autocomplete="off" hidden>
                                                                    <input type="text"
                                                                        class="form-control productname typeahead auto_calculate"
                                                                        name="part_number[]"
                                                                        value="{{ $sell->part_number }}"
                                                                        placeholder="{{ trans('Enter Part Number') }}"
                                                                        id="item_name-{{ $key }}"
                                                                        autocomplete="off">
                                                                    <input type="hidden"
                                                                        class="form-control result_item_name typeahead result_item_name"
                                                                        name="result_item_name[]"
                                                                        id="result_item_name-{{ $key }}"
                                                                        autocomplete="off"
                                                                        value="{{ $sell->product_name }}">
                                                                    <input type="hidden"
                                                                        class="form-control result_descriptions typeahead descriptions"
                                                                        name="result_descriptions[]"
                                                                        id="result_descriptions-{{ $key }}"
                                                                        autocomplete="off">
                                                                    <input type="hidden"
                                                                        class="form-control result_product_code typeahead result_product_code"
                                                                        name="result_product_code[]"
                                                                        id="result_product_code-{{ $key }}"
                                                                        value="{{ $sell->product_code }}">
                                                                    <input type="hidden"
                                                                        class="form-control result_id typeahead result_id"
                                                                        name="result_id[]"
                                                                        id="result_id-{{ $key }}"
                                                                        value="{{ $sell->variation_id }}"
                                                                        autocomplete="off">
                                                                    <input type="hidden"
                                                                        class="form-control item_id typeahead item_id"
                                                                        name="item_id[]" value="{{ $sell->item_id }}"
                                                                        id="item_id-{{ $key }}"
                                                                        autocomplete="off">

                                                                    <input type="hidden"
                                                                        class="form-control model typeahead"
                                                                        name="model[]" id="model-{{ $key }}"
                                                                        value="{{ $sell->model }}">

                                                                    <input type="hidden"
                                                                        class="form-control colour typeahead"
                                                                        name="colour[]"
                                                                        id="colour-{{ $key }}"
                                                                        value="{{ $sell->colour }}">

                                                                    <input type="hidden"
                                                                        class="form-control size typeahead"
                                                                        name="size[]" id="size-{{ $key }}"
                                                                        value="{{ $sell->size }}">

                                                                    <input type="hidden"
                                                                        class="form-control seater typeahead"
                                                                        name="seater[]"
                                                                        id="seater-{{ $key }}"
                                                                        value="{{ $sell->seater }}">
                                                                    {{-- </div> --}}
                                                                    {{-- </div> --}}
                                                                </td>
                                                                <td><input type="text"
                                                                        class="form-control description typeahead"
                                                                        value="{{ $sell->description }}"
                                                                        name="part_description[]"
                                                                        id='description-{{ $key }}'
                                                                        autocomplete="off"></td>
                                                                <td><input type="text"
                                                                        class="form-control req amnt auto_calculate"
                                                                        name="product_qty[]"
                                                                        id="amount-{{ $key }}"
                                                                        autocomplete="off"
                                                                        value="{{ $sell->product_qty }}">
                                                                    <input type="hidden"
                                                                        id="alert-{{ $key }}" value=""
                                                                        name="alert[]">
                                                                    <input type="hidden"
                                                                        id="delivered_qty-{{ $key }}"
                                                                        value="{{ $sell->delivered_qty }}"
                                                                        name="delivered_qty[]">
                                                                </td>
                                                                <td><input type="text"
                                                                        class="form-control item_unit"
                                                                        name="item_unit[]"
                                                                        id="item_unit-{{ $key }}"
                                                                        value="{{ $sell->unit }}"></td>
                                                                <!-- <td class="wholesale_td"><input type="text"
                                                                    class="form-control price" name="product_price[]"
                                                                    id="price-0" autocomplete="off"
                                                                    value="{{ $sell->product_price }}">
                                                            </td> -->
                                                                <td class="foc_td">
                                                                    <select
                                                                        class="form-control round auto_calculate foc"
                                                                        aria-label="Default select example"
                                                                        name="foc[]" id="foc-{{ $key }}">
                                                                        <option value="No"
                                                                            @if ($sell->foc == 'No') selected @endif>
                                                                            No</option>
                                                                        <option value="Yes"
                                                                            @if ($sell->foc == 'Yes') selected @endif>
                                                                            Yes</option>
                                                                    </select>
                                                                </td>
                                                                <td class="retail_td">
                                                                    <input type="text"
                                                                        class="form-control retail_price auto_calculate"
                                                                        name="retail_price[]"
                                                                        id="retail_price-{{ $key }}"
                                                                        autocomplete="off"
                                                                        value="{{ $sell->retail_price }}" readonly>
                                                                </td>
                                                                <input type="hidden" name="retail_unit_price[]"
                                                                    class="form-control retail_unit_price"
                                                                    id="retail_unit_price-{{ $key }}"
                                                                    value="{{ $sell->retail_unit_price }}">
                                                                <input type="hidden" name="retail_set_price[]"
                                                                    class="form-control retail_set_price"
                                                                    id="retail_set_price-{{ $key }}"
                                                                    value="{{ $sell->retail_set_price }}">
                                                                <input type="hidden" name="promotion_retail_unit[]"
                                                                    class="form-control promotion_retail_unit"
                                                                    id="promotion_retail_unit-{{ $key }}"
                                                                    value="{{ $sell->promotion_retail_unit }}">
                                                                <input type="hidden" name="promotion_retail_set[]"
                                                                    class="form-control promotion_retail_set"
                                                                    id="promotion_retail_set-{{ $key }}"
                                                                    value="{{ $sell->promotion_retail_set }}">
                                                                <td>
                                                                    <!-- <input type="text" class="form-control vat"
                                                                    name="discount[]" id="vat-0"
                                                                    autocomplete="off" value="{{ $sell->discount }}"> -->
                                                                    <div class="input-group">
                                                                        <input type="hidden"
                                                                            name="discount_category[]"
                                                                            value="{{ $sell->discount_category }}"
                                                                            class="discount_category"
                                                                            id="discount_category-{{ $key }}">
                                                                        <input type="hidden" name="discount_amt[]"
                                                                            value="{{ $sell->discount_amt }}"
                                                                            class="discount_amt" id="discount_amt-0">
                                                                        <input type="text"
                                                                            class="form-control vat auto_calculate"
                                                                            name="discount[]"
                                                                            id="vat-{{ $key }}"
                                                                            value="{{ $sell->discount }}"
                                                                            autocomplete="off">
                                                                        <div class="input-group-append">
                                                                            <button type="button"
                                                                                class="btn btn-success discount_cat"
                                                                                data-val="0">
                                                                                @if ($sell->discount_category == 'percent')
                                                                                    %
                                                                                @else
                                                                                    Ks
                                                                                @endif
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                    @php
                                                                        if ($sell->foc == 'No') {
                                                                            $item_dis += $sell->discount_amt;
                                                                        }
                                                                    @endphp
                                                                </td>
                                                                <td style="display: none;"><input type="text"
                                                                        class="form-control exp_date"
                                                                        name="exp_date[]"
                                                                        id="vat-{{ $key }}"
                                                                        autocomplete="off"
                                                                        value="{{ $sell->exp_date }}">
                                                                </td>
                                                                <td style="display: none;"><input type="text"
                                                                        class="form-control warehouse"
                                                                        name="warehouse[]"
                                                                        id="warehouse-{{ $key }}"
                                                                        autocomplete="off"
                                                                        value="{{ $sell->warehouse }}"></td>

                                                                @php
                                                                    $amount =
                                                                        $sell->product_qty * $sell->retail_price -
                                                                        $sell->discount_amt;
                                                                    $subtotal =
                                                                        $sell->product_qty * $sell->retail_price;
                                                                @endphp
                                                                <td style="text-align:center"><strong><span
                                                                            class='ttlText1'
                                                                            id="result-{{ $key }}">{{ $amount }}</span></strong>
                                                                </td>
                                                                @if ($key == 0)
                                                                @else
                                                                    <td style="width: 3%;"><button type="button"
                                                                            class="btn btn-danger remove_item_btn auto_calculate"
                                                                            id="removebutton"><i
                                                                                class="fa-solid fa-minus"></i></button>
                                                                    </td>
                                                                @endif
                                                            </tr>
                                                        @endforeach
                                                    @endforeach
                                                </tbody>

                                            </table>

                                            <div class="row table-responsive " style="margin-top:1vh;">

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
                                                                {{-- <button type="button" class="btn btn-success"
                                                                    id="addproduct"
                                                                    style="margin-top:20px;margin-bottom:20px;">
                                                                    <i class="fa fa-plus-square"></i>
                                                                    {{ trans('Add row') }}
                                                                </button> --}}
                                                                <!-- <button type="button" class="btn btn-primary"
                                                                id="calculate">
                                                                Calculate
                                                            </button> -->

                                                                @if (in_array('Invoice Delete', $choosePermission) || auth()->user()->is_admin == '1')
                                                                    <a href="{{ URL('items') }}" target="_blank"
                                                                        id="item_search">
                                                                        <button type="button" class="btn btn-danger">
                                                                            <i class="fa fa-plus-square"></i> Item
                                                                            Search
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

                                                        <tr class="" hidden>
                                                            <td colspan="2"></td>
                                                            <td colspan="3" align="right"><strong>Choose Currency
                                                                    Method</strong></td>
                                                            <td align="left" colspan="2" class="col-md-2">
                                                                <select name="currency_method" id="currency_method"
                                                                    class="form-control">
                                                                    <option value="MMK"
                                                                        @if ($invoice->currency_method == 'MMK') selected @endif>
                                                                        MMK</option>
                                                                    <option value="USD"
                                                                        @if ($invoice->currency_method == 'USD') selected @endif>
                                                                        USD</option>
                                                                    {{-- <option value="YUAN"
                                                                    @if ($invoice->currency_method == 'YUAN') selected @endif>
                                                                    YUAN</option> --}}
                                                                </select>
                                                            </td>

                                                        </tr>

                                                        <tr class="sub_c" style="display: table-row;">
                                                            <td colspan="2">

                                                            </td>
                                                            <td colspan="3" align="right"><strong>Sub Total
                                                                </strong>
                                                            </td>
                                                            @php
                                                                if ($invoice->currency_method == 'MMK') {
                                                                    $subtotal = $invoice->sub_total;
                                                                    $overall_dis = $invoice->overall_discount_mmk;
                                                                    $item_discount = $item_dis;
                                                                    $final_total = $invoice->total;
                                                                } else {
                                                                    $overall_dis = $invoice->overall_discount_mmk;
                                                                    $item_discount = number_format($item_dis, 2);
                                                                    $final_total = $invoice->total;
                                                                }
                                                            @endphp
                                                            <td align="left" colspan="2" class="col-md-4"><input
                                                                    type="text" name="sub_total"
                                                                    class="form-control" id="invoiceyoghtml" readonly
                                                                    data-val="{{ $subtotal }}"
                                                                    style="background-color: #E9ECEF"
                                                                    value="{{ $invoice->sub_total }}">

                                                            </td>

                                                        </tr>
                                                        <tr class="sub_c" style="display: table-row;">
                                                            <td colspan="2">

                                                            </td>
                                                            <td colspan="3" align="right"><strong>Overall Discount
                                                                </strong>
                                                            </td>
                                                            <td align="left" colspan="2" class="col-md-4">
                                                                <input type="hidden" name="overall_discount_mmk"
                                                                    value="{{ $invoice->overall_discount_mmk }}"
                                                                    class="form-control" id="overall_discount_mmk">
                                                                <input type="text" name="total_discount"
                                                                    data-val="{{ $overall_dis }}"
                                                                    class="form-control" id="total_discount"
                                                                    value="{{ $invoice->discount_total }}">
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
                                                                    data-val="{{ $item_dis }}"
                                                                    id="item_discount" value="{{ $item_discount }}"
                                                                    readonly>
                                                            </td>

                                                        </tr>
                                                        <tr class="sub_c" style="display: table-row;">
                                                            <td colspan="2">

                                                            </td>
                                                            <td colspan="3" align="right"><strong>Total
                                                                </strong>
                                                            </td>
                                                            <td align="left" colspan="2" class="col-md-4"><input
                                                                    type="text" name="total"
                                                                    class="form-control" id="total_total" readonly
                                                                    data-val="{{ $final_total }}"
                                                                    style="background-color: #E9ECEF"
                                                                    value="{{ $invoice->total }}">

                                                            </td>

                                                        </tr>


                                                    <tbody id="trContainer">
                                                        @foreach ($payment_method as $index => $payment)
                                                            <tr class="sub_c">
                                                                <td colspan="2"></td>
                                                                <td colspan="3" align="right">
                                                                    @if ($index === 0)
                                                                        <strong>Payment Method</strong>
                                                                    @endif
                                                                </td>
                                                                <td align="left" colspan="1" class="col-md-2">
                                                                    <input type="text" name="payment_amount[]"
                                                                        class="form-control payment_amount"
                                                                        id="payment_amount"
                                                                        value="{{ $payment->payment_amount }}"
                                                                        readonly>
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
                                                                                    <option
                                                                                        value="{{ $transaction->id }}"
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
                                                                                <button
                                                                                    class="removeRow btn btn-danger"
                                                                                    disabled><i
                                                                                        class="fa-solid fa-minus"></i></button>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>


                                                    <tr class="sub_c" style="display: table-row;">
                                                        <td colspan="2">

                                                        </td>
                                                        <td colspan="3" align="right"><strong>Deposit
                                                            </strong>
                                                        </td>
                                                        <td align="left" colspan="2"><input type="text"
                                                                name="paid" class="form-control" id="paid"
                                                                onchange="paidFunction()"
                                                                value="{{ $invoice->deposit }}">

                                                        </td>

                                                    </tr>
                                                    <tr class="sub_c" style="display: table-row;">
                                                        <td colspan="2">

                                                        </td>
                                                        <td colspan="3" align="right"><strong>Remaining Balance
                                                            </strong>
                                                        </td>
                                                        <td align="left" colspan="2"><input type="text"
                                                                name="balance" class="form-control" id="balance"
                                                                readonly value="{{ $invoice->remain_balance }}">

                                                        </td>
                                                    </tr>

                                                    <tr class="sub_c " style="display: table-row;">
                                                        <td colspan="12"> <label for="remark">Remark</label>
                                                            <textarea name="remark" id="remark" class="form-control" rows="2">{{ $invoice->remark }}</textarea>

                                                        </td>
                                                    </tr>
                                                    <tr class="sub_c " style="display: table-row;">


                                                        <td align="right" colspan="9">


                                                            <button id="submitButton" class="mt-3 btn btn-primary"
                                                                type="submit"
                                                                onclick="return confirm('Are you sure you want to update?');">
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
                        </div>

                    </div>


                </div>
            </div>

        </form>
        <script>
            function getAccount(payment_count) {
                var locationId = $("#location").val();
                var register_mode = $("#balance_due").val();
                $('#payment_method-' + payment_count).html(
                    '<option value="">Loading...</option>');
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

                let count = {{ $sells->count() - 1 }};
                // Search item name suggestion (get item name from db)

                // let ex_rate_val = localStorage.getItem('exchange_rate');

                // // Set default value if none exists
                // if (!ex_rate_val) {
                //     ex_rate_val = ''; // Default exchange rate
                //     localStorage.setItem('exchange_rate', ex_rate_val);
                // }

                // // Initialize input field
                // $('#exchange_rate').val(ex_rate_val);

                // // Update exchange rate on button click
                // $(document).on('click', '#rate_update', function() {
                //     let new_rate_val = $('#exchange_rate').val();
                //     if (!isNaN(new_rate_val) && new_rate_val.trim() !== '') {
                //         let formattedRate = parseFloat(new_rate_val);
                //         localStorage.setItem('exchange_rate', formattedRate);
                //         alert('Exchange rate updated successfully!');
                //     } else {
                //         alert('Invalid input. Please enter a valid exchange rate.');
                //     }
                // });


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
                        autoCalculate(existingRow);
                        $("#barcode").val('');
                        $("#productname").val('');

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
                        console.log(variation);
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

                        row_val.find('.retail_price').val(new_r);
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

                    // console.log(count);
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


                    let paid = parseFloat(document.getElementById("paid").value) ||
                        0;
                    // let balance = total - paid - total_discount;
                    let balance = total_total - paid;

                    $("#invoiceyoghtml").val(total);
                    $("#item_discount").val(totalTax);
                    $("#total_buy_price").val(total_purchase);
                    $("#commercial_text").val(taxt);
                    $("#total").val(total_total);
                    $('#total_total').val(total_dis);
                    $("#balance").val(balance);



                }
                $('#currency_method').on('change', function() {

                    //$('#exchange_rate').attr('disabled',true);
                    //$('#exchange_rate').val('');
                    $("#invoiceyoghtml").val($("#invoiceyoghtml").attr('data-val'));
                    $("#total_discount").val($("#total_discount").attr('data-val'));
                    $("#item_discount").val($("#item_discount").attr('data-val'));
                    $("#total_total").val($('#total_total').attr('data-val'));
                    $("#paid").val('');
                    $("#balance").val('');

                });


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
        </script>


        <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
</body>

</HTML>
