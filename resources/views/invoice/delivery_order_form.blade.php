<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Delivery Order</title>
    <style>
        .invoice-header {
            /* border-bottom: 2px solid #000; */
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .invoice-table th,
        .invoice-table td {
            padding: 10px;
            text-align: center;
        }

        .invoice-table th {
            background-color: #f8f9fa;
        }

        .invoice-total {
            /* border-top: 2px solid #000; */
            font-size: 1.25rem;
            font-weight: bold;
            /* padding-top: 15px; */
        }

        .invoice-footer {
            display: flex;
            justify-content: space-between;
            text-align: center;
            margin-top: 30px;
        }

        .adjustment {
            font-size: 16px;
        }

        .underline {
            display: inline-block;
            width: 120%;
            border-bottom: 1px dashed #000;
        }

        td {
            font-size: 15px !important;
        }

        .underline-dashed {
            text-decoration: underline;
            text-decoration-style: dashed;
            text-underline-offset: 5px;
            /* Optional: Adjusts the distance of the underline */
        }

        /* Print Styles */
        @media print {
            body {
                font-size: 20px;
                margin: 0;
                padding: 0;
            }

            .container {
                max-width: 100%;
                margin: 0;
            }

            .invoice-header {
                margin-bottom: 15px;
            }

            .invoice-footer {
                font-size: 10pt;
            }

            .invoice-table th,
            .invoice-table td {
                padding: 5px;
            }

            .invoice-footer p {
                margin: 10px 0;
            }

            .invoice-header img {
                height: 50px;
                margin-right: 10px;
            }

            .invoice-header h4,
            .invoice-header h5 {
                margin: 0;
            }

            /* Hide buttons and elements not needed for printing */
            .no-print {
                display: none;
            }

            .adjustment {
                font-size: 16px;
                white-space: nowrap;
                /* Prevent line breaks */
            }

            .underline {
                width: 120%;
                /* Limit the width for printing */
                white-space: nowrap;
                /* Prevent line wrapping */
            }

        }
    </style>
</head>

<body>
    <div class="container mt-5">

        <div class="invoice-header">
            <!-- Print Button (visible only on screen) -->
            <div class="text-start mb-2 no-print">
                <button class="btn btn-primary" onclick="window.print()">Print</button>
                <a class="btn btn-danger" style="border-radius:10px;" onclick="window.history.back()"><i
                        class="fa-solid fa-backward text-white"></i> Back</a>
            </div>

            <div class="row text-center">
                <h3>Delivery Order</h3>
                <div class="d-flex justify-content-center align-items-center" style="margin-bottom: 5px;">
                    <img src="{{ asset('img/netray_logo.png') }}" alt="Company Logo"
                        style="height: 80px;  margin-right: 10px;" />
                    <!-- <h4 class="mt-0 mb-0"><strong>Co.,Ltd.</strong></h4> -->
                </div>
            </div>

            <!-- Flex container for the two columns -->
            <div class="d-flex justify-content-between">
                <div class="col-md-9">
                    <p class="adjustment fw-bold" style="white-space: nowrap;">
                        Casabella
                        Yangon&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        22, Pyay Road, 9 Mile,
                        Mayangone Tsp,
                        Yangon, Tel: 09 43115965, 01 8664363
                        <br>
                        Casabella Mandalay&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Corner of 77 and 32
                        Street, Mandalay, Tel: 02 4069366, 09
                        777147889 <br>
                        Casabella NayPyiTaw&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Ocean Ottaya Thiri,
                        Ground Floor, Tel: 09 269378893 <br>
                        Distribution Office&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        136,
                        Lower Bo Myat
                        Tun Street, Botataung Tsp, Yangon,
                        Tel: 01 299523, 09
                        953156440 <br>
                        Head
                        Office&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        22, Pyay Road, 9 Mile,
                        Mayangone Tsp, Yangon, Tel: 01 656 586,
                        651 181
                    </p>
                </div>
                <div class="col-md-3 text-end">
                    <p class="adjustment fw-bold">
                        Invoice No: <span
                            class="underline-dashed">&nbsp;&nbsp;{{ $invoice->invoice_no }}&nbsp;&nbsp;</span><br>
                        Delivery Number: <span
                            class="underline-dashed">&nbsp;&nbsp;{{ $delivered_items->first()->do_no }}&nbsp;&nbsp;</span><br>
                        Date:<span class="underline-dashed">&nbsp;&nbsp;
                            {{ date_format(new DateTime($delivered_items->first()->date), 'd-m-Y') }}&nbsp;&nbsp;</span>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-7">
                    <h4>SOLD TO</h4>
                    <div class="row ms-1 me-1 fw-bold" style="border: 1px solid #000; border-radius: 10px;">
                        <div class="row mt-1">
                            <div class="col-2">
                                <p>Messers</p>
                            </div>
                            <div class="col-1">:</div>
                            <div class="col-8">
                                <p><span class="underline">{{ $invoice->customer_name }}</span></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-2">
                                <p>Address</p>
                            </div>
                            <div class="col-1">:</div>
                            <div class="col-8">
                                <p><span class="underline">{{ $invoice->address }}</span></p>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-2">
                                <p>Tel</p>
                            </div>
                            <div class="col-1">:</div>
                            <div class="col-8">
                                <p><span class="underline">{{ $invoice->phno }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-5">
                    <h4>DELIVER TO</h4>
                    <div class="row ms-1 me-1 fw-bold" style="border: 1px solid #000; border-radius: 10px;">
                        <div class="row mt-1">
                            <div class="col-2">
                                <p>Messers</p>
                            </div>
                            <div class="col-1">:</div>
                            <div class="col-8">
                                <p><span class="underline"></span></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-2">
                                <p>Address</p>
                            </div>
                            <div class="col-1">:</div>
                            <div class="col-8">
                                <p><span class="underline"></span></p>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-2">
                                <p>Tel</p>
                            </div>
                            <div class="col-1">:</div>
                            <div class="col-8">
                                <p><span class="underline"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="" style="border: 2px solid #000;"></div>
        <!-- Order Items Table -->
        <table class="table table-bordered invoice-table mb-0">
            <thead>
                <tr>
                    <th style="width: 10%">Qty</th>
                    <th>Model</th>
                    <th>Items</th>
                    <th>Items Code</th>
                    <th>Size/Color</th>
                    <th>Price</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total = 0;
                @endphp
                @foreach ($delivered_items as $item)
                    <tr class="text-start">
                        <td>{{ $item->delivered_qty }}</td>
                        <td>{{ $item->sell ? $item->sell->model : '' }}</td>
                        <td>{{ $item->sell ? $item->sell->part_number : '' }}</td>
                        <td>{{ $item->sell ? $item->sell->product_code : '' }}</td>
                        <td>
                            @if ($item->sell)
                                {{ $item->sell->size ? $item->sell->size . '/' : '' }}
                                {{ $item->sell->colour ?? '' }}
                            @else
                                {{ '' }}
                            @endif
                        </td>

                        <td>
                            {{ $item->sell && $item->sell->retail_price ? number_format($item->sell->retail_price, 2) : '' }}
                            {{ $item->invoice ? $item->invoice->currency_method : '' }}
                        </td>

                        @php
                            $amount = $item->sell ? $item->delivered_qty * $item->sell->retail_price : 0;

                            $total += $amount;
                        @endphp
                        <td class="text-end">
                            {{-- @if ($invoice->sale_price_category == 'Default')
                            @if ($invoice->type == 'Whole Sale')
                                {{ number_format($sell->product_price * $sell->product_qty - $sell->discount) }}
                            @else
                                {{ number_format($sell->retail_price * $sell->product_qty - $sell->discount) }}
                            @endif
                        @elseif ($invoice->sale_price_category == 'Whole Sale')
                            {{ number_format($sell->product_price * $sell->product_qty - $sell->discount) }}
                        @elseif ($invoice->sale_price_category == 'Retail')
                            {{ number_format($sell->retail_price * $sell->product_qty - $sell->discount) }}
                        @else
                            {{ number_format($sell->retail_price * $sell->product_qty - $sell->discount) }}
                        @endif --}}

                            {{ number_format($amount) }} {{ $item->invoice ? $item->invoice->currency_method : '' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Total Section -->
        <div class="" style="border: 2px solid #000;"></div>
        <div class="invoice-total row">
            <div class="col-5 pt-3">
                <p style="font-size: 15px;"><strong>Remark : </strong>ကုန်ပစ္စည်းများကို စစ်ဆေးပြီး
                    ကောင်းမွန်စွာလက်ခံရရှိပါသည်။ <br> <span style="margin-left: 70px;">Received
                        the following in good order and condition.</span></p>
            </div>
            <div class="col-3 pt-3 text-center">
                @if ($item->remark == null)
                @else
                    <p style="font-size: 15px;"><strong>Remark : </strong> {{ $item->remark }}</p>
                @endif

            </div>
            <div class="col-4 text-end">
                <!-- <p><strong>Total</strong> </p> -->
                <div class="row">
                    <table>
                        <tr>
                            <td style="padding: 7px;">TOTAL</td>
                            <td
                                style="padding: 10px;border: 2px solid #000;width: 32%;border-right: none;border-top: none;">
                                {{ number_format($total) }}
                                {{ $item->invoice ? $item->invoice->currency_method : '' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 7px;">Remaining Balance</td>
                            <td
                                style="padding: 10px;border: 2px solid #000;width: 32%;border-right: none;border-top: none;">

                                {{ $item->invoice ? number_format($item->invoice->remain_balance) : '' }}
                                {{ $item->invoice ? $item->invoice->currency_method : '' }}
                            </td>
                        </tr>
                    </table>
                </div>

            </div>
        </div>

        <!-- Footer -->
        <div class="invoice-footer">
            <div class="d-flex justify-content-center align-item-center flex-column">
                <p>_________________________</p>
                <p><strong>Customer</strong></p>
            </div>

            <div class="d-flex justify-content-center align-item-center flex-column">
                <p>_________________________</p>
                <p><strong>STORE MANAGER</strong></p>
            </div>
        </div>


    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ZQXYuPBBvP43v8RlaOUwaCTGSOH6UJOM2Gxj67J72IkF75Be32QZrUm9WPH5k7yc" crossorigin="anonymous">
    </script>
</body>

</html>
