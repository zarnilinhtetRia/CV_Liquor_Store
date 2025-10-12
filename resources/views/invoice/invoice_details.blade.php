<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SSE</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> -->
    <link rel="stylesheet" href="{{ asset('locallink/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">

    <style>
        input.small {
            width: 9px;
            height: 9px;
            color: #76453B;
        }

        .table td,
        .table th {
            font-size: 12px;
            color: #76453B;
        }

        #adjustment {
            font-size: 14px;
            color: #76453B;
        }

        /* .invoice-header {
        border-bottom: 2px solid #000;
        margin-bottom: 10px;
        padding-bottom: 11px;
        color: #76453B;
    }*/

        .table td,
        .table th {
            vertical-align: middle;
            color: #76453B;
        }

        body {
            color: #76453B;
        }

        @media print {
            .badge-btn {
                -webkit-print-color-adjust: exact;
                /* For WebKit browsers */
                color-adjust: exact;
                /* Standard property */
                background-color: #76453B;
                /* Ensure background color is printed */
            }

            body {
                font-size: 20px;
                margin: 0px;
                padding: 0;
                color: #76453B;
            }

            .container {
                width: 100%;
                max-width: 100%;
                margin: 0;
                padding: 0;
                color: #76453B;
            }

            .table {
                margin: 0;
                padding: 0;
                border-collapse: collapse;
                color: #76453B;
            }

            .table td,
            .table th {
                font-size: 11px;
                padding: 3px;
                line-height: 1;
                color: #76453B;
            }

            .table,
            tr,
            td,
            th {
                page-break-inside: avoid;
                color: #76453B;
            }

            input.small {
                width: 10px;
                height: 10px;
                color: #76453B;
            }

            .no-print {
                display: none;
            }

            .footer {
                align-items: center;
                bottom: 0;
                left: 40px;
                position: fixed;
            }
        }
    </style>
</head>

