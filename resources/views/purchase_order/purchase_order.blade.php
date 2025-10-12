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
            right: 12px;
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

    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mt-3">
            <h1>Purchase Order</h1>

        </div>
        <form method="post" id="data_form" action=" {{ URL('purchase_order_store') }}" enctype="multipart/form-data">
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
                        style="font-weight:bolder">{{ trans('Purchase Order Number') }}</label>

                    <div class="input-group">
                        <div class="input-group-addon"><span class="icon-file-text-o" aria-hidden="true"></span>
                        </div>
                        <input type="text" id="invoice_number" name="po_number" class="form-control round"
                            value="{{ $po_no }}" readonly>
                    </div>
                </div>




                <div class="mt-3 col-md-3">
                    <label for="invociedate" class="mt-1 caption"
                        style="font-weight:bolder">{{ trans('Purchase Order Date') }}</label>

                    <div class="mb-2 input-group">
                        <div class="input-group-addon"><span class="icon-calendar4" aria-hidden="true"></span>
                        </div>

                        <input type="date" name="po_date" id="invoice_date" class="form-control round "
                            autocomplete="off" max="<?php echo date('Y-m-d'); ?>" value="<?php echo date('Y-m-d'); ?>" required>


                    </div>
                </div>

                <div class="mt-3 col-md-3">
                    <label for="overdue" class="mt-1 caption"
                        style="font-weight:bolder">{{ trans('Payment OverDue Date') }}</label>
                    <div class="mb-2 input-group">
                        <div class="input-group-addon"><span class="icon-calendar4" aria-hidden="true"></span>
                        </div>

                        <input type="date" name="overdue_date" id="overdue_date" class="form-control round "
                            autocomplete="off" min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>">


                    </div>
                </div>

                <div class="frmSearch mt-3 col-md-3" style="display: none;">
                    <div class="frmSearch col-sm-12">
                        <div class="frmSearch col-sm-12">
                            <span style="font-weight:bolder">
                                <label for="cst" class="caption mt-1">Receiving Mode
                                </label>
                            </span>
                            <select name="balance_due" id="balance_due" class="mb-4 form-control balance_due" required>

                                <option value="PO">PO</option>
                                {{-- <option value="Sale Return Invoice">Sale Return</option> --}}

                            </select>

                            <div id="customer-box-result"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-3  col-md-3 " id="supplier_box">
                    <span style="font-weight:bolder">
                        <label for="supplier_id" class="caption mt-1">{{ trans('Supplier Name') }}</label>
                    </span>
                    <select name="supplier_id" id="supplier_id" class="form-control">
                        <option value="" selected disabled>Choose Supplier
                        </option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" data-branch="{{ $supplier->branch }}"
                                data-id="{{ $supplier->id }}">
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>

                </div>


                <div class="frmSearch col-sm-3 mb-3" id="">
                    <span style="font-weight:bolder">
                        <label for="internal_register_number"
                            class="caption">{{ trans('Internal Register Number') }}</label>
                    </span>
                    <input type="text" id="internal_register_number" name="internal_register_number"
                        class="form-control round">
                </div>

                <div class="frmSearch col-sm-3 mb-3" id="">
                    <span style="font-weight:bolder">
                        <label for="supplier_register_number"
                            class="caption">{{ trans('Supplier Register Number') }}</label>
                    </span>
                    <input type="text" id="supplier_register_number" name="supplier_register_number"
                        class="form-control round">
                </div>

                <div class="frmSearch col-sm-3 mb-3" id="">
                    <span style="font-weight:bolder">
                        <label for="container_register_number"
                            class="caption">{{ trans('Container Register Number') }}</label>
                    </span>
                    <input type="text" id="container_register_number" name="container_register_number"
                        class="form-control round">
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
                                <div class="col-sm-12 cmp-pnl">
                                    <div id="customerpanel" class="inner-cmp-pnl">

                                        <div class="form-group row">

                                            <div class="frmSearch col-md-6 col-lg-4">
                                                <div class="frmSearch col-sm-12">
                                                    <span style="font-weight:bolder">
                                                        <label for="cst"
                                                            class="caption">{{ trans('Search Product Name ') }}&nbsp;</label>
                                                    </span>
                                                    <input type="text"
                                                        class="form-control productname typeahead auto_calculate"
                                                        name="itemname" id='productname' autocomplete="off"
                                                        placeholder="Enter Product Name">

                                                    <input type="hidden"
                                                        class="form-control result_product_name typeahead result_product_name"
                                                        name="result_product_name[]" id="result_product_name-0"
                                                        autocomplete="off">
                                                    <input type="hidden" id="result_item_id">
                                                    <input type="hidden" id="result_variation_id">
                                                    <!-- <input type="hidden"
                                                        class="form-control result_product_code typeahead result_product_code"
                                                        name="result_product_code[]" id="result_product_code-0"
                                                        autocomplete="off">  -->

                                                    <div id="customer-box-result"></div>
                                                </div>

                                            </div>

                                            @if (auth()->user()->is_admin == '1')
                                                <div class="frmSearch col-md-3">
                                                    <div class="frmSearch col-sm-12">
                                                        <span style="font-weight:bolder">
                                                            <label for="cst"
                                                                class="caption">{{ trans('Location') }}&nbsp;</label>
                                                        </span>
                                                        <select name="location" id="location"
                                                            class="mb-4 form-control location" required>

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
                                                            <label for="cst"
                                                                class="caption">{{ trans('Location') }}&nbsp;</label>
                                                        </span>
                                                        <select name="location" id="location"
                                                            class="mb-4 form-control location" required>

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




                                        </div>
                                    </div>
                                    <div class="col-sm-6 cmp-pnl">
                                        <!-- <div class="inner-cmp-pnl">

                                        </div> -->
                                    </div>
                                </div>



                                <input type="hidden" value="invoice" name="status">

                                <div class="row ">
                                    <!-- <table class="table-responsive tfr my_stripe"> -->
                                    <table class="table mt-3">
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
                                            <tr>
                                                <td class="text-center" id="count">1</td>

                                                <td>

                                                    {{-- <div class="row align-items-center"> --}}
                                                    {{-- <div class="col-auto">
                                                            <input type="checkbox" id="sell_status-0" value="1"
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
                                                        placeholder="{{ trans('') }}" id="item_name-0"
                                                        autocomplete="off">
                                                    <input type="hidden"
                                                        class="form-control result_item_name typeahead result_item_name"
                                                        name="result_item_name[]" id="result_item_name-0"
                                                        autocomplete="off">
                                                    <!-- <input type="hidden"
                                                        class="form-control result_descriptions typeahead descriptions"
                                                        name="result_descriptions[]" id="result_descriptions-0"
                                                        autocomplete="off"> -->
                                                    <input type="hidden"
                                                        class="form-control result_product_code typeahead"
                                                        name="result_product_code[]" id="result_product_code-0"
                                                        autocomplete="off">
                                                    <input type="hidden" class="form-control result_id typeahead"
                                                        name="result_id[]" id="result_id-0" autocomplete="off">
                                                    <input type="hidden" class="form-control typeahead item_id"
                                                        name="item_id[]" id="item_id-0" autocomplete="off">



                                                    <input type="hidden" class="form-control model typeahead"
                                                        name="model[]" id="model-0" autocomplete="off">

                                                    <input type="hidden" class="form-control colour typeahead"
                                                        name="colour[]" id="colour-0" autocomplete="off">


                                                    {{-- </div>
                                                    </div> --}}

                                                </td>

                                                <td style="display: none;"><input type="text"
                                                        class="form-control description typeahead"
                                                        value="{{ old('part_description') }}"
                                                        name="part_description[]" placeholder="{{ trans('') }}"
                                                        id='description-0' autocomplete="off"></td>

                                                <td><input type="number" class="form-control req amnt auto_calculate"
                                                        name="product_qty[]" id="amount-0" autocomplete="off"
                                                        value="1"><input type="hidden" id="alert-0"
                                                        value="" name="alert[]"></td>

                                                <td>
                                                    <input type="text" name="item_unit[]"
                                                        class="form-control item_unit" id="item_unit-0">
                                                </td>

                                                <td><input type="text" class="form-control price auto_calculate"
                                                        name="product_price[]" id="price-0" autocomplete="off"
                                                        value="0">
                                                </td>

                                                <td>
                                                    <div class="input-group">
                                                        <input type="hidden" name="discount_category[]"
                                                            value="percent" class="discount_category"
                                                            id="discount_category-0">
                                                        <input type="number" name="discount_amt[]" value="0"
                                                            class="discount_amt" id="discount_amt-0" hidden>
                                                        <input type="number" class="form-control vat auto_calculate"
                                                            name="discount[]" id="vat-0" value="0"
                                                            autocomplete="off">
                                                        <div class="input-group-append">
                                                            <button type="button" class="btn btn-danger discount_cat"
                                                                data-val="0">
                                                                %
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td><input type="text" class="form-control exp_date "
                                                        name="exp_date[]" id="exp_date-0" autocomplete="off">
                                                </td>

                                                <td style="display: none;"><input type="text"
                                                        class="form-control warehouse " name="warehouse[]"
                                                        id="warehouse-0" autocomplete="off">
                                                </td>

                                                <td style="text-align:center">
                                                    <span class='ttlText' id="foc-0"></span>
                                                    <span class="currenty">{{ config('currency.symbol') }}</span>
                                                    <strong>
                                                        <span class='ttlText' id="result-0"></span>
                                                    </strong>
                                                </td>

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


                                                    @if (in_array('Item', $choosePermission) || auth()->user()->is_admin == '1')
                                                        <a href="{{ URL('items') }}" target="_blank"
                                                            id="item_search">
                                                            <button type="button" class="btn btn-danger">
                                                                <i
                                                                    class="fa-solid fa-magnifying-glass-plus"></i></i>Product
                                                                Search
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
                                                    <input type="number" name="total_discount" class="form-control"
                                                        id="total_discount">
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
                                                        id="total_total" readonly style="background-color: #E9ECEF">

                                                </td>

                                            </tr>

                                        <tbody id="trContainer">
                                            <tr class="sub_c">
                                                <td colspan="2"></td>
                                                <td colspan="3" align="right"><strong>Payment
                                                        Method</strong></td>
                                                <td align="left" colspan="1" class="col-md-2">
                                                    <input type="number" name="payment_amount[]"
                                                        class="form-control payment_amount" id="payment_amount"
                                                        required>
                                                </td>
                                                <td align="left" colspan="1" class="col-md-2 payment_method">
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
                                            <td align="left" colspan="2"><input type="number" name="paid"
                                                    class="form-control" id="paid" onchange="paidFunction()">
                                            </td>

                                        </tr>
                                        <tr class="sub_c" style="display: table-row;">
                                            <td colspan="2">

                                            </td>
                                            <td colspan="3" align="right"><strong>Remaining Balance
                                                </strong>
                                            </td>
                                            <td align="left" colspan="2"><input type="text" name="balance"
                                                    class="form-control" id="balance" readonly="">

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
                                                <a href="{{ url('purchase_order_manage') }}"
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

        </form>
    </div>

    </div>

    <script>
        $(document).ready(function() {
            let lastInvoiceNo = ""; // Store the last received invoice number
            let intervalId; // To store the interval ID for clearing it later

            function fetchInvoiceUpdates() {
                const locationId = $("#location").val(); // Get value of location input
                if (!locationId) {
                    console.error("Location ID is empty");
                    return;
                }

                fetch("{{ url('po_no_updates') }}?location_id=" + encodeURIComponent(locationId))
                    .then(response => response.json()) // Directly parse JSON
                    .then(data => {
                        const invoiceNoInput = $("#invoice_number"); // Get the invoice number input

                        // Update only if invoice_no has changed
                        if (data.invoice_no && data.invoice_no !== lastInvoiceNo) {
                            lastInvoiceNo = data.invoice_no; // Update last received invoice
                            invoiceNoInput.val(data.invoice_no);

                            // Stop further updates after the first update
                            clearInterval(intervalId);
                        }
                    })
                    .catch(error => console.error("Failed to fetch invoice updates:", error));
            }

            // Fetch invoice number every 3 seconds initially
            intervalId = setInterval(fetchInvoiceUpdates, 500);

            // Optional: You can trigger an update when the location ID changes
            $("#location").change(function() {
                lastInvoiceNo = ""; // Reset the lastInvoiceNo to force a fetch
                fetchInvoiceUpdates(); // Fetch immediately after location change
            });
        });

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
            var balance_due = $("#balance_due").val();
            $('#payment_method-' + payment_count).html(
                '<option value="">Loading...</option>');
            if (locationId) {
                $.ajax({
                    url: '{{ route('get_accounts_transaction_po') }}',
                    type: 'GET',
                    data: {
                        locationId: locationId,
                        balance_due: balance_due,
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
        function handleKeyUp(event) {
            console.log(`Key pressed: ${event.key}`);
            // You can add more logic here to respond to the event
        }
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
                var $expDate0 = $("#exp_date-0");
                var $partDesc0 = $("#description-0");
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
                                // description: description,
                                // product_code: product_code
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

                    let unitprice = parseFloat(variation['buy_price']) || 0;


                    // if (item_barcode.length > 1) {
                    displayName = item.item_name + ' (' + (variation.product_code ? variation.product_code + ' - ' :
                            '') +
                        (variation.model ? variation.model + ' - ' : '') +
                        (variation.colour ? variation.colour : '') + (variation.size ? ' - ' + variation.size :
                            '') +
                        (variation.seater ? ' - ' + variation.seater : '') + ') (' + (variation.variations_desc ?
                            variation.variations_desc : '') + ')';

                    // } else {
                    // var displayName = productname || "";
                    // }
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

                        '<td><input type="text" class="form-control price auto_calculate" name="product_price[]" id="price-' +
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

                let payment_count = 1;
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
                                            <select name="payment_method[]" class="form-control"  id="payment_method-${payment_count}"  required>
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


                //currency change
                // $('#currency_method').on('change',function(){
                //     if($(this).val() != 'MMK'){
                //         let exchange_rate = parseFloat($('#exchange_rate').val()) || 1;
                //         calculateOtherCurrency(exchange_rate);
                //     }else{
                //         $("#invoiceyoghtml").val($("#invoiceyoghtml").attr('data-val'));
                //         $("#total_discount").val($("#total_discount").attr('data-val'));
                //         $("#item_discount").val($("#item_discount").attr('data-val'));
                //         $("#total_total").val($('#total_total').attr('data-val'));
                //         $("#paid").val('');
                //         $("#balance").val('');
                //     }
                // });

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
                $(document).on('click', '.discount_cat', function() {
                    let id_val = $(this).data('val');
                    console.log(id_val);
                    if ($(this).text() == '$') {
                        $('#discount_category-' + id_val).val('percent');
                        // $('#vat-'+id_val).data('val','percent');
                        $(this).text('%');
                        autoCalculate();
                    } else {
                        $(this).text('$');
                        // $('#vat-'+id_val).data('val','kyat');
                        $('#discount_category-' + id_val).val('kyat');
                        autoCalculate();
                    }
                });

            });

            function calculateOtherCurrency(exchange_rate) {
                let subtotal_val = parseFloat($("#invoiceyoghtml").attr('data-val')) || 0;
                let total_discount_val = parseFloat($("#total_discount").attr('data-val')) || 0;
                let item_discount_val = parseFloat($("#item_discount").attr('data-val')) || 0;
                let total_val = parseFloat($('#total_total').attr('data-val')) || 0;
                let deposit_val = parseFloat($('#paid').attr('data-val')) || 0;
                let balance_due_val = parseFloat($('#balance').attr('data-val')) || 0;

                $("#invoiceyoghtml").val((subtotal_val / exchange_rate).toFixed(2));
                $("#total_discount").val((total_discount_val / exchange_rate).toFixed(2));
                $("#item_discount").val((item_discount_val / exchange_rate).toFixed(2));
                $("#total_total").val((total_val / exchange_rate).toFixed(2));
                // $("#paid").val((deposit_val / exchange_rate).toFixed(2));
                // $("#balance").val((balance_due_val / exchange_rate).toFixed(2));
            }

            $(document).on('click', '.typeahead .dropdown-item', function(e) {
                e.preventDefault();

                if ($("#customer").val()) {} else {
                    const row = $(this).closest('tr');

                    // const item_name = row.find('.result_item_name').val();
                    // const description = row.find('.result_descriptions').val();
                    // const product_code = row.find('.result_product_code').val();
                    // let cuz_name = $("#type").val();

                    const item_name = $('#result_product_name-0').val();
                    const productname = $('#productname').val();
                    let cuz_name = $("#type").val();
                    let item_id = $('#result_item_id').val();
                    let variation_id = $('#result_variation_id').val();

                    // updateItemName(item_name, row, description, product_code, cuz_name);
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

            // $(document).on("click", '#calculate', function(e) {
            function autoCalculate() {
                // e.preventDefault();
                let total = 0;
                let total_purchase = 0;
                let totalTax = 0;
                let total_discount = 0;
                // let total_foc = 0;

                for (let i = 0; i < (count + 1); i++) {
                    var qty = parseInt($('#amount-' + i).val() || 0);
                    var item_name = $('#productname-' + i).val() || 0;
                    var unit_m3 = $('#unit_m3-' + i).val() || 0;
                    var unit_kg = $('#unit_kg-' + i).val() || 0;
                    var buy_price = parseFloat($('#buy_price-' + i).val() || 0);
                    let discount = 0;

                    var price = parseFloat($('#price-' + i).val() || 0);

                    let taxRate = parseFloat($('#vat-' + i).val() || 0);

                    let total_unit_m3 = Math.round(unit_m3 * qty);
                    let total_unit_kg = Math.round(unit_kg * qty);
                    let total_amount = Math.round(price * qty);

                    $('#total_unit_m3-' + i).val(total_unit_m3);
                    $('#total_unit_kg-' + i).val(total_unit_kg);

                    if (!isNaN(taxRate) && taxRate > 0) {
                        // discount = taxRate;
                        // $("#result-" + i).text((price * qty) - discount);
                        let discount_cat = $('#discount_category-' + i).val();

                        if (discount_cat == 'percent') {
                            discount = Math.round(total_amount * (taxRate / 100));
                        } else {
                            discount = taxRate;
                        }
                        $("#result-" + i).text(total_amount - discount);
                        $('#discount_amt-' + i).val(discount);
                    } else {
                        // $("#result-" + i).text(price * qty);
                        $("#result-" + i).text(total_amount);
                        $('#discount_amt-' + i).val(taxRate);
                    }

                    // total += price * qty;
                    total += total_amount;
                    total_purchase += buy_price * qty;
                    total_discount += discount;
                    totalTax += discount;

                    //check foc
                    // if(foc == 'Yes'){
                    //     total_foc += total_amount;
                    // //substract item discount if foc is no
                    // }else{
                    //     total_discount += discount;
                    //     totalTax += discount;
                    // }

                }

                let taxt = total
                taxt = Math.ceil(taxt);

                let total_discount_val = $('#total_discount').attr('data-val') ?? $('#total_discount').val();

                let total_total = total - total_discount;
                // let total_dis = parseFloat(total_total - $('#total_discount').val());
                let total_dis = parseFloat(total_total - total_discount_val);

                //  //to calculate currency
                // $("#invoiceyoghtml").attr('data-val',total);
                // $("#item_discount").attr('data-val',totalTax);
                // $("#total_buy_price").attr('data-val',total_purchase);
                // $("#commercial_text").attr('data-val',taxt);
                // $("#total").attr('data-val',total_total);
                // $('#total_total').attr('data-val',total_dis);

                // console.log($('#currency_method').val());
                // if($('#currency_method').val() != 'MMK'){
                //     let ex_rate = $('#exchange_rate').val();
                //     calculateOtherCurrency(ex_rate);
                // }else{
                $("#invoiceyoghtml").val(total);
                $("#item_discount").val(totalTax);
                $("#total_buy_price").val(total_purchase);
                $("#commercial_text").val(taxt);
                $("#total").val(total_total);
                $('#total_total').val(total_dis);
                // }
            }


            function paidFunction() {
                let paid = document.getElementById("paid").value;
                let total_p = document.getElementById("invoiceyoghtml").value;
                let balance = total_p - paid;
                $("#balance").val(balance);
            }

            document.getElementById('submitButton').addEventListener('click', function() {
                var serviceType = document.getElementById('unit-' + count).value;
                var serviceTypeError = document.getElementById('uniterror');
                if (serviceType === 'Choose Unit') {
                    serviceTypeError.style.display = 'block';
                } else {
                    serviceTypeError.style.display = 'none';
                    // Add your code to handle the submission without a form
                }
            });

        });
    </script>
    <script>
        $(document).on("input", "#total_discount", function() {
            let subtotal = parseFloat($("#invoiceyoghtml").val()) || 0;
            let totalDiscount = parseFloat($("#total_discount").val()) || 0;
            let totalVAT = parseFloat($('#item_discount').val()) || 0;

            let total = subtotal - (totalDiscount + totalVAT);
            $("#total_total").val(total);
            //calculate base on currecncy
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
            $(document).on("change", "#balance_due", function() {
                if ($(this).val() == "PO") { // Check if balance_due is empty
                    $("#supplier_box").show(); // Show the supplier_box
                } else {
                    $("#supplier_box").hide(); // Hide the supplier_box if balance_due is not empty
                }
            });
        });

        $(document).ready(function() {
            $('#location').change(function() {
                var selectedLocation = $(this).val();
                $('#supplier_id option').each(function() {
                    var supplierBranch = $(this).data(
                        'branch');
                    if (supplierBranch == selectedLocation || $(this).val() == '') {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
                $('#supplier_id').val('');
            }).change();
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
