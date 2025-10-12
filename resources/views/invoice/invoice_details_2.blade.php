<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>POS</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"
        integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" /> -->
    <link rel="stylesheet" href="{{ asset('locallink/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <style>
        input[type="checkbox"] {
            background-color: #FF2929 !important;
            color: FF2929;
        }

        input.small {
            width: 9px;
            height: 9px;
        }

        .table td,
        .table th {
            font-size: 12px;
            /* color: #76453B; */
        }

        #adjustment {
            font-size: 14px;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        @media print {
            .badge-btn {
                -webkit-print-color-adjust: exact;
                /* For WebKit browsers */
                color-adjust: exact;
                /* Standard property */
                /* background-color: #76453B; */
                /* Ensure background color is printed */
            }

            body {
                font-size: 20px;
                margin: 5px;
                padding: 0;
                /* color: #76453B; */
            }

            .container {
                width: 100%;
                max-width: 100%;
                margin: 0;
                padding: 0;
                /* color: #76453B; */
            }

            .table {
                margin: 0;
                padding: 0;
                border-collapse: collapse;
                /* color: #76453B; */
            }

            .table td,
            .table th {
                font-size: 12px;
                padding: 5px;
                line-height: 1;
                /* color: #76453B; */
            }

            .table,
            tr,
            td,
            th {
                page-break-inside: avoid;
                /* color: #76453B; */
            }

            input.small {
                width: 10px;
                height: 10px;
                color: red;
            }

            .no-print {
                display: none;
            }

            .footer {
                position: absolute;
                bottom: 0;
                font-size: 17px;
            }
        }
    </style>
</head>

<body>
    <div class="container custom-container mt-4">
        <!-- Invoice Header -->
        <div class="invoice-header d-flex justify-content-around">
            <img src="{{ asset('img/goldcoil.png') }}" alt="Company Logo" style="height: 80px;">
            <img src="{{ asset('img/massimo.jpg') }}" alt="Company Logo" style="height: 50px" class="mt-2">
        </div>

        <div class="d-flex justify-content-center align-items-center mb-4">
            @if ($invoice->status == 'invoice')
                <span class="badge-btn"
                    style="color: white;background-color: #000;padding: 2px 30px 2px 30px;font-size: 20px;">INVOICE</span>
            @else
                <span class="badge-btn"
                    style="color: white;background-color: #000;padding: 2px 30px 2px 30px;font-size: 20px;">Customer
                    Return</span>
            @endif
        </div>

        <table class="table table-bordered mb-4">
            <tbody>
                <tr>
                    <td class="d-flex justify-content-between align-items-center">
                        <span>NAME OF BUYER : {{ $invoice->customer_name ?? '' }}</span>
                        <span>MEMBERSHIP NO :
                            {{ $invoice->customer ? $invoice->customer->customer_id : '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                    </td>

                    @if ($invoice->status == 'invoice')
                        <td>INVOICE
                            NO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                            {{ $invoice->branch_invoice_no }}
                        </td>
                    @else
                        <td>CR
                            NO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                            {{ $invoice->branch_invoice_no }}
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

        <table class="table table-bordered text-center">
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
                    $rowCount = 10;
                @endphp

                @foreach ($sells as $key => $sell)
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
                        if ($sell->foc == 'No') {
                            $itemDiscount += $sell->discount_amt;
                        }
                    @endphp
                @endforeach

                @for ($i = count($sells) + 1; $i <= $rowCount; $i++)
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

        <!-- Items Table -->
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
                    <td colspan="2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>A : FURNITURE ASSEMBLY SERVICE</span>
                            <span><input type="checkbox" name="" id="">
                                ASSEMBLY
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        </div>
                    </td>
                    <td style="width: 10%;">TOTAL</td>
                    <td style="width: 13%;"></td>
                    <td style="width: 20%;">{{ $sub_total }} {{ $invoice->currency_method }}</td>
                </tr>
                <tr>
                    <td colspan="2" style="">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>B : DELIVERY SALES</span>
                            <span><input type="checkbox" name="" id="">
                                DELIVERY
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        </div>
                    </td>
                    <td style="width: 10%;">DISCOUNT</td>
                    <td style="width: 13%;"></td>
                    <td style="width: 20%;">{{ number_format($discount_total + $item_discount) }}
                        {{ $invoice->currency_method }}</td>
                </tr>
                <tr>
                    <td colspan="2" style="">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>C : PICKING SERVICE</span>
                            <span><input type="checkbox" name="" id="">
                                PICKING
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        </div>
                    </td>
                    <td style="width: 10%;">NET AMOUNT</td>
                    <td style="width: 13%;"></td>
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
                    <td style="width: 13%;"></td>
                    <td style="width: 20%;">{{ number_format($invoice->deposit) }} {{ $invoice->currency_method }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 10%;">BALANCE</td>
                    <td style="width: 13%;"></td>
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
                        <!-- 1. CASH <input type="checkbox" name="" id="" class="small"><br>
                        2. BANK- <span>AYA <input type="checkbox" name="" id="" class="small"></span>
                        <span>KPAY <input type="checkbox" name="" id="" class="small"></span>
                        <span>CB <input type="checkbox" name="" id="" class="small"></span>
                        <span>KBZ-OLD <input type="checkbox" name="" class="small" id=""></span>
                        <span>KBZ-Spe <input type="checkbox" name="" id="" class="small"></span> -->
                    </td>
                    <td colspan="3" class="text-start">SALE PERSON : {{ $invoice->sale_by }}</td>
                </tr>
                <tr>
                    <td class="text-start">SIGNATURE OF BUYER</td>
                    <td colspan="3" class="text-start">SHOWROOM &nbsp;:
                        {{ $invoice->warehouse ? $invoice->warehouse->name : '' }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="3" class="text-start">REMARK
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $invoice->remark }}</td>
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
        {{-- <div class="row mt-3 text-center">
            <p style="letter-spacing: 5px;font-size: 12px;">quality . elegant design . excellent service . satisfaction
            </p>
        </div> --}}

        <div class="footer">
            <div class="d-flex justify-content-center align-items-center" style="margin-bottom: 5px;">
                <img src="{{ asset('img/netray_logo.png') }}" alt="Company Logo"
                    style="height: 60px; margin-right: 10px;">
                <!-- <h4 class="mt-0 mb-0"><strong>Co.,Ltd.</strong></h4> -->
            </div>

            <div class="d-flex justify-content-center align-items-center flex-column">
                <span><strong>Distribution Office & Showroom</strong></span>
                <span>No-136, BoMyatHtun St, Lower Bl, BotaHtaung Tsp, Yangon, Myanmar</span>
                <span><strong>Tel : 01 8299523, 09 953156440, 09 73156440&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Email :
                        natray.distribution@gmail.com</strong></span>
            </div>

        </div>
        <div class="text-start mt-4 no-print">
            <button class="btn text-white btn-primary" onclick="window.print()">Print
                Invoice</button>
            <a class="btn btn-danger" style="border-radius:10px;" onclick="window.history.back()"><i
                    class="fa-solid fa-backward text-white"></i> Back</a>
        </div>
    </div>

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ZQXYuPBBvP43v8RlaOUwaCTGSOH6UJOM2Gxj67J72IkF75Be32QZrUm9WPH5k7yc" crossorigin="anonymous">
    </script> -->
</body>

</html>
