<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSE POS</title>
    <link rel="shortcut icon" href="{{ asset('img/logo.jpg') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f8f9fa;
        }

        .voucher {
            /* padding: 10px; */
            max-width: 900px;
            margin: auto;
            background: #fff;
        }

        .voucher-title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .details {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }

        .details span {
            display: inline-block;
            border-bottom: 1px solid black;
            width: 300px;
        }

        .table-bordered th,
        .table-bordered td {
            border: 2px solid black !important;
            height: 40px;
            text-align: left;
        }

        .footer-section {
            margin-top: 20px;
        }

        .footer-content {
            display: flex;
            align-items: center;
            gap: 10px;
            /* Adjust space between text and underline */
        }

        .footer-content span {
            display: inline-block;
            border-bottom: 1px solid black;
            width: 90%;
        }

        .bordered-box {
            display: inline-block;
            border: 2px solid black;
            width: 200px;
            height: 30px;
            vertical-align: middle;
        }

        /* Print Styles */
        @media print {
            body {
                margin: 0;
                background: none;
            }

            .voucher {
                width: 100%;
                max-width: 410mm;
                page-break-after: always;
                /* border: 2px solid black; */
                /* padding: 10px; */
            }

            .voucher-title {
                font-size: 18px;
            }

            .table-bordered th,
            .table-bordered td {
                border: 1px solid black !important;
            }

            .bordered-box {
                display: inline-block;
                border: 2px solid black;
                width: 200px;
                height: 30px;
            }

            .footer-section {
                margin-top: 15px;
            }

            .footer-content {
                display: flex;
                align-items: center;
                gap: 10px;
                /* Adjust space between text and underline */
            }

            .footer-content span {
                display: inline-block;
                border-bottom: 1px solid black;
                width: 80%;
            }

            @page {
                size: auto;
                /* margin: 10mm; */
            }

            #back_button,
            #print_button {
                display: none;
            }
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            margin: 5px;
        }

        /* Back Button - Red */
        .btn-danger {
            background-color: red;
        }

        .btn-danger:hover {
            background-color: darkred;
        }

        /* Print Button - Blue (Primary Color) */
        .btn-primary {
            background-color: blue;
        }

        .btn-primary:hover {
            background-color: darkblue;
        }
    </style>
</head>

<body>

    <div class="voucher">
        <div class="voucher-title">
            DEBIT VOUCHER (PAYMENT)
        </div>

        <div class="details">
            <div>
                @if ($payment->payment_status == 'OUT')
                    <strong>Pay To:</strong>
                @else
                    <strong>Pay From:</strong>
                @endif

                <span>{{ $payment->transferAccount ? $payment->transferAccount->account_name : '' }}</span>
            </div>
            <div><strong>No:</strong> <span style="width: 100px;">{{ $payment->debit_note }}</span></div>
        </div>

        <div class="details">
            <div><strong>Date:</strong> <span style="width: 150px;">{{ $payment->payment_date }}</span></div>
        </div>

        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th width="25%">Account Code/Head</th>
                    <th width="55%">Description</th>
                    <th width="20%">Amount (MMK)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $payment->account ? $payment->account->account_name : '' }}</td>
                    <td>{{ $payment->note }}</td>
                    <td>{{ number_format($payment->amount) }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

            </tbody>
        </table>

        <div class="details">
            <div>

                <span class="bordered-box"><strong>Kyat:</strong>
                    <strong>{{ number_format($payment->amount) }}</strong></span>
            </div>
            <div>
                <strong>TOTAL</strong>
                <span class="bordered-box" style="width: 200px;">
                    &nbsp; <strong>{{ number_format($payment->amount) }}</strong></span>
            </div>
        </div>

        <div class="footer-section">
            <div class="footer-content">
                <strong>In words:</strong>
                <span></span>
            </div>
        </div>

        <div class="d-flex justify-content-between" style="margin-top: 100px !important;">
            <div><strong>Prepared by:</strong> </div>
            <div><strong>Checked/Authorized by:</strong> </div>
            <div><strong>Approved by:</strong> </div>
            <div><strong>(Recipient) Signed by:</strong> </div>
        </div>
    </div>

    <a href="{{ url('payment', $payment->transaction_id) }}" class="btn btn-danger" id="back_button">Back</a>
    <a href="javascript:window.print()" class="btn btn-primary" id="print_button">Print</a>

</body>

</html>
