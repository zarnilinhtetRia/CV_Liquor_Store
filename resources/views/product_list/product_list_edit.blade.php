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
                                <h1>Product List Edit</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">Product List Edit
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

            </section>
            {{-- Permission --}}
            @php
                $choosePermission = [];
                if (auth()->user()->permission) {
                    $decodedPermissions = json_decode(auth()->user()->permission, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $choosePermission = $decodedPermissions;
                    }
                }
            @endphp
            {{-- End Permission --}}

            <div class="content-body">
                <div class="container-fluid justify-content-center d-flex">
                    <div class="card card-default col-md-12" style="background-color: #007bff26 !important;">
                        <div class="card-header">
                            <h3 class="card-title">Product List Edit</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form action="{{ url('product_list_update', $product_list->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="item_id" id="item_id" value="{{ $product_list->item_id }}">
                            <input type="hidden" name="variation_id" id="variation_id"
                                value="{{ $product_list->variation_id }}">

                            <div class="card-body">
                                <!-- <div class="row">
                                    <div class="frmSearch col-md-3">

                                        @if (Auth::user()->is_admin == '1')
<label for="warehouse">Choose Location<span
                                                    class="text-danger">*</span></label>
                                            <select id="warehouse" class="form-control" required>
                                                <option value="" selected disabled>Choose Location</option>
                                                @foreach ($branches as $warehouse)
<option value="{{ $warehouse->id }}"
                                                        @if ($warehouse->id == $product_list->item->warehouse_id) selected @endif>
                                                        {{ $warehouse->name }}
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
<option value="{{ $warehouse->id }}"
                                                            @if ($warehouse->id == $product_list->item->warehouse_id) selected @endif>
                                                            {{ $warehouse->name }}
                                                        </option>
@endif
@endforeach
                                            </select>
