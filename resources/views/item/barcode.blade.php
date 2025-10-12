<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Show Barcode</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<style>
    @media print {
        body {
            -webkit-print-color-adjust: exact;
            color-adjust: exact;
        }

        .page-tools {
            display: none;
        }
    }
</style>

<body>
    <div class="mt-4 container-fluid barcode-container">
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if ($productCode)
            <div class="barcode">
                <table border="0">
                    <tr>
                        <td style="font-size: 10px;font-weight:bolder" class="text-center">
                            {{ $item->item_name }}</td>
                    </tr>
                    <tr>
                        <td class="text-center">
                            @if (isset($productCode->barcode))
                                {!! DNS1D::getBarcodeHTML($productCode->barcode, 'C128') !!}
                            @else
                                <p>Barcode not available</p>
                            @endif

                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 10px;font-weight:bolder" class="text-center">
                            {{ $productCode->barcode }}</td>
                    </tr>
                    <tr>
                        <td style="font-size: 10px;font-weight:bolder" class="text-center">
                            {{ $productCode->retail_price }} Kyats</td>
                    </tr>
                </table>
            </div>
        @else
            <div class="alert alert-danger">
                Item variation not found.
            </div>
        @endif
    </div>
</body>

</html>
