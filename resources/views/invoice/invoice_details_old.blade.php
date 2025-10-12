<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;

        }


        .invoice-box {
            position: relative;
        }

        .invoice-box::before {
            content: "";
            background: url('{{ asset('img/solar_logo.jpg') }}') no-repeat center;
            background-size: contain;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.1;
            /* Adjust opacity for watermark effect */
            z-index: -1;
        }


        .header-logo {
            text-align: center;
        }

        /* General table cell styles */
        .table th,
        .table td {
            text-align: center;
            vertical-align: middle;
        }

        /* Table borders */
        .table-bordered th,
        .table-bordered td {
            border: 1px solid black !important;
        }

        /* Remove border for cells with colspan="3" */
        .no-border {
            border: none !important;
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
            border-top: 1px solid black;
            width: 45%;
            padding-top: 5px;
        }
    </style>
</head>

<body>

    <div class="invoice-box container-fluid">
        <div class="header-logo mb-3">
            <img src="{{ asset('img/solar_logo.jpg') }}" alt="Company Logo" width="200">
        </div>

        <div class="row">
            <div class="col-6">
                <p><strong>Customer Name:</strong> Daw Khin Aye Thwin</p>
                <p><strong>Address:</strong> 82-84, 5(A), 49 St., Pazuntaung</p>
                <p><strong>Contact Number:</strong> 09-885161782</p>
            </div>
            <div class="col-6 text-end">
                <p><strong>Invoice No:</strong> 05541</p>
                <p><strong>Date:</strong> 14-3-2025</p>
            </div>
        </div>

        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>Unit Price (MMK)</th>
                    <th>Total Price (MMK)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Growatt 6KW</td>
                    <td>1</td>
                    <td>9,400,000</td>
                    <td>9,400,000</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Must 51.2V 300Ah</td>
                    <td>1</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="no-border"><strong>Site Date (After 20-3-2025)</strong></td>
                    <td><strong>Total (MMK):</strong></td>
                    <td>9,400,000</td>
                </tr>
                <tr>
                    <td colspan="3" class="no-border"></td>
                    <td><strong>Advance (AYA):</strong></td>
                    <td>9,400,000</td>
                </tr>
                <tr>
                    <td colspan="3" class="no-border"></td>
                    <td><strong>Balance:</strong></td>
                    <td>9,400,000</td>
                </tr>
            </tfoot>

        </table>

        {{-- <div class="row mt-3">
            <div class="col-6">
                <p><strong>Site Date (After 20-3-2025)</strong></p>
            </div>
            <div class="col-6">
                <table class="table">
                    <tr>
                        <td><strong>Total (MMK):</strong></td>
                        <td>9,400,000</td>
                    </tr>
                    <tr>
                        <td><strong>Advance (AYA):</strong></td>
                        <td>2,000,000</td>
                    </tr>
                    <tr>
                        <td><strong>Balance:</strong></td>
                        <td>7,400,000</td>
                    </tr>
                </table>
            </div>
        </div> --}}

        <div class="payment-terms mt-3">
            <p><strong>Payment Terms:</strong></p>
            <ul>
                <li>1st payment - 30% for project confirmation.</li>
                <li>2nd payment - 70% after arrival of main items.</li>
                <li>Final payment - Installation & Accessories fee after the test run.</li>
            </ul>
        </div>

        <div class="bank-details">
            <p><strong>Please Transfer To Special acc:</strong></p>
            <p>Account No: 12751199900124401 (KBZ)</p>
            <p>Account No: 0020454620001063 (Yoma)</p>
            <p>Account No: 40028681865 (AYA)</p>
            <p>Account No: 0107100900016179 (CB)</p>
            <p><strong>Bank Name:</strong> Phyo Kyaw Swar Phay</p>
            <p><strong>Great Solar Kabar Co., Ltd</strong></p>
        </div>

        <div class="footer-sign">
            <div class="signature">
                <p>Sale Manager Sign</p>
            </div>
            <div class="signature">
                <p>Customer Sign</p>
            </div>
        </div>

        <div class="text-center mt-4">
            <p><strong>Office:</strong> +95 9776116773</p>
            <p><strong>Sales:</strong> +95 9792972718, +95 9780491579</p>
            <p><strong>Email:</strong> greatsolarkabar@gmail.com</p>
            <p><strong>Address:</strong> No-58A4/B1, (7th) Quarter, South Okkalapa, Yangon</p>
        </div>
    </div>

</body>

</html>