@endif

                                        <div id="additional-elements" class="mt-2">
                                            <span style="font-weight: bolder;">
                                                <label for="cst"
                                                    class="caption">{{ trans('Search With Item Name') }}</label>
                                            </span>
                                            <input type="text" id="item" name="item"
                                                class="form-control round" autocomplete="off">
                                            <button type="submit" class="my-3 btn btn-primary"
                                                id="item_search">Add</button>
                                            <div id="item-box-result"></div>
                                        </div>
                                    </div>

                                </div> -->


                                <div id="item_data">
                                    <div class="mt-4 row">
                                        <div class="form-group col-lg-4 col-md-4">
                                            <label for="item_name">Item Name <span class="text-danger"> *</span></label>
                                            <input type="text" class="form-control" id="item_name"
                                                value="{{ $product_list->item->item_name }}" disabled>
                                        </div>

                                        <div class="form-group col-lg-4 col-md-4">
                                            <label for="item_descriptions">Item Descriptions</label>
                                            <input type="text" class="form-control" id="item_descriptions"
                                                value="{{ $product_list->item->item_descriptions }}" disabled>
                                        </div>

                                        <div class="form-group col-lg-4 col-md-4">
                                            <label for="warehouse_id">Location<span class="text-danger">*</span></label>
                                            <input type="hidden" name="warehouse_id" id="warehouse_id"
                                                value="{{ $product_list->warehouse_id }}">
                                            <input type="text" id="location_name" class="form-control"
                                                value="{{ $product_list->warehouse->name }}" disabled>
                                        </div>

                                        <div class="form-group col-lg-4 col-md-4">
                                            <label for="stock_type">Item Type<span class="text-danger">
                                                    *</span></label>
                                            <input type="text" value="{{ $product_list->item->type }}"
                                                class="form-control" id="type" disabled>
                                        </div>

                                        <div class="form-group col-lg-4 col-md-4">
                                            <label for="stock_type">Stock Type<span class="text-danger">
                                                    *</span></label>
                                            <input type="text" value="{{ $product_list->item->stock_type }}"
                                                class="form-control" id="stock_type" disabled>
                                        </div>

                                        <div class="form-group col-md-4 col-lg-4">
                                            <label for="item_type">Item Register Type</label>
                                            <input type="text" id="item_type"
                                                value="{{ $product_list->item->item_type }}" class="form-control"
                                                disabled>
                                        </div>

                                        <div class="form-group col-lg-4 col-md-4">
                                            <label for="item_category">Item Category<span class="text-danger">
                                                    *</span></label>
                                            <input type="text" id="item_category" class="form-control"
                                                value="{{ $product_list->item->item_category }}" disabled>
                                        </div>

                                        <div class="form-group col-lg-4 col-md-4">
                                            <label for="brand">Brand<span class="text-danger"> *</span></label>
                                            <input type="hidden" name="brand_id" id="brand_id"
                                                value="{{ $product_list->brand_id }}">
                                            <input type="text" id="brand" class="form-control"
                                                value="{{ $product_list->item->brand }}" disabled>
                                        </div>

                                    </div>

                                    <hr>

                                    <div id="rowContainer">

                                        <div class="mt-3 row form-row">

                                            <div class="form-group col-lg-4 col-md-4">
                                                <label for="variation_desc">Variation Descriptions</label>
                                                <input type="text" name="variation_desc" id="variation_desc"
                                                    class="form-control variation_desc"
                                                    placeholder="Enter Variation Descriptions"
                                                    value="{{ $variation->variation_desc }}" readonly>
                                            </div>

                                            <div class="form-group col-lg-4 col-md-4">
                                                <!-- <input type="hidden" name="brand_variation_id"
                                                    id="brand_variation_id"
                                                    value="{{ $product_list->brand_variation_id }}"> -->
                                                <label for="model">Model<span class="text-danger"> *</span></label>
                                                <input type="text" value="{{ $variation->model }}" id="model"
                                                    class="form-control" disabled>
                                            </div>

                                            <div class="form-group col-lg-4 col-md-4">
                                                <label for="colour">Colour<span class="text-danger">
                                                        *</span></label>
                                                <input type="text" value="{{ $variation->colour }}"
                                                    id="colour" class="form-control" disabled>
                                            </div>

                                            <div class="form-group col-lg-4 col-md-4">
                                                <label for="size">Size<span class="text-danger"> *</span></label>
                                                <input type="text" value="{{ $variation->size }}" id="size"
                                                    class="form-control" disabled>
                                            </div>

                                            <div class="form-group col-lg-4 col-md-4">
                                                <label for="seater">Seater<span class="text-danger">
                                                        *</span></label>
                                                <input type="text" value="{{ $variation->seater }}"
                                                    id="seater" class="form-control" disabled>
                                            </div>

                                            <div class="form-group col-lg-4 col-md-4">
                                                <label for="item_unit">
                                                    Unit <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" value="{{ $variation->unit }}" id="item_unit"
                                                    class="form-control" disabled>
                                            </div>

                                            <div class="form-group col-md-4 col-lg-4">
                                                <label for="warehouse_qty">Warehouse Qty <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control"
                                                    value="{{ $item_qty->warehouse_qty }}" id="warehouse_qty"
                                                    disabled>
                                            </div>


                                            <div class="form-group col-md-4 col-lg-4">
                                                <label for="barcode">Barcode</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $variation->first()->barcode }}" id="barcode"
                                                    disabled>
                                            </div>


                                            <div class="form-group col-md-4 col-lg-4">
                                                <label for="product_code">Product Code<span class="text-danger">
                                                        *</span></label>
                                                <input type="text" class="form-control"
                                                    value="{{ $variation->product_code }}" id="product_code"
                                                    disabled>
                                            </div>

                                            <div class="form-group col-md-4 col-lg-4">
                                                <label for="alert_qty">Stock Alert</label>
                                                <input type="text" class="form-control" id="alert_qty"
                                                    value="{{ $item_qty->alert_qty }}" disabled>
                                            </div>

                                            <div class="form-group col-md-4 col-lg-4">
                                                <label for="expired_date">Expired Date</label>
                                                <input type="date" class="form-control" id="expired_date"
                                                    value="{{ $variation->expired_date }}" disabled>
                                            </div>


                                            <div class="form-group col-md-4 col-lg-4">
                                                <label for="stock_agent_date">Stock Ageing Date</label>
                                                <input type="date" class="form-control" id="stock_agent_date"
                                                    value="{{ $variation->stock_agent_date }}" disabled>
                                            </div>

                                            <div class="form-group col-md-4 col-lg-4">
                                                <label for="wholesale_price">Wholesale Price</label>
                                                <input type="number" class="form-control" id="wholesale_price"
                                                    name="wholesale_price[]" placeholder="Enter WholeSale Price"
                                                    value="{{ $variation->wholesale_price }}" disabled>
                                            </div>

                                            <div class="form-group col-md-4 col-lg-4">
                                                <label for="retail_price">Retail Unit Price</label>
                                                <input type="number" class="form-control" id="retail_price"
                                                    name="retail_price[]" placeholder="Enter Retail Unit Price"
                                                    value="{{ $variation->retail_price }}" disabled>
                                            </div>

                                            <div class="form-group col-md-4 col-lg-4">
                                                <label for="retail_set_price">Retail Set Price</label>
                                                <input type="number" class="form-control" id="retail_set_price"
                                                    name="retail_set_price" placeholder="Enter Retail Set Price"
                                                    value="{{ $variation->retail_set_price }}" disabled>
                                            </div>

                                            <div class="form-group col-md-4 col-lg-4">
                                                <label for="promotion_retail_unit">Promotion Retail Unit</label>
                                                <input type="number" class="form-control" id="promotion_retail_unit"
                                                    name="promotion_retail_unit"
                                                    placeholder="Enter Promotion Retail Unit"
                                                    value="{{ $variation->promotion_retail_unit }}" disabled>
                                            </div>

                                            <div class="form-group col-md-4 col-lg-4">
                                                <label for="promotion_retail_set">Promotion Retail Set</label>
                                                <input type="number" class="form-control" id="promotion_retail_set"
                                                    name="promotion_retail_set"
                                                    placeholder="Enter Promotion Retail Set"
                                                    value="{{ $variation->promotion_retail_set }}" disabled>
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
                                            <input type="text" name="unit_price" class="form-control"
                                                id="unit_price" value="{{ $product_list->unit_price }}">
                                        </div>

                                        @if (in_array('Purchase Order', $choosePermission) || auth()->user()->is_admin == '1')
                                            <div class="form-group col-md-3 col-lg-3">
                                                <label for="cost_price">Cost Price</label>
                                                <input type="text" name="cost_price" class="form-control"
                                                    id="cost_price" value="{{ $product_list->cost_price }}">
                                            </div>
                                        @endif

                                        <div class="form-group col-md-3 col-lg-3">
                                            <label for="selling_price">Selling Price</label>
                                            <input type="text" name="selling_price" class="form-control"
                                                id="selling_price" value="{{ $product_list->selling_price }}">
                                        </div>

                                        <div class="form-group col-md-3 col-lg-3">
                                            <label for="unit">Unit</label>
                                            <input type="text" name="unit" class="form-control" id="unit"
                                                value="{{ $product_list->unit }}">
                                        </div>

                                        <div class="form-group col-md-3 col-lg-3">
                                            <label for="moq">MOQ</label>
                                            <input type="text" name="moq" class="form-control" id="moq"
                                                value="{{ $product_list->moq }}">
                                        </div>

                                        <div class="form-group col-md-3 col-lg-3">
                                            <label for="unit_m3">Unit M3</label>
                                            <input type="text" name="unit_m3" class="form-control" id="unit_m3"
                                                value="{{ $product_list->unit_m3 }}">
                                        </div>

                                        <div class="form-group col-md-3 col-lg-3">
                                            <label for="unit_kg">Unit Kg</label>
                                            <input type="text" name="unit_kg" class="form-control" id="unit_kg"
                                                value="{{ $product_list->unit_kg }}">
                                        </div>

                                        <!-- <div class="form-group col-md-3 col-lg-3">
                                            <label for="qty">Qty</label>
                                            <input type="number" name="qty" class="form-control" id="qty" value="{{ $product_list->qty }}">
                                        </div>

                                        <div class="form-group col-md-3 col-lg-3">
                                            <label for="total_unit_m3">Total Unit M3</label>
                                            <input type="text" class="form-control" id="total_unit_m3"
                                                name="total_unit_m3" value="{{ $product_list->total_unit_m3 }}" readonly>
                                        </div>

                                        <div class="form-group col-md-3 col-lg-3">
                                            <label for="total_unit_kg">Total Unit Kg</label>
                                            <input type="text" class="form-control" id="total_unit_kg"
                                                name="total_unit_kg" value="{{ $product_list->total_unit_kg }}" readonly>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="mb-3 ml-3 mt-3 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>

        </div>



    </div>


    @include('layouts.footer')
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
                                    display: item.item_name + ' (' + (item
                                            .product_code ? item.product_code +
                                            ' - ' : '') + (
                                            item.model ? item.model + ' - ' : '') +
                                        (
                                            item.colour ? item.colour : '') + (
                                            item.size ? ' - ' + item.size : '') + (
                                            item
                                            .seater ? ' - ' + item.seater : '') +
                                        ' )'
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

                        $("#buy_price").val(data['item']['buy_price']);
                        $("#retail_price").val(data['item']['retail_price']);
                        $("#wholesale_price").val(data['item']['wholesale_price']);
                        $("#retail_set_price").val(data['item']['retail_set_price']);
                        $("#promotion_retail_unit").val(data['item']['promotion_retail_unit']);
                        $("#promotion_retail_set").val(data['item']['promotion_retail_set']);
                        $("#selling_price").val(data['item']['retail_price']);
                        $("#unit").val(data['variation']['unit']);

                        // Adjusted to match server-side data
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });

            $(document).on('input', '#unit_m3, #unit_kg, #qty', function(e) {
                let unitm3 = parseFloat($('#unit_m3').val()) || 0;
                let unitkg = parseFloat($('#unit_kg').val()) || 0;
                let qty = parseFloat($('#qty').val()) || 0;

                // Calculate totals
                let totalM3 = unitm3 * qty;
                let totalKg = unitkg * qty;
                console.log(totalKg);
                // Update the totals in the respective fields
                $('#total_unit_m3').val(totalM3);
                $('#total_unit_kg').val(totalKg);
            });

        });
    </script>

    <script>
        document.getElementById("show").addEventListener("change", function() {
            var additionalElements = document.getElementById("additional-elements");
            if (this.checked) {
                additionalElements.style.display = "block";
            } else {
                additionalElements.style.display = "none";
            }
        });
    </script>
