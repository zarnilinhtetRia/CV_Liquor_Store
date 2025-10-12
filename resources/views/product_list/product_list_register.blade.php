@include('layouts.header')


<style>
    .typeahead.dropdown-menu {
        max-height: 200px;
        overflow-y: auto;
    }
</style>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav col-md-6">
                <li class="nav-item">
                    <a class="text-white nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>

                <li class="nav-item">
                    <a class="text-white nav-link" href="#">Date -
                        <?= $currentDate = date('d-m-y') ?></a>
                </li>


            </ul>

            <!-- Right navbar links -->
            <ul class="ml-auto navbar-nav">

                <div class="btn-group">
                    <button type="button" class="text-white btn dropdown-toggle" data-toggle="dropdown"
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
        <div class="content-wrapper">
            <!-- Main content -->
            <section class="content">

                <section class="content-header">
                    <div class="container-fluid">
                        <div class="mb-2 row">
                            <div class="col-sm-6">
                                <h1>Product List Register</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">Product List Register
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

            </section>

            @php
                use Carbon\Carbon;
                use Illuminate\Support\Str;

                $choosePermission = [];
                if (auth()->user()->permission) {
                    $decodedPermissions = json_decode(auth()->user()->permission, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $choosePermission = $decodedPermissions;
                    }
                }
            @endphp

            <div class="content-body">
                <div class="container-fluid justify-content-center d-flex">
                    <div class="card card-default col-md-12" style="background-color: #007bff26 !important;">
                        <div class="card-header">
                            <h3 class="card-title">Product List Register</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form action="{{ url('product_list_store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <input type="hidden" value="2" name="status">
                                <input type="hidden" name="item_id" id="item_id">
                                <input type="hidden" name="variation_id" id="variation_id">

                                <div class="row">
                                    <div class="frmSearch col-md-3">
                                        <!-- <input type="checkbox" id="show" class=""><label for="show">
                                            Items ရှိပြီးသားလား?</label> -->

                                        @if (Auth::user()->is_admin == '1')
                                            <label for="warehouse">Choose Location<span
                                                    class="text-danger">*</span></label>
                                            <select id="warehouse" class="form-control" required>
                                                @foreach ($branches as $warehouse)
                                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <label for="warehouse">Choose Location</label>
                                            <select id="warehouse" class="form-control" required>
                                                @php
                                                    $userPermissions = auth()->user()->level
                                                        ? json_decode(auth()->user()->level)
                                                        : [];
                                                @endphp
                                                @foreach ($branches as $warehouse)
                                                    @if (in_array($warehouse->id, $userPermissions))
                                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}
                                                        </option>
                                                    @endif
                                                @endforeach

                                            </select>

                                        @endif

                                        <div id="additional-elements" class="mt-2">
                                            <span style="font-weight: bolder;">
                                                <label for="cst"
                                                    class="caption">{{ trans('Search With Item / Model') }}</label>
                                            </span>
                                            <input type="text" id="item" name="item"
                                                class="form-control round" autocomplete="off">
                                            <button type="submit" class="my-3 btn btn-primary"
                                                id="item_search">Add</button>
                                            <div id="item-box-result"></div>
                                        </div>
                                    </div>

                                </div>


                                <div id="item_data" style="display: none;">
                                    <div class="mt-4 row">
                                        <div class="form-group col-lg-4 col-md-4">
                                            <label for="item_name">Item Name <span class="text-danger"> *</span></label>
                                            <input type="text" class="form-control" id="item_name" name="item_name"
                                                placeholder="Enter Item Name" value="{{ old('item_name') }}" disabled>
                                        </div>

                                        <div class="form-group col-lg-4 col-md-4">
                                            <label for="item_descriptions">Item Descriptions</label>
                                            <input type="text" class="form-control" id="item_descriptions"
                                                placeholder="Enter Item Descriptions" name="item_descriptions"
                                                value="{{ old('descriptions') }}" disabled>
                                        </div>


                                        <div class="form-group col-lg-4 col-md-4">
                                            <label for="warehouse_id">Location<span
                                                    class="text-danger">*</span></label>
                                            <input type="hidden" name="warehouse_id" id="warehouse_id">
                                            <input type="text" id="location_name" class="form-control" disabled>

                                        </div>

                                        <div class="form-group col-lg-4 col-md-4">
                                            <label for="stock_type">Item Type<span class="text-danger">
                                                    *</span></label>
                                            <input type="text" name="type" class="form-control" id="type"
                                                disabled>
                                        </div>

                                        <div class="form-group col-lg-4 col-md-4">
                                            <label for="stock_type">Stock Type<span class="text-danger">
                                                    *</span></label>
                                            <input type="text" name="stock_typ" class="form-control"
                                                id="stock_type" disabled>
                                        </div>

                                        <div class="form-group col-md-4 col-lg-4">
                                            <label for="item_type">Item Register Type</label>
                                            <input type="text" id="item_type" class="form-control" disabled>
                                        </div>

                                        <div class="form-group col-lg-4 col-md-4">
                                            <label for="item_category">Item Category<span class="text-danger">
                                                    *</span></label>
                                            <input type="text" name="item_category" id="item_category"
                                                class="form-control" disabled>
                                        </div>

                                        <div class="form-group col-lg-4 col-md-4">
                                            <label for="brand">Brand<span class="text-danger"> *</span></label>
                                            <input type="hidden" name="brand_id" id="brand_id">
                                            <input type="text" name="brand" id="brand" class="form-control"
                                                disabled>
                                        </div>


                                    </div>

                                    <hr>

                                    <div id="rowContainer">

                                        <div class="mt-3 row form-row">

                                            <div class="form-group col-lg-4 col-md-4">
                                                <label for="variation_desc">Variation Descriptions</label>
                                                <input type="text" name="variation_desc" id="variation_desc"
                                                    class="form-control variation_desc"
                                                    placeholder="Enter Variation Descriptions" readonly>
                                            </div>

                                            <div class="form-group col-lg-4 col-md-4">
                                                <!-- <input type="hidden" name="brand_variation_id"
                                                    id="brand_variation_id"> -->
                                                <label for="model">Model<span class="text-danger"> *</span></label>
                                                <input type="text" name="model" id="model"
                                                    class="form-control" readonly>
                                            </div>

                                            <div class="form-group col-lg-4 col-md-4">
                                                <label for="colour">Colour<span class="text-danger">
                                                        *</span></label>
                                                <input type="text" name="colour" id="colour"
                                                    class="form-control" readonly>
                                            </div>

                                            {{-- <div class="form-group col-lg-4 col-md-4">
                                                <label for="size">Size<span class="text-danger"> *</span></label>
                                                <input type="text" name="size" id="size"
                                                    class="form-control" readonly>
                                            </div>

                                            <div class="form-group col-lg-4 col-md-4">
                                                <label for="seater">Seater<span class="text-danger">
                                                        *</span></label>
                                                <input type="text" name="seater" id="seater"
                                                    class="form-control" readonly>
                                            </div> --}}

                                            <div class="form-group col-lg-4 col-md-4">
                                                <label for="item_unit">
                                                    Unit <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="item_unit" id="item_unit"
                                                    class="form-control" disabled>
                                            </div>

                                            <div class="form-group col-md-4 col-lg-4">
                                                <label for="warehouse_qty">Warehouse Qty <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="warehouse_qty[]"
                                                    id="warehouse_qty" value="{{ old('warehouse_qty') }}"
                                                    placeholder="Enter Quantity" disabled>
                                            </div>


                                            <div class="form-group col-md-4 col-lg-4">
                                                <label for="barcode">Barcode</label>
                                                <input type="text" class="form-control" name="barcode[]"
                                                    id="barcode" value="{{ old('barcode') }}"
                                                    placeholder="Enter Barcode" disabled>
                                            </div>


                                            <div class="form-group col-md-4 col-lg-4">
                                                <label for="product_code">Product Code<span class="text-danger">
                                                        *</span></label>
                                                <input type="text" class="form-control" name="product_code"
                                                    id="product_code" value="{{ old('product_code') }}"
                                                    placeholder="Enter Product Code" readonly>
                                            </div>

                                            <div class="form-group col-md-4 col-lg-4">
                                                <label for="alert_qty">Stock Alert</label>
                                                <input type="text" class="form-control" id="alert_qty"
                                                    name="alert_qty[]" value="{{ old('alert_qty') }}"
                                                    placeholder="Enter Stock Alert" disabled>
                                            </div>

                                            <div class="form-group col-md-4 col-lg-4">
                                                <label for="expired_date">Expired Date</label>
                                                <input type="date" class="form-control" id="expired_date"
                                                    name="expired_date[]" value="{{ old('expired_date') }}" disabled>
                                            </div>


                                            <div class="form-group col-md-4 col-lg-4">
                                                <label for="stock_agent_date">Stock Ageing Date</label>
                                                <input type="date" class="form-control" id="stock_agent_date"
                                                    name="stock_agent_date[]" value="{{ old('stock_agent_date') }}"
                                                    disabled>
                                            </div>

                                            {{-- <div class="form-group col-md-4 col-lg-4">
                                                <label for="buy_price">Purchase Price</label>
                                                <input type="number" class="form-control" id="buy_price"
                                                    name="buy_price[]" placeholder="Enter Buy Price"
                                                    value="{{ old('buy_price') }}" disabled>
                                            </div> --}}

                                            <div class="form-group col-md-4 col-lg-4">
                                                <label for="wholesale_price">Wholesale Price</label>
                                                <input type="number" class="form-control" id="wholesale_price"
                                                    name="wholesale_price[]" placeholder="Enter WholeSale Price"
                                                    value="{{ old('wholesale_price') }}" disabled>
                                            </div>

                                            <div class="form-group col-md-4 col-lg-4">
                                                <label for="retail_price">Retail Unit Price</label>
                                                <input type="number" class="form-control" id="retail_price"
                                                    name="retail_price[]" placeholder="Enter Retail Unit Price"
                                                    value="{{ old('retail_price') }}" disabled>
                                            </div>

                                            <div class="form-group col-md-4 col-lg-4">
                                                <label for="retail_set_price">Retail Set Price</label>
                                                <input type="number" class="form-control" id="retail_set_price"
                                                    name="retail_set_price" placeholder="Enter Retail Set Price"
                                                    value="{{ old('retail_set_price') }}" disabled>
                                            </div>

                                            <div class="form-group col-md-4 col-lg-4">
                                                <label for="promotion_retail_unit">Promotion Retail Unit</label>
                                                <input type="number" class="form-control" id="promotion_retail_unit"
                                                    name="promotion_retail_unit"
                                                    placeholder="Enter Promotion Retail Unit"
                                                    value="{{ old('promotion_retail_unit') }}" disabled>
                                            </div>

                                            <div class="form-group col-md-4 col-lg-4">
                                                <label for="promotion_retail_set">Promotion Retail Set</label>
                                                <input type="number" class="form-control" id="promotion_retail_set"
                                                    name="promotion_retail_set"
                                                    placeholder="Enter Promotion Retail Set"
                                                    value="{{ old('promotion_retail_set') }}" disabled>
                                            </div>

                                        </div>
                                    </div>

                                    <hr>


                                </div>

                                <hr>

                                <div id="rowContainer">
                                    <div class="mt-3 row form-row">
                                        <div class="form-group col-md-3 col-lg-3">
                                            <label for="unit_price">Unit Price</label>
                                            <input type="text" class="form-control" id="unit_price"
                                                name="unit_price" placeholder="Enter Unit Price"
                                                value="{{ old('unit_price') }}">
                                        </div>

                                        @if (in_array('Purchase Order', $choosePermission) || auth()->user()->is_admin == '1')
                                            <div class="form-group col-md-3 col-lg-3">
                                                <label for="cost_price">Cost Price</label>
                                                <input type="text" class="form-control" id="cost_price"
                                                    name="cost_price" placeholder="Enter Cost Price"
                                                    value="{{ old('cost_price') }}">
                                            </div>
                                        @endif

                                        <div class="form-group col-md-3 col-lg-3">
                                            <label for="selling_price">Selling Price</label>
                                            <input type="text" class="form-control" id="selling_price"
                                                name="selling_price" placeholder="Enter Selling Price"
                                                value="{{ old('selling_price') }}">
                                        </div>

                                        <div class="form-group col-md-3 col-lg-3">
                                            <label for="unit">Unit</label>
                                            <input type="text" class="form-control" id="unit" name="unit"
                                                placeholder="Enter unit" value="{{ old('unit') }}">
                                        </div>

                                        <div class="form-group col-md-3 col-lg-3">
                                            <label for="moq">MOQ</label>
                                            <input type="text" class="form-control" id="moq" name="moq"
                                                placeholder="Enter MOQ" value="{{ old('moq') }}">
                                        </div>

                                        <div class="form-group col-md-3 col-lg-3">
                                            <label for="unit_m3">Unit M3</label>
                                            <input type="text" class="form-control" id="unit_m3" name="unit_m3"
                                                placeholder="Enter Unit M3" value="{{ old('unit_m3') }}">
                                        </div>

                                        <div class="form-group col-md-3 col-lg-3">
                                            <label for="unit_kg">Unit Kg</label>
                                            <input type="text" class="form-control" id="unit_kg" name="unit_kg"
                                                placeholder="Enter Unit Kg" value="{{ old('unit_m3') }}">
                                        </div>

                                        <!-- <div class="form-group col-md-3 col-lg-3">
                                            <label for="qty">Qty</label>
                                            <input type="number" class="form-control" id="qty"
                                                name="qty" placeholder="Enter qty"
                                                value="{{ old('qty') }}">
                                        </div> -->

                                        <!-- <div class="form-group col-md-3 col-lg-3">
                                            <label for="total_unit_m3">Total Unit M3</label>
                                            <input type="text" class="form-control" id="total_unit_m3"
                                                name="total_unit_m3" placeholder="Enter Total Unit M3"
                                                value="{{ old('total_unit_m3') }}" readonly>
                                        </div> -->

                                        <!-- <div class="form-group col-md-3 col-lg-3">
                                            <label for="total_unit_kg">Total Unit Kg</label>
                                            <input type="text" class="form-control" id="total_unit_kg"
                                                name="total_unit_kg" placeholder="Enter Total Unit Kg"
                                                value="{{ old('total_unit_kg') }}" readonly>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="mb-3 ml-3 mt-3 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>

        </div>



    </div>

    <script src="{{ asset('plugins/jquery/jquery.min.js ') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    {{-- <script src="../../dist/js/demo.js"></script> --}}
    <!-- Page specific script -->
    <script src="{{ asset('locallink/js/ajax_jquery.js') }}"></script>
    <script src="{{ asset('locallink/js/typehead.min.js') }}"></script>
    <script src="{{ asset('locallink/js/moment.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('.option-checkbox').on('change', function() {
                if ($(this).is(':checked')) {
                    $('.option-checkbox').not(this).prop('checked', false);
                }
            });

            // var path = "{{ route('product_list.search_item_name') }}";
            $('#item').typeahead({
                source: function(query, process) {
                    var Selectedlocation = $('#warehouse').val();
                    console.log(Selectedlocation);
                    return $.ajax({
                        url: "{{ route('product_list.search_item_name') }}",
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            query: query,
                            location: Selectedlocation,
                        },
                        dataType: 'json',
                        success: function(data) {
                            const formattedData = data.map(function(item) {
                                return {
                                    item_name: item.item_name || '',
                                    product_code: item.product_code || '',
                                    model: item.model || '',
                                    colour: item.colour || '',
                                    size: item.size || '',
                                    seater: item.seater || '',
                                    id: item.id,
                                    item_id: item.item_id,
                                    variation_desc: item.variation_desc,
                                    display: item.item_name + ' (' + (item
                                            .product_code ? item.product_code +
                                            ' - ' : '') + (
                                            item.model ? item.model + ' - ' : '') +
                                        (
                                            item.colour ? item.colour : '') + (
                                            item.size ? ' - ' + item.size : '') + (
                                            item
                                            .seater ? ' - ' + item.seater : '') +
                                        ' )' + ' (' + ((item
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
                    $('#variation_id').val(item.id);
                    $('#item_id').val(item.item_id);
                },
                autoSelect: true,
                items: 'all'
            });

            $(document).on('click', '#item_search', function(e) {
                e.preventDefault();
                let variation_id = $('#variation_id').val();
                let item_id = $('#item_id').val();
                let location = $('#warehouse').val();

                $.ajax({
                    type: 'POST',
                    url: "{{ route('product_list.item.fill') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        item_id: item_id,
                        variation_id: variation_id,
                        location: location // Adjusted to match server-side parameter name
                    },
                    success: function(data) {
                        console.log(data);
                        $('#item_data').show();
                        // $("#item_id").val(data['item']['id']);
                        $("#item_name").val(data['item']['item_name']);
                        $("#item_descriptions").val(data['item']['item_descriptions']);
                        // Find the option with the selected value and hide it
                        $("#warehouse_id").val(data['warehouse']['id']);
                        $("#location_name").val(data['warehouse']['name']);
                        $("#item_category").val(data['item']['item_category']);
                        // $("#brand_variation_id").val(data['variation']['brand_variation_id']);
                        $("#brand_id").val(data['item']['brand_id']);
                        $("#brand").val(data['item']['brand']);
                        $("#variation_desc").val(data['variation']['variation_desc']);
                        $("#model").val(data['variation']['model']);
                        $("#colour").val(data['variation']['colour']);
                        $("#size").val(data['variation']['size']);
                        $("#seater").val(data['variation']['seater']);
                        $("#type").val(data['item']['type']);
                        $("#stock_type").val(data['item']['stock_type']);
                        $("#item_unit").val(data['variation']['unit']);
                        $("#warehouse_qty").val(data['item_qty']['warehouse_qty']);
                        $("#barcode").val(data['variation']['barcode']);
                        $("#product_code").val(data['variation']['product_code']);
                        $("#alert_qty").val(data['item_qty']['alert_qty']);
                        $("#expired_date").val(data['variation']['expired_date']);
                        $("#stock_agent_date").val(data['variation']['stock_agent_date']);
                        $("#item_type").val(data['item']['item_type']);

                        $("#buy_price").val(data['variation']['buy_price']);
                        $("#retail_price").val(data['variation']['retail_price']);
                        $("#wholesale_price").val(data['variation']['wholesale_price']);
                        $("#retail_set_price").val(data['variation']['retail_set_price']);
                        $("#promotion_retail_unit").val(data['variation'][
                            'promotion_retail_unit'
                        ]);
                        $("#promotion_retail_set").val(data['variation'][
                            'promotion_retail_set'
                        ]);
                        $("#selling_price").val(data['variation']['retail_price']);
                        $("#unit").val(data['variation']['unit']);

                        // Adjusted to match server-side data
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });

            //    $(document).on('input', '#unit_m3, #unit_kg, #qty', function(e) {
            //         let unitm3 = parseFloat($('#unit_m3').val()) || 0;
            //         let unitkg = parseFloat($('#unit_kg').val()) || 0;
            //         let qty = parseFloat($('#qty').val()) || 0;

            //         // Calculate totals
            //         let totalM3 = unitm3 * qty;
            //         let totalKg = unitkg * qty;
            //         console.log(totalKg);
            //         // Update the totals in the respective fields
            //         $('#total_unit_m3').val(totalM3);
            //         $('#total_unit_kg').val(totalKg);
            //     });

        });
    </script>

    <script>
        // document.getElementById("show").addEventListener("change", function() {
        //     var additionalElements = document.getElementById("additional-elements");
        //     if (this.checked) {
        //         additionalElements.style.display = "block";
        //     } else {
        //         additionalElements.style.display = "none";
        //     }
        // });
    </script>

</body>

</html>
