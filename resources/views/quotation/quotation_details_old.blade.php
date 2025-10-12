<!DOCTYPE html>
<HTML>

<head>
    <link rel="stylesheet" href="{{ asset('locallink/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}" />

</head>
<style>
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

<body style="margin:25px;">

    <div>

        <h1>
            Quotation
        </h1>

        <div class="content-wrapper">
            <div class="content-body">
                <div class="card">
                    <div class="card-content">

                        <div class="card-body">

                            <div class="row" style="display:flex;position:relative">

                                <div style="width:40%;">
                                    <h4>Quotation to</h4>
                                    <div class="input-group"><span style="font-weight:bolder">Customer Name :&nbsp;
                                        </span>{{ $quotation->customer_name }} </div>
                                    <br>
                                    <br>
                                </div>
                                <div style="width:30%;position:absolute;right:0px;top:0px;">

                                    <label for="invociedate" class="caption"
                                        style="font-weight:bolder">{{ trans('Quotations Number') }}</label>
                                    <div class="input-group"> {{ $quotation->quote_no }} </div>
                                    <label for="invociedate" class="caption"
                                        style="font-weight:bolder">{{ trans('Quotation Date') }}</label>
                                    <div class="input-group"> {{ $quotation->quote_date }}</div>

                                </div>

                            </div>
                            <br>
                            <div class="row" style="margin: 5;margin-top: -50px;">
                                <h3>Information</h3>
                                <div class="table-responsive">
                                    <table class="table table-bordered text-center">
                                        <thead class="bg-primary" style="color: black;">
                                            <tr class="text-white">
                                                <th style="width: 25%;">Phone Number</th>
                                                <th style="width: 25%;">Address</th>
                                                <th style="width: 25%;">Customer Type</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ $quotation->phno }}</td>
                                                <td>{{ $quotation->address }}</td>
                                                <td>{{ $quotation->type }}</td>

                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="row" style="margin-top: 1vh;">
                                <div class="table-responsive">
                                    <table class="table table-bordered text-center" style="width: 100%">
                                        <thead class="bg-primary" style="color: black;">
                                            <tr class="text-white">
                                                <th>{{ trans('No') }}</th>
                                                <th>{{ trans('Item Name') }}</th>
                                                <th>{{ trans('Descriptions') }}</th>
                                                <th>{{ trans('Qty') }}</th>
                                                <th>{{ trans('Unit') }}</th>
                                                <th>{{ trans('Price') }}</th>
                                                <th>{{ trans('Discounts') }}</th>

                                                {{-- <th style="width: 3%;"></th> --}}
                                                <th>{{ trans('Total') }}
                                                    {{-- ({{ config('currency.symbol') }}) --}}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $no = 1;
                                                $itemDiscount = 0;
                                                $subtotal = 0;
                                            @endphp
                                            @foreach ($sells as $sell)
                                                <tr>
                                                    <td>{{ $no }}</td>
                                                    <td>{{ $sell->part_number }}</td>
                                                    <td>{{ $sell->description }}</td>
                                                    <td>{{ $sell->product_qty }}</td>
                                                    <td>{{ $sell->unit }}</td>
                                                    <td>
                                                        @if ($quotation->sale_price_category == 'Default')
                                                            @if ($quotation->type == 'Whole Sale')
                                                                {{ number_format($sell->product_price) }}
                                                            @else
                                                                {{ number_format($sell->retail_price) }}
                                                            @endif
                                                        @elseif ($quotation->sale_price_category == 'Whole Sale')
                                                            {{ number_format($sell->product_price) }}
                                                        @elseif ($quotation->sale_price_category == 'Retail')
                                                            {{ number_format($sell->retail_price) }}
                                                        @else
                                                            {{ number_format($sell->retail_price) }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($sell->discount_category == 'kyat')
                                                        {{ number_format($sell->discount).' Ks' }}
                                                        @else
                                                        {{ number_format($sell->discount).' %' }}
                                                        @endif
                                                    </td>

                                                    <td>
                                                    @php
                                                        $amount = round($sell->product_qty * $sell->retail_price) - $sell->discount_amt;
                                                        $subtotal = round($sell->product_qty * $sell->retail_price);
                                                    @endphp
                                                        <span class="currenty"></span>
                                                        <span class='ttlText'>

                                                            {{-- @if ($quotation->sale_price_category == 'Default')
                                                                @if ($quotation->type == 'Whole Sale')
                                                                    {{ number_format($sell->product_price * $sell->product_qty - $sell->discount) }}
                                                                @else
                                                                    {{ number_format($sell->retail_price * $sell->product_qty - $sell->discount) }}
                                                                @endif
                                                            @elseif ($quotation->sale_price_category == 'Whole Sale')
                                                                {{ number_format($sell->product_price * $sell->product_qty - $sell->discount) }}
                                                            @elseif ($quotation->sale_price_category == 'Retail')
                                                                {{ number_format($sell->retail_price * $sell->product_qty - $sell->discount) }}
                                                            @else
                                                                {{ number_format($sell->retail_price * $sell->product_qty - $sell->discount) }}
                                                            @endif --}}
                                                            {{number_format($amount)}}
                                                        </span>
                                                    </td>
                                                </tr>
                                                @php
                                                    $no++;
                                                    if($sell->foc == 'No'){
                                                        $itemDiscount += $sell->discount_amt;
                                                    }
                                                @endphp
                                            @endforeach

                                        </tbody>
                                        <tfoot>
                                        @php
                                            if($quotation->currency_method == 'MMK'){
                                                $sub_total = number_format($quotation->sub_total);
                                                $discount_total = number_format($quotation->discount_total);
                                                $item_discount = number_format($itemDiscount);
                                                $total = number_format($quotation->total);
                                            }else{
                                                $sub_total = number_format($quotation->sub_total);
                                                $discount_total = number_format($quotation->discount_total);
                                                $item_discount = round($itemDiscount / $quotation->exchange_rate);
                                                $total = number_format($quotation->total);
                                            }
                                        @endphp
                                            <tr>
                                                <td colspan="6" class="text-right"></td>
                                                <td style="font-weight: bolder;">Sub Total
                                                </td>
                                                <td style="font-weight: bolder; ">
                                                {{ $sub_total }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="6" class="text-right"></td>
                                                <td style="font-weight: bolder; ">Overall Discount
                                                </td>
                                                <td style="font-weight: bolder;">
                                                {{ $discount_total }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="6" class="text-right"></td>
                                                <td style="font-weight: bolder; ">Item Discount
                                                </td>
                                                <td style="font-weight: bolder;">
                                                {{ $item_discount }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="6" class="text-right"></td>
                                                <td style="font-weight: bolder;">Total
                                                </td>
                                                <td style="font-weight: bolder; ">
                                                {{ $total }}
                                                </td>
                                            </tr>

                                            @foreach ($payment_methods as $index => $payment_method)
                                                <tr>
                                                    <td colspan="6" class="text-right"></td>
                                                    @if ($index == 0)
                                                        <td style="font-weight: bolder;">Payment Method
                                                            - {{ $payment_method->payment_method }}
                                                        </td>
                                                    @else
                                                        <td style="font-weight: bolder;">
                                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                            {{ $payment_method->payment_method }}
                                                        </td>
                                                    @endif

                                                    <td style="font-weight: bolder;">
                                                    @if($quotation->currency_method == 'MMK')
                                                        {{ number_format($payment_method->payment_amount) }}
                                                    @else
                                                        {{ number_format($payment_method->payment_amount) }}
                                                    @endif
                                                    </td>
                                                </tr>
                                            @endforeach

                                            <tr>
                                                <td colspan="6" class="text-right"></td>
                                                <td style="font-weight: bolder;">Deposit
                                                </td>
                                                <td style="font-weight: bolder;">
                                                @if($quotation->currency_method == 'MMK')
                                                    {{ number_format($quotation->deposit) }}
                                                @else
                                                    {{ number_format($quotation->deposit) }}
                                                @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="6" class="text-right"></td>
                                                <td style="font-weight: bolder;">Remaining Balance
                                                </td>
                                                <td style="font-weight: bolder;">
                                                @if($quotation->currency_method == 'MMK')
                                                    {{ number_format($quotation->remain_balance) }}
                                                @else
                                                    {{ number_format($quotation->remain_balance) }}
                                                @endif
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>


                            <table width="60%" class="">
                                <tr>
                                    <td style="font-weight: bolder">Remark - {{ $quotation->remark }}
                                    </td>
                                </tr>
                            </table>


                            <br><br>



                            {{-- <a href="{{ URL('/quotation/pdf', $idd) }}"> Print</a> --}}
                        </div>

                    </div>
                </div>
            </div>
            <a onclick="printPage()" id="printButton" class="btn btn-success mt-4">Print</a>

        </div>

    </div>


</body>

</HTML>
<script>
    function printPage() {
        window.print();
    }
</script>
