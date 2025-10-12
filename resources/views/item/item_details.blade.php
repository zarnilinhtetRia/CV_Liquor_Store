<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Item Details</title>
    <link rel="stylesheet" href="{{ asset('locallink/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
</head>
<style>
    th {
        font-size: 14px;
        font-family: "Times New Roman", serif;
        font-weight: bold;
        /* text-transform: uppercase; */
    }

    td {
        font-size: 14px;
        font-family: "Times New Roman", serif;
    }

    button {
        font-family: 'Times New Roman', serif;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        font-family: "Times New Roman", serif;
        font-weight: bold;
    }

    a {
        font-family: 'Times New Roman', serif;
        font-size: 14px;
        /* Adjust size as needed */
        font-weight: bold;

        color: #007bff;
        /* Default link color (blue) */
        text-decoration: none;
        /* Removes underline */
    }

    @media print {
        #calculate {
            display: none;
        }
    }

    @media print {
        body {
            color: black;
            /* Set text color for printing */
        }

        /* Add any other styles you want to modify for printing */
    }

    @media print {

        #test,
        #printButton,
        .excelButton {
            display: none;
        }

        @page {
            size: auto;
            margin: 0;
        }
    }

    @media print {
        body {
            -webkit-print-color-adjust: exact;
        }
    }
</style>

<style>
    @media print {
        body {
            font-size: 12px;
            color: #333;
            text-align: center;
            /* Center the content horizontally */
        }

        .container {
            width: 100%;
            margin: 0 auto;
            /* Center the container horizontally */
            padding: 0;
        }

        .card {
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: none;
            text-align: left;
            /* Reset text alignment for card content */
        }

        .card-header {
            background-color: #f0f0f0;
            border-bottom: 1px solid #ccc;
            padding: 10px 15px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table th,
        .table td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        .btn {
            display: none;
        }
    }

    .user-name {
        /* Add your styling here */
        color: red;
        /* For example, set the text color to red */
        font-weight: bold;
        /* Set the font weight to bold */
        /* Add more styles as needed */
    }

    .fw-large {
        /* font-weight: bold; */
        font-size: larger;
        /* Add any other styles you want */
    }

    .font-bold {
        font-weight: 500;
    }

    .text-black {
        color: black;
    }

    .equal-width-table td {
        width: 50%;
        word-wrap: break-word;
    }

    #example1_filter {
        text-align: end;
        margin-bottom: 10px;
    }

    #example1_paginate ul {
        text-align: right;
        display: flex;
        justify-content: flex-end;
        list-style: none;
        padding: 0;
        margin: 0;
        margin-bottom: 10px;

    }
</style>




