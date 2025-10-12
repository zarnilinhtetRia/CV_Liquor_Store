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
    @include('layouts.invoice_style')
    <style>
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
            right: 12px;
            color: black;
            opacity: 1;
        }

        .typeahead.dropdown-menu {
            max-height: 200px;
            overflow-y: auto;
        }
    </style>

</head>
@include('layouts.header')

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand">
            <!-- Left navbar links -->
            <ul class="navbar-nav col-md-6">
                <li class="nav-item">
                    <a class="nav-link text-white" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white" href="#">Date -
                        <?= $currentDate = date('d-m-y') ?></a>
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
        <div class="container-fluid">
            <form action=" {{ url('store_transfer_item') }}" method="POST">
                @csrf
                <div class="content-wrapper">
                    <div class="content-body">
                        <div class="card col-md-11 mx-auto my-4">
                            <div class="card-header">
                                <h3 class="card-title">Transfer Product</h3>
                            </div>
                            <div class="card-content">

                                <div class="card-body">
                                    <div class="col-sm-12 cmp-pnl">
                                        <div id="customerpanel" class="inner-cmp-pnl">

                                            <div class="form-group row">
                                                <div class="frmSearch col-sm-12">
                                                    <div class="row">
                                                        {{-- @if (Auth::user()->is_admin == '1' || Auth::user()->type == 'Admin') --}}

                                                        <div class="frmSearch col-sm-3">
                                                            <label for="from" style="font-weight:bolder">From
                                                                Location</label>
                                                            <select name="from_location" id="from_location"
                                                                class="form-control">

                                                                @if (auth()->user()->is_admin == '1')
                                                                    <option value="" selected disabled>Choose
                                                                        Location
                                                                    </option>
                                                                    @foreach ($warehouses as $warehouse)
                                                                        <option value="{{ $warehouse->id }}">
                                                                            {{ $warehouse->name }}</option>
                                                                    @endforeach
                                                                @else
                                                                    @php
                                                                        $userPermissions = auth()->user()->level
                                                                            ? json_decode(auth()->user()->level)
                                                                            : [];
                                                                    @endphp

                                                                    @foreach ($warehouses as $warehouse)
                                                                        @if (in_array($warehouse->id, $userPermissions))
                                                                            <option value="{{ $warehouse->id }}">
                                                                                {{ $warehouse->name }}
                                                                            </option>
                                                                        @endif
                                                                    @endforeach
                                                                @endif




                                                            </select>
                                                        </div>
                                                        {{-- @else
                                                            <div class="frmSearch col-sm-3">
                                                                <label for="from" style="font-weight:bolder">From
                                                                    Location</label>
                                                                <select name="from_location" id="from_location"
                                                                    class="form-control">
                                                                    <option value="" selected disabled>Choose
                                                                        Location
                                                                    </option>
                                                                    @foreach ($warehouses as $warehouse)
                                                                        @if (auth()->user()->level == $warehouse->id)
                                                                            <option value="{{ $warehouse->id }}">
                                                                                {{ $warehouse->name }}</option>
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        @endif --}}
                                                        <div class="frmSearch col-sm-3">
                                                            <label for="to" style="font-weight:bolder">To
                                                                Location</label>
                                                            <select name="to_location" id="to_location"
                                                                class="form-control">
                                                                <option value="" selected disabled>Choose Location
                                                                </option>
                                                                @foreach ($warehouses as $warehouse)
                                                                    <option value="{{ $warehouse->id }}">
                                                                        {{ $warehouse->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="frmSearch col-sm-3">
                                                            <label for="invoice_date"
                                                                style="font-weight:bolder">{{ trans(' Date') }}</label>

                                                            <div class="input-group mb-2">
                                                                <div class="input-group-addon"><span
                                                                        class="icon-calendar4"
                                                                        aria-hidden="true"></span>
                                                                </div>

                                                                <input type="date" name="date" id="date"
                                                                    class="form-control round " autocomplete="off"
                                                                    max="<?= date('Y-m-d') ?>" required>


                                                            </div>
                                                        </div>
                                                        <div class="frmSearch col-sm-3">
                                                            <label for="transfer_number"
                                                                style="font-weight:bolder">{{ trans(' Transfer Number') }}</label>
                                                            <div class="input-group mb-2">
                                                                <input type="text" name="transfer_number"
                                                                    id="transfer_number" class="form-control round "
                                                                    required readonly>
                                                            </div>
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
                                        <table class="">
                                            <thead>
                                                <tr class="item_header bg-gradient-directional-blue white"
                                                    style="margin-bottom:10px;">
                                                    <th width="5%" class="text-center">{{ trans('No') }}</th>
                                                    <th width="18%" class="text-center">
                                                        {{ trans('Product Name') }}
                                                    </th>
                                                    <th width="18%" class="text-center">
                                                        {{ trans('Total Quantity') }}
                                                    </th>

                                                    <th width="8%" class="text-center">{{ trans('Quantity') }}
                                                    </th>


                                                </tr>

                                            </thead>
                                            <tbody id="showitem123">
                                                <tr>
                                                    <td class="text-center" id="count">1</td>
                                                    <td>

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
                                                            class="form-control result_id typeahead result_id"
                                                            name="result_id[]" id="result_id-0" autocomplete="off">
                                                        <input type="hidden"
                                                            class="form-control item_id typeahead item_id"
                                                            name="item_id[]" id="item_id-0" autocomplete="off">
                                                        <input type="hidden"
                                                            class="form-control product_code typeahead product_code"
                                                            name="product_code[]" id="product_code-0"
                                                            autocomplete="off">

                                                        <input type="hidden"
                                                            class="form-control typeahead stock_type"
                                                            name="stock_type[]" id="stock_type-0" autocomplete="off">
                                                        <input type="hidden" class="form-control desc typeahead desc"
                                                            name="desc[]" id="desc-0" autocomplete="off">
                                                        <input type="hidden"
                                                            class="form-control model typeahead model" name="model[]"
                                                            id="model-0" autocomplete="off">
                                                        <input type="hidden"
                                                            class="form-control colour typeahead colour"
                                                            name="colour[]" id="colour-0" autocomplete="off">
                                                        <input type="hidden" class="form-control size typeahead size"
                                                            name="size[]" id="size-0" autocomplete="off">
                                                        <input type="hidden"
                                                            class="form-control seater typeahead seater"
                                                            name="seater[]" id="seater-0" autocomplete="off">

                                                    </td>
                                                    <td><input type="text" class="form-control total_product_qty"
                                                            name="total_product_qty[]" id="total_amount-0"
                                                            autocomplete="off">
                                                    <td><input type="text" class="form-control req amnt"
                                                            name="product_qty[]" id="amount-0" autocomplete="off"
                                                            value="1"><input type="hidden" id="alert-0"
                                                            value="" name="alert[]"></td>


                                                    <input type="hidden" class="form-control vat "
                                                        name="product_tax[]" id="vat-0" value="0">
                                                    <input type="hidden" name="total_tax[]" id="taxa-0"
                                                        value="0">
                                                    <input type="hidden" name="total_discount[]" id="disca-0"
                                                        value="0">
                                                    <input type="hidden" class="ttInput" name="product_subtotal[]"
                                                        id="total-0" value="0">
                                                    <input type="hidden" class="pdIn" name="product_id[]"
                                                        id="pid-0" value="0">

                                                    <input type="hidden" name="unit_m[]" id="unit_m-0"
                                                        value="1">
                                                    <input type="hidden" name="code[]" id="hsn-0"
                                                        value="">
                                                    <input type="hidden" name="serial[]" id="serial-0"
                                                        value="">
                                                    {{-- <td></td> --}}
                                                </tr>
                                            </tbody>

                                            <tr class="last-item-row sub_c">
                                                <td></td>
                                                <td class="add-row">
                                                    <button type="button" class="btn btn-danger" id="addproduct"
                                                        style="margin-top:30px;margin-bottom:20px;">
                                                        <i class="fa fa-plus-square"></i> {{ trans('Add row') }}
                                                    </button>

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
                                            <tbody id="showitem">
                                                <tr style="display: table-row;">
                                                    <td></td>
                                                    <td colspan="">
                                                    </td>
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




                                                </tr>

                                                <!-- <tr class="sub_c " style="display: table-row;">
                                                <td colspan="12"> <label for="remark">Remark</label>
                                                    <textarea name="remark" id="remark" class="form-control" rows="2"></textarea>

                                                </td>
                                            </tr> -->

                                            </tbody>
                                            </tbody>
                                        </table>

                                        <div>
                                            <textarea name="remark" id="remark" class="form-control" rows="3" placeholder="Enter Remark"></textarea>
                                        </div>

                                        <div class="text-end">
                                            <button id="submitButton" class="mt-3 btn btn-danger"
                                                type="submit">Transfer</button>

                                        </div>
                                    </div>


                                </div>

                            </div>
                        </div>
                    </div>

            </form>
        </div>
    </div>
    </div>
    </div>
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
                var previousSelection = '';
                $('#productname-' + count).typeahead({
                    source: function(query, process) {
                        var Selectedlocation = $('#from_location').val();
                        return $.ajax({
                            url: "{{ url('autocomplete-part-code-location') }}",
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
                                        stock_type: item.stock_type,
                                        brand: item.brand || '',
                                        item_category: item.item_category || '',
                                        display: item.item_name + ' (' + (item
                                            .product_code ? item.product_code +
                                            ' - ' : '') + (
                                            item.model ? item.model + ' - ' : ''
                                        ) + (
                                            item.colour ? item.colour : '') + (
                                            item.size ? ' - ' + item.size : ''
                                        ) + (item
                                            .seater ? ' - ' + item.seater : ''
                                        ) + ' )' + ' (' + ((item
                                            .variation_desc ?
                                            item
                                            .variation_desc : '') + ')'),
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
                        $('#result_id-' + count).val(item.id);
                        $('#item_id-' + count).val(item.item_id);
                        $('#result_item_name-' + count).val(item.item_name);
                        $('#product_code-' + count).val(item.product_code);
                        $('#stock_type-' + count).val(item.stock_type);
                        $('#colour-' + count).val(item.colour);
                        $('#model-' + count).val(item.model);
                        $('#size-' + count).val(item.size);
                        $('#seater-' + count).val(item.seater);
                        $('#brand-' + count).val(item.brand);
                        $('#desc-' + count).val(item.description);
                        console.log(item.item_category);
                    },
                    autoSelect: true,
                    items: 'all'
                });

                $('#from_location').change(function() {
                    var selectedValue = $(this).val();
                    $('#to_location').val('');
                    if (previousSelection !== '') {
                        $('#to_location option[value="' + previousSelection + '"]').show();
                    }
                    $('#to_location option[value="' + selectedValue + '"]').hide();
                    previousSelection = selectedValue;
                });
            }

            function initializeTypeaheads() {
                for (let i = 0; i <= count; i++) {
                    initializeTypeahead(i);
                }
            }



            function updateItemName(row, item_id, variation_id) {
                let itemNameInput = row.find('.price');
                let toal_quantity = row.find('.total_product_qty');
                var selectedLocation = $('#from_location').val();


                $.ajax({
                    type: 'POST',
                    url: "{{ url('get-part-data-location') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        item_id: item_id,
                        variation_id: variation_id,
                        // location: Selectedlocation,
                    },
                    success: function(data) {
                        itemNameInput.val(data.retail_price);
                        toal_quantity.val(data.quantity);
                        console.log(data.quantity);
                        toal_quantity.attr('readonly', true);
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
                    '<td style="width:30%"><input type="text" class="form-control productname typeahead" name="part_number[]" id="productname-' +
                    count +
                    '" autocomplete="off"><input type="hidden" class="form-control result_id typeahead result_id" name="result_id[]" id="result_id-' +
                    count +
                    '" autocomplete="off"><input type="hidden" class="form-control item_id typeahead item_id" name="item_id[]" id="item_id-' +
                    count +
                    '" autocomplete="off"><input type="hidden" class="form-control  typeahead result_item_name" name="result_item_name[]" id="result_item_name-' +
                    count +
                    '" autocomplete="off"><input type="hidden" class="form-control  typeahead product_code" name="product_code[]" id="product_code-' +
                    count +
                    '" autocomplete="off"><input type="hidden" class="form-control  typeahead brand" name="brand[]" id="brand-' +
                    count +
                    '" autocomplete="off"><input type="hidden" class="form-control  typeahead stock_type" name="stock_type[]" id="stock_type-' +
                    count +
                    '" autocomplete="off"><input type="hidden" class="form-control typeahead desc" name="desc[]" id="desc-' +
                    count +
                    '" autocomplete="off"><input type="hidden" class="form-control  typeahead model" name="model[]" id="model-' +
                    count +
                    '" autocomplete="off"><input type="hidden" class="form-control  typeahead colour" name="colour[]" id="colour-' +
                    count +
                    '" autocomplete="off"><input type="hidden" class="form-control  typeahead size" name="size[]" id="size-' +
                    count +
                    '" autocomplete="off"><input type="hidden" class="form-control  typeahead seater" name="seater[]" id="seater-' +
                    count +
                    '" autocomplete="off"></td>' +
                    '<td style=""><input type="text" class="form-control  total_product_qty" name="total_product_qty[]" id="total_amount-' +
                    count +
                    '"   autocomplete="off" ></td>' +

                    '<td style="width:30%"><input type="text" class="form-control req amnt" name="product_qty[]" id="amount-' +
                    count +
                    '"   autocomplete="off" value="1"><input type="hidden" id="alert-0" value="" name="alert[]"></td>' +
                    '<td style="width: 5%;"><button type="submit" class="btn btn-danger remove_item_btn" id="removebutton">Remove</button></td>' +
                    '</tr>';

                $("#showitem123").append(newRow);

                initializeTypeahead(count);
            });

            $(document).on('click', '.remove_item_btn', function(e) {
                e.preventDefault();
                let row_item = $(this).parent().parent();
                $(row_item).remove();

                $('#showitem123 tr').each(function(index) {
                    $(this).find('td:first').text(index + 1);
                });

                initializeTypeaheads();
            });

            $(document).on('click', '.typeahead .dropdown-item', function(e) {
                e.preventDefault();
                const row = $(this).closest('tr');
                const item_id = row.find('.item_id').val();
                const variation_id = row.find('.result_id').val();
                updateItemName(row, item_id, variation_id);
                $('#productname').val('');

            });


            // Initialize typeahead for the first row
            initializeTypeahead(count);





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
    </script>



    <script>
        $("input").on("change", function() {
            if (this.value && moment(this.value, "YYYY-MM-DD").isValid()) {
                this.setAttribute(
                    "data-date",
                    moment(this.value, "YYYY-MM-DD").format("DD/MM/YYYY")
                );
            } else {
                this.setAttribute("data-date", "dd/mm/yyyy");
            }
        }).trigger("change");
    </script>

    <!--Enter Key click add row-->
    <script>
        $(document).on('keydown', '.form-control', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                $('#addproduct').click();

            }
        });
    </script>


    <!-- Bootstrap 4 -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js ') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js ') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/vfs_fonts.js ') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    {{-- <script src="../../dist/js/demo.js"></script> --}}
    <!-- Page specific script -->
    <script>
        $(function() {
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "pageLength": 30,
                // "buttons": ["excel", "pdf", "print"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "pageLength": 30,
                "buttons": ["excel", "pdf", "print"]
            }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
        });


        $(document).ready(function() {
            getBranchTransferNo();
        });


        function getBranchTransferNo() {


            $.ajax({
                type: 'POST',
                url: "{{ route('get_branch_transfer_no') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#transfer_number').val(data.no);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });

        }

        setInterval(getBranchTransferNo, 3000);
    </script>
</body>

</HTML>
