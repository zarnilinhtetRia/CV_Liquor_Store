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
    </style>
</head>

<body>

    <div class="container-fluid">

        <h1 class="mt-3">
            Sale Return
        </h1>
        <form method="post" id="data_form" action=" {{ URL('purchase_order_store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row">


                <div class="mt-3 col-md-3">

                    <label for="invocieno" class="mt-1 caption"
                        style="font-weight:bolder">{{ trans('Sale Return Number') }}</label>

                    <div class="input-group">
                        <div class="input-group-addon"><span class="icon-file-text-o" aria-hidden="true"></span>
                        </div>
                        <input type="text" id="invoice_number" name="po_number" class="form-control round"
                            value="{{ $po_no }}" readonly>
                    </div>
                </div>




                <div class="mt-3 col-md-3">
                    <label for="invociedate" class="mt-1 caption"
                        style="font-weight:bolder">{{ trans('Sale Return Date') }}</label>

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

                {{-- <div class="mt-3 col-md-3">
                    <label for="remark" class="mt-1" style="font-weight:bolder">{{ trans('Payment Method') }}
                    </label>
                    <select class="mb-4 form-control round" aria-label="Default select example" name="payment_method"
                        required>

                        <option value="Cash">Cash</option>
                        <option value="Credit">Credit</option>
                        <option value="Consignment Terms">Consignment Terms</option>
                    </select>
                </div> --}}




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
                                <div class="col-sm-6 cmp-pnl">
                                    <div id="customerpanel" class="inner-cmp-pnl">

                                        <div class="form-group row">





                                            @if (auth()->user()->is_admin == '1')
                                                <div class="frmSearch col-md-4">
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
                                                <div class="frmSearch col-md-4">
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


                                            <div class="frmSearch col-sm-4">
                                                <div class="frmSearch col-sm-12">
                                                    <div class="frmSearch col-sm-12">
                                                        <span style="font-weight:bolder">
                                                            <label for="cst" class="caption">Receiving Mode
                                                            </label>
                                                        </span>
                                                        <select name="balance_due" id="balance_due"
                                                            class="mb-4 form-control balance_due" required>
                                                            <option value="Sale Return">Sale Return</option>

                                                        </select>

                                                        <div id="customer-box-result"></div>
                                                    </div>


                                                </div>



                                            </div>



                                        </div>
                                    </div>
                                    <div class="col-sm-6 cmp-pnl">

                                        <div class="inner-cmp-pnl">

                                        </div>
                                    </div>
                                </div>



                                <input type="hidden" value="invoice" name="status">

                                <div class="row " style="margin-top:1vh;">
                                    <!-- <table class="table-responsive tfr my_stripe"> -->
                                    <table class="table mt-3">
                                        <thead style="background-color:#0047AA;color:white; border: 1px solid white;">
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
                                                        class="form-control productname typeahead item_name"
                                                        name="part_number[]" value="{{ old('part_number') }}"
                                                        placeholder="{{ trans('Enter Part Number') }}"
                                                        id="productname-0" autocomplete="off">
                                                    <input type="hidden"
                                                        class="form-control result_item_name typeahead result_item_name"
                                                        name="result_item_name[]" id="result_item_name-0"
                                                        autocomplete="off">
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
                                                        name="result_id[]" id="result_id-0" autocomplete="off">
                                                    <input type="hidden"
                                                        class="form-control item_id typeahead item_id"
                                                        name="item_id[]" id="item_id-0" autocomplete="off">
                                                    {{-- </div>
                                                        </div> --}}

                                                </td>

                                                <td><input type="text" class="form-control description typeahead"
                                                        value="{{ old('part_description') }}"
                                                        name="part_description[]" placeholder="{{ trans('') }}"
                                                        id='description-0' autocomplete="off"></td>

                                                <td><input type="text" class="form-control req amnt"
                                                        name="product_qty[]" id="amount-0" autocomplete="off"
                                                        value="1"><input type="hidden" id="alert-0"
                                                        value="" name="alert[]"></td>

                                                <td>
                                                    <input type="text" name="item_unit[]"
                                                        class="form-control item_unit" id="item_unit-0">
                                                </td>

                                                <td><input type="text" class="form-control price"
                                                        name="product_price[]" id="price-0" autocomplete="off"
                                                        value="0">
                                                </td>

                                                <td><input type="text" class="form-control vat " name="discount[]"
                                                        id="vat-0" value="0" autocomplete="off"
                                                        value="{{ old('discount') }}">
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
                                                <td align="left" colspan="2" class="col-md-4"><input
                                                        type="text" name="total_discount" class="form-control"
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
                                                    <input type="text" name="payment_amount[]"
                                                        class="form-control payment_amount" id="payment_amount"
                                                        required>
                                                </td>
                                                <td align="left" colspan="1" class="col-md-2 payment_method">
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
                                        </tbody>



                                        <tr class="sub_c" style="display: table-row;">
                                            <td colspan="2">

                                            </td>
                                            <td colspan="3" align="right"><strong>Deposit
                                                </strong>
                                            </td>
                                            <td align="left" colspan="2"><input type="text" name="paid"
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
                                            <td colspan="12"> <label for="remark">Remark</label>
                                                <textarea name="remark" id="remark" class="form-control" rows="2"></textarea>

                                            </td>
                                        </tr>
                                        <tr class="sub_c " style="display: table-row;">


                                            <td align="right" colspan="9">

                                                <button id="submitButton" class="mt-3 btn btn-primary"
                                                    type="submit">Save</button>


                                                <a href="{{ url('sale_return') }}" type="submit"
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
    <script></script>







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


                // Assuming {{ $units }} is a string representation of an array



                count++;
                let rowCount = $("#showitem123 tr").length;
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

                // Function to add a new row
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

            $(document).on("click", '#calculate', function(e) {
                e.preventDefault();
                let total = 0;
                let total_purchase = 0;
                let totalTax = 0;
                let total_discount = 0;

                for (let i = 0; i < (count + 1); i++) {
                    var qty = parseInt($('#amount-' + i).val() || 0);
                    var item_name = $('#productname-' + i).val() || 0;
                    var sel = $('#focsel-' + i).val() || 0;
                    var buy_price = parseFloat($('#buy_price-' + i).val() || 0);
                    let discount = 0;

                    var price = parseFloat($('#price-' + i).val() || 0);

                    let taxRate = parseFloat($('#vat-' + i).val() || 0);

                    if (!isNaN(taxRate) && taxRate > 0) {
                        discount = taxRate;
                        $("#result-" + i).text((price * qty) - discount);
                    } else {
                        $("#result-" + i).text(price * qty);
                    }

                    total += price * qty;
                    total_purchase += buy_price * qty;
                    total_discount += discount;
                    totalTax += discount;

                }

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
            });


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
