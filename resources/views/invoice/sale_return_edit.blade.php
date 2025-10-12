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
            right: 12px;
            color: black;
            opacity: 1;
        } */
    </style>

</head>

<body>

    <div class="container-fluid">

        <h1 class="mt-3">
            Sale Return Edit
        </h1>
        <form method="post" id="data_form" action=" {{ URL('purchase_order_update', $purchase_orders->id) }}"
            enctype="multipart/form-data">
            @csrf
            <div class="row">

                <div class="mt-3 col-md-3">
                    <label for="invocieno" class="mt-1 caption"
                        style="font-weight:bolder">{{ trans('Sale Return Number') }}</label>

                    <div class="input-group">
                        <div class="input-group-addon"><span class="icon-file-text-o" aria-hidden="true"></span>
                        </div>
                        <input type="text" id="invoice_number" name="po_number" class="form-control round"
                            value="{{ $purchase_orders->quote_no }}" readonly>


                    </div>
                </div>




                <div class="mt-3 col-md-3">
                    <label for="invociedate" class="mt-1 caption"
                        style="font-weight:bolder">{{ trans('Sale Return Date') }}</label>

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

                        <option selected value="{{ $purchase_orders->payment_method }}">
                            {{ $purchase_orders->payment_method }}
                        </option>
                        <option value="Cash">Cash</option>
                        <option value="Credit">Credit</option>
                        <option value="Consignment Terms">Consignment Terms</option>
                    </select>
                </div> --}}


                @if (auth()->user()->is_admin == '1')
                    <div class="mt-4 frmSearch col-md-3">
                        <div class="frmSearch col-sm-12">
                            <span style="font-weight: bolder;">
                                <label for="cst" class="caption">{{ trans('Location') }}&nbsp;</label>
                            </span>
                            <select name="location" id="location" class="mb-4 form-control location" required>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}"
                                        {{ $purchase_orders->branch == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @else
                    <div class="mt-4 frmSearch col-md-3">
                        <div class="frmSearch col-sm-12">
                            <span style="font-weight:bolder">
                                <label for="cst" class="caption">{{ trans('Location') }}&nbsp;</label>
                            </span>
                            <select name="location" id="location" class="mb-4 form-control location" required>

                                @php
                                    $userPermissions = auth()->user()->level ? json_decode(auth()->user()->level) : [];
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

                <div class="frmSearch col-md-3 col-sm-6">
                    <div class="frmSearch col-sm-12">
                        <div class="frmSearch col-sm-12">
                            <span style="font-weight:bolder">
                                <label for="cst" class="caption"> Receiving Mode
                                </label>
                            </span>
                            <select name="balance_due" id="balance_due" class="mb-4 form-control balance_due">


                                <option value="Sale Return" @if ($purchase_orders->balance_due == 'Sale Return') selected @endif>Sale
                                    Return</option>

                            </select>

                            <div id="customer-box-result"></div>
                        </div>


                    </div>



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
                                    <div class="col-sm-6 cmp-pnl">
                                        <div id="customerpanel" class="inner-cmp-pnl">

                                            <div class="form-group row">
                                                <div class="frmSearch col-sm-12">
                                                    <span style="font-weight:bolder">

                                                    </span>




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
                                    <div class="col-sm-6 cmp-pnl">

                                        <div class="inner-cmp-pnl">
                                            <div class="frmSearch col-sm-12">

                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="row " style="margin-top:1vh;">
                                    <!-- <table class="table-responsive tfr my_stripe"> -->


                                    <table class="table">
                                        <thead style="background-color:#0047AA;color:white;">
                                            <tr class="item_header bg-gradient-directional-blue white"
                                                style="margin-bottom:10px;">
                                                <th width="5%" class="text-center">{{ trans('No') }}</th>
                                                <th width="18%" class="text-center">{{ trans('Item Name') }}
                                                </th>

                                                <th width="20%" class="text-center">{{ trans('Descriptions') }}
                                                </th>

                                                <th width="7%" class="text-center">{{ trans('Quantity') }}
                                                </th>
                                                <th width="10%" class="text-center">{{ trans('Unit') }}
                                                </th>

                                                <th width="10%" class="text-center">{{ trans('Unit Price') }}
                                                </th>

                                                <th width="10%" class="text-center">
                                                    {{ trans('Discounts') }}
                                                </th>

                                                <th width="10%" class="text-center">
                                                    {{ trans('Expiry') }}
                                                </th>

                                                <th width="14%" class="text-center">{{ trans('Amount') }}
                                                    ({{ config('currency.symbol') }})
                                                </th>

                                            </tr>

                                        </thead>
                                        <tbody id="showitem123">
                                            @foreach ($purchase_sells as $key => $po)
                                                <tr>
                                                    <td class="text-center" id="count">{{ $key + 1 }}</td>

                                                    <td>

                                                        {{-- <div class="row align-items-center"> --}}
                                                        {{-- <div class="col-auto">
                                                                    <input type="checkbox" id="sell_status-0"
                                                                        value="1"
                                                                        class="form-check-input sell_status"
                                                                        {{ $po->status == 1 ? 'checked' : '0' }} />


                                                                    <input type="hidden" name="sell_status[]"
                                                                        id="sell_status_input-0"
                                                                        class="form-control sell_status_input"
                                                                        value="0" />

                                                                </div> --}}
                                                        {{-- <div class="col"> --}}
                                                        <input type="text"
                                                            class="form-control productname typeahead"
                                                            name="part_number[]" value="{{ $po->part_number }}"
                                                            placeholder="{{ trans('Enter Part Number') }}"
                                                            id='productname-0' autocomplete="off">
                                                        <input type="hidden"
                                                            class="form-control result_item_name typeahead result_item_name"
                                                            name="result_item_name[]" id="result_item_name-0"
                                                            autocomplete="off" value="{{ $po->product_name }}">
                                                        <input type="hidden"
                                                            class="form-control result_descriptions typeahead descriptions"
                                                            name="result_descriptions[]" id="result_descriptions-0"
                                                            autocomplete="off">
                                                        <input type="hidden"
                                                            class="form-control result_product_code typeahead result_product_code"
                                                            name="result_product_code[]" id="result_product_code-0"
                                                            autocomplete="off">
                                                        <input type="hidden"
                                                            class="form-control result_id typeahead result_id"
                                                            name="result_id[]" id="result_id-0"
                                                            value="{{ $po->variation_id }}" autocomplete="off">
                                                        <input type="hidden"
                                                            class="form-control item_id typeahead item_id"
                                                            name="item_id[]" value="{{ $po->item_id }}"
                                                            id="item_id-0" autocomplete="off">
                                                        {{-- </div>
                                                            </div> --}}

                                                    </td>

                                                    <td><input type="text"
                                                            class="form-control description typeahead"
                                                            value="{{ $po->description }}" name="part_description[]"
                                                            placeholder="{{ trans('') }}" id='description-0'
                                                            autocomplete="off"></td>

                                                    <td><input type="text" class="form-control req amnt"
                                                            name="product_qty[]" id="amount-{{ $key }}"
                                                            autocomplete="off" value="{{ $po->product_qty }}"><input
                                                            type="hidden" id="alert-{{ $key }}"
                                                            name="alert[]">
                                                    </td>
                                                    <td><input type="text" class="form-control item_unit "
                                                            name="item_unit[]" id="item_unit-0" autocomplete="off"
                                                            value="{{ $po->unit }}">
                                                    </td>


                                                    <td><input type="text" class="form-control price"
                                                            name="product_price[]" id="price-{{ $key }}"
                                                            autocomplete="off" value="{{ $po->product_price }}">
                                                    </td>

                                                    <td><input type="text" class="form-control vat"
                                                            name="discount[]" id="vat-0" autocomplete="off"
                                                            value="{{ $po->discount }}">
                                                    </td>


                                                    <td><input type="text" class="form-control exp_date "
                                                            name="exp_date[]" id="exp_date-0"
                                                            value="{{ $po->exp_date }}" autocomplete="off">
                                                    </td>

                                                    <td style="display: none;"><input type="text"
                                                            class="form-control warehouse " name="warehouse[]"
                                                            id="warehouse-0" autocomplete="off"
                                                            value="{{ $po->warehouse }}">
                                                    </td>


                                                    <td style="text-align:center">
                                                        <strong>
                                                            <span class='ttlText1' id="foc-0"></span>
                                                        </strong>
                                                    </td>


                                                    <td style="width: 5%;"><button type="submit"
                                                            class="btn btn-danger remove_item_btn"
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
                                                    <button type="button" class="btn btn-success" id="addproduct"
                                                        style="margin-top:20px;margin-bottom:20px;">
                                                        <i class="fa fa-plus-square"></i>
                                                        {{ trans('Add row') }}
                                                    </button>
                                                    <button type="button" class="btn btn-primary" id="calculate">
                                                        Calculate
                                                    </button>

                                                    <a href="{{ URL('items') }}" target="_blank" id="item_search">
                                                        <button type="button" class="btn btn-success">
                                                            <i class="fa fa-plus-square"></i> Item Search
                                                        </button></a>




                                                </td>
                                                <td colspan="6"></td>
                                                <br><br>

                                            </tr>


                                            <tr class="sub_c" style="display: table-row;">
                                                <td colspan="2">
                                                    @if (isset($employees[0]))
                                                        {{ trans('general.employee') }}
                                                        <select name="user_id" class="selectpicker form-control">
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
                                            <tr class="sub_c" style="display: table-row;">
                                                <td colspan="2">

                                                </td>
                                                <td colspan="3" align="right"><strong>Sub Total
                                                    </strong>
                                                </td>
                                                <td align="left" colspan="2" class="col-md-4"><input
                                                        type="text" name="sub_total" class="form-control"
                                                        id="invoiceyoghtml" readonly style="background-color: #E9ECEF"
                                                        value="{{ $purchase_orders->sub_total }}">

                                                </td>

                                            </tr>
                                            <tr class="sub_c" style="display: table-row;">
                                                <td colspan="2">

                                                </td>
                                                <td colspan="3" align="right"><strong>Overall Discount
                                                    </strong>
                                                </td>
                                                <td align="left" colspan="2" class="col-md-4"><input
                                                        type="text" name="total_discount" class="form-control"
                                                        id="total_discount"
                                                        value="{{ $purchase_orders->discount_total }}">

                                                </td>

                                            </tr>
                                            <tr class="sub_c" style="display: table-row;">
                                                <td colspan="2">

                                                </td>
                                                <td colspan="3" align="right"><strong>Item Discount
                                                    </strong>
                                                </td>
                                                <td align="left" colspan="2" class="col-md-4"><input
                                                        type="text" class="form-control" id="item_discount"
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
                                                        type="text" name="total" class="form-control"
                                                        id="total_total" readonly style="background-color: #E9ECEF"
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
                                                        <input type="text" name="payment_amount[]"
                                                            class="form-control payment_amount" id="payment_amount"
                                                            value="{{ $payment->payment_amount }}" required>
                                                    </td>
                                                    <td align="left" colspan="1"
                                                        class="col-md-2 payment_method">
                                                        <div class="input-group">
                                                            <select name="payment_method[]" id="payment_method"
                                                                class="form-control" required>
                                                                <option value="Cash"
                                                                    {{ $payment->payment_method === 'Cash' ? 'selected' : '' }}>
                                                                    Cash</option>
                                                                <option value="K Pay"
                                                                    {{ $payment->payment_method === 'K Pay' ? 'selected' : '' }}>
                                                                    K Pay</option>
                                                                <option value="Wave"
                                                                    {{ $payment->payment_method === 'Wave' ? 'selected' : '' }}>
                                                                    Wave</option>
                                                                <option value="Others"
                                                                    {{ $payment->payment_method === 'Others' ? 'selected' : '' }}>
                                                                    Others</option>
                                                            </select>
                                                            <div class="input-group-append">
                                                                @if ($index === 0)
                                                                    <button type="button" id="addRow"
                                                                        class="btn btn-primary">
                                                                        <i class="fa-solid fa-plus"></i>
                                                                    </button>
                                                                @else
                                                                    <button class="removeRow btn btn-danger"><i
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
                                                        <input type="text" name="payment_amount[]"
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
                                            <td align="left" colspan="2"><input type="text" name="paid"
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
                                            <td colspan="12"> <label for="remark">Remark</label>
                                                <textarea name="remark" id="remark" class="form-control" rows="2">{{ $purchase_orders->remark }}</textarea>

                                            </td>
                                        </tr>
                                        <tr class="sub_c " style="display: table-row;">


                                            <td align="right" colspan="9">

                                                <button id="submitButton" class="mt-3 btn btn-primary"
                                                    type="submit">Save</button>


                                                <a href="{{ url('purchase_order_manage') }}" type="submit"
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
        $(document).ready(function() {
            let count = 0;

            function initializeTypeahead(count) {
                $('#productname-' + count).typeahead({
                    source: function(query, process) {
                        if (!$('#sell_status-' + count).is(':checked')) {
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
                                    const formattedData = data.map(function(item) {
                                        return {
                                            item_name: item.item_name,
                                            description: item.description,
                                            product_code: item.product_code,
                                            id: item.id,
                                            item_id: item.item_id,
                                            display: item.item_name + ' (' + item
                                                .description + ' - ' + item
                                                .product_code + ')'
                                        };
                                    });
                                    process(formattedData);
                                }
                            });
                        }
                    },
                    displayText: function(item) {
                        return item.display;
                    },
                    afterSelect: function(item) {
                        $('#result_descriptions-' + count).val(item.description);
                        $('#result_product_code-' + count).val(item.product_code);
                        $('#result_item_name-' + count).val(item.item_name);
                        $('#result_id-' + count).val(item.id);
                        $('#item_id-' + count).val(item.item_id);
                    },
                    autoSelect: true
                });
            }

            function initializeTypeaheads() {
                for (let i = 0; i <= count; i++) {
                    initializeTypeahead(i);
                }
            }




            function updateItemName(item_name, row, description, product_code, cuz_name) {
                let itemNameInput = row.find('.price');
                let partDesc = row.find('.description');
                let exp_date = row.find('.exp_date');
                let item_unit = row.find('.item_unit');
                let warehouse = row.find('.warehouse');
                var Selectedlocation = $('#location').val();

                $.ajax({
                    type: 'POST',
                    url: "{{ route('get-part-data-invoice') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        result_item_name: item_name,
                        result_descriptions: description,
                        result_product_code: product_code,
                        location: Selectedlocation,
                    },
                    success: function(data) {
                        itemNameInput.val(data.buy_price);
                        partDesc.val(data.descriptions);
                        exp_date.val(data.expired_date);
                        item_unit.val(data.item_unit);
                        warehouse.val(data.warehouse_id);
                        if (parseFloat(data.reorder_level_stock) >= parseFloat(data.quantity)) {
                            alert(data.quantity + " quantity!");
                        }
                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
            }


            $("#addproduct").click(function(e) {
                e.preventDefault();
                count++;
                let rowCount = $("#showitem123 tr").length + 0;
                let newRow = '<tr>' +
                    '<td class="text-center">' + (rowCount + 1) + '</td>' +
                    '<td>' +
                    '<input type="text" class="form-control productname typeahead item_name" name="part_number[]" id="productname-' +
                    count +
                    '" autocomplete="off" placeholder="Enter Part Number"><input type="hidden" class="form-control result_item_name typeahead result_item_name" name="result_item_name[]" id="result_item_name-' +
                    count +
                    '" autocomplete="off"><input type="hidden" class="form-control result_descriptions typeahead result_descriptions" name="result_descriptions[]" id="result_descriptions-' +
                    count +
                    '" autocomplete="off"><input type="hidden" class="form-control result_product_code typeahead result_product_code" name="result_product_code[]" id="result_product_code-' +
                    count +
                    '" autocomplete="off"><input type="hidden" class="form-control result_id typeahead result_id" name="result_id[]" id="result_id-' +
                    count +
                    '" autocomplete="off"><input type="hidden" class="form-control item_id typeahead item_id" name="item_id[]" id="item_id-' +
                    count + '" autocomplete="off">' +
                    '</td>' +
                    '<td><input type="text" class="form-control description typeahead" name="part_description[]"  id="description-' +
                    count + '" autocomplete="off"></td>' +
                    '<td><input type="text" class="form-control req amnt" name="product_qty[]" id="amount-' +
                    count +
                    '"   autocomplete="off" value="1"><input type="hidden" id="alert-0" value="" name="alert[]"></td>' +
                    '<td><input type="text" class="form-control item_unit" name="item_unit[]" id="item_unit-' +
                    count +
                    '"   autocomplete="off"></td>' +
                    '<td><input type="text" class="form-control price" name="product_price[]" value="0" id="price-' +
                    count + '"   autocomplete="off"></td>' +

                    '<td><input type="text" class="form-control vat" name="discount[]" value="0" id="vat-' +
                    count + '"   autocomplete="off"></td>' +
                    '<td><input type="text" class="form-control exp_date" name="exp_date[]" id="exp_date-' +
                    count + '"   autocomplete="off"></td>' +
                    '<td style="display : none;"><input type="text" class="form-control warehouse " name="warehouse[]" id="warehouse-' +
                    count +
                    '"   autocomplete="off"></td>' +

                    '<td style="text-align:center"><span class="currenty"></span><strong><span id="result-' +
                    count + '">0</span></strong></td>' +
                    '<td style="width: 5%;"><button type="submit" class="btn btn-danger remove_item_btn" id="removebutton">Remove</button></td>' +
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
            <select name="payment_method[]" class="form-control" required>
                <option value="Cash">Cash</option>
                <option value="K Pay">K Pay</option>
                <option value="Wave">Wave</option>
                <option value="Others">Others</option>
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

            $(document).on('click', '.typeahead .dropdown-item', function(e) {
                e.preventDefault();

                if ($("#customer").val()) {} else {
                    const row = $(this).closest('tr');

                    const item_name = row.find('.result_item_name').val();
                    const description = row.find('.result_descriptions').val();
                    const product_code = row.find('.result_product_code').val();
                    let cuz_name = $("#type").val();
                    updateItemName(item_name, row, description, product_code, cuz_name);
                    $('#productname').val('');
                }
            });
            // Initialize typeahead for the first row
            initializeTypeahead(count);

            $(document).ready(function() {
                function calculateTotals() {
                    let salePriceCategory = $('#sale_price_category').val();

                    let total = 0;
                    let totalTax = 0;
                    let totalTotal = 0;
                    let itemDiscount = 0;

                    $('#showitem123 tr').each(function() {
                        let row = $(this);
                        let qty = parseInt(row.find('.req.amnt').val()) || 0;
                        var price = parseFloat(row.find('.price').val()) || 0;
                        let discount = parseFloat(row.find('.vat').val()) || 0;
                        let itemTotal = qty * price;
                        totalTotal += itemTotal;


                        if (!isNaN(discount) && discount >= 0) {
                            let itemTax = itemTotal - discount;
                            totalTax += itemTax;
                        }

                        if (!isNaN(discount) && discount > 0) {
                            let discountAmount = itemTotal - discount;
                            itemTotal = discountAmount;
                        }

                        total += itemTotal;
                        itemDiscount += discount;


                        row.find('.ttlText1').text(itemTotal);

                    });


                    let paid = parseFloat(document.getElementById("paid").value) ||
                        0;
                    let total_p = parseFloat(document.getElementById("total_total").value) ||
                        0;
                    let total_discount = parseFloat(document.getElementById("total_discount").value) ||
                        0;


                    let totalDiscount = total - total_discount;
                    let balance = total - paid - total_discount;


                    $("#balance").val(balance);
                    $("#item_discount").val(itemDiscount);
                    $('#invoiceyoghtml').val(totalTotal);
                    $('#total_total').val(totalDiscount);
                }

                // Bind function to button click
                $(document).on("click", '#calculate', function(e) {
                    e.preventDefault();
                    calculateTotals();
                });

                // Automatically run on page load
                calculateTotals();


            });

            function paidFunction() {
                let paid = document.getElementById("paid").value;
                let total_p = document.getElementById("invoiceyoghtml").value;
                let balance = total_p - paid;
                $("#balance").val(balance);
            }
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
        document.getElementById('submit-data').addEventListener('click', function() {
            var serviceType = document.getElementById('service_type').value;
            var serviceTypeError = document.getElementById('serviceTypeError');

            if (serviceType === '') {
                serviceTypeError.style.display = 'block';
            } else {
                serviceTypeError.style.display = 'none';
                // Add your code to handle the submission without a form
            }
        });
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
            let totalVAT = 0;
            $(".vat").each(function() {
                totalVAT += parseFloat($(this).val()) || 0;
            });
            let total = subtotal - totalDiscount - totalVAT;
            $("#total_total").val(total);
        });
    </script>
    <script>
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