<body>
    <div class="container custom-container">
        <!-- Invoice Header -->
        <div class="invoice-header">
            <div class="d-flex justify-content-end align-items-end pt-2">

                @if ($invoice->status == 'invoice')
                    <span class="badge-btn"
                        style="color: white;background-color: #76453B;padding: 2px 20px 2px 20px;font-size: 20px;">Invoice</span>
                @else
                    <span class="badge-btn"
                        style="color: white;background-color: #76453B;padding: 2px 20px 2px 20px;font-size: 20px;">Customer
                        Return</span>
                @endif

            </div>

            <div class="d-flex justify-content-center flex-column align-items-center">
                <div class="">
                    {{-- <img src="{{ asset('img/navlogo.png') }}" alt="Login Image" width="200px"
                        style="background-color: #3b5176"> --}}
                    <h3><strong>SSE Web Solutions</strong></h3>
                </div>


                {{-- <div class="d-flex mb-2">
                    <img src="{{ asset('img/chair_1.png') }}" alt="Company Logo"
                        style="height: 18px; margin-right: 10px;">
                    <img src="{{ asset('img/caculation.png') }}" alt="Company Logo"
                        style="height: 18px; margin-right: 10px;">
                    <img src="{{ asset('img/couch.png') }}" alt="Company Logo"
                        style="height: 18px; margin-right: 10px;">
                    <img src="{{ asset('img/desk.png') }}" alt="Company Logo" style="height: 18px; margin-right: 10px;">
                    <img src="{{ asset('img/sofa-bed.png') }}" alt="Company Logo"
                        style="height: 18px; margin-right: 10px;">
                    <img src="{{ asset('img/chair.png') }}" alt="Company Logo"
                        style="height: 18px; margin-right: 10px;">
                </div> --}}

                <p id="adjustment" class="" style="white-space: nowrap;">
                    YANGON&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    No.153,Seik Kan Tha street(Lower block),Kyauktada Township Yangon, Myanmar, 11182,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tel
                    : 09 784 043109
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;email :
                    sse@gmail.com <br>

                </p>
            </div>
        </div>

        <table class="table table-bordered mb-4">
            <tbody>
                <tr>
                    <td class="d-flex justify-content-between align-items-center">
                        <span>NAME OF BUYER : {{ $invoice->customer_name ?? '' }}</span>
                        <span>MEMBERSHIP
                            NO
                            :
                            {{ $invoice->customer ? $invoice->customer->customer_id : '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                    </td>
                    @if ($invoice->status == 'invoice')
                        <td>INVOICE
                            NO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                            {{ $invoice->branch_invoice_no ?? '' }}
                        </td>
                    @else
                        <td>CR
                            NO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                            {{ $invoice->branch_invoice_no ?? '' }}
                        </td>
                    @endif

                </tr>
                <tr>
                    <td>DELIVERY ADDRESS : {{ $invoice->address ?? '' }}</td>

                    @if ($invoice->status == 'invoice')
                        <td>INVOICE DATE&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                            {{ $invoice->invoice_date ? date_format(new DateTime($invoice->invoice_date), 'd-m-Y') : '' }}
                        </td>
                    @else
                        <td>CR DATE&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                            {{ $invoice->invoice_date ? date_format(new DateTime($invoice->invoice_date), 'd-m-Y') : '' }}
                        </td>
                    @endif


                </tr>
                <tr>
                    <td></td>
                    <td>D/O
                        NO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        : {{ $invoice->do_no }}</td>
                </tr>
                <tr>
                    <td class="d-flex justify-content-between align-items-center">
                        <span>TEL : {{ $invoice->phno ?? '' }}</span>
                        <span>EMAIL :
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                    </td>
                    <td>DELIVERY DATE&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                        {{ $invoice->delivery_date ? date_format(new DateTime($invoice->delivery_date), 'd-m-Y') : '' }}
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Items Table -->
        <table class="table table-bordered text-center mb-1">
            <div class="d-flex justify-content-center">
                {{-- <img src="{{ asset('img/casabella2.png') }}" alt=""
                    style="position:absolute ;opacity: 0.1;height: 180px;width: 80%;"> --}}
            </div>
            <thead>
                <tr>
                    <th style="width: 7%;">SR</th>
                    <th>BRAND</th>
                    <th>DESCRIPTION</th>
                    <th style="width: 7%;">QTY</th>
                    <th>UNIT PRICE</th>
                    <th>AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $itemDiscount = 0;
                    $subtotal = 0;
                    $defaultRows = 10;
                    $rowCount = 0;
                @endphp
                @foreach ($sells as $key => $sell)
                    @php $rowCount++; @endphp
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $sell->item ? $sell->item->brand : '' }}</td>
                        <td>{{ $sell->description }}</td>
                        <td>{{ $sell->product_qty }}</td>
                        <td>{{ number_format($sell->retail_price) }} {{ $invoice->currency_method }}</td>
                        @php
                            $amount = round($sell->product_qty * $sell->retail_price);
                        @endphp
                        <td>{{ number_format($amount) }} {{ $invoice->currency_method }}</td>
                    </tr>
                    @php

                        $itemDiscount += $sell->discount_amt;

                    @endphp
                @endforeach
                @for ($i = $rowCount + 1; $i <= $defaultRows; $i++)
                    <tr>
                        <td>{{ $i }}</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    </tr>
                @endfor
            </tbody>
        </table>

        @php
            if ($invoice->currency_method == 'MMK') {
                $sub_total = number_format($invoice->sub_total);
                $discount_total = $invoice->discount_total;
                $item_discount = $itemDiscount;
                $total = number_format($invoice->total);
            } else {
                $sub_total = number_format($invoice->sub_total);
                $discount_total = $invoice->discount_total;
                $item_discount = round($itemDiscount / $invoice->exchange_rate);
                $total = number_format($invoice->total);
            }
        @endphp
        <table class="table table-bordered text-center">
            <tbody>
                <tr>
                    <td colspan="2" style="width: 59%;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>A : FURNITURE ASSEMBLY SERVICE</span>
                            <span><input type="checkbox" name="" id="">
                                ASSEMBLY
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        </div>
                    </td>
                    <td style="width: 10%;">TOTAL</td>
                    <td style="width: 12.5%;"></td>
                    <td style="width: 20%;">{{ $sub_total }} {{ $invoice->currency_method }}</td>
                </tr>
                <tr>
                    <td colspan="2" style="width: 59%;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>B : DELIVERY SALES</span>
                            <span><input type="checkbox" name="" id="">
                                DELIVERY
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        </div>
                    </td>
                    <td style="width: 10%;">DISCOUNT</td>
                    <td style="width: 12.5%;"></td>
                    <td style="width: 20%;">{{ number_format($discount_total + $item_discount) }}
                        {{ $invoice->currency_method }}</td>
                </tr>
                <tr>
                    <td colspan="2" style="width: 59%;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>C : PICKING SERVICE</span>
                            <span><input type="checkbox" name="" id="">
                                PICKING
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        </div>
                    </td>
                    <td style="width: 10%;">NET AMOUNT</td>
                    <td style="width: 12.5%;"></td>
                    <td style="width: 20%;">{{ $total }} {{ $invoice->currency_method }}</td>
                </tr>
                <tr>
                    <td colspan="2" rowspan="2" class="text-start">
                        <span>
                            E, & O,E - I/WE,THE UNDERSIGNED,CONFIRM THE ORDER AND AGREE WITH <br>
                            THE TERMS AND CONDITIONS STIPULATED HERE IN AND OVERLEAF <br>
                            GOODS DELIVERED AND EXAMINED ARE NOT RETURNABLE
                        </span>
                    </td>
                    <td style="width: 10%;">DEPOSIT</td>
                    <td style="width: 12.5%;"></td>
                    <td style="width: 20%;">{{ number_format($invoice->deposit) }} {{ $invoice->currency_method }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 10%;">BALANCE</td>
                    <td style="width: 12.5%;"></td>
                    <td style="width: 20%;">{{ number_format($invoice->remain_balance) }}
                        {{ $invoice->currency_method }}</td>
                </tr>
                <tr>
                    <td class="text-start">
                        RECEIVED ABOVE GOODS <br>
                        IN GOOD ORDER AND CONDITION
                    </td>
                    <td rowspan="3" style="font-size: 12px;width: 15%;" class="small">
                        PAYMENT BY <br>
                        @foreach ($payment_category as $key => $category)
                            {{ $key + 1 . '. ' . strtoupper($category->transaction->transaction_name) }} <input
                                type="checkbox" name="" id="" class="small" checked><br>
                        @endforeach
                        <!-- 1. CASH <input type="checkbox" name="" id="" class="small"><br> -->
                        <!-- <span>KBZ-OLD <input type="checkbox" name="" class="small" id=""></span>
                        <span>KBZ-Spe <input type="checkbox" name="" id="" class="small"></span> -->
                    </td>
                    <td colspan="3" class="text-start">SALE PERSON&nbsp;&nbsp;:&nbsp;&nbsp;{{ $invoice->sale_by }}
                    </td>
                </tr>
                <tr>
                    <td class="text-start">SIGNATURE OF BUYER</td>
                    <td colspan="3" class="text-start">
                        SHOWROOM&nbsp;&nbsp;:&nbsp;&nbsp;{{ $invoice->warehouse ? $invoice->warehouse->name : '' }}
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="3" class="text-start">REMARK&nbsp;&nbsp;:&nbsp;&nbsp;{{ $invoice->remark }}</td>
                </tr>
            </tbody>
        </table>

        @if ($invoice->status == 'invoice')
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Cash Voucher Number</th>
                        <th>Payment Method</th>
                        <th>Amount</th>
                        <th>Note</th>
                        <th>Payment Date</th>
                        <th class="no-print"></th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($make_payments as $key => $make_payment)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $make_payment->invoice_no }}</td>
                            <td>{{ $make_payment->transaction->transaction_name }}</td>
                            <td>{{ number_format($make_payment->amount) }} {{ $invoice->currency_method }}</td>
                            <td>{{ $make_payment->note }}</td>
                            <td>{{ $make_payment->payment_date }}</td>
                            <td class="no-print">
                                <a href="{{ url('cash_voucher', $make_payment->id) }}"
                                    class="btn btn-primary">Print</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        @endif


        <!-- Total Section -->

        {{-- <div class="footer">
            <div class="row text-center">
                <div class="col-12">
                    <p style="letter-spacing: 5px;font-size: 12px;">quality . elegant design . excellent service .
                        satisfaction
                    </p>
                </div>
                <div class="col-3" style="margin-top: -13px;">
                    <img src="{{ asset('img/lorenzo.png') }}" alt="Company Logo"
                        style="height: 60px; margin-right: 10px;">
                </div>
                <div class="col-3" style="margin-top: -13px;">
                    <img src="{{ asset('img/dunlopillo-Brand.png') }}" alt="Company Logo"
                        style="height: 50px; margin-right: 10px;">
                </div>
                <div class="col-3" style="margin-top: -13px;">
                    <img src="{{ asset('img/osim.png') }}" alt="Company Logo"
                        style="height: 60px; margin-right: 10px;">
                </div>
                <div class="col-3" style="margin-top: -13px;">
                    <img src="{{ asset('img/simmons_logo.png') }}" alt="Company Logo"
                        style="height: 25px; margin-right: 10px; margin-top: 10px;">
                </div>
            </div>
            <div class="row mb-2 text-center">
                <div class="col-4 text-center">
                    <img src="{{ asset('img/eurotex_Logo.jpg') }}" alt="Company Logo"
                        style="height: 55px; margin-right: 10px;">
                </div>
                <div class="col-4 text-start" style="margin-top: 5px;">
                    <img src="{{ asset('img/goldcoil.png') }}" alt="Company Logo"
                        style="height: 45px; margin-left: 20px;">
                </div>
                <div class="col-4 text-start">
                    <img src="{{ asset('img/massimo.jpg') }}" alt="Company Logo"
                        style="height: 40px; margin-right: 10px;">
                </div>
            </div>

            <div class="d-flex justify-content-around">
                <p style="font-size: 14px;"><i
                        class="fa-brands fa-facebook me-1"></i>www.facebook/CashbellaLuxuryFurnitureMyanmar</p>
                <p style="font-size: 14px;"><i class="fa-solid fa-globe me-1"></i>www.casabellamm.com</p>
            </div>
        </div> --}}

        <!-- Footer -->
        <div class="text-start mt-4 no-print">
            <button class="btn btn-primary" onclick="window.print()">Print Invoice</button>
            <a class="btn btn-danger" style="border-radius:10px;" onclick="window.history.back()"><i
                    class="fa-solid fa-backward text-white"></i> Back</a>
        </div>
    </div>

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ZQXYuPBBvP43v8RlaOUwaCTGSOH6UJOM2Gxj67J72IkF75Be32QZrUm9WPH5k7yc" crossorigin="anonymous">
    </script> -->
</body>

</html>
