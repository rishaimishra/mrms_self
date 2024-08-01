<div class="receipt-second">
    <div class="container">
        <div class="receipt-content">
            <div class="row">
                <div class="col-lg-12">
                    <p class="font-weight-bold" style="margin-bottom:0px;font-size: 12px;text-transform: uppercase;text-align:justify;">THE {{$district->council_name}} DEMANDS
                        PAYMENT OF MUNICIPAL RATE IN
                        RESPECT OF THE PERIOD COMMENCING 1ST JANUARY TO 31ST
                        DECEMBER {{ $assessment->created_at->year }} IN 2
                        INSTALLMENTS ON OR
                        BEFORE THE FOLLOWING DATES</p>

                    <table width="100%">
                        <tr>
                            <td width="30%" align="left" style="text-align: left; font-weight:bold;" class="installment-section">
                                <ul>
                                    <li><span>FIRST INSTALLMENT</span></li>
                                    <li><span>SECOND INSTALLMENT</span></li>
                                    {{--  <li><span>THIRD INSTALLMENT</span></li>
                                    <li><span>FINAL INSTALLMENT</span></li>  --}}
                                </ul>
                            </td>
                            <td align="left" style="text-align: left; font-weight:bold;" class="installment-section">
                                <ul>
                                    <li><span>- 30-06-{{ $assessment->created_at->year }}</span></li>
                                    <li><span>- 31-12-{{ $assessment->created_at->year }}</span></li>
                                    {{--  <li><span>- 30-09-{{ $assessment->created_at->year }}</span></li>
                                    <li><span>- 31-12-{{ $assessment->created_at->year }}</span></li>  --}}
                                </ul>
                            </td>
