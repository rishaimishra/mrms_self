<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Receipt</title>

    <style>
        /*body img {*/
        /*    max-width: 100%;*/
        /*}*/
        @page {
            size: 8.5in 11in;
            margin: 35pt 7.5pt;
        }

        body {

        }

        body h6 {
            font-size: 1.2em;
        }

        h2 {
            margin: 8px;
        }

        body h4 {
            font-size: 1.3em;
        }

        /*body .table td,*/
        /*body .table th {*/
        /*    text-align: center;*/
        /*    vertical-align: top;*/
        /*    padding: .15rem !important;*/
        /*    font-size: 0.75rem;*/
        /*}*/

        /*body .table td {*/
        /*    height: 20px;*/
        /*}*/

        /*body .table th {*/
        /*    text-transform: uppercase;*/
        /*}*/


        .footer-text {
            width: 550px;
            margin: auto;
            color: gray;
            font-weight: bold;
            margin-top: 15px;
        }


        .page {
            width: 593pt;
            min-height: 300mm;
            padding: 1mm;
            border-radius: 5px;
            background: #ffffff;
            font-size: 0.85rem;

        }

        .special-text {
            font-size: 0.85rem;
        }

        .h-warning {
            font-size: 0.85rem !important;
        }

        /*@page {
            size: A4;
            margin: 35px 10px;
        }*/

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

        @media print {
            .pagebreak {
                clear: both;
                page-break-after: always;
            }
        }

        .page-break {
            page-break-after: always;
        }

        .receipt-description p {
            font-weight: 400 !important;
        }

    </style>
</head>
<body style="font-family: Arial;">


<div class="page">
    @foreach($properties as $key => $property)

        @if(($key + 1) % 2 != 0)
            <div style="margin-top: 7.5pt;">
                @endif

                <div style="width: 280pt;border: 1px solid; padding: 5px 0 0 5px;border-radius: 10px; float: left; margin-right: 5.62pt; margin-left: 5.64pt;">
                    <table width="100%">
                        <tr>
                            <td style="text-align: center;padding-left: 0px;">
                                {{-- <img src="{{ asset('images/logonew1.jpeg') }}" alt="" style="height:55px; width:55px;"> --}}
                                <img src="{{  $property->districts->getPrimaryLogoUrl(55,55) }}" alt="" >
                            </td>

                            <td style="text-align: center; margin-bottom: 15px;">
                                <h2 style="font-size: 12.6px; padding: 0; margin: 0;">{{$property->districts->council_name}}</h2>
                                <h2 style="margin-bottom: 15px; font-size: 17px; padding: 0; margin: 0;">({{$property->districts->council_short_name}})</h2>
                            </td>

                            <td style="text-align: center;padding-right: 0px;">
                                {{-- <img src="{{ asset('images/logonew2.svg') }}" style="height:55px;width:55px;"> --}}
                                <img src="{{ $property->districts->getSecondaryLogoUrl(55,55) }}" alt="">
                            </td>
                        </tr>
                    </table>

                    <table width="100%">
                        <tr>
                            <td>
                                <p style="margin: 5px 0px;"><strong >Property ID: </strong> {{ $property->id  }}</p>
                                <p style="margin: 5px 0px;"><strong >Name: </strong> {{ $property->is_organization ?   substr($property->organization_name,0,25) :  substr((optional($property->landlord)->first_name . ' ' . optional($property->landlord)->middle_name . ' ' . optional($property->landlord)->surname),0,25) }}</p>
                                <p style="margin: 5px 0px;"><strong>Payment ID: </strong> 00{{ optional($property->payments()->orderBy('id')->first())->id }}</p>
                                <p style="margin: 5px 0px;"><strong>PAID IN FULL {{ $request->demand_draft_year ? $request->demand_draft_year : 2019 }}</strong></p>

                            </td>
                            <td width="20%" >
                                <img style="margin-right: 0px; width: 100px;" src="data:image/png;base64, {!! base64_encode(
                                        \QrCode::format('png')->size(200)->generate(
                                        "ON : ". ($property->is_organization ?  $property->organization_name :  (optional($property->landlord)->first_name . ' ' . optional($property->landlord)->middle_name . ' ' . optional($property->landlord)->surname)).
                                        ",\n DA : ". (optional($property->geoRegistry)->digital_address).
                                        ",\n RD : ". (number_format($property->getAssessment())).
                                        ",\n ARR : ". (number_format($property->getBalance())).
                                        ",\n PT : ". ($property->types->pluck('label')->implode(', ')).
                                        ",\n PD : ". ((optional(optional($property->assessment)->dimension)->label) . ' ' . (optional(optional($property->assessment)->dimension)->id == 1 ? '' : ' SQM')) .
                                      // ",\n MOW : ". (optional(optional($property->assessment)->wallMaterial)->label) .
                                      //  ",\n MOR : ". (optional(optional($property->assessment)->roofMaterial)->label) .
                                        //",\n VA : ". ($property->valueAdded->pluck('label')->implode(', ')) .
                                        ",\n OT : ". (optional($property->occupancy)->type) .
                                        ",\n ADD : ". ($property->street_number . ', ' . $property->street_name . ', ' . $property->ward . ', '. $property->section )

                                        ))!!}">
                            </td>

                        </tr>
                    </table>

                </div>

                @if(($key + 1) % 2 == 0)
            </div>
        @endif

        @if(($key + 1) % 2 == 0)
            <div style="clear: both;">&nbsp;</div>
        @endif

        @if(($key + 1) % 10 == 0)
           {{-- <div class="page-break"></div>--}}
        @endif
    @endforeach


</div>


</body>
</html>
