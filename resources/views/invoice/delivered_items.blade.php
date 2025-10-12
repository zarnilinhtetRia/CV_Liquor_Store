<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Delivered Items</title>
    <link rel="stylesheet" href="{{ asset('locallink/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
</head>


<style>
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

    /* .equal-width-table td {
        width: 0%;
        word-wrap: break-word;
    } */
</style>

<body>
    <div class="container-fluid mt-4">
        <div class="col-md-12">
            @if (session('success'))
                <div class="col-md-12 alert alert-success alert-dismissible fade show d-flex justify-content-between"
                    role="alert">
                    <strong>{{ session('success') }}</strong>
                    <button type="button" class="close btn-success" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="col-md-12 alert alert-danger alert-dismissible fade show d-flex justify-content-between"
                    role="alert">
                    <strong>{{ session('error') }}</strong>
                    <button type="button" class="close btn-danger" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr class="font-bold text-black">
                                    <th class="text-center">
                                        <h5 class="mt-2">Total Origin Qty - {{ $total_deliver }}</h5>
                                    </th>
                                    <th class="text-center">
                                        <h5 class="mt-2">Remain Qty To Deliver -
                                            {{ $total_deliver - $delivered_qty }}</h5>
                                    </th>
                                    <th class="text-center">
                                        <h5 class="mt-2">Delivered Qty - {{ $delivered_qty }}</h5>
                                    </th>
                                    <th class="text-center">
                                        <h5 class="mt-2">Remaining Balance - {{ $invoice->remain_balance }}</h5>
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Delivery Items</h5>
                    </div>

                    <form method="POST" id="myForm" action="{{ url('delivered_items_save') }}">
                        @csrf
                        <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                        <input type="hidden" name="branch" id="branch" value="{{ $invoice->branch }}">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped equal-width-table">
                                    <thead>
                                        <th style="width: 5%;"></th>
                                        <th style="width: 40%;">Name</th>
                                        {{-- <th style="width: 10%;">Date</th> --}}
                                        <th style="width: 10%;">Origin Qty</th>
                                        <th style="width: 10%;">Delivered Qty</th>
                                        <th style="width: 10%;">Remain Qty</th>
                                        <th style="width: 10%;">Qty To Deliver</th>
                                    </thead>
                                    <tbody>
                                        @foreach ($sells as $key => $sell)
                                            <tr class="font-bold text-black">
                                                <td class="text-center">
                                                    <input type="checkbox" class="row-checkbox" name="ids[]"
                                                        value="{{ $key }}"
                                                        @if ($sell->product_qty - $sell->delivered_qty == 0) disabled @endif>
                                                    <input type="hidden" name="sell_id[]" value="{{ $sell->id }}">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control name" name="name[]"
                                                        value="{{ $sell->part_number }}" id="name-{{ $key }}"
                                                        readonly>
                                                    <input type="hidden" class="form-control variation_id"
                                                        name="variation_id[]" id="variation_id-{{ $sell->id }}"
                                                        value="{{ $sell->variation_id }}">

                                                    <input type="hidden" class="form-control item_id typeahead"
                                                        name="item_id[]" id="item_id-{{ $key }}"
                                                        value="{{ $sell->item_id }}">
                                                </td>
                                                {{-- <td>
                                                    <input type="date" class="form-control name" name="date[]"
                                                        value="{{ date('Y-m-d') }}" id="name-{{ $key }}">

                                                </td> --}}
                                                <td><input type="text" class="form-control origin_qty"
                                                        value="{{ $sell->product_qty }}" name="origin_qty[]"
                                                        id='origin_qty-{{ $key }}' readonly>
                                                </td>

                                                <td><input type="text" class="form-control delivered_qty"
                                                        value="{{ $sell->delivered_qty ?? 0 }}" name="delivered_qty[]"
                                                        id='delivered_qty-{{ $key }}' readonly>
                                                </td>
                                                <td><input type="text" class="form-control remain_qty"
                                                        value="{{ $sell->product_qty - $sell->delivered_qty ?? 0 }}"
                                                        id='remain_qty-{{ $key }}' readonly>
                                                </td>
                                                <td><input type="number" class="form-control to_deliver_qty"
                                                        value="" name="to_deliver_qty[]"
                                                        id='to_deliver_qty-{{ $key }}'
                                                        @if ($sell->product_qty - $sell->delivered_qty == 0) readonly @endif>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <div class="row mt-3 mb-3">
                                    <div class="form-group col-md-6">
                                        <label for="note">Date</label>
                                        <input type="date" name="date" id="date" class="form-control">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="note">Remark</label>
                                        <textarea class="form-control" name="remark" id="remark" cols="10" rows="3"></textarea>
                                    </div>
                                </div>

                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary m-2"
                                    onclick="return confirm('Are you sure you want to deliver this items ?')">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card mb-4 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Delivered Items Record</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mt-2">
                                <!-- <thead>
                                    <tr class="font-bold text-black">
                                        <th>Expired Date</th>
                                        <th>Model</th>
                                        <th>Colour</th>
                                    </tr>
                                </thead> -->
                                <tbody>
                                    @foreach ($delivered_items as $key => $delivered_item)
                                        <tr>
                                            <td class="text-center">{{ $key + 1 }}</td>
                                            {{-- <td class="text-center">Record {{$key+1}}</td> --}}
                                            <td class="text-center">{{ $delivered_item->do_no }}</td>
                                            <td class="text-center"><span class="text-success mt-0 pt-0"
                                                    style="font-weight:bolder !important;">Delivered</span></td>
                                            <td class="text-center">
                                                {{ date_format(new DateTime($delivered_item->date), 'd-m-Y') }}
                                            </td>
                                            <td class="text-center">
                                                Remark - {{ $delivered_item->remark ?? '' }}
                                            </td>
                                            {{-- <td class="text-center">
                                                <a href="{{ url('/do_form', $delivered_item->delivery_group_no) }}"
                                                    class="btn btn-success btn-sm">DO Form</a>
                                            </td> --}}
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class=" d-flex justify-content-end">
                            <!-- <button type="button" class="btn btn-primary m-2" onclick="printPage()">Print</button> -->
                            <!-- <a href="{{ url('items') }}" class="m-2 btn btn-danger">Back</a> -->
                            <a href="{{ url('invoice') }}" class="m-2 btn btn-danger">Back</a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script src="{{ asset('locallink/js/ajax_jquery.js') }}"></script>
    <script src="{{ asset('plugins/jquery/jquery.min.js ') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        // function printPage() {
        //     window.print();
        // }

        $(document).ready(function() {
            $(document).on('change', '.row-checkbox', function() {
                let row = $(this).closest('tr');
                let check_val = $(this).prop('checked'); // Use .prop('checked') to get the checkbox state
                console.log(check_val);
                if (check_val) {
                    row.find('.to_deliver_qty').prop('required', true);
                } else {
                    row.find('.to_deliver_qty').prop('required', false);
                }
            });
        });
    </script>
</body>

</html>
