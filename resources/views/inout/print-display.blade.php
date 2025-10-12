<!DOCTYPE html>
<HTML>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
</head>
<style>
    @page {
        size: auto;
        margin: 0;
    }

    @media print {
        body {
            color: black;
            /* Set text color for printing */
        }

        /* Add any other styles you want to modify for printing */
    }

    @media print {

        #test,
        #printButton,
        .excelButton {
            display: none;
        }
    }

    @media print {
        body {
            -webkit-print-color-adjust: exact;
        }
    }

    @media print,
    @media screen and (max-width: 800px) {
        #printButton {
            display: none;
            /* Hide the button when printing or generating PDF */
        }
    }
</style>

<body style="margin:25px;">
    <div class="container-fluid mt-3" id="content">
        <div class="row ">
            {{-- <div class="col">
                <img src="" alt="logo" style="width: 150px; height: 150px;">
                <h5 style="font-weight: bolder;">
                    Shwe Mann
                </h5>
                <div style="width: 120%;" class="mt-2">
                    <p>
                        <span>
                            No.286, Kyaik Ka San Road, Tarmwe Township, Yangon,<br>
                            Phone: 09-740867976
                        </span>
                    </p>
                </div>
            </div> --}}
            <div class="col-md-12">
                <h2 style="margin-top:30px">Pickup Note</h2>
                <div style="font-weight: bold">Date : {{ date('d-m-Y', strtotime($inout->date)) }}
                </div>
            </div>
        </div>



        <br>
        <br>
        <br>
        <div class="row" style="margin-top: -20px;">
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <div class="row">
                        <div class="table-responsive">
                            <table class="table table-bordered text-center" style="border:1px solid black">
                                <thead style="background-color: #0B5ED7;color:white;">
                                    <tr class="item_header bg-gradient-directional-blue white" style="margin-bottom:10px;">
                                        <th class="text-center">Item Name</th>
                                        <th class="text-center">Category</th>
                                        <th class="text-center">Quantity</th>
                                    </tr>
                                </thead>
                                <tr>
                                    <td class="text-center" id="count" style="border:1px solid black">
                                        {{ $item->item_name }}
                                    </td>
                                    <td class="text-center" id="count" style="border:1px solid black">
                                        {{ $item->category }}
                                    </td>
                                    <td class="text-center" id="count" style="border:1px solid black">
                                        {{ $inout->quantity }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                </table>

                <table width="60%" class="table mt-3">
                    <tr>
                        <td class="border-0">
                            <div style="font-weight: bold;">Advisor Name
                            </div>
                            <span class="text-danger text-center"> {{ Auth::user()->name }}</span>
                        </td>
                    </tr>
                </table>
                <div id="downloadPdf" class="btn btn-primary mt-5" style="width: 65px"> Print</div>
            </div>
        </div>


</body>

</HTML>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.1/html2pdf.bundle.min.js"></script>
<script>
    document.getElementById('downloadPdf').addEventListener('click', function() {
        // Select the element to be converted to PDF
        const element = document.getElementById('content');

        document.getElementById('downloadPdf').style.display = 'none';

        setTimeout(function() {
            document.getElementById('downloadPdf').style.display = 'block';
        }, 2000);

        // Options for the PDF generation
        const options = {
            margin: 10,
            filename: 'document.pdf',
            image: {
                type: 'jpeg',
                quality: 0.98
            },
            html2canvas: {
                scale: 2
            },
            jsPDF: {
                unit: 'mm',
                format: 'a4',
                orientation: 'portrait'
            }
        };


        html2pdf()
            .from(element)
            .set(options)
            .save();
    });
</script>
