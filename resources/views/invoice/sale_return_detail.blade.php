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

        @foreach ($profile as $pic)
            @if ($purchase_order->branch == $pic->branch)
                <div class="mt-5 text-center">
                    <img src="{{ asset('logos/' . ($pic->logos ?? 'null')) }}" width="180" height="120">

                </div>
                <div class="row" style="margin-top: 30px;">
                    <h4 class="text-center fw-bold">{{ $pic->name }}</h4>

                    <p class="text-center fw-bold" style="font-size: 14px;">
                        {{ $pic->address }}
                        <br>
                        {{ $pic->phno1 }}, {{ $pic->phno2 }}
                    </p>
                </div>
            @endif
        @endforeach

        {{-- <h1>
            Purchase Order
        </h1> --}}

        <div class="content-wrapper">
            <div class="content-body">
                <div class="card">
                    <div class="card-content">

                        <div class="card-body">

                            <div class="row" style="display:flex;position:relative">

                                <div style="width:40%;">



                                    <div class="input-group"><span style="font-weight:bolder"> Receiving Mode :&nbsp;
                                        </span><span style="font-weight:bolder">{{ $purchase_order->balance_due }}
                                        </span></div>
                                    <br>
                                    <br>
                                </div>
                                <div style="width:30%;position:absolute;right:0px;top:0px;">

                                    <label for="invociedate" class="caption"
                                        style="font-weight:bolder">{{ trans('Sale Return Number') }}</label>
                                    <div class="input-group"><span
                                            style="font-weight:bolder">{{ $purchase_order->quote_no }}</span> </div>
                                    <label for="invociedate" class="caption"
                                        style="font-weight:bolder">{{ trans('Sale Return  Date') }}</label>
                                    <div class="input-group"><span
                                            style="font-weight:bolder">{{ $purchase_order->po_date }}</span> </div>

                                </div>

                            </div>
                            <br>


                            <div class="row">
                                <div class="table-responsive">
                                    <table class="table text-center table-bordered" style="width: 100%">
                                        <thead class="bg-primary" style="color: black;">
                                            <tr class="text-white">
                                                <th>{{ trans('No') }}</th>
                                                <th>{{ trans('Item Name') }}</th>
                                                <th>{{ trans('Description') }}</th>
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
                                            @endphp
                                            @foreach ($purchase_sells as $sell)
                                                <tr>
                                                    <td>{{ $no }}</td>
                                                    <td>{{ $sell->product_name }}</td>
                                                    <td>{{ $sell->description }}</td>
                                                    <td>{{ $sell->product_qty }}</td>
                                                    <td>{{ $sell->unit }}</td>
                                                    <td>{{ number_format($sell->product_price) }}</td>
                                                    <td>{{ number_format($sell->discount) }}</td>

                                                    <td>
                                                        <span class="currenty"></span>
                                                        <span
                                                            class='ttlText'>{{ number_format($sell->product_qty * $sell->product_price - $sell->discount) }}</span>
                                                    </td>
                                                </tr>
                                                @php
                                                    $no++;
                                                @endphp
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="5" class="text-right"></td>
                                                <td style="font-weight: bolder; ">Sub Total
                                                </td>
                                                <td style="font-weight: bolder; ">
                                                    {{ number_format($purchase_order->sub_total) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" class="text-right"></td>
                                                <td style="font-weight: bolder; ">Overall Discount
                                                </td>
                                                <td style="font-weight: bolder; ">
                                                    {{ number_format($purchase_order->discount_total) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" class="text-right"></td>
                                                <td style="font-weight: bolder; ">Item Discount
                                                </td>
                                                <td style="font-weight: bolder; ">
                                                    {{ number_format($purchase_sells->sum('discount')) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" class="text-right"></td>
                                                <td style="font-weight: bolder; ">Total
                                                </td>
                                                <td style="font-weight: bolder; ">
                                                    {{ number_format($purchase_order->total) }}
                                                </td>
                                            </tr>
                                            @foreach ($payment_methods as $index => $payment_method)
                                                <tr>
                                                    <td colspan="5" class="text-right"></td>
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
                                                        {{ number_format($payment_method->payment_amount) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="5" class="text-right"></td>
                                                <td style="font-weight: bolder;">Deposit
                                                </td>
                                                <td style="font-weight: bolder;">
                                                    {{ number_format($purchase_order->deposit) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" class="text-right"></td>
                                                <td style="font-weight: bolder;">Remaning Balance
                                                </td>
                                                <td style="font-weight: bolder;">
                                                    {{ number_format($purchase_order->remain_balance) }}
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>





                            <br><br>

                            <table width="60%" class="">
                                <tr>
                                    <td style="font-weight: bolder">Remark - {{ $purchase_order->remark }}
                                    </td>
                                </tr>
                            </table>
                            {{-- <a href="{{ URL('/purchase_order/pdf', $idd) }}"> Print</a> --}}
                        </div>

                    </div>
                </div>
            </div>
            <a onclick="printPage()" id="printButton" class="mt-4 btn btn-success">Print</a>

        </div>

    </div>


</body>

</HTML>
<script>
    function printPage() {
        window.print();
    }
</script>
