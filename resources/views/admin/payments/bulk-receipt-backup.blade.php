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

        body h1,h6 {
            font-size: 1.2em;
        }

        body h4 {
            font-size: 1.3em;
        }

        body .table td,
        body .table th {
            text-align: center;
            vertical-align: top;
            padding: .15rem !important;
            font-size: 0.75rem;
        }

        body .table td {
            height: 20px;
        }

        body .table th {
            text-transform: uppercase;
        }

        .receipt {
            padding: 10px 0;
            max-width: 1200px;
            margin: 0 auto;
        }

        .receipt .receipt-content img {
            height: 130px;
        }

        .receipt .receipt-content {
            border: 2px solid #000000;
            padding: 7px;
            font-size: 0.70rem;
        }

        .receipt .receipt-content .total {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .receipt .receipt-content .total h5 {
            text-transform: uppercase;
            padding: 10px 10px;
            border: 2px solid #DDDDDD;
            margin: 0 0 0 10px;
            font-weight: 700;
            font-size: 0.85rem;
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

        .receipt-second .receipt-content {
            border: 2px solid #DDDDDD;
            padding: 10px;
        }

        .receipt-second .receipt-content ul {
            list-style: none;
            padding: 0;
        }

        .receipt-second .receipt-content ul li {
            padding: 4px 0;
        }

        .receipt-second .receipt-content ul li span {
            width: 35%;
            display: inline-block;
        }

        .receipt-second .receipt-content p,
        .receipt-second .receipt-content li,
        .receipt-second .receipt-content h6 {
            font-weight: 600;
        }

        .receipt-second .receipt-content h6 .red {
            color: red;
        }

        /*@media print {*/
        /*.print_row {*/
        /*visibility: hidden;*/
        /*}*/

        /*html, body {*/
        /*width: 100%;*/
        /*height: 297mm;*/
        /*}*/

        /*.page {*/

        /*border: initial;*/
        /*border-radius: initial;*/
        /*width: initial;*/
        /*min-height: initial;*/
        /*box-shadow: initial;*/
        /*background: initial;*/
        /*page-break-after: always;*/
        /*}*/
        /*}*/

        .page {
            width: 100%;
            min-height: 300mm;
            padding: 1mm;
            border-radius: 5px;
            background: white;
            font-size: 0.85rem;
        }

        @page {
            size: A4;
            margin: 10px;
        }

        .print_row {
            float: right;
            position: absolute;
            top: 115px;
            right: 100px;
            padding: 18px;
            border: 1px solid #ccc;
            border-radius: 0px;
            background: #eee;
            font-size: 24px;
            font-weight: 600;
            box-shadow: 0px 2px 2px #ccc;
        }

    </style>
</head>
<body>

<div class="page">
    @foreach($properties as $property)
        <div class="receipt">
            <div class="container">
                <div class="receipt-content">
                    <div class="row">
                        <div class="col-lg-12">
                            <p class="text-uppercase font-weight-bold text-center">PLEASE BRING THIS DEMAND NOTE
                                TOGETHER
                                WITH
                                RECEIPTS FOR ALL PREVIOUS INSTALLMENTS AT TIME OF PAYMENT</p>
                        </div>
                        <div class="" style="width: 18%; float: left; padding-left: 15px;">
                            <img src="{{ asset('images/logo1.png') }}" alt="">
                        </div>

                        <div class="" style="width: 64%;  float: left;">
                            <h1 class="text-center">WESTERN AREA RURAL DISTRICT COUNCIL - WARDC</h1>
                            <h6 class="text-center mb-4">Old York Road, Waterloo, Sierra Leone.</h6>
                            <h4 class="text-center font-weight-600">CITY RATE DEMAND NOTE</h4>
                            <h6 class="text-center font-weight-bold mb-3">JANUARY – DECEMBER {{ date('Y') }}</h6>
                        </div>

                        <div class="" style="width: 18%;float: left;text-align: right;padding-right: 15px;">
                            <img src="{{ asset('images/logo2.jpg') }}" alt="">
                        </div>

                        <div class="col-lg-12">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th scope="col" colspan="7">ASSESSMENT PARAMETERS</th>
                                </tr>
                                </thead>
                            </table>

                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th scope="col" width="25%" class="text-left">{{ $property->is_organization ? 'ORGANIZATION' : 'OWNER' }} NAME</th>
                                    <th scope="col">{{ $property->is_organization ?  $property->organization_name :  ($property->landlord->first_name . ' ' . $property->landlord->middle_name . ' ' . $property->landlord->surname) }}</th>
                                </tr>
                                </thead>
                            </table>

                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th scope="col" width="25%"   class="text-left">PROPERTY ADDRESS</th>
                                    <th scope="col">{{ $property->street_number }}, {{ $property->street_name }}, {{ $property->section }}</th>
                                </tr>
                                </thead>
                            </table>
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th scope="col">PROPERTY TYPE</th>
                                    <th scope="col">PROPERTY DIMENSION (SQ. METERS)</th>
                                    <th scope="col">MATERIAL USED ON WALLS</th>
                                    <th scope="col">MATERIAL USED ON ROOF</th>
                                    <th scope="col">VALUE ADDED</th>
                                    <th scope="col">PROPERTY USE</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>{{ strtoupper($property->types->pluck('label')->implode(', ')) }}</td>
                                    <td>{{ strtoupper(optional($property->assessment->dimension)->label) }} {{ optional($property->assessment->dimension)->id == 1 ? '' : ' SQ METERS' }}</td>
                                    <td>{{ strtoupper(optional($property->assessment->wallMaterial)->label) }}</td>
                                    <td>{{ strtoupper(optional()->label) }}</td>
                                    <td>{{ strtoupper($property->valueAdded()->limit(2)->pluck('label')->implode(', ')) }}{{ $property->valueAdded()->count()  > 2 ? '...' : ''}}</td>
                                    <td>{{ strtoupper($property->occupancy->type) }}</td>
                                </tr>
                                </tbody>
                            </table>

                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th scope="col">RATE DUE</th>
                                    <th scope="col">ARREARS</th>
                                    <th scope="col">1st INSTALLMENT DUE 31-03-2019</th>
                                    <th scope="col">2nd INSTALLMENT DUE 30-06-2019</th>
                                    <th scope="col">3rd INSTALLMENT DUE 30-09-2019</th>
                                    <th scope="col">TOTAL AMOUNT DUE 31-12-2019</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>{!! number_format($property->getAssessment()) !!}</td>
                                    <td>{!! number_format($property->getBalance()) !!}</td>
                                    <td>{!! isset($paymentInQuarter[1]) ?  number_format($paymentInQuarter[1]) : '-' !!}</td>
                                    <td>{!! isset($paymentInQuarter[2]) ?  number_format($paymentInQuarter[2]) : '-' !!}</td>
                                    <td>{!! isset($paymentInQuarter[3]) ?  number_format($paymentInQuarter[3]) : '-' !!}</td>
                                    <td>{!! number_format($property->getAssessment()) !!}</td>
                                </tr>
                                </tbody>
                            </table>

                            <div class="total">
                                <h6 class="font-weight-bold" style="width: 30%; float: left;margin-top: 15px">PLEASE DISREGARD
                                    ARREARS IF PAID</h6>
                                <div class="align-items-center" style="width: 70%; float: left;">
                                    <div style="width: 30%; float: left; display: inline-block;"><h5
                                                 class="red">notice number:</h5></div>
                                    <div style="width: 70%; float: left; display: inline-block;">
                                        <h5>{{ $property->geoRegistry->digital_address }}</h5></div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="receipt-second">
            <div class="container">
                <div class="receipt-content">
                    <div class="row">
                        <div class="col-lg-12">
                            <p>THE WESTERN AREA RURAL DISTRICT COUNCIL DEMANDS PAYMENT OF MUNICIPAL RATE IN
                                RESPECT OF THE PERIOD COMMENCING 1ST JANUARY TO 31ST DECEMBER {{ date('Y') }} IN 3
                                INSTALLMENTS ON OR
                                BEFORE THE FOLLOWING DATES</p>
                            <ul style="width: 50%; float: left;">
                                <li><span>FIRST INSTALLMENT</span> - 31-03-2019</li>
                                <li><span>SECOND INSTALLMENT</span> - 30-06-2019</li>
                                <li><span>FINAL INSTALLMENT</span> - 30-09-2019</li>
                            </ul>
                            {{--<div class="" style="width: 50%; float: left">
                                <img src="http://asinfrastructure.com/emailer/logo3.png" alt="">
                            </div>--}}
                            <div class="clearfix"></div>
                            <h6><span class="red">WARNING:</span> POUNDAGE OF 25% WILL BE CHRGED ON THE TOTAL AMOUNT DUE
                                AFTER
                                EACH INSTALLMENT PERIOD ELAPSES.</h6>
                            <p>BANK ACCOUNTS FOR COLLECTION OF MUNICIPAL RATE REVENUE</p>

                            <table class="mb-4 table-bordered">
                                <thead>
                                <tr>
                                    <th>BANK</th>
                                    <th>ACCOUNT NUMBER</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>UNITED BANK OF AFRICA &nbsp;&nbsp;</td>
                                    <td>&nbsp;&nbsp;54071003000598</td>
                                </tr>
                                <tr>
                                    <td>GTBANK </td>
                                    <td>&nbsp;&nbsp;2043413675110</td>
                                </tr>
                                <tr>
                                    <td>KEYSTONE BANK </td>
                                    <td>&nbsp;&nbsp;2102000080</td>
                                </tr>
                                <tr>
                                    <td>COMMERCIAL BANK </td>
                                    <td>&nbsp;&nbsp;003003041244112110</td>
                                </tr>
                                <tr>
                                    <td>ZENITH BANK </td>
                                    <td>&nbsp;&nbsp;6010173957</td>
                                </tr>
                                </tbody>
                            </table>
                            <p class="mb-0">PLEASE CALL OR ENQUIRE FOR DEMAND NOTICES IF ORIGINAL IS NOT RECEIVED</p>
                            <p style="text-align: right;width: 100%;"class="officer-text">Assessment Officer : {{$property->user->getName()}}</p>
                            <p class="mb-0 text-center">Sigma Ventures Ltd. Digital Property Management System</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-break"></div>
    @endforeach
</div>



<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
</body>
</html>
