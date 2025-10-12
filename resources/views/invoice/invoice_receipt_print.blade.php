<!DOCTYPE html>
<HTML>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="{{ asset('locallink/css/bootstrap.min.css') }}" rel="stylesheet">

    {{--
    <script src="{{ asset('backend/js/jquery191.js') }}"></script>
    <script src="{{ asset('backend/js/typehead401.js') }}"></script>

    <script src="{{ asset('backend/js/moment2103.js') }}"></script> --}}
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <style>
        @media print {
            @page {
                size: 80mm auto;
                /* 80mm width, auto height */
                margin: 0;
                /* Remove default margins */
            }

            body {
                width: 100%;
                margin: 0;
                padding: 5px;
                font-size: 9px
            }

            table {
                width: 100%;
                border-collapse: collapse;
                /* Ensure no extra spaces */
            }

            th,
            td {
                font-size: 9px;
                /* Adjust for readability */
            }

            .no-print {
                display: none;
                /* Hide buttons in print */
            }
        }
    </style>
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
</head>

<body>

    <div class="">
        <div class="row">
            <div class="col-6">
            </div>
            @php
                $userPermissions = [];
                if (auth()->user()->permission) {
                    $decodedPermissions = json_decode(auth()->user()->permission, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $userPermissions = $decodedPermissions;
                    }
                }
            @endphp
            <div class="gap-2 pt-2 col-6 d-flex align-items-center justify-content-end ">
                {{-- <a href="javascript:void(0);"
                    onclick="printReceipt({{ json_encode($invoice) }},{{ json_encode($sells) }},{{ json_encode($profile) }})"
                    class="no-print btn btn-primary" style="border-radius:10px;"><i class="fa-solid fa-print"></i>
                    Print</a> --}}
                <a onclick="printPage()" class="no-print btn btn-success" style="border-radius:10px;"><i
                        class="fa-solid fa-print"></i> Print</a>

                @if (in_array('POS Register', $userPermissions) || auth()->user()->is_admin == '1')
                    <a href="{{ url('invoice_reg') }}" class="no-print text-white btn btn-primary"
                        style="border-radius:10px;"><i class="fa-solid fa-circle-plus"></i> Invoice Register</a>
                @endif
            </div>
        </div>
        @foreach ($profile as $pic)
            @if ($invoice->branch == $pic->branch)
                <div class="mt-2 text-center">
                    <img src="{{ asset('logos/' . ($pic->logos ?? '')) }}" alt="{{ $pic->name ?? '' }} pos"
                        width="60" height="120">
                </div>
                <div class="row ">
                    <h6 class="text-center" style="font-weight: 600">{{ $pic->name ?? '' }}</h6>
                    <p class="text-center fw-bold mb-2" style="font-size: 11px;line-height:20px;">
                        {{ $pic->address ?? '' }}
                        <br>
                        {{ $pic->phno1 ?? '' }} @if ($pic && $pic->phno2)
                            , {{ $pic->phno2 ?? '' }}
                        @endif
                    </p>
                </div>
            @endif
        @endforeach


        {{-- <div class="mt-3 row">
            <h6 class="text-center" style="font-size: 14px;">Sales Receipt<br>
                {{ $invoice->created_at ? date('d-m-Y', strtotime($invoice->created_at)) : '' }}</h6>
        </div> --}}

        <div class="mt-3 row">
            <div class="col-6" style=" margin-bottom: 2px;">
                <p class="fw-bold" style="font-size: 10px; line-height: 1; margin: 0;">
                    Sale ID : {{ $invoice->invoice_no }}
                </p>
            </div>
            <div class="col-6" style=" margin-bottom: 2px;">
                <p class="fw-bold text-end" style="font-size: 10px; line-height: 1; margin: 0;">
                    Date : {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-F-Y') }}
                </p>
            </div>
            <div class="col-12 mb-2" style=" margin-bottom: 2px;">
                <p class="fw-bold" style="font-size: 10px; line-height: 1; margin: 0;">
                    Patient : {{ $invoice->customer_name }}
                </p>
            </div>



            <div class="mt-1 table-responsive">
                <table class="mt-1" style="font-size: 9px;width:100%">
                    <thead>
                        <tr class="text-left">
                            <th style="width: 40%; border-top: 1px dashed black; border-bottom: 1px dashed black;">Item
                                Name</th>
                            <th style="width: 15%; border-top: 1px dashed black; border-bottom: 1px dashed black;">
                                Qty</th>
                            <th style="width: 15%; border-top: 1px dashed black; border-bottom: 1px dashed black;">Price
                            </th>
                            <th style="width: 15%; border-top: 1px dashed black; border-bottom: 1px dashed black;">
                                Discount
                            </th>
                            <th class="text-end"
                                style="width: 10%; border-top: 1px dashed black; border-bottom: 1px dashed black;">Total
                            </th>
                        </tr>
                    </thead>

                    <tbody class="text-center" style="height:30px">
                        @php
                            $totalDiscount = 0;
                        @endphp
                        @foreach ($invoices as $invoice)
                            @foreach ($invoice->sells as $key => $sell)
                                <tr class="text-start">
                                    <td class=""> {{ $sell->product_name }}</td>
                                    <td class="">{{ $sell->product_qty ?? 0 }} {{ $sell->unit }}</td>
                                    <td class="">
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
                                    <td>
                                        @if ($sell->discount_category == 'kyat')
                                            {{ number_format($sell->discount) . ' Ks' }}
                                        @else
                                            {{ number_format($sell->discount) . ' %' }}
                                        @endif
                                        @php

                                            $totalDiscount += $sell->discount_amt;

                                        @endphp
                                    </td>
                                    <td class="text-end ">
                                        @if ($invoice->sale_price_category == 'Default')
                                            @if ($invoice->type == 'Whole Sale')
                                                {{ number_format($sell->product_price * $sell->product_qty) }}
                                            @else
                                                {{ number_format($sell->retail_price * $sell->product_qty) }}
                                            @endif
                                        @elseif ($invoice->sale_price_category == 'Whole Sale')
                                            {{ number_format($sell->product_price * $sell->product_qty) }}
                                        @elseif ($invoice->sale_price_category == 'Retail')
                                            {{ number_format($sell->retail_price * $sell->product_qty) }}
                                        @else
                                            {{ number_format($sell->retail_price * $sell->product_qty) }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                    {{-- <tfoot style="border-top: 2px solid black !important;">


                        <tr style="line-height: 20px;">
                            <td colspan="3" class="text-end fw-bold">Sub Total</td>
                            <td class="text-end fw-bold">{{ number_format($invoice->net_total) ?? 0 }}</td>
                        </tr>
                        <tr style="line-height: 20px;">
                            <td colspan="3" class="text-end fw-bold">Discount</td>
                            <td class="text-end fw-bold">{{ number_format($invoice->discount_total) ?? 0 }}</td>
                        </tr>
                        <tr style="line-height: 20px;">
                            <td colspan="3" class="text-end fw-bold">Total</td>
                            <td class="text-end fw-bold">{{ number_format($invoice->total) ?? 0 }}</td>
                        </tr>
                    </tfoot> --}}

                    <tfoot style="border-top: 1px dashed black !important;">

                        <tr>
                            <td colspan="2"></td>

                            <td colspan="2" class="">Sub Total</td>
                            <td class="text-end ">{{ number_format($invoice->sub_total) }}</td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>

                            <td colspan="2">Overall Discount</td>
                            <td class="text-end ">{{ number_format($invoice->discount_total) }}</td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>


                            <td colspan="2" class="">Item Discount</td>
                            <td class="text-end ">{{ number_format($totalDiscount ?? 0) }}</td>
                        </tr>

                        <tr>
                            <td colspan="2"></td>

                            <td colspan="2" class="">Deposit</td>
                            <td class="text-end ">{{ number_format($invoice->deposit) ?? 0 }}</td>
                        </tr>
                        @foreach ($payment_methods as $key => $payment)
                            <tr>
                                <td colspan="2"></td>


                                <td colspan="2">
                                    {{ $payment->transaction ? $payment->transaction->transaction_name : '' }}</td>
                                <td class="text-end ">
                                    {{ number_format($payment->payment_amount) ?? 0 }}</td>

                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2"></td>
                            <td colspan="2" class="">Remaining</td>
                            <td class="text-end "> {{ number_format($invoice->remain_balance) ?? 0 }}
                            </td>
                        </tr>

                    </tfoot>
                </table>
            </div>
            <hr class="my-2"
                style="color: red !important; background-color: red  !important; height: 2px !important;">
            <div class="row">
                <p class="text-center fw-bold" style="font-size: 9px;"><i>*** မှားယွင်းမှုတစ်စုံတစ်ရာရှိပါက (24)
                        နာရီအတွင်းအကြောင်းကြားပေးပါရန် ***</i></p>
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