<!--                             <td width="40%" align="right" style="text-align: right;">
                                 <img style="margin-right: 5px;" src="{{$assessment->getImageAnyUrl(85,85)}}">
                            </td> -->
                        
                        </tr>
                        <tr>
                            <td colspan="2">
                                <p class="mb-0 special-text font-weight-bold " style="text-align:justify;margin-top:0px;">
                                    <!-- WARNING: {{-- $district->warning_note --}} -->
                                    PLEASE NOTE: A SURCHARGE OF 25% WILL BE LEVIED ON THE TOTAL UNPAID OR ARREARS AMOUNT DUE AFTER 31 DECEMBER <br> OF EVERY CALENDAR YEAR
                                </p>
                            </td> 
                           
                        </tr>
                    </table>

                   
                    <p class="font-weight-bold">BANK ACCOUNTS FOR COLLECTION OF MUNICIPAL RATE REVENUE</p>

                    <table width="100%">
                        <tr style="vertical-align: top;">
                            <td>
                                <table class="mb-4 table-bordered table" style="width:100%;">
                                    <thead>
                                    <tr>
                                        <th style="border:1px solid lightgray;"></th>
                                        <th style="border:1px solid lightgray;text-align: left;">Council</th>
                                        <th style="border:1px solid lightgray;">BANK</th>
                                        <th style="border:1px solid lightgray;">ACCOUNT NAME</th>
                                        <th style="border:1px solid lightgray;">ACCOUNT NUMBER</th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if($district->bank_details)
                                        @foreach($district->bank_details as $bkey => $bank_detail)
                                            @if(!($bank_detail['location'] == '' && $bank_detail['name'] == '' && $bank_detail['account_name'] == '' && $bank_detail['account_number'] == ''))
                                            <tr>
                                                <td style="border:1px solid lightgray;">{{ $bkey + 1}}</td>
                                                <td style="border:1px solid lightgray;text-align: left;"><strong>{{$bank_detail['location']}}</strong></td>
                                                <td style="border:1px solid lightgray;">{{$bank_detail['name']}}</td>
                                                <td style="border:1px solid lightgray;">{{$bank_detail['account_name']}}</td>
                                                <td style="border:1px solid lightgray;">{{$bank_detail['account_number']}}</td>

                                            </tr>
                                            @endif
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </td>

                        </tr>
                    </table>
                    @if($district->collection_point)
                    <p class="font-weight-bold">CONTACT CENTER(S)</p>

                    @php
                        $maxNoOfTd = max(count(array_filter($district->collection_point)), count(array_filter($district->collection_point2)));
                    @endphp
                    <table class="table-bordered table" style="width:100%;">
                        <tbody>
                        @if(array_filter($district->collection_point))    
                        <tr>
                        @php
                            $point1TdCount = count(array_filter($district->collection_point));
                        @endphp

                            @foreach($district->collection_point as $ekey => $collection)
                                @if ($loop->first)
                                    <td style="border:1px solid lightgray;text-align: left;"><strong class="font-weight-bold special-text" style="font-size: 12px;color:#696766;">{{$collection}}</strong></td>
                                @else
                                    <td style="border:1px solid lightgray;">{{$collection}}</td>
                                @endif
                            @endforeach

                            @php
                                $remainingPoint1TdCount = $maxNoOfTd - $point1TdCount;
                            @endphp                            

                            @for ($i = 0; $i < $remainingPoint1TdCount; $i++)
                                <td style="border:1px solid lightgray;">&nbsp;</td>
                            @endfor

                        </tr>
                        @endif

                        @if(array_filter($district->collection_point2))
                        <tr>
                        @php
                            $point2TdCount = count(array_filter($district->collection_point2));
                        @endphp                            
                            @foreach(array_filter($district->collection_point2) as $ekey => $collection2)
                                @if ($loop->first)
                                    <td style="border:1px solid lightgray;text-align: left;"><strong class="font-weight-bold special-text" style="font-size: 12px;color:#696766;">{{$collection2}}</strong>
                                    </td>
                                @else
                                    <td style="border:1px solid lightgray;">{{$collection2}}</td>
                                @endif

                            @endforeach

                            @php
                                $remainingPoint2TdCount = $maxNoOfTd - $point2TdCount;
                            @endphp                            

                            @for ($i = 0; $i < $remainingPoint2TdCount; $i++)
                                <td style="border:1px solid lightgray;">&nbsp;</td>
                            @endfor

                        </tr>
                        @endif
                        </tbody>
                    </table>
                    @endif

                    <p class="mb-2 special-text font-weight-bold">PLEASE QUOTE ACCOUNT NAME AND NUMBER ABOVE ON BANK
                        SLIPS WHEN MAKING CHEQUE AND CASH PAYMENTS.</p>
                    <p class="mb-0 special-text font-weight-bold">PAYMENT IS DUE 4 WEEKS AFTER RECIEPT OF THIS NOTICE
                        AND MUST COVER PREVIOUS/PAST INSTALLMENT DATES SHOWN ABOVE.</p>
                </div>
            </div>

            <table width="100%">
                <tr>
                    <td style="text-align: left; width: 33%">
                        <table style="width: 100%; text-align: left;">
                            <tr>
                                <td style="text-align: left; padding-left: 47px;">
                                    {{--<img src="{{ asset('images/ca_signature1.jpg') }}" alt="" style="height: 40px;">--}}
                                    <img src="{{  $district->getChifAdministratorSignUrl(0,0,true) }}" alt=""
                                         style="height: 70px; width: 80px;">
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: left;">...............................................</td>
                            </tr>
                            <tr>
                                <td style="text-align: left; font-size: 12px">CHIEF ADMINISTRATOR
                                    ({{  $district->council_short_name }})
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td width="10%" align="right" class="qr-code-wrapper" style="text-align: right;">
                        <img style="margin-right: 20px;margin-bottom: 30px;" src="data:image/png;base64,{!! base64_encode(
                                \QrCode::format('png')->size(140)->generate(
                                "ON : ". ($property->is_organization ?  $property->organization_name :  (optional($property->landlord)->first_name . ' ' . optional($property->landlord)->middle_name . ' ' . optional($property->landlord)->surname)).
                                ",\n DA : ". (optional($property->geoRegistry)->digital_address).
                                ",\n RD : ". (number_format($assessment->getCurrentYearAssessmentAmount())).
                                ",\n ARR : ". number_format((float) $assessment->getCurrentYearTotalDue(), 2, '.', ',').
                                ",\n PT : ". ($assessment->types->pluck('label')->implode(', ')).
                                ",\n PD : ". ((optional(optional($assessment)->dimension)->label) . ' ' . (optional(optional($assessment)->dimension)->id == 1 ? '' : ' SQ METERS')) .
                               ",\n MOW : ". (optional(optional($assessment)->wallMaterial)->label) .
                                ",\n MOR : ". (optional(optional($assessment)->roofMaterial)->label) .
                               // ",\n VA : ". ($property->valueAdded->pluck('label')->implode(', ')) .
                                ",\n OT : ". (optional($property->occupancy)->type) .
                                ",\n ADD : ". ($property->street_number . ', ' . $property->street_name . ', ' . $property->ward . ', ' . $property->constituency . ', '. $property->section . ', ' . $property->district . ', ' . $property->province)

                                ))!!}">
                    </td>
                    <td style=" width: 33%;">
                        <table style="width: 100%;   ">
                            <tr>
                                <td align="center" style=" text-align: right; padding-right: 25px;">
                                    <img src="{{  $district->getCeoSignUrl(0,0,true) }}" alt="" style="height: 70px; width: 80px;">
                                </td>
                            </tr>
                            <tr>
                                <td style=" text-align: right; padding-right: 5px;">
                                    .....................................
                                </td>
                            </tr>
                            <tr>
