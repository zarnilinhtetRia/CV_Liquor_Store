<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .transfer-note {
            width: 100%;
            border-collapse: collapse;
        }

        .transfer-note th,
        .transfer-note td {
            border-left: 1px solid black;
            border-right: 1px solid black;
            padding: 8px;
            text-align: center;
        }

        .transfer-note th {
            background-color: #f2f2f2;
            border-top: 1px solid black;
            border-bottom: 1px solid black;
        }

        .transfer-note td {
            border-top: 1px solid black;
        }

        .transfer-note tr:last-child td {
            border-bottom: 1px solid black;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        @media print {
            #print-button {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        {{-- <h2>NatRay Co., Ltd</h2> --}}
        <h3>TRANSFER NOTE</h3>
    </div>

    <div class="details" style="margin-top: 50px;">
        <div style="margin-top: 7px;">Transfer Number: {{ $histories->first()->transfer_no }}</div>

        <div style="margin-top: 7px;">From: {{ $histories->first()->from_warehouse->name }}</div>

        <div style="margin-top: 7px;">To: {{ $histories->first()->to_warehouse->name }}</div>

        <div style="margin-bottom: 10px;margin-top: 7px;">
            Date: {{ \Carbon\Carbon::parse($histories->first()->date)->format('d/m/Y') }}
        </div>

        <br>
    </div>

    <table class="transfer-note">
        <thead>
            <tr>
                <th>No.</th>
                <th>Product Name</th>
                <th>Qty</th>

                <th>Unit Price</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
                $rowCount = 0;
                $defaultRows = 10;
            @endphp
            @foreach ($histories as $key => $history)
                @php
                    $rowCount++;
                @endphp
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $history->variation ? $history->variation->item->item_name : '' }}</td>
                    <td>{{ $history->quantity }}
                    </td>

                    <td>{{ $history->variation ? $history->variation->retail_price : '' }}


                    </td>
                    <td>{{ $history->variation ? abs($history->variation->retail_price * $history->quantity) : 'N/A' }}
                    </td>
                </tr>
            @endforeach
            @for ($i = $rowCount + 1; $i <= $defaultRows; $i++)
                <tr>
                    <td>&nbsp;</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>

                </tr>
            @endfor
        </tbody>
    </table>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 70px;">
        <div class="prepared-left">
            <button id="print-button" onclick="window.print()"
                style="background-color: #007bff; border: none; color: white; padding: 10px 20px; font-size: 16px; font-weight: bold; border-radius: 5px; cursor: pointer; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2); transition: background-color 0.3s, transform 0.2s;"
                onmouseover="this.style.backgroundColor='#0056b3'; this.style.transform='scale(1.05)';"
                onmouseout="this.style.backgroundColor='#007bff'; this.style.transform='scale(1)';"
                onmousedown="this.style.backgroundColor='#004085'; this.style.transform='scale(0.98)';">
                Print
            </button>
        </div>
        <div class="prepared-by">
            <p>Prepared By: __________________</p>
        </div>
    </div>

</body>

</html>
