@include('layouts.header')



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
                                <h1>Product Register</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">Product Register
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

            <div class="content-body">
                <div class="container-fluid justify-content-center d-flex">
                    <div class="card card-default col-md-12">
                        <div class="card-header">
                            <h3 class="card-title">Product Register</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form action="{{ url('item_store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                {{-- <div class="row">
                                    <div class="frmSearch col-sm-4">
                                        <input type="checkbox" id="show" class=""><label for="show">
                                            Items ရှိပြီးသားလား?</label>
                                        <div id="additional-elements" style="display: none;" class="mt-2">
                                            @if (Auth::user()->is_admin == '1')
                                                <label for="warehouse">Choose Location<span
                                                        class="text-danger">*</span></label>
                                                <select id="warehouse" class="form-control">
                                                    <option value="" selected disabled>Choose Location</option>
                                                    @foreach ($branchs as $warehouse)
                                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <label for="warehouse">Choose Location</label>
                                                <select id="warehouse" class="form-control">
                                                    @php
                                                        $userPermissions = auth()->user()->level
                                                            ? json_decode(auth()->user()->level)
                                                            : [];
                                                    @endphp
                                                    @foreach ($branchs as $warehouse)
                                                        @if (in_array($warehouse->id, $userPermissions))
                                                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>

                                            @endif

                                            <span style="font-weight: bolder;">
                                                <label for="cst"
                                                    class="caption">{{ trans('Search With Item Name') }}</label>
                                            </span>
                                            <input type="text" id="customer" name="customer"
                                                class="form-control round" autocomplete="off">
                                            <button type="submit" class="my-3 btn btn-primary"
                                                id="customer_search">Add</button>
                                            <div id="customer-box-result"></div>
                                        </div>
                                    </div>

                                </div> --}}



                                <div class="mt-4 row">
                                    <div class="form-group col-lg-4 col-md-4">
                                        <label for="item_name">Image<span class="text-danger"> *</span></label>
                                        <input type="file" class="form-control" id="image" name="image"
                                            placeholder="Enter Image" value="{{ old('image') }}">

                                        {{-- parent id --}}
                                        <input type="hidden" value="0" name="parent_id">
                                    </div>

                                    <div class="form-group col-lg-4 col-md-4">
                                        <label for="item_name">Product Name <span class="text-danger"> *</span></label>
                                        <input type="text" class="form-control" id="item_name" name="item_name"
                                            placeholder="Enter Item Name" value="{{ old('item_name') }}" required>

                                        {{-- parent id --}}
                                        <input type="hidden" value="0" name="parent_id">
                                    </div>

                                    <div class="form-group col-lg-4 col-md-4">
                                        <label for="item_descriptions">Product Descriptions</label>
                                        <input type="text" class="form-control" id="item_descriptions"
                                            placeholder="Enter Item Descriptions" name="item_descriptions"
                                            value="{{ old('descriptions') }}">
                                    </div>

                                    @if (auth()->user()->is_admin == '1')
                                        <div class="form-group col-lg-4 col-md-4">
                                            <label for="warehouse_id">Location<span class="text-danger">*</span></label>
                                            <input type="hidden" id="warehouse_id_from" name="warehouse_id_from">
                                            <select name="warehouse_id" id="warehouse_id" class="form-control" required>
                                                <option value="" selected disabled>Select Location</option>
                                                @foreach ($branchs as $branch)
                                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @else
                                        <div class="form-group col-lg-4 col-md-4">
                                            <label for="warehouse_id">Location<span class="text-danger">*</span></label>
                                            <input type="hidden" id="warehouse_id_from" name="warehouse_id_from">
                                            <select name="warehouse_id" id="warehouse_id" class="form-control" required>
                                                @php
                                                    $userPermissions = auth()->user()->level
                                                        ? json_decode(auth()->user()->level)
                                                        : [];
                                                @endphp

                                                @foreach ($branchs as $branch)
                                                    @if (in_array($branch->id, $userPermissions))
                                                        <option value="{{ $branch->id }}">{{ $branch->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    {{-- <div class="form-group col-lg-4 col-md-4">
                                        <label for="stock_type">Product Type<span class="text-danger">
                                                *</span></label>
                                        <select name="type" class="form-control" id="type">
                                            <option selected disabled>Select Product Type</option>
                                            <option value="Standard">Standard</option>
                                            <option value="Clearance">Clearance</option>
                                            <option value="Discontinues">Discontinues</option>
                                            <option value="Slowmoving">Slowmoving</option>
                                            <option value="Sample">Sample</option>
                                            <option value="Repair Damage">Repair Damage</option>
                                            <option value="Damage">Damage</option>
                                            <option value="Display">Display</option>

                                        </select>
                                    </div> --}}

                                    <div class="form-group col-lg-4 col-md-4">
                                        <label for="stock_type">Stock Type<span class="text-danger">
                                                *</span></label>
                                        <select name="stock_type" class="form-control" id="stock_type">
                                            <option value="Stock" selected>Stock</option>
                                            <option value="Service">Service</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-4 col-lg-4">
                                        <label for="item_type">Product Register Type</label>
                                        <!-- <input type="text" class="form-control" id="item_type"
                                            name="item_type[]" value="{{ old('item_type') }}"
                                            placeholder="Enter Item Type"> -->
                                        <select name="item_type" id="item_type" class="form-control" required>
                                            <option value="" disabled>Select Product Type</option>
                                            <option value="Solar" selected>Solar</option>
                                            <option value="Inverter">Inverter</option>
                                            <option value="Battery">Battery</option>
                                            <option value="Breaker">Breaker</option>
                                            <option value="Accessories">Accessories</option>
                                            <option value="ATS">ATS</option>
                                            <option value="Cable">Cable</option>
                                            <option value="MCB">MCB</option>
                                        </select>
                                    </div>

                                    {{-- <div class="form-group col-lg-4 col-md-4">
                                        <label for="item_category">Item Category</label>
                                        <select name="item_category" id="item_category" class="form-control"
                                            required>
                                            <option value="" selected disabled>Select Category</option>
                                            @foreach ($brand_categories as $b_cat)
                                                <option value="{{ $b_cat->name }}"
                                                    data-branch-id="{{ $b_cat->branch }}">
                                                    {{ $b_cat->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>



                                    <div class="form-group col-lg-4 col-md-4">
                                        <label for="brand">Brand</label>
                                        <input type="hidden" name="brand" id="brand">
                                        <select name="brand_id" id="brand_id" class="form-control" required>
                                            <option value="" selected disabled>Select Brand</option>
                                        </select>
                                    </div> --}}

                                </div>


                                <hr>


                                <div id="rowContainer">

                                    <div class="mt-3 form-row dynamic-row  row ">

                                        <div class="form-group col-lg-4 col-md-4">
                                            <label for="variation_desc">Variation Descriptions</label>
                                            <input type="text" name="variation_desc[]" id="variation_desc"
                                                class="form-control variation_desc"
                                                placeholder="Enter Variation Descriptions">
                                        </div>

                                        <div class="form-group col-lg-4 col-md-4">
                                            <input type="hidden" value="1" name="status[]">
                                            <input type="hidden" name="brand_variation_id[]" id="brand_variation_id"
                                                class="brand_variation_id">
                                            <label for="model">Model</label>
                                            <!-- <select name="model[]" id="model" class="form-control model">
                                                <option value="" selected disabled>Select Model</option>
                                            </select> -->
                                            <input type="text" name="model[]" id="model"
                                                class="form-control model" placeholder="Enter model">
                                        </div>

                                        <div class="form-group col-lg-4 col-md-4">
                                            <label for="colour">Colour</label>
                                            <!-- <select name="colour[]" id="colour" class="form-control colour">
                                                <option value="" selected disabled>Select Colour</option>
                                            </select> -->
                                            <input type="text" name="colour[]" id="colour"
                                                class="form-control colour" placeholder="Enter colour">
                                        </div>

                                        {{-- <div class="form-group col-lg-4 col-md-4">
                                            <label for="size">Size</label>
                                            <!-- <select name="size[]" id="size" class="form-control size">
                                                <option value="" selected disabled>Select Size</option>
                                            </select> -->
                                            <input type="text" name="size[]" id="size"
                                                class="form-control size" placeholder="Enter size">
                                        </div>

                                        <div class="form-group col-lg-4 col-md-4">
                                            <label for="seater">Seater</label>
                                            <!-- <select name="seater[]" id="seater" class="form-control seater">
                                                <option value="" selected disabled>Select Seater</option>
                                            </select> -->
                                            <input type="text" name="seater[]" id="seater"
                                                class="form-control seater" placeholder="Enter seater">
                                        </div> --}}


                                        <div class="form-group col-lg-4 col-md-4">
                                            <label for="unit">
                                                Unit <span class="text-danger">*</span>
                                            </label>
                                            <select name="unit[]" id="unit" class="form-control" required>
                                                <option selected disabled>Select Unit</option>
                                                @foreach ($units as $unit)
                                                    <option value="{{ $unit->unit }}">{{ $unit->unit }}</option>
                                                @endforeach
                                            </select>
                                            @error('unit')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="form-group col-md-4 col-lg-4">
                                            <label for="warehouse_qty">Warehouse Qty <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="warehouse_qty[]"
                                                id="warehouse_qty" value="{{ old('warehouse_qty') }}"
                                                placeholder="Enter Quantity" required>
                                        </div>
                                        @if (in_array('Available Qty', $choosePermission) || auth()->user()->is_admin == '1')
                                            <div class="form-group col-md-4 col-lg-4">
                                                <label for="available_qty">Available Qty<span
                                                        class="text-danger">*</span></label>
                                                <input type="number" class="form-control" name="available_qty[]"
                                                    id="available_qty" value="{{ old('available_qty') }}"
                                                    placeholder="Enter Quantity">
                                            </div>
                                        @endif

                                        @if (in_array('Delivery Qty', $choosePermission) || auth()->user()->is_admin == '1')
                                            <div class="form-group col-md-4 col-lg-4">
                                                <label for="deliver_qty">Delivery Qty<span
                                                        class="text-danger">*</span></label>
                                                <input type="number" class="form-control" name="deliver_qty[]"
                                                    id="deliver_qty" value="{{ old('deliver_qty') }}"
                                                    placeholder="Enter Quantity">
                                            </div>
                                        @endif

                                        <div class="form-group col-md-4 col-lg-4">
                                            <label for="warranty">Warranty</label>
                                            <input type="text" class="form-control" name="warranty[]"
                                                id="warranty" value="{{ old('warranty') }}"
                                                placeholder="Enter Warranty">
                                        </div>


                                        <div class="form-group col-md-4 col-lg-4">
                                            <label for="barcode">Barcode</label>
                                            <input type="text" class="form-control" name="barcode[]"
                                                id="barcode" value="{{ old('barcode') }}"
                                                placeholder="Enter Barcode">
                                        </div>


                                        <div class="form-group col-md-4 col-lg-4">
                                            <label for="product_code">Product Code<span class="text-danger">
                                                    *</span></label>
                                            <input type="text" class="form-control" name="product_code[]"
                                                id="product_code" value="{{ old('product_code') }}"
                                                placeholder="Enter Product Code">
                                        </div>

                                        <div class="form-group col-md-4 col-lg-4">
                                            <label for="alert_qty">Stock Alert</label>
                                            <input type="number" class="form-control" id="alert_qty"
                                                name="alert_qty[]" value="{{ old('alert_qty') }}"
                                                placeholder="Enter Stock Alert">
                                        </div>

                                        <div class="form-group col-md-4 col-lg-4">
                                            <label for="expired_date">Expired Date</label>
                                            <input type="date" class="form-control" id="expired_date"
                                                name="expired_date[]" value="{{ old('expired_date') }}">
                                        </div>


                                        <div class="form-group col-md-4 col-lg-4">
                                            <label for="stock_agent_date">Stock Arrival Date</label>
                                            <input type="date" class="form-control" id="stock_agent_date"
                                                name="stock_agent_date[]" value="{{ old('stock_agent_date') }}">
                                        </div>


                                        <div class="form-group col-md-4 col-lg-4" style="">
                                            <label for="buy_price">Purchase Price</label>
                                            <input type="text" class="form-control" id="buy_price"
                                                name="buy_price[]" placeholder="Enter Purchase Price"
                                                value="{{ old('buy_price') }}">
                                        </div>

                                        {{-- <div class="form-group col-md-4 col-lg-4">
                                            <label for="wholesale_price">Wholesale Price</label>
                                            <input type="text" class="form-control" id="wholesale_price"
                                                name="wholesale_price[]" placeholder="Enter WholeSale Price"
                                                value="{{ old('wholesale_price') }}">
                                        </div> --}}

                                        <div class="form-group col-md-4 col-lg-4">
                                            <label for="retail_price"> Current Price</label>
                                            <input type="text" class="form-control" id="retail_price"
                                                name="retail_price[]" placeholder="Enter Current Price"
                                                value="{{ old('retail_price') }}">
                                        </div>

                                        <div class="form-group col-md-4 col-lg-4">
                                            <label for="retail_set_price"> Previous Price</label>
                                            <input type="text" class="form-control" id="retail_set_price"
                                                name="retail_set_price[]" placeholder="Enter Previous Price"
                                                value="{{ old('retail_set_price') }}">
                                        </div>

                                        <div class="form-group col-md-4 col-lg-4">
                                            <label for="promotion_retail_unit"> Retail Price</label>
                                            <input type="text" class="form-control" id="promotion_retail_unit"
                                                name="promotion_retail_unit[]" placeholder="Enter Retail Price"
                                                value="{{ old('promotion_retail_unit') }}">
                                        </div>

                                        <div class="form-group col-md-4 col-lg-4">
                                            <label for="promotion_retail_set">Wholesale Price</label>
                                            <input type="text" class="form-control" id="promotion_retail_set"
                                                name="promotion_retail_set[]" placeholder="Enter Wholesale Price"
                                                value="{{ old('promotion_retail_set') }}">
                                        </div>


                                    </div>

                                    <button type="button" class="btn btn-success mt-3 addRow main-add-row"><i
                                            class="fa-solid fa-plus"></i></button>

                                </div>

                                <hr>


                            </div>
                            <!-- /.card-body -->

                            <div class="mb-3 mx-3 mt-3 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary btn-md">Save</button>
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
        //add row and remove row
        $(document).ready(function() {
            let rowCount = 0;

            function toggleMainAddRowButton() {
                if ($('.row-group').length > 0) {
                    $('.main-add-row').hide();
                } else {
                    $('.main-add-row').show();
                }
            }

            toggleMainAddRowButton();

            $(document).on('click', '.addRow', function() {
                rowCount++;
                var newRow = $('.form-row').first().clone();
                newRow.find('input').val('');
                var rowGroup = $('<div class="row-group"></div>');
                rowGroup.append('<hr>');
                rowGroup.append(newRow);

                let buttonLabel = rowCount;

                rowGroup.append(
                    `<button type="button" class="btn btn-success mx-1 mt-3 addRow ${buttonLabel}-add-row"><i class="fa-solid fa-plus"></i></button>` +
                    '<button type="button" class="btn btn-danger removeRow mt-3"><i class="fa-solid fa-minus"></i></button>'
                );

                $('#rowContainer').append(rowGroup);

                if (rowCount > 1) {
                    $(`.${rowCount - 1}-add-row`).hide();
                }

                toggleMainAddRowButton();
            });

            $(document).on('click', '.removeRow', function() {
                var parentGroup = $(this).closest('.row-group');
                parentGroup.remove();

                rowCount--;
                if (rowCount > 0) {
                    $(`.${rowCount}-add-row`).show();
                }
                toggleMainAddRowButton();
            });
        });
    </script>
