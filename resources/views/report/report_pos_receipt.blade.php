<!DOCTYPE html>
<HTML>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.min.js"></script>
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



                <a id="test" href="{{ url('pos') }}" class="btn btn-danger" style="border-radius:10px;"><i
                        class="fa-solid fa-backward text-white"></i> Back</a>
            </div>
        </div>
        @foreach ($profile as $pic)
            @if ($invoice->branch == $pic->branch)
                <div class="mt-5 text-center">
                    <img src="{{ asset('logos/' . ($pic->logos ?? 'null')) }}" width="130" height="80">
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

        <div class="row">
            <p class="fw-bold" style="font-size: 15px;">Sale ID: {{ $invoice->invoice_no }}<br>
                Customer :
                {{ $invoice->customer_name }}
                <br>
                Employee :
                {{ auth()->user()->name }}
            </p>

            <div class="mt-1 table-responsive">
                <table class="mt-1" style="font-size: 14px;width:100%">
                    <thead>
                        <tr class="text-left">
                            <th style="width: 40%;font-size: 15px;">Item Name.</th>
                            <th style="width: 15%;font-size: 15px;">Sale Price</th>
                            <th style="width: 15%;font-size: 15px;">Purchase Price</th>
                            <th style="width: 10%;font-size: 15px;">Qty</th>
                            <th class="text-end" style="width: 10%;font-size: 15px;"> Total Purchase</th>
                            <th class="text-end" style="width: 10%;font-size: 15px;"> Sale</th>
                        </tr>
                    </thead>
                    <tbody class="text-center" style="height:30px;font-size: 15px;">

                        @foreach ($invoices as $invoice)
                            @foreach ($invoice->sells as $key => $sell)
                                <tr class="text-start">
                                    <td>{{ $sell->part_number }}</td>
                                    <td>
                                        @if ($invoice->sale_price_category == 'Default')
                                            @if ($invoice->type == 'Whole Sale')
                                                {{ $sell->product_price }}
                                            @else
                                                {{ $sell->retail_price }}
                                            @endif
                                        @elseif ($invoice->sale_price_category == 'Whole Sale')
                                            {{ $sell->product_price }}
                                        @elseif ($invoice->sale_price_category == 'Retail')
                                            {{ $sell->retail_price }}
                                        @else
                                            {{ $sell->retail_price }}
                                        @endif
                                    </td>
                                    <td>{{ $sell->buy_price }}</td>
                                    <td>{{ $sell->product_qty }}</td>
                                    <td class="text-end">

                                        {{ $sell->buy_price * $sell->product_qty }}
                                    </td>
                                    <td class="text-end">
                                        @if ($invoice->sale_price_category == 'Default')
                                            @if ($invoice->type == 'Whole Sale')
                                                {{ $sell->product_price * $sell->product_qty }}
                                            @else
                                                {{ $sell->retail_price * $sell->product_qty }}
                                            @endif
                                        @elseif ($invoice->sale_price_category == 'Whole Sale')
                                            {{ $sell->product_price * $sell->product_qty }}
                                        @elseif ($invoice->sale_price_category == 'Retail')
                                            {{ $sell->retail_price * $sell->product_qty }}
                                        @else
                                            {{ $sell->retail_price * $sell->product_qty }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                    <tfoot style="border-top: 2px solid black !important;font-size: 15px;">
                        <!-- <tr style="line-height: 30px;">
                            <td colspan="3" class="text-end fw-bold">Sub Total</td>
                            <td class="text-end fw-bold">{{ $invoice->net_total ?? 0 }}</td>
                        </tr> -->
                        <!-- <tr style="line-height: 30px;">
                            <td colspan="3" class="text-end fw-bold">Discount</td>
                            <td class="text-end fw-bold">{{ $invoice->discount_total ?? 0 }}</td>
                        </tr>-->
                        <tr style="line-height: 30px;">
                            <td colspan="5" class="text-end fw-bold">Total Purchase</td>
                            <td class="text-end fw-bold">{{ $invoice->total_buy_price ?? 0 }}</td>
                        </tr>
                        <tr style="line-height: 30px;">
                            <td colspan="5" class="text-end fw-bold">Total Sale</td>
                            <td class="text-end fw-bold">{{ $invoice->total ?? 0 }}</td>
                        </tr>
                        <tr style="line-height: 30px;">
                            <td colspan="5" class="text-end fw-bold">Discount</td>
                            <td class="text-end fw-bold">{{ $invoice->discount_total ?? 0 }}</td>
                        </tr>
                        <tr style="line-height: 30px;">
                            <td colspan="5" class="text-end fw-bold">Cash</td>
                            <td class="text-end fw-bold">{{ $invoice->deposit ?? 0 }}</td>
                        </tr>
                        <tr style="line-height: 30px;">
                            <td colspan="5" class="text-end fw-bold">Change Due</td>
                            <td class="text-end fw-bold">{{ $invoice->remain_balance ?? 0 }}</td>
                        </tr>
                        <!--<tr style="line-height: 30px;">
                            <td colspan="3" class="text-end fw-bold">Deposit </td>
                            <td class="text-end fw-bold">{{ $invoice->deposit ?? 0 }}</td>
                        </tr>
                        <tr style="line-height: 30px;">
                            <td colspan="3" class="text-end fw-bold">Remaining Balance </td>
                            <td class="text-end fw-bold">{{ $invoice->remain_balance ?? 0 }}</td>
                        </tr> -->
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
