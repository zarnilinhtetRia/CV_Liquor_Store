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
        .typeahead.dropdown-menu {
            max-height: 200px;
            overflow-y: auto;
        }
    </style>

</head>

<body>

    <div class="container-fluid" id="content">


        <div class="d-flex justify-content-between align-items-center mx-4 mt-3">
            <h1>Quotation</h1>

        </div>

        <form method="post" id="myForm" action="{{ url('invoice_register') }}" enctype="multipart/form-data">
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

                <div class="my-2 row">
                    <div class="col-md-3">
                        <label for="quote_no" style="font-weight:bolder">Quotation Number <span
                                class="text-danger">*</span></label>
                        <input type="text" id="invoice_no" class="form-control" name="quote_no" value=""
                            required>
                    </div>
                    <div class="col-md-3">
                        <label for="quote_date" style="font-weight:bolder">Date</label>
                        <input type="date" name="quote_date" class="form-control" max="{{ date('Y-m-d') }}"
                            value="{{ date('Y-m-d') }}" required>
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

                    <input type="hidden" name="quote_category" id="invoice_category" value="Invoice">



                </div>
                <hr>
                <div class="content-wrapper">
                    <div class="content-body">
                        <div class="">
                            <div class="card-content">

                                <div class="card-body">

                                    <div class="row">
                                        <div class="col-sm-6 cmp-pnl">
                                            <div id="customerpanel" class="inner-cmp-pnl">

                                                <div class="form-group row">
                                                    <div class="frmSearch col-sm-12" style="display: none;">

                                                        <div class="frmSearch col-sm-7">
                                                            <div class="frmSearch col-sm-12">
                                                                <span style="font-weight:bolder">
                                                                    <label for="cst"
                                                                        class="caption">{{ trans('Search  Customer Name & Phone No.') }}</label>
                                                                </span>
                                                                <div class="form-group d-flex">
                                                                    <input type="text" id="customer" name="customer"
                                                                        class="mr-2 form-control round"
                                                                        autocomplete="off" placeholder="Search.....">
                                                                    &nbsp;&nbsp;&nbsp; <button type="submit"
                                                                        class="btn btn-primary"
                                                                        id="customer_search">Add</button>
                                                                </div>

                                                                <div id="customer-box-result"></div>
                                                            </div>


                                                        </div>

                                                    </div>
                                                    <input type="hidden" id="service_id" name="service_id"
                                                        value="0">


                                                    <input type="hidden" name="manager_type"
                                                        value="{{ Auth::user()->type }}">

                                                    <!-- <input type="text" name="status" class="form-control"
                                                        value="draft" style="display: none"> -->

                                                </div>
                                            </div>
                                            <div class="col-sm-6 cmp-pnl">

                                                <div class="inner-cmp-pnl">

                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-1 row table-responsive ">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr class="item_header bg-gradient-directional-blue white">
                                                        <th class="text-center" style="width: 13%;">
                                                            {{ trans('Customer Name') }}
                                                        </th>
                                                        <th class="text-center" style="width: 13%;">
                                                            {{ trans('Phone Number') }}
                                                        </th>
                                                        {{-- <th class="text-center" style="width: 18%;">
                                                            {{ trans('Customer Type') }}
                                                        </th> --}}
                                                        <th class="text-center" style="width: 13%;">
                                                            {{ trans('Address') }}
                                                        </th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="item_header bg-gradient-directional-blue white">
                                                        <td class="text-center"><input type='text'
                                                                name='customer_name' id="name"
                                                                class="form-control" autocomplete="off"
                                                                placeholder="Enter Customer Name"></td>
                                                        <input type='hidden' name='customer_id' id="customer_id"
                                                            class="form-control">
                                                        <input type='hidden' name='status' id="status"
                                                            class="form-control" value="quotation">
                                                        <td class="text-center"><input type='text' name='phno'
                                                                id="phone_no" class="form-control"></td>
                                                        <td class="text-center" style="display: none"><input
                                                                type='text' name='type' id="type"
                                                                class="form-control" value="Retail"></td>
                                                        <td class="text-center"><input type='text' name='address'
                                                                class="form-control" id="address"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="row table-responsive " style="margin-top:1vh;">
                                            <div class="mt-2 frmSearch col-md-3" style="display: none;">
                                                <label for="payment"
                                                    style="font-weight:bolder">{{ trans('Sale Price Category') }}
                                                </label>
                                                <select class="mb-4 form-control round "
                                                    aria-label="Default select example" name="sale_price_category"
                                                    id="sale_price_category" required>

                                                    <option value="Default" selected>Default</option>
                                                    {{-- <option value="Whole Sale">Whole Sale</option>
                                                    <option value="Retail">Retail</option> --}}
                                                </select>
                                            </div>





                                            <table class="table table-bordered">
                                                <!-- Fixed width -->
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
                                                        <th width="19%" class="text-center">
                                                            {{ trans('Product Name') }}
                                                        </th>
                                                        <th width="19%" class="text-center">
                                                            {{ trans('Descriptions') }}
                                                        </th>
                                                        <th width="12%" class="text-center">
                                                            {{ trans('Category') }}</th>
                                                        <th width="6%" class="text-center">
                                                            {{ trans('Qty') }}
                                                        </th>
                                                        <th width="7%" class="text-center">{{ trans('Unit') }}
                                                        </th>
                                                        <!-- <th width="9%" class="text-center wholesale_th">
                                                            {{ trans('လက်ကားစျေး') }}
                                                        </th> -->
                                                        {{-- <th width="7%" class="text-center wholesale_th">
                                                            {{ trans('FOC') }}
                                                        </th> --}}
                                                        <th style="display: none;" width="7%"
                                                            class="text-center wholesale_th">
                                                            {{ trans('Warranty') }}
                                                        </th>
                                                        <th width="8%" class="text-center retail_th">
                                                            {{ trans('Price') }}
                                                        </th>
                                                        <th width="9%" class="text-center">
                                                            {{ trans('Discounts') }}
                                                        </th>
                                                        <th style="display: none;" width="9%"
                                                            class="text-center">
                                                            {{ trans('Expiry') }}
                                                        </th>
                                                        <th width="9%" class="text-center">
                                                            {{ trans('Amount') }}

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
                                                                <option value="retail_unit_price" selected>Current
                                                                    Price</option>
                                                                <option value="retail_set_price">Previous Price
                                                                </option>
                                                                <option value="promotion_retail_unit">
                                                                    Retail Price
                                                                </option>
                                                                <option value="promotion_retail_set">Wholesale Price
                                                                </option>
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
                                                                class="form-control productname typeahead item_name auto_calculate"
                                                                name="part_number[]" value="{{ old('part_number') }}"
                                                                placeholder="{{ trans('Enter Product Name') }}"
                                                                id="productname-0" autocomplete="off">
                                                            <input type="hidden"
                                                                class="form-control result_item_name typeahead result_item_name"
                                                                name="result_item_name[]" id="result_item_name-0"
                                                                autocomplete="off">
                                                            <input type="hidden"
                                                                class="form-control result_descriptions typeahead descriptions"
                                                                name="result_descriptions[]"
                                                                id="result_descriptions-0" autocomplete="off">
                                                            <input type="hidden"
                                                                class="form-control result_product_code typeahead result_product_code"
                                                                name="result_product_code[]"
                                                                id="result_product_code-0" autocomplete="off">
                                                            <input type="hidden"
                                                                class="form-control result_id typeahead result_id"
                                                                name="result_id[]" id="result_id-0"
                                                                autocomplete="off">
                                                            <input type="hidden"
                                                                class="form-control item_id typeahead item_id"
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
                                                        <td><select name="product_category[]" id="product_category-0"
                                                                class="form-control product_category" required>

                                                                <option value="Major Hardware">Major Hardware</option>
                                                                <option value="Solar Mounting">Solar Mounting</option>
                                                                <option value="Inverter Mounting">Inverter Mounting
                                                                </option>
                                                                <option value="Battery Mounting">Battery Mounting
                                                                </option>
                                                            </select></td>
                                                        <td><input type="number"
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

                                                        {{-- <td class="foc_td">
                                                            <select class="form-control round auto_calculate"
                                                                aria-label="Default select example" name="foc[]"
                                                                id="foc-0">
                                                                <option value="No" selected>No</option>
                                                                <option value="Yes">Yes</option>
                                                            </select>
                                                        </td> --}}

                                                        <td style="display: none;">
                                                            <input type="text" class="form-control warranty "
                                                                name="warranty[]" id="warranty-0" autocomplete="off">
                                                        </td>
                                                        <td class="retail_td">
                                                            <input type="number"
                                                                class="form-control retail_price auto_calculate"
                                                                name="retail_price[]" id="retail_price-0"
                                                                autocomplete="off" value="0">
                                                            <input type="number" name="retail_unit_price[]"
                                                                class="form-control retail_unit_price auto_calculate"
                                                                id="retail_unit_price-0" hidden>
                                                            <input type="number" name="retail_set_price[]"
                                                                class="form-control retail_set_price"
                                                                id="retail_set_price-0" hidden>
                                                            <input type="number" name="promotion_retail_unit[]"
                                                                class="form-control promotion_retail_unit"
                                                                id="promotion_retail_unit-0" hidden>
                                                            <input type="number" name="promotion_retail_set[]"
                                                                class="form-control promotion_retail_set"
                                                                id="promotion_retail_set-0" hidden>
                                                        </td>
                                                        <td>
                                                            <!-- <input type="text" class="form-control vat "
                                                                name="discount[]" id="vat-0" value="0"
                                                                autocomplete="off" value="{{ old('discount') }}"> -->
                                                            <div class="input-group">
                                                                <input type="hidden" name="discount_category[]"
                                                                    value="percent" class="discount_category"
                                                                    id="discount_category-0">
                                                                <input type="number" name="discount_amt[]"
                                                                    value="0" class="discount_amt"
                                                                    id="discount_amt-0" hidden>
                                                                <input type="number"
                                                                    class="form-control vat auto_calculate"
                                                                    name="discount[]" id="vat-0" value="0"
                                                                    autocomplete="off">
                                                                <div class="input-group-append">
                                                                    <button type="button"
                                                                        class="btn btn-danger discount_cat"
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
                                                            <button type="button" class="btn btn-danger"
                                                                id="addproduct"
                                                                style="margin-top:20px;margin-bottom:20px;">
                                                                <i class="fa fa-plus-square"></i>
                                                                {{ trans('Add row') }}
                                                            </button>
                                                            <!-- <button type="button" class="btn btn-primary"
                                                                id="calculate">
                                                                Calculate
                                                            </button> -->

                                                            @if (in_array('Item', $choosePermission) || auth()->user()->is_admin == '1')
                                                                <a href="{{ URL('items') }}" target="_blank"
                                                                    id="item_search">
                                                                    <button type="button"
                                                                        class=" mx-2 btn btn-danger">
                                                                        <i
                                                                            class="fa-solid fa-magnifying-glass-plus"></i>
                                                                        Product
                                                                        Search
                                                                    </button></a>
                                                            @endif


                                                        </td>
                                                        <td colspan="6"></td>
                                                        <br><br>

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
                                                                <!-- <option value="YUAN">YUAN</option> -->
                                                            </select>
                                                        </td>

                                                    </tr>

                                                    <tr class="sub_c" style="display: table-row;">
                                                        <td>

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
                                                        <td colspan="3" align="right"><strong>Overall Discount
                                                            </strong>
                                                        </td>
                                                        <td align="left" colspan="2" class="col-md-4">
                                                            <input type="hidden" name="overall_discount_mmk"
                                                                class="form-control" id="overall_discount_mmk">
                                                            <input type="text" name="total_discount"
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

                                                <tr class="sub_c" style="display: table-row;">
                                                    <td colspan="2">

                                                    </td>
                                                    <td colspan="3" align="right"><strong>Deposit
                                                        </strong>
                                                    </td>
                                                    <td align="left" colspan="2"><input type="text"
                                                            name="paid" class="form-control" id="paid"
                                                            onchange="paidFunction()" required>

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
                                                            readonly="">

                                                    </td>
                                                </tr>

                                                <tr class="sub_c " style="display: table-row;">
                                                    <td colspan="12"> <label for="remark"
                                                            style="font-size: 14px;font-weight:bold">Remark</label>
                                                        <textarea name="remark" id="remark" class="form-control" rows="2"></textarea>

                                                    </td>
                                                </tr>
                                                <tr class="sub_c " style="display: table-row;">


                                                    <td align="right" colspan="9">
                                                        <a href="{{ url('quotation') }}"
                                                            class="mt-3 btn btn-danger">Back</a>
                                                        <button id="submitButton" class="mt-3 mx-2 btn btn-primary"
                                                            type="submit"
                                                            onclick="return confirm('Are you sure you want to Save?');">
                                                            Save
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
        </form>

    </div>
    <script>
        $(document).ready(function() {
            getAccount(0);
        });
        $('#location').on('change', function() {
            getAccount(0);
        });

        function getAccount(payment_count) {

            var locationId = $("#location").val();
            $('#payment_method-' + payment_count).html(
                '<option value="">Loading...</option>');

            if (locationId) {
                $.ajax({
                    url: 'get_accounts_transaction_quotation/' +
                        locationId, // Update this URL to match your route
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
            $('#location').on('change', function() {
                getAccount(payment_count);
            });
        }
    </script>

    <script>
        $(document).ready(function() {
            let count = 0;





            function initializeTypeahead(count) {
                $('#productname-' + count).typeahead({
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
                                            .product_code + ' - ' : '') + (
                                            item.model ? item.model +
                                            ' - ' : '') + (
                                            item.colour ? item.colour : ''
                                        ) + (
                                            item.size ? ' - ' + item.size :
                                            '') + (item
                                            .seater ? ' - ' + item.seater :
                                            '') + ' )' + ' (' + ((item
                                            .description ?
                                            item
                                            .description : '') + ')'),
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
                        $('#result_descriptions-' + count).val(item.variation_desc);
                        $('#result_product_code-' + count).val(item.product_code);
                        $('#result_item_name-' + count).val(item.item_name);
                        $('#result_id-' + count).val(item.id);
                        $('#item_id-' + count).val(item.item_id);
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


            function updateItemName(row, item_id, variation_id, cuz_name) {
                let model_input = row.find('.model');
                let item_name = row.find('.item_name');
                let colour_input = row.find('.colour');
                let size_input = row.find('.size');
                let seater_input = row.find('.seater');
                let result_product_code = row.find('.result_product_code');

                let itemNameInput = row.find('.price');
                let partDesc = row.find('.description');
                let exp_date = row.find('.exp_date');
                let item_unit = row.find('.item_unit');
                let retail_price = row.find('.retail_price');
                let warranty = row.find('.warranty');
                let warehouse = row.find('.warehouse');
                let buy_price = row.find('.buy_price');
                var Selectedlocation = $('#location').val();
                let price_category_val = row.find('.price_category').val();

                $.ajax({
                    type: 'POST',
                    url: "{{ route('get-part-data-invoice') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        item_id: item_id,
                        variation_id: variation_id,
                        // location: Selectedlocation,
                    },
                    success: function(data) {
                        let retailprice = parseFloat(data.retail_price) || 0;
                        let retail_set_price = parseFloat(data.retail_set_price) || 0;
                        let promotion_retail_unit = parseFloat(data.promotion_retail_unit) || 0;
                        let promotion_retail_set = parseFloat(data.promotion_retail_set) || 0;
                        // Calculate new retail price and round to the nearest integer

                        // let rounded_retail = Math.round(number / 10) * 10; //to round the integer

                        if (parseFloat(data.reorder_level_stock) >= parseFloat(data.quantity || 0)) {
                            alert((data.quantity || 0) + " quantity!");
                        } else {
                            alert((data.quantity || 0) + " quantity!");
                        }



                        model_input.val(data.model);
                        warranty.val(data.warranty);
                        colour_input.val(data.colour);
                        size_input.val(data.size);
                        seater_input.val(data.seater);
                        result_product_code.val(data.product_code);

                        itemNameInput.val(data.wholesale_price);

                        if (price_category_val == 'retail_set_price') {
                            let new_retail = Math.round(retail_set_price);
                            retail_price.val(new_retail);
                        } else if (price_category_val == 'promotion_retail_unit') {
                            let new_retail = Math.round(promotion_retail_unit);
                            retail_price.val(new_retail);
                        } else if (price_category_val == 'promotion_retail_set') {
                            let new_retail = Math.round(promotion_retail_set);
                            retail_price.val(new_retail);
                        } else {
                            let new_retail = Math.round(retailprice);
                            retail_price.val(new_retail);
                        }

                        row.find('.retail_set_price').val(retail_set_price);
                        row.find('.promotion_retail_unit').val(promotion_retail_unit);
                        row.find('.promotion_retail_set').val(promotion_retail_set);
                        row.find('.retail_unit_price').val(retailprice);

                        partDesc.val(data.descriptions);
                        exp_date.val(data.expired_date);
                        item_unit.val(data.item_unit);
                        // retail_price.val(data.retail_price);

                        warehouse.val(data.warehouse_id);
                        buy_price.val(data.buy_price);

                        autoCalculate();
                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
            }

            $("#addproduct").click(function(e) {
                e.preventDefault();
                count++;

                let rowCount = $("#showitem123 tr").length;
                let newRow = '<tr>' +
                    '<td class="text-center">' + (rowCount + 1) + '</td>' +
                    '<td class="price_td"><select class="form-control round auto_calculate price_category" aria-label="Default select example" name="price_category[]"' +
                    'id="price_category-' + count +
                    '"> <option value="retail_unit_price" selected>Current Price</option> <option value="retail_set_price">Previous Price</option>' +
                    '<option value="promotion_retail_unit">Retail Price</option> <option value="promotion_retail_set">Wholesale Price</option></select>' +
                    '</td>' +
                    '<td style="display:none"><input type="hidden" class="form-control barcode typeahead" name="barcode[]" id="barcode-' +
                    count + '" autocomplete="off"></td>' +
                    '<td>' +
                    '<input type="text" class="form-control productname typeahead item_name auto_calculate" name="part_number[]" id="productname-' +
                    count +
                    '" autocomplete="off" placeholder="Enter Product Name"><input type="hidden" class="form-control result_item_name typeahead result_item_name" name="result_item_name[]" id="result_item_name-' +
                    count +
                    '" autocomplete="off"><input type="hidden" class="form-control result_descriptions typeahead result_descriptions" name="result_descriptions[]" id="result_descriptions-' +
                    count +
                    '" autocomplete="off"><input type="hidden" class="form-control result_product_code typeahead result_product_code" name="result_product_code[]" id="result_product_code-' +
                    count +
                    '" autocomplete="off"><input type="hidden" class="form-control result_id typeahead result_id" name="result_id[]" id="result_id-' +
                    count +
                    '" autocomplete="off"><input type="hidden" class="form-control item_id typeahead item_id" name="item_id[]" id="item_id-' +
                    count + '" autocomplete="off">' +
                    '<input type="hidden" class="form-control model typeahead" name="model[]" id="model-' +
                    count + '" autocomplete="off">' +
                    '<input type="hidden" class="form-control colour typeahead" name="colour[]" id="colour-' +
                    count + '" autocomplete="off">' +
                    '<input type="hidden" class="form-control size typeahead" name="size[]" id="size-' +
                    count + '" autocomplete="off">' +
                    '<input type="hidden" class="form-control seater typeahead" name="seater[]" id="seater-' +
                    count + '" autocomplete="off">' +
                    '</td>' +
                    '<td><input type="text" class="form-control description typeahead" name="part_description[]"  id="description-' +
                    count + '" autocomplete="off"></td>' +
                    '<td><select name="product_category[]" id="product_category-' + count +
                    '" class="form-control product_category" required> <option value="Major Hardware">Major Hardware</option> <option value="Solar Mounting">Solar Mounting</option><option value="Inverter Mounting">Inverter Mounting </option><option value="Battery Mounting">Battery Mounting</option></select></td>' +
                    '<td><input type="number" class="form-control req amnt auto_calculate" name="product_qty[]" id="amount-' +
                    count +
                    '"   autocomplete="off" value="1"><input type="hidden" id="alert-0" value="" name="alert[]"></td>' +
                    '<td><input type="text" class="form-control item_unit " name="item_unit[]" id="item_unit-' +
                    count +
                    '" autocomplete ="off"> </td>' +

                    // '<td class="wholesale_td"><input type="text" class="form-control price" name="product_price[]" value="0" id="price-' +
                    // count + '"   autocomplete="off"></td>' +
                    // ' <td class="foc_td"><select class="form-control round auto_calculate" name="foc[]" id="foc-' +
                    // count + '"><option value="No" selected>No</option>' +
                    // '<option value="Yes">Yes</option></select></td>' +
                    '<td style="display: none;"><input type="text" class="form-control warranty auto_calculate" name="warranty[]" id="warranty-' +
                    count + '"   autocomplete="off"></td>' +
                    '<td class="retail_td"><input type="number" class="form-control retail_price auto_calculate" name="retail_price[]" value="0" id="retail_price-' +
                    count + '"   autocomplete="off"></td>' +
                    '<input type="hidden" class="form-control retail_set_price" name="retail_set_price[]" id="retail_set_price-' +
                    count + '" value="0">' +
                    '<input type="hidden" class="form-control promotion_retail_unit" name="promotion_retail_unit[]" id="promotion_retail_unit-' +
                    count + '" value="0">' +
                    '<input type="hidden" class="form-control promotion_retail_set" name="promotion_retail_set[]" id="promotion_retail_set-' +
                    count + '" value="0">' +
                    '<input type="hidden" class="form-control retail_unit_price" name="retail_unit_price[]" id="retail_unit_price-' +
                    count + '" value="0">' +
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

                    '<td style="display : none;"><input type="text" class="form-control exp_date" name="exp_date[]" id="exp_date-' +
                    count + '"   autocomplete="off"></td>' +
                    '<td style="display : none;"><input type="text" class="form-control warehouse " name="warehouse[]" id="warehouse-' +
                    count +
                    '"   autocomplete="off"></td>' +

                    '<td style="text-align:center"><span class="currenty"></span><strong><span class="ttlText1" id="result-' +
                    count + '">0</span></strong></td>' +

                    '<td style="width: 3%;"><button type="submit" class="btn btn-danger remove_item_btn auto_calculate" id="removebutton"><i class="fa-solid fa-minus"></i></button></td>' +
                    '</tr>';
                $("#showitem123").append(newRow);
                initializeTypeahead(count);
            });



            $(document).on('click', '.remove_item_btn', function(e) {
                e.preventDefault();
                let row_item = $(this).parent().parent();
                $(row_item).remove();

                // Update row numbers
                $('#showitem123 tr').each(function(index) {
                    $(this).find('td:first').text(index + 1);
                });

                initializeTypeaheads();
            });

            $(document).ready(function() {
                function calculatePayment() {
                    let total = 0;
                    $('.payment_amount').each(function() {
                        let value = parseFloat($(this).val()) || 0;
                        total += value;
                    });
                    // total = Math.round(total);
                    $('#paid').val(total);
                    paidFunction();
                }

                function paidFunction() {
                    let paid = parseFloat($('#paid').val()) || 0;
                    let total_p = parseFloat($('#total_total').val()) || 0;
                    let balance = total_p - paid;
                    // if($('#currency_method').val() == 'MMK'){
                    //     balance = Math.round(balance);
                    // }else{
                    //     balance = balance.toFixed(2);
                    // }
                    balance = Math.round(balance);
                    $('#balance').val(balance);
                }

                $(document).on('input', '.payment_amount', function() {
                    calculatePayment();
                });

                $('#paid').on('input', function() {
                    paidFunction();
                });
                let payment_count = 1;
                // Function to add a new row
                $('#addRow').click(function() {
                    if ($('#trContainer tr.sub_c').length < 4) {
                        var newRow = `<tr class="sub_c">
                                        <td colspan="2"></td>
                                        <td colspan="3" align="right"><strong></strong></td>
                                        <td align="left" colspan="1" class="col-md-2">
                                            <input type="text"  name="payment_amount[]" class="form-control payment_amount">
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
                });

                calculatePayment();
            });


            // $(document).on('click', '.typeahead .dropdown-item', function(e) {
            //     e.preventDefault();

            //     if ($("#customer").val()) {} else {
            //         const row = $(this).closest('tr');

            //         // const item_name = row.find('.result_item_name').val();
            //         const item_id = row.find('.item_id').val();
            //         const variation_id = row.find('.result_id').val();
            //         // const description = row.find('.result_descriptions').val();
            //         // const product_code = row.find('.result_product_code').val();
            //         let cuz_name = $("#type").val();
            //         // updateItemName(item_name, row, description, product_code, cuz_name);
            //         updateItemName(row, item_id, variation_id, cuz_name);
            //         $('#productname').val('');
            //     }
            // });
            $(document).on('click', '.typeahead .dropdown-item', function(e) {
                e.preventDefault();

                let nameField = $("#name"); // Customer input field
                let productField = $(this).closest('tr').find('.productname'); // Product input field in row

                if (nameField.is(":focus")) {

                    let name = nameField.val().trim();
                    if (name) {
                        console.log("Calling updateCustomer with:", name);
                        updateCustomer(name);
                    }
                } else if (productField.length) {
                    // If the click comes from a product input field, call updateItemName
                    const row = $(this).closest('tr');
                    const item_id = row.find('.item_id').val();
                    const variation_id = row.find('.result_id').val();
                    let cuz_name = $("#type").val();

                    console.log("Calling updateItemName ");
                    updateItemName(row, item_id, variation_id, cuz_name);
                    // productField.val(''); // Clear product input
                }
            });

            //change price category
            $(document).on('change', '.price_category', function(e) {
                let row_val = $(this).closest('tr');
                let p_category = $(this).val();
                calculateUnitPrice(row_val, p_category);

            });


            function calculateUnitPrice(row_val, p_category) {
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
            }


            // Initialize typeahead for the first row
            initializeTypeahead(count);

            // $(document).on("click", '#calculate', function(e) {
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

            function autoCalculate() {
                let total = 0;
                let total_purchase = 0;
                let totalTax = 0;
                let salePriceCategory = $('#sale_price_category').val();
                let total_discount = 0;
                let total_foc = 0;

                for (let i = 0; i < (count + 1); i++) {
                    var qty = parseInt($('#amount-' + i).val() || 0);
                    var item_name = $('#productname-' + i).val() || 0;
                    // var sel = $('#focsel-' + i).val() || 0;
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

                    let total_amount = Math.round(price * qty);
                    let taxRate = parseFloat($('#vat-' + i).val() || 0);

                    if (!isNaN(taxRate) && taxRate > 0) {
                        let discount_cat = $('#discount_category-' + i).val();
                        console.log(discount_cat);

                        if (discount_cat == 'percent') {
                            discount = Math.round(total_amount * (taxRate / 100));
                        } else {
                            discount = taxRate;
                        }
                        $("#result-" + i).text(total_amount - discount);
                        $('#discount_amt-' + i).val(discount);
                    } else {
                        $("#result-" + i).text(total_amount);
                        $('#discount_amt-' + i).val(taxRate);

                    }

                    total += total_amount;
                    total_purchase += buy_price * qty;

                    //check foc
                    if (foc == 'Yes') {
                        total_foc += total_amount;
                        //substract item discount if foc is no
                    } else {
                        total_discount += discount;
                        totalTax += discount;
                    }
                }

                //substract foc amount
                total -= total_foc;

                let total_discount_val = $('#total_discount').attr('data-val') ?? $('#total_discount').val();
                let taxt = total;
                taxt = Math.ceil(taxt);
                let total_total = total - total_discount;
                // let total_dis = parseFloat(total_total - $('#total_discount').val());
                let total_dis = parseFloat(total_total - total_discount_val);




                $("#invoiceyoghtml").val(total);
                $("#item_discount").val(totalTax);
                $("#total_buy_price").val(total_purchase);
                $("#commercial_text").val(taxt);
                $("#total").val(total_total);
                $('#total_total').val(total_dis);

            }



            //calculate other currency
            // $(document).on('input', '#exchange_rate', function(e) {
            //     e.preventDefault();

            //     let exchange_rate = parseFloat($(this).val()) || 0;
            //     if (exchange_rate <= 0) {
            //         alert("Invalid exchange rate.");
            //         return;
            //     }

            //     if ($('#currency_method').val() != 'MMK') {
            //         calculateOtherCurrency(exchange_rate);
            //     }
            // });

            //change discount category
            // $(document).on('click', '.discount_cat', function() {
            //     let id_val = $(this).data('val');
            //     console.log(id_val);
            //     if ($(this).text() == '%') {
            //         $('#discount_category-' + id_val).val('percent');
            //         // $('#vat-'+id_val).data('val','percent');
            //         $(this).text('%');
            //         autoCalculate();
            //     } else {
            //         $(this).text('Ks');
            //         // $('#vat-'+id_val).data('val','kyat');
            //         $('#discount_category-' + id_val).val('kyat');
            //         autoCalculate();
            //     }
            // });

            $(document).on('click', '.discount_cat', function() {
                let id_val = $(this).data('val');
                let currentText = $(this).text().trim();
                let nextText, nextVal;

                if (currentText === '%') {
                    nextText = 'Ks';
                    nextVal = 'kyat';
                } else if (currentText === 'Ks') {
                    nextText = 'Est';
                    nextVal = 'estimate';
                } else {
                    nextText = '%';
                    nextVal = 'percent';
                }

                $(this).text(nextText);
                $('#discount_category-' + id_val).val(nextVal);
                autoCalculate();
            });


        });
    </script>
    <script>
        $(document).on('click', '.remove_item_btn', function(e) {
            e.preventDefault();
            let row_item = $(this).parent().parent();
            $(row_item).remove();
            count--;
        });


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // function paidFunction() {

        //     let paid = document.getElementById("paid").value;
        //     let total_p = document.getElementById("total_total").value;
        //     let balance = total_p - paid;
        //     $("#balance").val(balance); //update balance
        // }
    </script>

    <script>
        $(document).ready(function() {
            var path = "{{ route('customer_service_search') }}";
            $('#name').typeahead({
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



        });

        function updateCustomer(name) {
            $.ajax({
                type: 'POST',
                url: "{{ route('customer_service_search_fill') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    model: name,
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
        }
    </script>
    {{-- <script>
        $("input[type='date']").on("change", function() {
            if (this.value && moment(this.value, "YYYY-MM-DD").isValid()) {
                this.setAttribute(
                    "data-date",
                    moment(this.value, "YYYY-MM-DD").format("DD/MM/YYYY")
                );
            } else {
                this.setAttribute("data-date", "dd/mm/yyyy");
            }
        }).trigger("change");
    </script> --}}

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
            $('#total_discount').attr('data-val', totalDiscount);
            $('#overall_discount_mmk').val(totalDiscount);
            $('#total_total').attr('data-val', total);

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('change', function(e) {
                if (e.target && e.target.classList.contains('sell_status')) {
                    const checkbox = e.target;
                    const inputId = checkbox.id.replace('sell_status-', 'sell_status_input-');
                    const input = document.getElementById(inputId);
                    if (input) {
                        input.value = checkbox.checked ? '1' : '0';
                    }
                }
            });
        });
    </script>
</body>

</HTML>
