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
                background-size: contain; */
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                opacity: 0.1;
                /* Ensure watermark effect */
                z-index: -1;
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
            position: absolute;
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
            font-size: 14px;
        }

        .footer-sign {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }

        .signature {
            text-align: center;
            margin-top: 50px;
            color: black;
            font-weight: 600;
            /* border-top: 1px solid black; */
            width: 45%;
            padding-top: 5px;
        }
    </style>
</head>

<body>

    <div class="invoice-box container-fluid">
        <div class="header-logo mb-3">
            <img src="{{ asset('img/quotation_logo.jpeg') }}" alt="Company Logo" width="300" height="170"
                style="align-items: start;">


        </div>


        <div class="row">
            <div class="col-6">
                <span><span>Name:</span> {{ $quotation->customer_name }}</span>
                <br>
                <span><span>Address:</span> {{ $quotation->address }}</span>
                <br>
                <span><span>Contact Number:</span> {{ $quotation->phno }}</span>
            </div>
            <div class="col-6 text-end">
                {{-- <span><span>Quotation No:</span> {{ $quotation->quote_no }}</span>
                <br> --}}
                <span><span>Date:</span> {{ \Carbon\Carbon::parse($quotation->quote_date)->format('d-m-Y') }}
                </span>
            </div>
        </div>

        <table class="table mt-3">
            <thead>
                <tr class="align-middle">
                    <th class="py-1">No</th>
                    <th>Brand</th>
                    <th class="py-1">Product Description</th>
                    {{-- <th class="py-1">Warranty</th> --}}
                    <th class="py-1">Qty</th>
                    <th class="py-1">Unit</th>
                    <th class="py-1">Price </th>
                    <th class="py-1">Discount</th>
                    <th class="py-1">Amount</th>
                </tr>
            </thead>
            <tbody>
                {{-- @foreach ($sells as $key => $sell)

                        <tr class="align-middle">
                            <td class="py-1">{{ $key + 1 }}</td>


                            <td class="py-1">{{ $sell->product_name }} </td>
                            <td class="py-1">{{ $sell->description }}</td>

                            <td class="py-1">{{ $sell->product_qty }}</td>
                            <td class="py-1">{{ $sell->unit }}</td>
                            <td class="py-1">
                                {{ number_format($sell->retail_price) }}
                            </td>
                            <td class="py-1">
                                @if ($sell->discount_category == 'kyat')
                                    {{ number_format($sell->discount) . ' Ks' }}
                                @else
                                    {{ number_format($sell->discount) . ' %' }}
                                @endif
                            </td>
                            <td class="py-1">

                                @if ($sell->discount_category == 'kyat')
                                    {{ number_format($sell->retail_price * $sell->product_qty - $sell->discount) }}
                                @else
                                    {{ number_format($sell->retail_price * $sell->product_qty - ($sell->retail_price * $sell->product_qty * $sell->discount) / 100) }}
                                @endif
                            </td>
                        </tr>


                @endforeach
 --}}
                @php

                    $categoryOrder = ['Major Hardware', 'Solar Mounting', 'Inverter Mounting', 'Battery Mounting'];

                    $totals = [];
                    $groupedSells = [];

                    foreach ($categoryOrder as $category) {
                        $totals[$category] = 0;
                        $groupedSells[$category] = [];
                    }

                    foreach ($sells as $sell) {
                        if (isset($groupedSells[$sell->product_category])) {
                            $groupedSells[$sell->product_category][] = $sell;

                            if ($sell->discount_category == 'kyat') {
                                $totals[$sell->product_category] +=
                                    $sell->retail_price * $sell->product_qty - $sell->discount;
                            } else {
                                $totals[$sell->product_category] +=
                                    $sell->retail_price * $sell->product_qty -
                                    ($sell->retail_price * $sell->product_qty * $sell->discount) / 100;
                            }
                        }
                    }
                @endphp

                @foreach ($categoryOrder as $category)
                    @if (!empty($groupedSells[$category]))
                        @foreach ($groupedSells[$category] as $key => $sell)
                            <tr class="align-middle">
                                <td class="py-1">{{ $key + 1 }}</td>
                                <td class="py-1">{{ $sell->product_name }}</td>
                                <td class="py-1">{{ $sell->description }}</td>
                                <td class="py-1">{{ $sell->product_qty }}</td>
                                <td class="py-1">{{ $sell->unit }}</td>
                                <td class="py-1">{{ number_format($sell->retail_price) }}</td>
                                <td class="py-1">
                                    @if ($sell->discount_category == 'kyat')
                                        {{ number_format($sell->discount) . ' Ks' }}
                                    @elseif($sell->discount_category == '%')
                                        {{ number_format($sell->discount) . ' %' }}
                                    @else
                                        {{ 'estimate' }}
                                    @endif
                                </td>
                                <td class="py-1">
                                    @if ($sell->discount_category == 'kyat')
                                        {{ number_format($sell->retail_price * $sell->product_qty - $sell->discount) }}
                                    @else
                                        {{ number_format($sell->retail_price * $sell->product_qty - ($sell->retail_price * $sell->product_qty * $sell->discount) / 100) }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="7" class="text-center"><strong>{{ $category }}</strong></td>
                            <td class="py-1"><strong>{{ number_format($totals[$category]) }}</strong></td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr class="align-middle">
                    <td rowspan="3" colspan="5" class="py-1"></td>
                    <td class="py-1" colspan="2"><strong>Total (estimate):</strong></td>
                    <td class="py-1">{{ number_format($quotation->total) }}</td>
                </tr>

                <tr>
                    <td class="py-1" colspan="2"><strong>Deposit:</strong></td>
                    <td class="py-1">{{ number_format($quotation->deposit) }}</td>
                </tr>
                <tr>
                    <td class="py-1" colspan="2"><strong>Balance:</strong></td>
                    <td class="py-1">{{ number_format($quotation->remain_balance) }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="payment-terms mt-3">
            <strong>Price Validity:</strong>
            <span style="color: black;">Price can be changed after 24 hours.Quantity of
                the items can be changed according to
                requirement of installation but price per unit is stable within 24 hours.</span>
            <br>
            <strong>Warranty:</strong>
            <span style="color: black;">Installation warranty 1 Year, Inverter 5 Years, Battery 5 Years
                and Solar Panels 12 Years.</span>
            <br>
            <strong>Payment Policy:</strong>
            <span style="color: black;">30% payment will be done upon confirmation of the project,
                another 30% after the
                transportation of items to the project's area and the final payment after commision of the
                project.</span>
            <br>
        </div>



        <button class="btn btn-primary btn  my-4 print" onclick="window.print()">Print</button>
    </div>

</body>

</html>
