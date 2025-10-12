<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .print {
                display: none;
            }

            .invoice-box::before {
                content: "";
                /* background: url('{{ asset('img/solar_logo.jpg') }}') no-repeat center;
                background-size: contain;
                position: absolute; */
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                opacity: 0.1;
                /* Ensure watermark effect */
                z-index: -1;
            }

            .footer {
                position: fixed;
                bottom: 0;
            }

            .footer-sign {
                /* position: fixed; */
                /* Fixes the footer to the bottom of the page */
                bottom: 0;
                /* Ensures the footer is at the bottom */
                left: 0;
                /* Align the footer to the left edge */
                width: 100%;
                /* Ensure the footer spans the full width */
                text-align: center;
                font-family: 'Times New Roman', serif;
                font-size: 11px;
                padding: 10px 0;

                z-index: 1000;
                /* Optional: Ensure the footer stays on top of other content */
            }


        }

        body {
            font-family: Arial, sans-serif;

        }


        .table,
        .table th,
        .table td {
            background-color: transparent !important;
        }

        .invoice-box {
            position: relative;
        }

        .invoice-box::before {
            content: "";
            /* background: url('{{ asset('img/solar_logo.jpg') }}') no-repeat center center;
            background-size: contain; */
            /* Change to 'cover' if you want full coverage */
            /* position: absolute; */
            top: 45%;
            left: 50%;
            width: 95%;
            height: 50%;
            opacity: 0.1;
            /* Adjust for watermark effect */
            transform: translate(-50%, -50%);
            /* Ensures the image is perfectly centered */
            z-index: -1;
        }




        /* General table cell styles */
        .table th,
        .table td {
            text-align: center;
            vertical-align: middle;
        }


        .no-border {
            border: none !important;
        }

        /* Optional: Customize borders for other cells if needed */
        td,
        th {
            border: 1px solid #343a40 !important;
            /* Dark border color */
        }


        .payment-terms {
            font-size: 11px;
        }

        .footer-sign {

            text-align: center;
            font-family: 'Times New Roman', serif;
            font-size: 11px;

        }

        .signature-container {
            width: 100%;
        }

        .signature-row {
            display: flex;
            justify-content: space-around;
            align-items: center;
            margin-bottom: 10px;
        }

        .signature-box {
            text-align: center;
            flex: 1;
            padding: 0 10px;
        }

        .signature-box p {
            margin-bottom: 5px;
            font-weight: bold;
        }

        .signature-line {
            display: inline-block;
            width: 80%;
            border-bottom: 1px solid black;
            height: 20px;
        }
    </style>
</head>

