@include('layouts.header')
<style>
    .changelogout:hover {
        background-color: whitesmoke;
        color: red;
    }
</style>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
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
            <ul class="ml-auto navbar-nav">
                <div class="btn-group">
                    <button type="button" class=" text-white btn dropdown-toggle" data-toggle="dropdown"
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
                                <h1>Product Edit</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">Product Edit
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
                    <div class="card card-default col-md-12" style="background-color: #007bff26">
                        <!-- <div class="card-header">
                            <h3 class="card-title">Item Edit</h3>
                        </div> -->
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form action="{{ url('item_update', $item->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="mt-4 row">
                                    <div class="form-group col-lg-4 col-md-4">
                                        <label for="image">Image <span class="text-danger"> *</span></label>
                                        <input type="file" class="form-control" id="image" name="image"
                                            accept="image/*" onchange="previewImage(event)">

                                        @if (!empty($item->image))
                                            @php
                                                $imagePath = asset('item_images/' . $item->image);
                                            @endphp
                                            <br>
                                            <img id="imagePreview" src="{{ $imagePath }}" alt="Item Image"
                                                width="100" height="100" class="img-thumbnail">
                                        @else
                                            <br>
                                            <img id="imagePreview" src="{{ asset('img/default.png') }}"
                                                alt="Default Image" width="100" height="100" class="img-thumbnail">
                                        @endif
                                    </div>


                                    <div class="form-group col-lg-4 col-md-4">
                                        <label for="item_name">Product Name <span class="text-danger"> *</span></label>
                                        <input type="text" class="form-control" id="item_name" name="item_name"
                                            placeholder="Enter Item Name" value="{{ $item->item_name }}" required>

                                        {{-- parent id --}}
                                        <input type="hidden" value="0" name="parent_id">
                                    </div>

                                    <div class="form-group col-lg-4 col-md-4">
                                        <label for="item_descriptions">Product Descriptions</label>
                                        <input type="text" class="form-control" id="item_descriptions"
                                            placeholder="Enter Item Descriptions" name="item_descriptions"
                                            value="{{ $item->item_descriptions }}">
                                    </div>

                                    @if (auth()->user()->is_admin == '1')
                                        <div class="form-group col-lg-4 col-md-4">
                                            <label for="warehouse_id">Location<span class="text-danger">*</span></label>
                                            <input type="hidden" id="warehouse_id_from" name="warehouse_id_from">
                                            <select name="warehouse_id" id="warehouse_id" class="form-control" required>
                                                <option value="{{ $item->warehouse_id }}" selected>
                                                    {{ $item->warehouse->name }}
                                                </option>
                                                @foreach ($branchs as $branch)
                                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @else
                                        <div class="form-group col-lg-4 col-md-4">
                                            <label for="warehouse_id">Location<span
                                                    class="text-danger">*</span></label>
                                            <input type="hidden" id="warehouse_id_from" name="warehouse_id_from">
                                            <select name="warehouse_id" id="warehouse_id" class="form-control"
                                                required>
                                                @php
                                                    $userPermissions = auth()->user()->level
                                                        ? json_decode(auth()->user()->level)
                                                        : [];
                                                @endphp
                                                <option value="{{ $item->warehouse_id }}" selected>
                                                    {{ $item->warehouse->name }}
                                                </option>
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
                                        <label for="stock_type">Product Type<span class="text-danger">*</span></label>
                                        <select name="type" class="form-control" id="type">
                                            <option disabled>Select Product Type</option>
                                            <option value="Standard" {{ $item->type == 'Standard' ? 'selected' : '' }}>
                                                Standard</option>
                                            <option value="Clearance"
                                                {{ $item->type == 'Clearance' ? 'selected' : '' }}>Clearance</option>
                                            <option value="Discontinues"
                                                {{ $item->type == 'Discontinues' ? 'selected' : '' }}>Discontinues
                                            </option>
                                            <option value="Slowmoving"
                                                {{ $item->type == 'Slowmoving' ? 'selected' : '' }}>Slowmoving</option>
                                            <option value="Sample" {{ $item->type == 'Sample' ? 'selected' : '' }}>
                                                Sample</option>
                                            <option value="Repair Damage"
                                                {{ $item->type == 'Repair Damage' ? 'selected' : '' }}>Repair Damage
                                            </option>
                                            <option value="Damage" {{ $item->type == 'Damage' ? 'selected' : '' }}>
                                                Damage</option>
                                            <option value="Display" {{ $item->type == 'Display' ? 'selected' : '' }}>
                                                Display</option>
                                        </select>
                                    </div> --}}

                                    <div class="form-group col-lg-4 col-md-4">
                                        <label for="stock_type">Stock Type<span class="text-danger">*</span></label>
                                        <select name="stock_type" class="form-control" id="stock_type">
                                            <option value="Stock"
                                                {{ $item->stock_type == 'Stock' ? 'selected' : '' }}>Stock</option>
                                            <option value="Service"
                                                {{ $item->stock_type == 'Service' ? 'selected' : '' }}>Service</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 col-lg-4">
                                        <label for="item_type">Product Register Type</label>
                                        <!-- <input type="text" class="form-control" id="item_type"
                                            name="item_type[]" value="{{ old('item_type') }}"
                                            placeholder="Enter Item Type"> -->
                                        <select name="item_type" id="item_type" class="form-control" required>
                                            <option value="" disabled>Select Product Type</option>
                                            <option value="Solar" @if ($item->item_type == 'Solar') selected @endif>
                                                Solar</option>
                                            <option value="Inverter" @if ($item->item_type == 'Inverter') selected @endif>
                                                Inverter</option>
                                            <option value="Battery" @if ($item->item_type == 'Battery') selected @endif>
                                                Battery</option>
                                            <option value="Breaker" @if ($item->item_type == 'Breaker') selected @endif>
                                                Breaker</option>
                                            <option value="Accessories"
                                                @if ($item->item_type == 'Accessories') selected @endif>Accessories</option>
                                            <option value="ATS" @if ($item->item_type == 'ATS') selected @endif>
                                                ATS</option>
                                            <option value="Cable" @if ($item->item_type == 'Cable') selected @endif>
                                                Cable</option>
                                            <option value="MCB" @if ($item->item_type == 'MCB') selected @endif>
                                                MCB</option>
                                        </select>
                                    </div>

                                    {{-- <div class="form-group col-md-4 col-lg-4">
                                        <label for="item_type">Product Register Type</label>
                                        <select name="item_type" id="item_type" class="form-control" required>
                                            <option value="" disabled>Select Product Register Type</option>
                                            <option value="Existing Product"
                                                {{ $item->item_type == 'Existing Item' ? 'selected' : '' }}>Existing
                                                Product</option>
                                            <option value="New Order Product"
                                                {{ $item->item_type == 'New Order Item' ? 'selected' : '' }}>New Order
                                                Product</option>
                                        </select>
                                    </div> --}}


                                </div>

                                <hr>

                                <div id="rowContainer">
                                    @foreach ($item->variations as $key => $variation)
                                        @if ($key > 0)
                                            <hr>
                                        @endif
                                        <div class="row-group">
                                            <div class="row mt-3 form-row dynamic-row">
                                                {{-- variation_id --}}
                                                <input type="hidden" value="{{ $variation->status }}"
                                                    name="status[]">
                                                <input type="hidden" name="variation_id[]"
                                                    value="{{ $variation->id }}">

                                                {{-- quantity_id --}}
                                                <input type="hidden" name="quantity_id[]"
                                                    value="{{ $variation->item_quantity ? $variation->item_quantity->id : '' }}">
                                                <div class="form-group col-lg-4 col-md-4">
                                                    <label for="variation_desc">Variation Descriptions</label>
                                                    <input type="text" name="variation_desc[]" id="variation_desc"
                                                        class="form-control variation_desc"
                                                        placeholder="Enter Variation Descriptions"
                                                        value="{{ $variation->variation_desc }}">
                                                </div>

                                                <!-- Model dropdown -->
                                                <div class="form-group col-lg-4 col-md-4">
                                                    <!-- <input type="hidden" name="brand_variation_id[]"
                                                        id="brand_variation_id" class="brand_variation_id"
                                                        value="{{ $variation->brand_variation_id }}"> -->
                                                    <label for="model">Model</label>
                                                    <!-- <select name="model[]" id="model" class="form-control model">
                                                        <option value="" selected disabled>Select Model</option>
                                                        @foreach ($selected_models[$loop->index] as $model)
