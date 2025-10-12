<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS</title>
    {{-- <link rel="shortcut icon" href="{{ asset('img/logo.jpg') }}" type="image/x-icon"> --}}
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .header {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            text-decoration: underline;
        }

        .company-info,
        .invoice-info {
            width: 100%;
            margin-bottom: 20px;
        }

        .company-info p,
        .invoice-info p {
            margin: 5px 0;
        }

        .left {
            float: left;
            width: 50%;
        }

        .right {
            float: right;
            width: 50%;
            text-align: right;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
            font-size: 14px;
        }

        th {
            font-weight: bold;
        }

        .footer {
            margin-top: 20px;
        }

        .footer div {
            width: 32%;
            display: inline-block;
            vertical-align: top;
        }

        .footer p {
            margin: 5px 0;
        }

        @media print {

            #print-button,
            #back-button {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="header">Good Received Report</div>

    <div class="company-info clearfix">
        <div class="left">
            {{-- <p><strong>NATRAY CO., LTD.</strong></p> --}}
            <p>GOODS RECEIVING REPORT</p>
            <p>DATE: {{ $transfer_history->first()->created_at->format('d/m/Y') }}</p>
            <p>FROM: {{ $transfer_history->first()->from_warehouse->name }}</p>
            <p>TO: {{ $transfer_history->first()->to_warehouse->name }}</p>
        </div>
        <div class="right">
            <p>INVOICE NO.: __________</p>
            <p>DO NO. GR: __________</p>
            <p>CONTAINER: __________</p>
            <p>CONTAINER NO.: __________</p>
            <p>Seal No.: __________</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Sr.</th>
                <th>Brand</th>
                <th>NR Item Code</th>
                <th>Type of Item</th>
                <th>CTN No</th>
                <th>Item</th>
                <th>Model</th>
                <th>Colour</th>
                <th>Description</th>
                <th>Size</th>
                <th>Unit Price USD</th>
                <th>Inv Qty</th>
                <th>Pack Qty</th>
                <th>Unit</th>
                <th>Rec Qty</th>
                <th>Diff Qty</th>
                <th>Good Qty</th>
                <th>Damg Qty</th>
                <th>Ledger Qty</th>
                <th>Total Valuation USD</th>
                <th>Remark</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transfer_history as $key => $transfer)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $transfer->item ? $transfer->item->brand : '' }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ $transfer->item ? $transfer->item->item_name : '' }}</td>
                    <td>{{ $transfer->variation ? $transfer->variation->model : '' }}</td>
                    <td>{{ $transfer->variation ? $transfer->variation->colour : '' }}</td>
                    <td>{{ $transfer->variation ? $transfer->variation->variation_desc : '' }}</td>
                    <td>{{ $transfer->variation ? $transfer->variation->size : '' }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ $transfer->variation ? $transfer->variation->unit : '' }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ $transfer->remark }}</td>
                </tr>
            @endforeach

            <tr>
                <td colspan="21" style="text-align:right;">-</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div>
            <p><strong>Checked By:</strong></p>
            <p>Signature</p>
            <p>Purchasing</p>
            <p>Accountant</p>
            <p>Manager</p>
            <p>M.D</p>
        </div>
        <div style="text-align: center;">
            <p><strong>Prepared By:</strong></p>
            <p>Inventory</p>
        </div>
        <div style="text-align: right;">
            <p><strong>Received By:</strong></p>
            <p>Signature</p>
            <p>Name</p>
            <p>Store Manager</p>
        </div>
    </div>
    <button id="print-button" onclick="window.print()"
        style="background-color: #007bff; border: none; color: white; padding: 10px 20px; font-size: 16px; font-weight: bold; border-radius: 5px; cursor: pointer; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2); transition: background-color 0.3s, transform 0.2s;"
        onmouseover="this.style.backgroundColor='#0056b3'; this.style.transform='scale(1.05)';"
        onmouseout="this.style.backgroundColor='#007bff'; this.style.transform='scale(1)';"
        onmousedown="this.style.backgroundColor='#004085'; this.style.transform='scale(0.98)';">
        Print
    </button>
    <a id="back-button" href="{{ url('show_transfer_history') }}"
        style="background-color: #f92e00; border: none; color: white; padding: 10px 20px; font-size: 16px; font-weight: bold; border-radius: 5px; cursor: pointer; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2); transition: background-color 0.3s, transform 0.2s;text-decoration: none;"
        onmouseover="this.style.backgroundColor='#ff5a35'; this.style.transform='scale(1.05)';"
        onmouseout="this.style.backgroundColor='#f92e00'; this.style.transform='scale(1)';"
        onmousedown="this.style.backgroundColor='#ff5a35'; this.style.transform='scale(0.98)';">Back</a>
</body>

</html>