<body>

    <div class="invoice-box container-fluid">
        <button class="btn btn-primary btn-sm mb-5 mt-5 print" onclick="window.print()">Print</button>

        <div class="header-logo mb-3 d-flex align-items-center">
            <img src="{{ asset('img/solar_logo.jpg') }}" alt="Company Logo" width="150" height="60"
                class="me-3">
            @if ($invoice->branch == '1')
                <div style="font-size: 11px;">
                    <strong>No-584(A/B) (7th) Quarter, South Okkalapa, Yangon, Myanmar.</strong>
                    <br>
                    <strong>Sale:</strong> +95 9754527716, +95 9780943579<br>
                    <strong>Office:</strong> +95 9776116773<br>
                    <strong>Email:</strong> greatsolarkabar@gmail.com

                    <br>
                    <br>
                    <strong>No-110st, 48st x 49st, Chan Mya Thar Si, Mandalay, Myanmar.</strong>
                    <br>
                    <strong>Tel:</strong> +95 9776116752, +95 9776116729, +95 9954688257<br>
                </div>
            @else
                <div style="font-size: 11px;">
                    <strong>No-110st, 48st x 49st, Chan Mya Thar Si, Mandalay, Myanmar.</strong>
                    <br>
                    <strong>Tel:</strong> +95 9776116752, +95 9776116729, +95 9954688257<br>


                    <br>
                    <br>
                    <strong>No-584(A/B) (7th) Quarter, South Okkalapa, Yangon, Myanmar.</strong>
                    <br>
                    <strong>Sale:</strong> +95 9754527716, +95 9780943579<br>
                    <strong>Office:</strong> +95 9776116773<br>
                    <strong>Email:</strong> greatsolarkabar@gmail.com
                </div>
            @endif



        </div>


        <br>

        <div class="row" style="font-size: 11px">
            <div class="col-6">
                <span><span>Customer Name:</span> {{ $invoice->customer_name }}</span>
                <br>
                <span><span>Address:</span> {{ $invoice->address }}</span>
                <br>
                <span><span>Contact Number:</span> {{ $invoice->phno }}</span>
            </div>
            <div class="col-6 text-end">
                <span><span>Invoice No:</span> {{ $invoice->invoice_no }}</span>
                <br>
                <span><span>Date:</span> {{ $invoice->invoice_date }}</span>
            </div>
        </div>

        <table class="table table-sm" style="font-size: 11px;">
            <thead class="align-middle">
                <tr>
                    <th class="py-1">No</th>
                    <th class="py-1" style="width: 39%;">Product Description</th>
                    <th class="py-1" style="width: 15%;">Warranty</th>
                    <th class="py-1" style="width: 8%;">Qty</th>
                    <th class="py-1" style="width: 7%;">Unit</th>

                    <th class="py-1" style="width: 16%;">Unit Price (MMK)</th>

                    <th class="py-1" style="width: 16%;">Total Price (MMK)</th>
                </tr>
            </thead>
            <tbody class="align-middle">
                @foreach ($sells as $key => $sell)
                    <tr>
                        <td class="py-1">{{ $key + 1 }}</td>
                        <td class="py-1">{{ $sell->product_name }}
                        </td>
                        <td class="py-1">{{ $sell->warranty }}</td>
                        <td class="py-1">{{ $sell->product_qty }}</td>
                        <td class="py-1">{{ $sell->unit }}
                        </td>
                        <td class="py-1">{{ number_format($sell->retail_price) }}</td>


                        <td class="py-1">{{ number_format($sell->retail_price * $sell->product_qty) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="align-middle">
                <tr>
                    <td colspan="4" rowspan="15" class="py-1 text-start" style="font-size: 11px;">
                        <strong>
                            Notes - Items sold are not refundable or exchangeable
                        </strong>
                        <br>
                        <strong>{{ $invoice->remark }}</strong>
                    </td>


                    {{-- <td class="py-1" colspan="2"><strong>Total (MMK):</strong></td> --}}
                    <td class="py-1" colspan="2"><strong>Total </strong></td>
                    <td class="py-1">{{ number_format($invoice->sub_total) }}</td>
                </tr>
                <tr>
                    <td class="py-1" colspan="2"><strong>Discount:</strong></td>
                    <td class="py-1">
                        @if ($invoice->discount_total == 0)
                            -
                        @else
                            {{ number_format($invoice->discount_total) }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="py-1" colspan="2"><strong>Sub Total:</strong></td>
                    <td class="py-1">{{ number_format($invoice->total) }}</td>
                </tr>
                @foreach ($make_payments as $make_payment)
                    <tr>
                        <td class="py-1" colspan="2"><strong>Advance
                                ({{ $make_payment->transaction->transaction_name }})
                                :</strong></td>
                        <td class="py-1">
                            @if ($make_payment->amount == 0)
                                -
                            @else
                                {{ number_format($make_payment->amount) }}
                            @endif
                        </td>
                    </tr>
                @endforeach

                <tr>
                    <td class="py-1" colspan="2"><strong>Balance:</strong></td>
                    <td class="py-1">{{ number_format($invoice->remain_balance) }}</td>
                </tr>
            </tfoot>
        </table>

        @if ($invoice->site == 'site')
            <div class="payment-terms mt-3  mx-5">
                <strong>Payment Terms:</strong>
                <span style="color: black;font-weight: 600"> 1st payment - 30% for project confirmation.</span>
                <br>
                <span style="margin-left: 80px;color: black;font-weight: 600">:2nd payment - 70% after arrival of main
                    items.</span>
                <br>
                <span style="margin-left: 80px;color: black;font-weight: 600">:Final payment - Installation &
                    Accessories
                    fee after the test
                    run.</span>


            </div>
        @endif

        <div class="bank-details mx-5 mt-5" style="font-size: 11px;">
            <span>Please Transfer To Special acc:</span>
            <br>
            <span>Account No: 12751199900124401 (KBZ)</span>
            <br>
            <span>Account No: 0020454620001063 (Yoma)</span>
            <br>
            <span>Account No: 40028681865 (AYA)</span>
            <br>
            <span>Account No: 0107100900016179 (CB)</span>
            <br>
            <span>Bank Name: Phyo Kyaw Swar Phay</span>
            <br>
            <span>Great Solar Kabar Co., Ltd</span>
        </div>

        <div class="footer-sign">
            <div class="signature-container">
                <p style="text-align: center; font-weight: bold;">THANK YOU FOR YOUR BUSINESS!</p>
                {{-- <p style="text-align: left; margin-left: 10px;"><i>"Goods are received and delivered in good
                        condition."</i></p> --}}


                <div class="signature-row">
                    <div class="signature-box">
                        <p>Customer's</p>
                    </div>
                    <div class="signature-box">
                        <p>Sale Staff's</p>
                    </div>
                    <div class="signature-box">
                        <p>Delivery Staff's</p>
                    </div>
                    <div class="signature-box">
                        <p>Authorized Person's</p>
                    </div>
                </div>

                <div class="signature-row">
                    <div class="signature-box"><span class="signature-line"></span></div>
                    <div class="signature-box"><span class="signature-line"></span></div>
                    <div class="signature-box"><span class="signature-line"></span></div>
                    <div class="signature-box"><span class="signature-line"></span></div>
                </div>

                <div class="signature-row">
                    <div class="signature-box">
                        <p>Signature</p>
                    </div>
                    <div class="signature-box">
                        <p>Signature</p>
                    </div>
                    <div class="signature-box">
                        <p>Signature</p>
                    </div>
                    <div class="signature-box">
                        <p>Signature</p>
                    </div>
                </div>

                <div class="signature-row">
                    <div class="signature-box"><span class="signature-line"></span></div>
                    <div class="signature-box"><span class="signature-line"></span></div>
                    <div class="signature-box"><span class="signature-line"></span></div>
                    <div class="signature-box"><span class="signature-line"></span></div>
                </div>

                <div class="signature-row">
                    <div class="signature-box">
                        <p>Name</p>
                    </div>
                    <div class="signature-box">
                        <p>Name</p>
                    </div>
                    <div class="signature-box">
                        <p>Name</p>
                    </div>
                    <div class="signature-box">
                        <p>Name</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="footer">
            <img src="{{ asset('img/logo_footer.jpeg') }}" alt="" style="width: 100%; height: 60px;">
        </div> --}}

    </div>

</body>

</html>