<option value="{{ $model->model }}"
                                                                @if ($model->model == $variation->model) selected @endif>
                                                                {{ $model->model }}
                                                            </option>
@endforeach
                                                    </select> -->
                                                    <input type="text" name="model[]" id="model"
                                                        class="form-control model" value="{{ $variation->model }}">
                                                </div>

                                                <!-- Colour dropdown -->
                                                <div class="form-group col-lg-4 col-md-4">
                                                    <label for="colour">Colour</label>
                                                    <!-- <select name="colour[]" id="colour"
                                                        class="form-control colour">
                                                        <option value="" disabled>Select Colour</option>
                                                        @foreach ($selected_colours[$loop->index] as $colour)
<option value="{{ $colour->colour }}"
                                                                @if ($colour->colour == $variation->colour) selected @endif>
                                                                {{ $colour->colour }}
                                                            </option>
@endforeach
                                                    </select> -->
                                                    <input type="text" name="colour[]" id="colour"
                                                        class="form-control colour" value="{{ $variation->colour }}">
                                                </div>




                                                <!-- Unit dropdown -->
                                                <div class="form-group col-lg-4 col-md-4">
                                                    <label for="unit">Unit <span
                                                            class="text-danger">*</span></label>
                                                    <select name="unit[]" id="unit" class="form-control"
                                                        required>
                                                        <option selected disabled>Select Unit</option>
                                                        @foreach ($units as $unit)
                                                            <option value="{{ $unit->unit }}"
                                                                {{ $variation->unit == $unit->unit ? 'selected' : '' }}>
                                                                {{ $unit->unit }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('unit')
                                                        <p class="text-danger">{{ $message }}</p>
                                                    @enderror
                                                </div>

                                                <!-- Warehouse Qty input -->
                                                <div class="form-group col-md-4 col-lg-4">
                                                    <label for="warehouse_qty">Warehouse Qty <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" name="warehouse_qty[]"
                                                        id="warehouse_qty"
                                                        value="{{ $variation->item_quantity ? $variation->item_quantity->warehouse_qty : '' }}"
                                                        placeholder="Enter Quantity" required>
                                                </div>


                                                @if (in_array('Available Qty', $choosePermission) || auth()->user()->is_admin == '1')
                                                    <div class="form-group col-md-4 col-lg-4">
                                                        <label for="available_qty">Available Qty<span
                                                                class="text-danger">*</span></label>
                                                        <input type="number" class="form-control"
                                                            name="available_qty[]" id="available_qty"
                                                            value="{{ $variation->item_quantity ? $variation->item_quantity->available_qty : '' }}"
                                                            placeholder="Enter Quantity">
                                                    </div>
                                                @endif

                                                @if (in_array('Delivery Qty', $choosePermission) || auth()->user()->is_admin == '1')
                                                    <div class="form-group col-md-4 col-lg-4">
                                                        <label for="deliver_qty">Delivery Qty<span
                                                                class="text-danger">*</span></label>
                                                        <input type="number" class="form-control"
                                                            name="deliver_qty[]" id="deliver_qty"
                                                            value="{{ $variation->item_quantity ? $variation->item_quantity->deliver_qty : '' }}"
                                                            placeholder="Enter Quantity">
                                                    </div>
                                                @endif


                                                <div class="form-group col-md-4 col-lg-4">
                                                    <label for="warranty">Warranty</label>
                                                    <input type="text" class="form-control" name="warranty[]"
                                                        id="warranty" value="{{ $variation->warranty }}"
                                                        placeholder="Enter Warranty">
                                                </div>

                                                <!-- Barcode input -->
                                                <div class="form-group col-md-4 col-lg-4">
                                                    <label for="barcode">Barcode</label>
                                                    <input type="text" class="form-control" name="barcode[]"
                                                        id="barcode" value="{{ $variation->barcode }}"
                                                        placeholder="Enter Barcode">
                                                </div>

                                                <!-- Product Code input -->
                                                <div class="form-group col-md-4 col-lg-4">
                                                    <label for="product_code">Product Code <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="product_code[]"
                                                        id="product_code" value="{{ $variation->product_code }}"
                                                        placeholder="Enter Product Code">
                                                </div>

                                                <!-- Stock Alert input -->
                                                <div class="form-group col-md-4 col-lg-4">
                                                    <label for="alert_qty">Stock Alert</label>
                                                    <input type="number" class="form-control" id="alert_qty"
                                                        name="alert_qty[]"
                                                        value="{{ $variation->item_quantity ? $variation->item_quantity->alert_qty : '' }}"
                                                        placeholder="Enter Stock Alert">
                                                </div>

                                                <!-- Expiry Date input -->
                                                <div class="form-group col-md-4 col-lg-4">
                                                    <label for="expired_date">Expired Date</label>
                                                    <input type="date" class="form-control" id="expired_date"
                                                        name="expired_date[]" value="{{ $variation->expired_date }}">
                                                </div>

                                                <!-- Stock Ageing Date input -->
                                                <div class="form-group col-md-4 col-lg-4">
                                                    <label for="stock_agent_date">Stock Arrival Date</label>
                                                    <input type="date" class="form-control" id="stock_agent_date"
                                                        name="stock_agent_date[]"
                                                        value="{{ $variation->stock_agent_date }}">
                                                </div>



                                                <div class="form-group col-md-4 col-lg-4">
                                                    <label for="buy_price">Purchase Price</label>
                                                    <input type="text" class="form-control" id="buy_price"
                                                        name="buy_price[]" placeholder="Enter Buy Price"
                                                        value="{{ $variation->buy_price }}">
                                                </div>

                                                {{-- <div class="form-group col-md-4 col-lg-4">
                                                    <label for="wholesale_price">Wholesale Price</label>
                                                    <input type="text" class="form-control" id="wholesale_price"
                                                        name="wholesale_price[]" placeholder="Enter WholeSale Price"
                                                        value="{{ $variation->wholesale_price }}">
                                                </div> --}}

                                                <div class="form-group col-md-4 col-lg-4">
                                                    <label for="retail_price"> Current Price</label>
                                                    <input type="text" class="form-control" id="retail_price"
                                                        name="retail_price[]" placeholder="Enter Current Price"
                                                        value="{{ $variation->retail_price }}">
                                                </div>

                                                <div class="form-group col-md-4 col-lg-4">
                                                    <label for="retail_set_price">Previous Price</label>
                                                    <input type="text" class="form-control" id="retail_set_price"
                                                        name="retail_set_price[]" placeholder="Enter Previous Price"
                                                        value="{{ $variation->retail_set_price }}">
                                                </div>

                                                <div class="form-group col-md-4 col-lg-4">
                                                    <label for="promotion_retail_unit">Retail Price</label>
                                                    <input type="text" class="form-control"
                                                        id="promotion_retail_unit" name="promotion_retail_unit[]"
                                                        placeholder="Enter Retail Price"
                                                        value="{{ $variation->promotion_retail_unit }}">
                                                </div>

                                                <div class="form-group col-md-4 col-lg-4">
                                                    <label for="promotion_retail_set">Wholesale Price</label>
                                                    <input type="text" class="form-control"
                                                        id="promotion_retail_set" name="promotion_retail_set[]"
                                                        placeholder="Enter Wholesale Price"
                                                        value="{{ $variation->promotion_retail_set }}">
                                                </div>

                                            </div>

                                            <hr>

                                            <!-- Add or remove rows based on condition -->
                                            @if ($key == 0)
                                                <button type="button" class="row-0 btn btn-success mt-3 addRow">
                                                    <i class="fa-solid fa-plus"></i>
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-success mt-3 addRow add-row-{{ $key }}">
                                                    <i class="fa-solid fa-plus"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger removeRow mt-3">
                                                    <i class="fa-solid fa-minus"></i>
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach




                                </div>



                            </div>
                            <!-- /.card-body -->
                            <div class="mb-3 ml-3 mt-3 d-flex justify-content-end">
                                <a href="{{ url('items') }}" class="btn btn-danger">Back</a>
                                <button type="submit" class="btn mx-2 btn-primary">Update</button>
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

            // Example AJAX call to fetch brand based on category
            $(document).on('change', '#item_category', function() {
                let category_name = $(this).val();
                let warehouse = $('#warehouse_id').val();

                $.ajax({
                    type: 'POST',
                    url: "{{ route('get.brand') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        category: category_name,
                        warehouse: warehouse
                    },
                    success: function(brands) {
                        $('#brand_id').empty(); // Clear current options
                        $('#brand_id').append(
                            '<option value="" disabled selected>Select Brand</option>'
                        ); // Default option
                        $.each(brands, function(index, brand) {
                            $('#brand_id').append('<option value="' + brand.id + '">' +
                                brand.name + '</option>');
                        });
                    }
                });

            });

            // Example AJAX call to fetch model based on brand
            $(document).on('change', '#brand_id', function() {
                let brand_id = $(this).val();
                let brand_name = $('#brand_id option:selected').text();
                $('#brand').val(brand_name);
                // console.log(brand_id);
                // $.ajax({
                //     type: 'POST',
                //     url: "{{ route('get.brand.model') }}",
                //     data: {
                //         _token: "{{ csrf_token() }}",
                //         brand: brand_id,
                //     },
                //     success: function(variations) {
                //         $('#model').empty(); // Clear current options
                //         $('#model').append(
                //             '<option value="" disabled selected>Select Model</option>'
                //         ); // Default option
                //         $.each(variations, function(index, variation) {
                //             $('#model').append('<option value="' + variation.model +
                //                 '">' +
                //                 variation.model + '</option>');
                //         });
                //     }
                // });

            });

            //change category and brand selected value when location change
            $(document).ready(function() {
                const selectedBranchId = $('#warehouse_id').val();
                const itemCategoryDropdown = $('#item_category');

                itemCategoryDropdown.find('option').each(function() {
                    const branchId = $(this).data('branch-id');

                    if (!branchId || branchId == selectedBranchId) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });

                $(document).on('change', '#warehouse_id', function() {
                    const newSelectedBranchId = $(this).val();
                    itemCategoryDropdown.val('');

                    itemCategoryDropdown.find('option').each(function() {
                        const branchId = $(this).data('branch-id');

                        if (!branchId || branchId == newSelectedBranchId) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                });
            });


            // $(document).on('change', '.model', function() {
            //     let model_name = $(this).val();
            //     let brand_id = $('#brand_id').val();
            //     let row = $(this).closest('.dynamic-row');

            //     $.ajax({
            //         type: 'POST',
            //         url: "{{ route('get.brand.colour') }}",
            //         data: {
            //             _token: "{{ csrf_token() }}",
            //             model: model_name,
            //             brand: brand_id,
            //         },
            //         success: function(variations) {
            //             row.find('.colour').empty().append(
            //                 '<option value="" disabled selected>Select Colour</option>');
            //             $.each(variations, function(index, variation) {
            //                 row.find('.colour').append('<option value="' + variation
            //                     .colour + '">' + variation.colour + '</option>');
            //             });

            //         }
            //     });
            // });

            // $(document).on('change', '.colour', function() {
            //     let colour_name = $(this).val();
            //     let brand_id = $('#brand_id').val();
            //     let model_name = $(this).closest('.dynamic-row').find('.model').val();
            //     let row = $(this).closest('.dynamic-row');

            //     $.ajax({
            //         type: 'POST',
            //         url: "{{ route('get.brand.size') }}",
            //         data: {
            //             _token: "{{ csrf_token() }}",
            //             colour: colour_name,
            //             model: model_name,
            //             brand: brand_id,
            //         },
            //         success: function(variations) {
            //             row.find('.size').empty().append(
            //                 '<option value="" disabled selected>Select Size</option>');
            //             $.each(variations, function(index, variation) {
            //                 row.find('.size').append('<option value="' + variation
            //                     .size + '">' + variation.size + '</option>');
            //             });
            //         }
            //     });
            // });

            // $(document).on('change', '.size', function() {
            //     let size_name = $(this).val();
            //     let colour_name = $(this).closest('.dynamic-row').find('.colour').val();
            //     let brand_id = $('#brand_id').val();
            //     let model_name = $(this).closest('.dynamic-row').find('.model').val();
            //     let row = $(this).closest('.dynamic-row');

            //     $.ajax({
            //         type: 'POST',
            //         url: "{{ route('get.brand.seater') }}",
            //         data: {
            //             _token: "{{ csrf_token() }}",
            //             colour: colour_name,
            //             model: model_name,
            //             size: size_name,
            //             brand: brand_id,
            //         },
            //         success: function(variations) {
            //             row.find('.seater').empty().append(
            //                 '<option value="" disabled selected>Select Seater</option>');
            //             $.each(variations, function(index, variation) {
            //                 row.find('.seater').append('<option value="' + variation
            //                     .seater + '">' + variation.seater + '</option>');
            //             });
            //         }
            //     });
            // });

            // $(document).on('change', '.seater', function() {
            //     let size_name = $(this).closest('.dynamic-row').find('.size').val();
            //     let colour_name = $(this).closest('.dynamic-row').find('.colour').val();
            //     let brand_id = $('#brand_id').val();
            //     let model_name = $(this).closest('.dynamic-row').find('.model').val();
            //     let seater = $(this).val();
            //     let row = $(this).closest('.dynamic-row');

            //     $.ajax({
            //         type: 'POST',
            //         url: "{{ route('get.brand.variation.id') }}",
            //         data: {
            //             _token: "{{ csrf_token() }}",
            //             colour: colour_name,
            //             model: model_name,
            //             size: size_name,
            //             brand: brand_id,
            //             seater: seater,
            //         },
            //         success: function(variation) {
            //             row.find('.brand_variation_id').val(variation.id);
            //         }
            //     });
            // });
        });
    </script>


    <script>
        $(document).ready(function() {
            let rowCount = {{ count($item_variations) }};
            console.log($('.row-group').length);

            function toggleMainAddRowButton() {
                if ($('.row-group').length === 1) {
                    $('.row-0').show();
                } else {
                    $('.row-0').hide();
                }
            }

            function updateAddRemoveButtons() {
                $('.row-group').each(function(index) {
                    if (index === $('.row-group').length - 1) {
                        $(this).find('.addRow').show();
                    } else {
                        $(this).find('.addRow').hide();
                    }
                    $(this).find('.removeRow').show();
                });
            }

            toggleMainAddRowButton();
            updateAddRemoveButtons();

            $(document).on('click', '.addRow', function() {
                rowCount++;
                console.log(rowCount);

                var newRow = $('.form-row').first().clone();
                newRow.find('input').val('');

                // var brandVariationIdValue = $('.brand_variation_id').first().val();
                // newRow.find('.brand_variation_id').val(brandVariationIdValue);

                var rowGroup = $('<div class="row-group"></div>');

                if ($('#rowContainer .row-group').length > 0) {
                    $('#rowContainer').append('<hr>');
                }
                rowGroup.append(newRow);

                rowGroup.append(
                    `<button type="button" class="btn btn-success mx-1 mt-3 addRow"><i class="fa-solid fa-plus"></i></button>` +
                    '<button type="button" class="btn btn-danger removeRow mt-3"><i class="fa-solid fa-minus"></i></button>'
                );

                $('#rowContainer').append(rowGroup);

                updateAddRemoveButtons();
                toggleMainAddRowButton();
            });

            $(document).on('click', '.removeRow', function() {
                var parentGroup = $(this).closest('.row-group');
                parentGroup.prev('hr').remove();
                parentGroup.remove();
                rowCount--;

                updateAddRemoveButtons();
                toggleMainAddRowButton();
            });
        });

        function previewImage(event) {
            var imagePreview = document.getElementById('imagePreview');
            var file = event.target.files[0];

            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
