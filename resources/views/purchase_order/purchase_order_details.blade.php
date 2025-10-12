<!DOCTYPE html>
<HTML>

<head>
    <link rel="stylesheet" href="{{ asset('locallink/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>POS</title>
</head>
<style>
    @media print {
        body {
            color: black;
        }

        #test,
        .printButton,
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
        <!-- Header Section -->
        <!-- <div class="mt-3 text-center">
            <img src="{{ asset('images/download (1).jpg') }}" width="180" height="120">
        </div> -->
        @foreach ($profile as $pic)
            @if ($purchase_order->branch == $pic->branch)
                <div class="mt-3 text-center">
                    <img src="{{ asset('logos/' . ($pic->logos ?? 'null')) }}" width="180" height="120">

                </div>
                <div class="row" style="margin-top: 15px;">
                    <h4 class="text-center fw-bold">{{ $pic->name }}</h4>

                    <p class="text-center fw-bold" style="font-size: 14px;">
                        {{ $pic->address }}
                        <br>
                        {{ $pic->phno1 }}, {{ $pic->phno2 }}
                    </p>
                </div>
            @endif
        @endforeach

        <!-- Content Section -->
        <div class="content-wrapper">
            <div class="content-body">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row" style="display:flex;position:relative">
                                <div style="width:40%;">

                                    @if ($purchase_order->balance_due == 'PO')
                                        <h4>Purchase Order to</h4>
                                        <div class="input-group"><span style="font-weight:bolder"> Supplier Name :&nbsp;
                                            </span><span
                                                style="font-weight:bolder">{{ $purchase_order->supplier->name ?? 'N/A' }}
                                            </span></div>
                                    @endif

                                    <div class="input-group"><span style="font-weight:bolder"> Receiving Mode :&nbsp;
                                        </span><span style="font-weight:bolder">
                                            @if ($purchase_order->balance_due == 'Sale Return Invoice')
                                                Sale Return
                                            @else
                                                {{ $purchase_order->balance_due }}
                                            @endif
                                        </span></div>
                                    <br>
                                    <br>
                                </div>
                                <div style="width:30%;position:absolute;right:0px;top:0px;">

                                    <label for="invociedate" class="caption"
                                        style="font-weight:bolder">{{ trans('Purchase Order Number') }}</label>
                                    <div class="input-group mb-1"><span
                                            style="font-weight:bolder">{{ $purchase_order->quote_no }}</span> </div>

                                    <label for="invociedate" class="caption"
                                        style="font-weight:bolder">{{ trans('Supplier Register Number') }}</label>
                                    <div class="input-group mb-1"><span
                                            style="font-weight:bolder">{{ $purchase_order->supplier_register_number ?? '-' }}</span>
                                    </div>

                                    <label for="invociedate" class="caption"
                                        style="font-weight:bolder">{{ trans('Purchase Order Date') }}</label>
                                    <div class="input-group"><span
                                            style="font-weight:bolder">{{ $purchase_order->po_date }}</span> </div>

                                </div>
                            </div>
                            <br>

                            <!-- Table Section -->
                            <div class="row mt-2">
                                <div class="table-responsive">
                                    <table class="table text-center table-bordered" style="width: 100%">
                                        <thead class=""
                                            style="color: black;background: radial-gradient(circle, rgb(255, 100, 100), rgb(52, 52, 52));">
                                            <tr class="text-white">
                                                <th>{{ trans('No') }}</th>
                                                <th style="width: 32%;">{{ trans('Item Name') }}</th>
                                                <th style="width: 28%;">{{ trans('Description') }}</th>
                                                <th style="width: 5%;">{{ trans('Qty') }}</th>
                                                <th style="width: 5%;">{{ trans('Unit') }}</th>
                                                <th style="width: 10%;">{{ trans('Price') }}</th>
                                                <th style="width: 10%;">{{ trans('Discounts') }}</th>
                                                {{-- <th style="width: 3%;"></th> --}}
                                                <th style="width: 10%;">{{ trans('Total') }}
                                                    {{-- ({{ config('currency.symbol') }}) --}}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $no = 1;
                                                $itemDiscount = 0;
                                            @endphp
                                            @foreach ($purchase_sells as $sell)
                                                <tr>
                                                    <td>{{ $no }}</td>
                                                    <td style="width: 30%;">{{ $sell->product_name }}</td>
                                                    <td style="width: 30%;">{{ $sell->description }}</td>
                                                    <td style="width: 5%;">{{ $sell->product_qty }}</td>
                                                    <td style="width: 5%;">{{ $sell->unit }}</td>
                                                    <td style="width: 10%;">{{ number_format($sell->product_price) }}
                                                    </td>
                                                    <td style="width: 10%;">
                                                        @if ($sell->discount_category == 'kyat')
                                                            {{ number_format($sell->discount) . ' $' }}
                                                        @else
                                                            {{ number_format($sell->discount) . ' %' }}
                                                        @endif
                                                    </td>
                                                    @php
                                                        $amount =
                                                            round($sell->product_qty * $sell->product_price) -
                                                            $sell->discount_amt;
                                                    @endphp
                                                    <td style="width: 10%;">
                                                        <span class="currenty"></span>
                                                        <!-- <span class='ttlText'>{{ number_format($sell->product_qty * $sell->product_price - $sell->discount) }}</span> -->
                                                        <span class='ttlText'>{{ number_format($amount) }}</span>
                                                    </td>
                                                </tr>
                                                @php
                                                    $no++;
                                                    $itemDiscount += $sell->discount_amt;

                                                @endphp
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            {{-- @php
                                                if($purchase_order->currency_method == 'MMK'){
                                                    $sub_total = number_format($purchase_order->sub_total);
                                                    $discount_total = number_format($purchase_order->discount_total);
                                                    $item_discount = number_format($itemDiscount);
                                                    $total = number_format($purchase_order->total);
                                                }else{
                                                    $sub_total = number_format($purchase_order->sub_total);
                                                    $discount_total = number_format($purchase_order->discount_total);
                                                    $item_discount = round($itemDiscount / $purchase_order->exchange_rate);
                                                    $total = number_format($purchase_order->total);
                                                }
                                            @endphp --}}
                                            <tr>
                                                <td colspan="5" class="text-right"></td>
                                                <td colspan="2" style="font-weight: bolder; ">Sub Total
                                                </td>
                                                <td style="font-weight: bolder; ">
                                                    {{ $purchase_order->sub_total }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" class="text-right"></td>
                                                <td colspan="2" style="font-weight: bolder; font-size:15px;">Overall
                                                    Discount
                                                </td>
                                                <td style="font-weight: bolder; ">
                                                    {{ $purchase_order->discount_total }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" class="text-right"></td>
                                                <td colspan="2" style="font-weight: bolder; font-size:15px;">Item
                                                    Discount
                                                </td>
                                                <td style="font-weight: bolder; ">
                                                    {{ $itemDiscount }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" class="text-right"></td>
                                                <td colspan="2" style="font-weight: bolder; ">Total
                                                </td>
                                                <td style="font-weight: bolder; ">
                                                    {{ $purchase_order->total }}
                                                </td>
                                            </tr>
                                            @foreach ($payment_methods as $index => $payment_method)
                                                <tr>
                                                    <td colspan="5" class="text-right"></td>
                                                    @if ($index == 0)
                                                        <td colspan="2" style="font-weight: bolder;font-size:15px;">
                                                            Payment
                                                            Method
                                                            {{ $payment_method->transaction->transaction_name }}
                                                        </td>
                                                    @else
                                                        <td colspan="2" style="font-weight: bolder;">

                                                            {{ $payment_method->transaction->transaction_name }}
                                                        </td>
                                                    @endif

                                                    <td style="font-weight: bolder;">
                                                        @if ($purchase_order->currency_method == 'MMK')
                                                            {{ number_format($payment_method->payment_amount) }}
                                                        @else
                                                            {{ number_format($payment_method->payment_amount) }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="5" class="text-right"></td>
                                                <td colspan="2" style="font-weight: bolder;">Deposit
                                                </td>
                                                <td style="font-weight: bolder;">
                                                    @if ($purchase_order->currency_method == 'MMK')
                                                        {{ number_format($purchase_order->deposit) }}
                                                    @else
                                                        {{ number_format($purchase_order->deposit) }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" class="text-right"></td>
                                                <td colspan="2" style="font-weight: bolder;font-size:15px;">
                                                    Remaning
                                                    Balance
                                                </td>
                                                <td style="font-weight: bolder;">
                                                    @if ($purchase_order->currency_method == 'MMK')
                                                        {{ number_format($purchase_order->remain_balance) }}
                                                    @else
                                                        {{ number_format($purchase_order->remain_balance) }}
                                                    @endif
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <br><br>
                            <table width="60%" class="">
                                <tr>
                                    <td style="font-weight: bolder">Remark -
                                        {{ $purchase_order->remark }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <a onclick="printPage()" class="mt-4 btn btn-success printButton">Print</a>
            <a class="btn btn-danger mt-4 printButton" style="border-radius:10px;" onclick="window.history.back()"><i
                    class="fa-solid fa-backward text-white"></i> Back</a>

        </div>
    </div>
</body>

</HTML>
<script>
    function printPage() {
        window.print();
    }
</script>
