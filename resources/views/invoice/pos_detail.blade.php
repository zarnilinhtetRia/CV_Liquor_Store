<!DOCTYPE html>
<HTML>

<head>
    <link rel="stylesheet" href="{{ asset('locallink/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <style>
        input {
            position: relative;
            width: 150px;
            height: 40px;
            color: white;
        }

        input:before {
            position: absolute;
            top: 3px;
            left: 3px;
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
            top: 3px;
            right: 0;
            color: black;
            opacity: 1;
        }
    </style>
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

            #print1 {
                display: none;
            }

            #print2 {
                display: none;
            }

            #print3 {
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


</head>

<body>

    <div class="container mx-auto">
        <div class="row">
            <div class="col-6">
            </div>
            <div class="gap-2 pt-2 col-6 d-flex align-items-center justify-content-end">
                <a onclick="printPage()" id="printButton" class=" btn btn-success" class="btn btn-primary"
                    style="border-radius:10px;"><i class="fa-solid fa-print text-white"></i> Print</a>
                <a href="{{ url('daily_sales') }}" class="btn btn-primary" style="border-radius:10px;" id="print2"><i
                        class="fa-regular fa-calendar-days"></i> Daily Sales</a>

                <a href="{{ url('pos_register') }}" class="text-white btn btn-primary" style="border-radius:10px;"
                    id="print3"><i class="fa-solid fa-circle-plus"></i> POS Register</a>

                <a id="test" href="{{ url('pos') }}" class="btn btn-danger" style="border-radius:10px;"><i
                        class="fa-solid fa-backward text-white"></i> Back</a>
            </div>
        </div>
        @foreach ($profile as $pic)
            @if ($invoice->branch == $pic->branch)
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
        <div class="mt-3 row">
            <h6 class="text-center">Sales Receipt<br>
                <?= $currentDate = date('d-m-Y') ?></h6>
        </div>

        <div class="mt-3 row">
            <p class="fw-bold" style="font-size: 12px;">Sale ID: {{ $invoice->invoice_no }}
                <br>Employee :
                {{ auth()->user()->name }}
                <br>Customer :
                {{ $invoice->customer_name }}
                <br>Phone :
                {{ $invoice->phno }}
                <br>Address :
                {{ $invoice->address }}
            </p>

            <div class="mt-1 table-responsive">
                <table class="mt-1" style="font-size: 14px;width:100%">
                    <thead>
                        <tr class="text-left">
                            <th style="width: 40%;font-size: 10px;">Item Name.</th>
                            <th style="width: 10%;font-size: 10px;">Qty</th>
                            <th style="width: 20%;font-size: 10px;">Price</th>
                            <th style="width: 20%;font-size: 10px;">Discounts</th>
                            <th class="text-end" style="width: 10%;font-size: 10px;">Total</th>
                        </tr>
                    </thead>
                    <tbody class="text-center" style="height:30px">

                        @foreach ($invoices as $invoice)
                            @foreach ($invoice->sells as $key => $sell)
                                <tr class="text-start">
                                    <td style="font-size: 12px;">{{ $sell->product_name }}</td>
                                    <td style="font-size: 12px;">{{ $sell->product_qty }}</td>
                                    <td style="font-size: 12px;">
                                        @if ($invoice->sale_price_category == 'Default')
                                            @if ($invoice->type == 'Whole Sale')
                                                {{ number_format($sell->product_price) }}
                                            @else
                                                {{ number_format($sell->retail_price) }}
                                            @endif
                                        @elseif ($invoice->sale_price_category == 'Whole Sale')
                                            {{ number_format($sell->product_price) }}
                                        @elseif ($invoice->sale_price_category == 'Retail')
                                            {{ number_format($sell->retail_price) }}
                                        @else
                                            {{ number_format($sell->retail_price) }}
                                        @endif
                                    </td>
                                    <td style="font-size: 12px;">
                                        {{ number_format($sell->discount) }}@if ($sell->discount_category == 'percent')
                                            %
                                        @else
                                            ks
                                        @endif
                                    </td>

                                    <td class="text-end" style="font-size: 12px;">
                                        @if ($invoice->sale_price_category == 'Default')
                                            @if ($invoice->type == 'Whole Sale')
                                                @if ($sell->discount_category == 'percent')
                                                    {{ number_format($sell->product_price * $sell->product_qty - ($sell->product_price * $sell->product_qty * $sell->discount) / 100) }}
                                                @else
                                                    {{ number_format($sell->product_price * $sell->product_qty - $sell->discount) }}
                                                @endif
                                            @else
                                                @if ($sell->discount_category == 'percent')
                                                    {{ number_format($sell->retail_price * $sell->product_qty - ($sell->retail_price * $sell->product_qty * $sell->discount) / 100) }}
                                                @else
                                                    {{ number_format($sell->retail_price * $sell->product_qty - $sell->discount) }}
                                                @endif
                                            @endif
                                        @elseif ($invoice->sale_price_category == 'Whole Sale')
                                            @if ($sell->discount_category == 'percent')
                                                {{ number_format($sell->product_price * $sell->product_qty - ($sell->product_price * $sell->product_qty * $sell->discount) / 100) }}
                                            @else
                                                {{ number_format($sell->product_price * $sell->product_qty - $sell->discount) }}
                                            @endif
                                        @elseif ($invoice->sale_price_category == 'Retail')
                                            @if ($sell->discount_category == 'percent')
                                                {{ number_format($sell->retail_price * $sell->product_qty - ($sell->retail_price * $sell->product_qty * $sell->discount) / 100) }}
                                            @else
                                                {{ number_format($sell->retail_price * $sell->product_qty - $sell->discount) }}
                                            @endif
                                        @else
                                            @if ($sell->discount_category == 'percent')
                                                {{ number_format($sell->retail_price * $sell->product_qty - ($sell->retail_price * $sell->product_qty * $sell->discount) / 100) }}
                                            @else
                                                {{ number_format($sell->retail_price * $sell->product_qty - $sell->discount) }}
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                    <tfoot style="border-top: 2px solid black !important;font-size: 12px;">

                        <tr style="line-height: 20px;">
                            <td colspan="4" class="text-end fw-bold">Total</td>
                            <td class="text-end fw-bold">{{ number_format($invoice->total ?? 0) }}</td>
                        </tr>
                        <tr style="line-height: 20px;">
                            <td colspan="4" class="text-end fw-bold">Overall Discount</td>
                            <td class="text-end fw-bold">{{ number_format($invoice->discount_total ?? 0) }}</td>
                        </tr>
                        {{-- <tr style="line-height: 20px;">
                            <td colspan="4" class="text-end fw-bold">Item Discount</td>
                            <td class="text-end fw-bold">
                                {{ number_format($sells->sum('discount')) }}</td>
                        </tr> --}}
                        @foreach ($payment_methods as $index => $payment_method)
                            <tr style="line-height: 20px;">

                                <td colspan="4" class="text-end fw-bold">
                                    @if ($index == 0)
                                        Payment -
                                    @endif
                                    {{ $payment_method->transaction->transaction_name ?? '' }}
                                </td>

                                <td class="text-end fw-bold">
                                    {{ number_format($payment_method->payment_amount ?? 0) }}

                                </td>
                            </tr>
                        @endforeach

                        <tr style="line-height: 20px;">
                            <td colspan="4" class="text-end fw-bold">Change Due</td>
                            <td class="text-end fw-bold">{{ number_format($invoice->remain_balance ?? 0) }}</td>
                        </tr>

                    </tfoot>
                </table>
            </div>

        </div>
    </div>

    <script>
        function printPage() {
            window.print();
        }
    </script>


</body>

</HTML>