<!--                                 <td style=" text-align: right; margin-right: 30px;font-size: 12px">CEO (SIGMA VENTURES
                                    LTD.)
                                </td>   -->                              
                                <td style=" text-align: right; margin-right: 30px;font-size: 12px">
                                    @if($district->council_short_name == 'KCC' || $district->council_short_name =='FCC' || $district->council_short_name =='BMC' || $district->council_short_name =='BOCC' || $district->council_short_name =='MCC' || $district->council_short_name =='KNSCC' || $district->council_short_name =='PLCC')
                                    <span style="margin-right: 35px;">MAYOR ({{  $district->council_short_name }})</span>
                                    @else
                                    <span style="margin-right: 35px;">CHAIRMAN ({{  $district->council_short_name }})</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <table width="100%">
                <tr>
                    <td style="color:#000000; background-color:#E0E0E0; padding:5px; width:50%;">
                        <p style="text-align: left;font-size: 13px;">
                            @if($district->enquiries_phone!='')        
                                <span style="display: block;">
                                    <span style="color: black; font-weight: bold;">TELEPHONE - </span>
                                    <span style="font-size: 14px; font-weight: bold">{{  $district->enquiries_phone }}</span>
                                </span>
                            @endif
                            @if($district->enquiries_phone2!='')  
                                <span style="display: block;">
                                    <span style="color: black; font-weight: bold;">TELEPHONE - </span>
                                    <span style="font-size: 14px; font-weight: bold">{{  $district->enquiries_phone2 }}</span>
                                </span>
                            @endif

                            <span style="display: block; margin-bottom: 4px; font-weight: bold;">E-MAIL - <a
                                    href="mailto:{{  $district->enquiries_email }}"> {{  $district->enquiries_email }}</a>
                            </span>
                            
                        </p>
                    </td>
                    {{--  <td rowspan="2" width="20%" align="right" class="qr-code-wrapper" style="text-align: right;">
                        <img style="margin-right: 20px;" src="data:image/png;base64,{!! base64_encode(
                                \QrCode::format('png')->size(140)->generate(
                                "ON : ". ($property->is_organization ?  $property->organization_name :  (optional($property->landlord)->first_name . ' ' . optional($property->landlord)->middle_name . ' ' . optional($property->landlord)->surname)).
                                ",\n DA : ". (optional($property->geoRegistry)->digital_address).
                                ",\n RD : ". (number_format($assessment->getCurrentYearAssessmentAmount())).
                                ",\n ARR : ". (number_format($assessment->getCurrentYearTotalDue())).
                                ",\n PT : ". ($assessment->types->pluck('label')->implode(', ')).
                                ",\n PD : ". ((optional(optional($assessment)->dimension)->label) . ' ' . (optional(optional($assessment)->dimension)->id == 1 ? '' : ' SQ METERS')) .
                               ",\n MOW : ". (optional(optional($assessment)->wallMaterial)->label) .
                                ",\n MOR : ". (optional(optional($assessment)->roofMaterial)->label) .
                               // ",\n VA : ". ($property->valueAdded->pluck('label')->implode(', ')) .
                                ",\n OT : ". (optional($property->occupancy)->type) .
                                ",\n ADD : ". ($property->street_number . ', ' . $property->street_name . ', ' . $property->ward . ', ' . $property->constituency . ', '. $property->section . ', ' . $property->district . ', ' . $property->province)

                                ))!!}">
                    </td>  --}}
                </tr>
            </table>

            <div class="row">
                <p class="footer-text" style="color: #C8C8C8;">MUNICIPAL RATE MANAGEMENT SYSTEM - POWERED BY SIGMA VENTURES LTD. </p>
            </div>
        </div>
    </div>
</div>