<body>
    {{-- <div class="container mt-3">
        <div class="card p-4">
            <div class="card-header" style="">
                <h4 style="font-size: 18px" class="fw-semibold">Item Details</h4>
            </div>

            @php
                $choosePermission = [];
                if (auth()->user()->permission) {
                    $decodedPermissions = json_decode(auth()->user()->permission, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $choosePermission = $decodedPermissions;
                    }
                }
            @endphp




            <div class="mt-3 d-flex justify-content-end">
                <a type="button" id="printButton" class="btn btn-primary m-2" onclick="printPage()">Print</a>
                <a type="button" id="printButton" class="m-2 excelButton btn btn-danger"
                    href="{{ url('items') }}">Back</a>
            </div>
        </div>
    </div> --}}

    <div class="container-fluid mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Product Details</h5>
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

                        @php
                            //can access permission
                            $isAdmin = auth()->user()->is_admin == '1';
                            $warehousePermission = auth()->user()->level ? json_decode(auth()->user()->level) : [];
                            $canAccess = $isAdmin || in_array($items->warehouse_id, $warehousePermission);
                        @endphp
                        @if (in_array('Item Edit', $choosePermission) || auth()->user()->is_admin == '1')
                            <a href="{{ $canAccess ? url('item_edit', $items->id) : '#' }}"
                                class="btn btn-success btn-sm {{ $canAccess ? '' : 'disabled' }}"
                                title="{{ $canAccess ? 'Edit Item' : 'You do not have permission to edit this item' }}">
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </a>
                        @endif
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped equal-width-table">
                                <tbody>
                                    <tr class="font-bold text-black">
                                        <td>Image</td>
                                        <td>
                                            <a href="{{ asset('item_images/' . $items->image) }}" target="_blank">
                                                <img src="{{ asset('item_images/' . $items->image) }}" alt="Item Image"
                                                    width="100" height="100" class="img-thumbnail">
                                            </a>
                                        </td>
                                    </tr>


                                    <tr class="font-bold text-black">
                                        <td>Product Name</td>
                                        <td>{{ $items->item_name }}</td>
                                    </tr>
                                    <tr class="font-bold text-black">
                                        <td>Product Descriptions</td>
                                        <td>{{ $items->item_descriptions }}</td>
                                    </tr>
                                    {{-- <tr class="font-bold text-black">
                                        <td>Item Type</td>
                                        <td>{{ $items->type }}</td>
                                    </tr> --}}
                                    <tr class="font-bold text-black">
                                        <td>Stock Type</td>
                                        <td>{{ $items->stock_type }}</td>
                                    </tr>
                                    <tr class="font-bold text-black">
                                        <td>Product Type</td>
                                        <td>{{ $items->item_type }}</td>
                                    </tr>
                                    {{-- <tr class="font-bold text-black">
                                        <td>Item Category</td>
                                        <td>{{ $items->item_category }}</td>
                                    </tr>
                                    <tr class="font-bold text-black">
                                        <td>Item Brand</td>
                                        <td>{{ $items->brand }}</td>
                                    </tr> --}}
                                    <tr class="font-bold text-black">
                                        <td>Location</td>
                                        <td>{{ $items->warehouse->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr class="font-bold text-black">
                                        <td>Total Quantity</td>
                                        <td>
                                            @if ($items->stock_type == 'Stock')
                                                {{ $items->variations->isNotEmpty() ? $items->variations->sum('item_quantity.warehouse_qty') : '0' }}
                                            @else
                                                {{ $items->variations->isNotEmpty() ? abs($items->variations->sum('item_quantity.warehouse_qty')) : '0' }}
                                            @endif
                                            {{-- {{ $variation->item_quantity->warehouse_qty }}{{ ' ' }}{{ $variation->unit }} --}}
                                        </td>
                                    </tr>


                                </tbody>
                            </table>
                        </div>



                        <h5 class="mt-5">Product Variations</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mt-2" id="example1">
                                <thead>
                                    <tr class="font-bold text-black">
                                        <th>Variation Description</th>
                                        <th>Expired Date</th>
                                        <th>Model</th>
                                        <th>Colour</th>
                                        {{-- <th>Size</th>
                                        <th>Seater</th> --}}
                                        <th>Warehouse Qty</th>
                                        <th>Available Qty</th>
                                        <th>Deliver Qty</th>
                                        <th>Warranty</th>

                                        <th>Alert Qty</th>
                                        <th>Stock Arrival Date</th>
                                        <th>Stock Range Date</th>
                                        <th>Product Code</th>
                                        @if (auth()->user()->is_admin == '1')
                                            <th>Purchase Price</th>
                                        @endif

                                        @if (in_array('Purchase Order', $choosePermission) || auth()->user()->is_admin == '1')
                                            <th>Cost Price</th>
                                        @endif
                                        {{-- <th>Wholesale Price</th> --}}
                                        <th>Current Price </th>
                                        <th> Previous Price</th>
                                        <th> Retail Price</th>
                                        <th>Wholesale Price</th>
                                        <th>Barcode</th>
                                        <th>Generate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($item_variations as $variation)
                                        <tr class="font-bold text-black">
                                            <td>{{ $variation->variation_desc }}</td>
                                            <td>{{ $variation->expired_date }}</td>
                                            <td>{{ $variation->model }}</td>
                                            <td>{{ $variation->colour }}</td>
                                            {{-- <td>{{ $variation->size }}</td>
                                            <td>{{ $variation->seater }}</td> --}}

                                            @php
                                                $warehouseqty = $variation->item_quantity
                                                    ? $variation->item_quantity->warehouse_qty
                                                    : '0';
                                                $availableqty = $variation->item_quantity
                                                    ? $variation->item_quantity->available_qty
                                                    : '0';
                                                $deliverqty = $variation->item_quantity
                                                    ? $variation->item_quantity->deliver_qty
                                                    : '0';
                                                $alert = $variation->item_quantity
                                                    ? $variation->item_quantity->alert_qty
                                                    : '0';
                                            @endphp
                                            <td
                                                class="@if ($warehouseqty <= $alert) text-danger @else text-success @endif text-center rounded">
                                                @if ($items->stock_type == 'Stock')
                                                    <span
                                                        style="font-weight: bold !important;font-size:16px !important;">{{ $warehouseqty . ' ' . $variation->unit }}</span>
                                                @else
                                                    <span
                                                        style="font-weight: bold !important;font-size:16px !important;">{{ abs($warehouseqty) . ' ' . $variation->unit }}</span>
                                                @endif
                                            </td>
                                            <td
                                                class="@if ($availableqty <= $alert) text-danger @else text-success @endif text-center rounded">
                                                @if ($items->stock_type == 'Stock')
                                                    <span
                                                        style="font-weight: bold !important;font-size:16px !important;">{{ $availableqty . ' ' . $variation->unit }}</span>
                                                @else
                                                    <span
                                                        style="font-weight: bold !important;font-size:16px !important;">{{ abs($availableqty) . ' ' . $variation->unit }}</span>
                                                @endif
                                            </td>
                                            <td
                                                class="@if ($deliverqty >= 10) text-danger @else text-success @endif text-center rounded">
                                                @if ($items->stock_type == 'Stock')
                                                    <span
                                                        style="font-weight: bold !important;font-size:16px !important;">{{ $deliverqty . ' ' . $variation->unit }}</span>
                                                @else
                                                    <span
                                                        style="font-weight: bold !important;font-size:16px !important;">{{ abs($deliverqty) . ' ' . $variation->unit }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $variation->warranty }}</td>

                                            <td>{{ $alert }}{{ ' ' }}{{ $variation->unit }}
                                            </td>
                                            <td>{{ $variation->stock_agent_date }}</td>
                                            <td>
                                                @php

                                                    $stockAgentDate = Carbon::parse($variation->stock_agent_date);
                                                    $currentDate = Carbon::now();

                                                    $diffInYears = $stockAgentDate->diffInYears($currentDate);
                                                    $diffInMonths = $stockAgentDate->diffInMonths($currentDate) % 12;
                                                    $totalDays = $stockAgentDate->diffInDays($currentDate);
                                                    $diffInWeeks = floor(($totalDays % 365.25) / 7);
                                                    $diffInDays = ($totalDays % 365.25) % 7;

                                                    $result = [];
                                                    if ($diffInYears > 0) {
                                                        $result[] =
                                                            $diffInYears . ' ' . Str::plural('year', $diffInYears);
                                                    }
                                                    if ($diffInMonths > 0) {
                                                        $result[] =
                                                            $diffInMonths . ' ' . Str::plural('month', $diffInMonths);
                                                    }
                                                    if ($diffInWeeks > 0) {
                                                        $result[] =
                                                            $diffInWeeks . ' ' . Str::plural('week', $diffInWeeks);
                                                    }
                                                    if ($diffInDays > 0) {
                                                        $result[] = $diffInDays . ' ' . Str::plural('day', $diffInDays);
                                                    }

                                                    $formattedDifference = implode(', ', $result);
                                                @endphp

                                                {{ $formattedDifference ?: 'No Sotck Agent Date' }}

                                            </td>

                                            <td>{{ $variation->product_code }}</td>
                                            @if (auth()->user()->is_admin == '1')
                                                <td>{{ $variation->buy_price ?? '0' }}</td>
                                            @endif
                                            @if (in_array('Purchase Order', $choosePermission) || auth()->user()->is_admin == '1')
                                                <td>{{ $variation->cost_price ?? '0' }}</td>
                                            @endif
                                            {{-- <td>{{ $variation->wholesale_price }}</td> --}}
                                            <td>{{ $variation->retail_price }}</td>
                                            <td>{{ $variation->retail_set_price }}</td>
                                            <td>{{ $variation->promotion_retail_unit }}</td>
                                            <td>{{ $variation->promotion_retail_set }}</td>
                                            <td>{{ $variation->barcode }}</td>

                                            <td>
                                                <a href="{{ $canAccess ? url('in_out', $variation->id) : '#' }}"
                                                    class="mt-1 btn btn-info btn-sm text-white {{ $canAccess ? '' : 'disabled' }}">In/Out
                                                    History </a>

                                                <a href="{{ $canAccess ? url('barcode/' . $items->id, $variation->id) : '#' }}"
                                                    class="mt-1 text-white btn btn-warning btn-sm  {{ $canAccess ? '' : 'disabled' }}">Generate</a>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>


                        <div class="mt-3 d-flex justify-content-end">
                            <button type="button" class="btn btn-primary m-2" onclick="printPage()">Print</button>
                            <!-- <a href="{{ url('items') }}" class="m-2 btn btn-danger">Back</a> -->
                            <a href="#" class="m-2 btn btn-danger" onclick="window.history.back()">Back</a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script src="{{ asset('plugins/jquery/jquery.min.js ') }}"></script>
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
                "lengthChange": false,
                "autoWidth": false,
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
                // "buttons": ["excel", "pdf", "print"]
            }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
        });
    </script>

    <script>
        function printPage() {
            window.print();
        }
    </script>

</body>

</html>
