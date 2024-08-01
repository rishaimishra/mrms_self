<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Receipt</title>

    <style type="text/css" media="all">
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.1;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            z-index: -1;
            width: 50%;
            /* Adjust the size as needed */
            height: auto;
            z-index: -1;
        }

        p {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        p.notice-text {
            font-size: 12px !important;
        }

        .policy-content p {
            font-size: 14px !important;
        }

        h1,
        h6 {
            font-size: 14px;
            font-weight: bold;
            font-family: Arial, sans-serif;
        }

        h6 {
            font-size: 13px;
        }

        h1 {
            margin-bottom: 0;
            font-family: Arial, sans-serif;
            font-size: 15px;
            font-weight: bold;
        }

        h4 {
            font-size: 16px;
        }

        .table th {
            text-transform: uppercase;
        }

        .table th {
            text-align: center;
        }

        @page {
            size: A4;
            margin: 10px;
        }

        .receipt,
        .receipt-second {
            border: 2px solid #9e9e9e;
            margin: 10px;
            padding: 0 10px 10px;
        }

        .policy-content {
            padding-top: 10px;
        }

        .receipt table tr td img {
            height: 100px;
            width: 130px;
        }

        .qr-code-wrapper img {
            height: 100px;
        }

        .page-break {
            page-break-after: always;
        }

        table {
            border-spacing: 0;
            /* margin-bottom: 5px !important; */
        }

        table tr td {
            text-align: center;
        }

        .table td,
        .table th {
            text-align: center;
            vertical-align: top;
            font-size: 10px;
            border-color: #eee;
        }

        .font-weight-bold {
            font-weight: bold;
        }

        .qr-code-wrapper svg {
            height: 170px;
        }

        .pagebreak {
            clear: both;
            page-break-after: always;
        }

        .page {
            border-radius: 5px;
            /* background: white; */
            font-size: 0.85rem;
        }

        .special-text {
            font-size: 12px;
        }

        .h-warning {
            font-size: 10px !important;
        }

        /*.receipt .receipt-content {*/
        /*    font-size: 0.85rem;*/
        /*}*/

        .receipt .receipt-content .total h5 {
            text-transform: uppercase;
            padding: 5px 5px;
            margin: 0 0 0 10px;
            font-weight: 700;
            font-size: 15px;
        }

        .receipt .receipt-content .total h6 {
            margin: 0;
        }

        .receipt .receipt-content .total .red {
            color: red;
        }

        .receipt-second .receipt-content ul {
            list-style: none;
            padding: 0;
        }

        .installment-section ul li {
            margin-bottom: 3px;
        }

        .installment-section ul li span {
            width: 40%;
            font-size: 13px;
        }

        .footer-text {
            text-align: center;
            color: gray;
            font-weight: bold;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        p {
            margin-bottom: 5px;
        }

        .fw-500 {
            font-weight: 500 !important;
        }

        .fw-600 {
            font-weight: 600 !important;
        }

        .h6 {
            font-size: 20px;
            font-weight: 700 !important;
        }

        .h4 {
            margin: 5px 0 0 !important;
            font-weight: 500 !important;
            font-size: 15px;
        }

        .h6,
        .h4 {
            font-family: sans-serif;
        }

        .d-flex {
            display: flex;
            justify-content: space-between;
        }

        .blue-bg {
            background: #116cca;
            color: #fff;
            padding: 7px 10px 7px;
        }

        .text-grey {
            color: grey;
            padding: 0 0 0 20px;
        }

        .blue-bg,
        .text-grey {}

        table.table th,
        table.table td {
            font-size: 17px;
            font-family: serif;
        }

        .table-i {}

        .table-i th {
            font-weight: 600;
            color: #626262;
            padding: 10px 10px 7px;
        }

        .table-i th:nth-child(1) {
            text-align: left;
        }

        .table-i th:nth-child(2) {
            text-align: right;
        }

        .watermark img {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0.1;
        z-index: -1;
        width: 40%;
        height: auto;
      }

      .page {
        background: none;
        position: relative;
      }
    </style>
</head>

<body>
    {{-- <div class="watermark">
        <img src="{{ asset('images/logo23.png') }}" alt="Watermark">
    </div> --}}
    <div class="page" style="width: 100%">
        <div class="receipt">
            <div class="container">
                <div class="receipt-content">
                    <div class="row">
                        <table class="" width="100%">
                            <tbody>
                                <tr>
                                    <td align="left" style="width: 18%">
                                        <img style="padding: 0 15px" src="{{ asset('images/logo23.png') }}"
                                            alt="" />
                                    </td>
                                    <td style="text-align: center">
                                        <h6 class="h6 text-center mb-4" style="margin: 0; font-weight: 500">MUNICIPAL
                                            RATE MANAGEMENT SYSTEM</h6>
                                        <h4 class="h4 text-center font-weight-600"
                                            style="margin-bottom: 5px; font-weight: 500">PROPERTY RATE PAYMENT
                                            TRANSACTION RECEIPT</h4>
                                        <h4 class="h4 text-center font-weight-bold mb-3"
                                            style="margin: 0; font-weight: bold">Western Area Rural District</h4>
                                    </td>
                                    <td align="right" style="width: 18%">
                                        <img style="padding: 0 15px" src="{{ $district->getPrimaryLogoUrl() }}"
                                            alt="" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <hr />

                        <div class="col-lg-12">
                            <table class="table table-bordered"
                                style="width: 100%; margin-bottom: 15px; margin-top: 25px">
                                <thead>
                                    <tr>
                                        <th scope="col" colspan="7">
                                        <th class="blue-bg" style="">RECEIPT NUMBER:</th>
                                        <th class="text-grey">{{ sprintf('%007d', $payment->id) }} </th>
                                        <th class="blue-bg">DATE:</th>
                                        <th class="text-grey">{{ @$payment->created_at->toDayDateTimeString() }}</th>
                                        </th>
                                    </tr>
                                </thead>
                            </table>

                            <table class="table table-bordered table-i" style="width: 100%; margin-bottom: 15px">
                                <thead>
                                    <!-- <th scope="col">Mr. Skeh-Gibrill Fanday Rogers</th> -->
                                    <tr>
                                        <th>Property ID:</th>
                                        <th>{{ @$property->getPrintableId() }}</th>
                                    </tr>
                                    <tr>
                                        <th>Landlord Owner Name:</th>
                                        <th>{{ $property->is_organization ? $property->organization_name : $property->landlord->first_name . ' ' . $property->landlord->middle_name . ' ' . $property->landlord->surname }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Payment Category:</th>
                                        <th>PROPERTY RATE</th>
                                    </tr>
                                    <tr>
                                        <th>Transaction Status:</th>
                                        <th>{{ $payment->is_complete ? 'success' : 'pending' }}</th>
                                    </tr>
                                    <tr>
                                        <th>Rate Payable 2024:</th>
                                        <th>{{ number_format((float) $property->assessment->getPropertyTaxPayable(), 2, '.', ',') }}
                                        </th>
                                    </tr>
                                    @php
                                        $disability_discount = 0;
                                        $pensioner_discount = 0;

                                        $property_tax_payable = (float) $property->assessment->getPropertyTaxPayable();
                                        if (@$property->assessment->pensioner_discount == 1) {
                                            $pensioner_discount = $property_tax_payable * (10 / 100);
                                        }
                                        if (@$property->assessment->disability_discount == 1) {
                                            $disability_discount = $property_tax_payable * (10 / 100);
                                        }

                                        // dd($pensioner_discount, $disability_discount);

                                        $discounted_rate_payable =
                                            $property->assessment->getPropertyTaxPayable() -
                                            $pensioner_discount -
                                            $disability_discount;
                                        if (
                                            $property->assessment->pensioner_discount &&
                                            $property->assessment->disability_discount
                                        ) {
                                            $discounted_value = $property_tax_payable * (20 / 100);
                                            $pensioner_discount = $property_tax_payable * (10 / 100);
                                            $disability_discount = $property_tax_payable * (10 / 100);
                                        } elseif (
                                            $property->assessment->pensioner_discount &&
                                            $property->assessment->disability_discount != 1
                                        ) {
                                            $discounted_value = $property_tax_payable * (10 / 100);
                                            $pensioner_discount = $property_tax_payable * (10 / 100);
                                        } elseif (
                                            $property->assessment->pensioner_discount != 1 &&
                                            $property->assessment->disability_discount
                                        ) {
                                            $discounted_value = $property_tax_payable * (10 / 100);
                                            $disability_discount = $property_tax_payable * (10 / 100);
                                        } else {
                                            $discounted_value = 0;
                                        }
                                    @endphp

                                    <tr>
                                        <th>Discount(s) Applicable:</th>
                                        <th>NLe {{ number_format((float) $discounted_value, 2, '.', ',') }}</th>
                                    </tr>
                                    <tr>
                                        <th>Discounted Rate Payable:</th>
                                        <th>{{ number_format((float) $discounted_rate_payable, 2, '.', ',') }}</th>
                                    </tr>
                                    <tr>
                                        <th>Arrears:</th>
                                        <th>{{ number_format((float) $property->assessment->getArrearDueAttribute(), 2, '.', ',') }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Penalty:</th>
                                        <th>{{ number_format((float) $property->assessment->getPenaltyAttribute(), 2, '.', ',') }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Amount Due 2024:</th>
                                        <th>NLe {{ $payment->totalAssessment() }}</th>
                                    </tr>
                                    <tr>
                                        <th>Amount Paid:</th>
                                        <th>NLe {{ $payment->amountPaid() }}</th>
                                    </tr>
                                    <tr>
                                        <th>Balance Due:</th>
                                        <th>NLe
                                            {{ number_format((float) $property->assessment->getCurrentYearTotalDue(), 2, '.', ',') }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                    </tr>
                                   
                                    <tr>
                                        <th colspan="2">
                                            THANK YOU FOR YOUR COMPLIANCE. YOUR CONTRIBUTION IS ESSENTIAL IN HELPING US
                                            MAINTAIN AND IMPROVE OUR COMMUNITY SERVICES AND INFRASTRUCTURE

                                        </th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>

</html>
