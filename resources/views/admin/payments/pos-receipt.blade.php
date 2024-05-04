<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <title>Receipt</title>

    <style>
        body img {
            max-width: 100%;
        }

        body h1 {
            font-size: 1.5em;
        }

        body .table td,
        body .table th {
            text-align: center;
            vertical-align: top;
        }

        body .table th {
            text-transform: uppercase;
        }

        .receipt {
            padding: 10px;
            width: 100%;
            margin: 0 auto;
        }

        .receipt .receipt-content img {
            height: 130px;
        }

        .receipt .receipt-content {
            border: 2px solid #000000;
            padding: 10px;
            font-size: 0.95rem;
        }

        .table-font-size {
            font-size: 0.85rem;
        }

        .receipt .receipt-content .total {
            display: block;
            align-items: center;
            justify-content: space-between;
        }

        .receipt .receipt-content .total h5 {
            text-transform: uppercase;
            padding: 10px 10px;
            border: 2px solid #DDDDDD;
            margin: 0 0 0 10px;
            font-weight: 700;
        }

        .receipt .receipt-content .total h6 {
            margin: 0;
        }

        .receipt .receipt-content .total .red {
            color: red;
        }

        .page-break {
            page-break-after: always;
        }


        .receipt-second {
            padding: 10px 0;
        }

        .underline {
            text-decoration: underline;
        }

        .font-weight-700 {
            font-weight: 600;
        }

        .custom-font-size {
            font-size: 1.2rem;
        }

        .custom-red {
            color: red;
        }

        .custom-width {

        }

        @media print {
            .print_row {
                visibility: hidden;
            }
        }

        @page {

            margin: 10px;
        }

        .print_row.btn {
            float: right;
            position: absolute;
            top: 87px;
            right: 15px;
            padding: 18px;
            border: 1px solid #ccc;
            border-radius: 0px;
            background: #eee;
            font-size: 24px;
            font-weight: 600;
            box-shadow: 0px 2px 2px #ccc;
        }

        td {
            font-size: 10px !important;
        }

    </style>
    <script>
        function myFunction() {
            //window.print();
            var divToPrint = document.getElementById("receipt-content");
            newWin = window.open("");
            newWin.document.write(divToPrint.outerHTML);
            newWin.print();
            newWin.close();
        }
    </script>
</head>
<body>

<div class="receipt">
    <div class="">
        <div class="receipt-content" id='receipt-content'>
            <div class="custom-width">


                <div class="custom-width" style="font-family: arial; font-size: 11px">
                    <p style="text-align: center; font-family: arial; font-size: 18px">RECEIPT - {{ $property->assessment->created_at->format('Y') }}</p>
                    <p style="text-align: center; font-family: arial; font-size: 18px">WARDC</span> <span
                            style="font-weight: 600;">Municipal Rate</span></p>

                    <p style="text-align: center; font-family: arial; font-size: 14px">TRANSACTION NO. <span
                            style="color: red">{{sprintf("%007d", $payment->id)}}</span></p>

                    <p style="text-align: center; font-family: arial; font-size: 14px">Date : </span> <span
                            style="font-weight: 600;">{{ $payment->created_at->toDayDateTimeString() }}</span></p>

                    <table class="font-weight-bold table-font-size">
                        <tr>
                            <td style="font-size: 12px; padding-bottom: 8px">OWNER : </td>
                            <td style="font-size: 12px;">{{$property->is_organization ?  $property->organization_name : $property->landlord->first_name.' '.$property->landlord->middle_name.' '.$property->landlord->surname}}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 12px;padding-bottom: 8px">PROPERTY DIGITAL ADDRESS:</td>
                            <td style="font-size: 12px;color: red;padding-bottom: 8px">{{$property->geoRegistry->getDigitalAddress()}}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 12px;padding-bottom: 8px">TRANSACTION ID:</td>
                            <td style="font-size: 12px;">{{$property->getPrintableId()}}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 12px;padding-bottom: 8px">WARD:</td>
                            <td style="font-size: 12px;">{{$property->ward}}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 12px;padding-bottom: 8px">CONSTITUENCY:</td>
                            <td style="font-size: 12px;">{{$property->constituency}}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 12px;padding-bottom: 8px">DISTRICT:</td>
                            <td style="font-size: 12px;">{{$property->district}}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 12px;padding-bottom: 8px">PROVINCE:</td>
                            <td style="font-size: 12px;">{{$property->province}}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 12px;padding-bottom: 8px">AMOUNT DUE:</td>
                            <td style="font-size: 12px;">
                                Le {{$payment->totalAssessment()}}</td>
                        </tr>
{{--                        <tr>--}}
{{--                            <td style="font-size: 12px;padding-bottom: 8px">AMOUNT DUE:</td>--}}
{{--                            <td style="font-size: 12px;">--}}
{{--                                Le {{ number_format($property->assessment->getCurrentInstallmentDueAmount() + $payment->amount)  }}</td>--}}
{{--                        </tr>--}}
                        <tr>
                            <td style="font-size: 12px;padding-bottom: 8px">AMOUNT PAID:</td>
                            <td style="font-size: 12px;">Le {{($payment->amountPaid())}}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 12px;padding-bottom: 8px">BALANCE:</td>
                            <td style="font-size: 12px;">
                                Le {{ number_format(max($payment->balance, 0)) }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">Thank you for your patronage</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="print_row btn"><a onclick="myFunction()">Print this page</a></div>



</body>
</html>
