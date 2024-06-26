<div class="receipt">
    <div class="container">
        <div class="receipt-content">
            <div class="row">
                <div class="col-lg-12">
                    <p class="text-uppercase text-center" style="text-align:center; font-size: 12px; margin-bottom: 5px;"><strong>PLEASE
                            BRING THIS DEMAND NOTE
                            TOGETHER
                            WITH
                            RECEIPTS FOR ALL PREVIOUS INSTALMENTS AT TIME OF PAYMENT</strong></p>
                </div>

                <table class="" width="100%">
                    <tr>
                        <td align="left" style="width: 18%;">
                            {{--<img style="padding: 0 15px;" src="{{ asset('images/logo1.png') }}" alt="">--}}
                            <img style="padding: 0 15px;" src="{{  $district->getPrimaryLogoUrl(0,0,true) }}" alt="">
                        </td>
                        <td style="text-align:center;">
                            <h1 class="text-center" style="font-weight:500;">{{$district->council_name}} - {{$district->council_short_name}}</h1>
                            <h6 class="text-center mb-4" style="margin:0;font-weight:500;">{{$district->council_address}}</h6>
                            <h4 class="text-center font-weight-600" style="margin-bottom:5px;font-weight:500;">PROPERTY
                                RATE DEMAND NOTE</h4>
                            <h6 class="text-center font-weight-bold mb-3" style="margin:0; font-weight:bold;">JANUARY –
                                DECEMBER {{ $assessment->created_at->year }}</h6>
                        </td>
                        <td align="right" style="width: 18%;">
                            {{--<img style="padding: 0 15px;" src="{{ asset('images/logo2.jpg') }}" alt="">--}}
                            <img style="padding: 0 15px;" src="{{ $assessment->getImageAnyUrl(85,85,true) }}" alt="">
                        </td>
                    </tr>
                </table>

                <div class="col-lg-12">
                    <table class="table table-bordered"
                           style=" border:1px solid lightgray;width:100%; margin-bottom:15px;margin-top:5px;">
                        <thead>
                        <tr>
                            <th scope="col" colspan="7">ASSESSMENT DETAILS</th>
                        </tr>
                        </thead>
                    </table>

                    <table class="table table-bordered" style="width:100%;margin-bottom:15px;">
                        <thead>
                        <tr>
                            <th style="border:1px solid #ccc; text-align:left;width: 10%" scope="col"
                                class="text-left">

                                <span style="white-space: pre">{{ $property->is_organization ? 'ORGANIZATION' : 'OWNER' }} NAME</span>
                            </th>
                            <th style="border:1px solid #ccc;"
                                scope="col">{{ $property->is_organization ?  $property->organization_name :  optional(App\Models\UserTitleTypes::find($property->landlord->ownerTitle))->label.' '.(optional($property->landlord)->first_name . ' ' . optional($property->landlord)->middle_name . ' ' . optional($property->landlord)->surname) }}</th>
                            <th style="border:1px solid #ccc;width: 5%" scope="col">
                                <span style="white-space: pre">TEL:</span>
                            </th>
                            <th style="border:1px solid #ccc; width: 10%"
                                scope="col">
                                <span style="white-space: pre">{{ $property->landlord->mobile_1 }} {{ (strlen( $property->landlord->mobile_2) > 5) ?  ', ' . $property->landlord->mobile_2  : '' }}</span>
                            </th>
                        </tr>
                        </thead>
                    </table>

                    <table class="table table-bordered" style="width:100%;margin-bottom:15px;">
                        <thead>
                        <tr>
                            <th style="border:1px solid #ccc;text-align:left; width: 10%" scope="col"
                                class="text-left">
                                <span style="white-space: pre">PROPERTY ADDRESS</span>
                            </th>
                            <th style="border:1px solid #ccc;" scope="col">{{ $property->street_number }}
                                , {{ $property->street_name }}, {{ $property->section }},Ward:{{ $property->ward }},
                                Constituency:{{ $property->constituency }}, {{ $property->chiefdom }}
                                , {{ $property->province }}</th>
                        </tr>
                        </thead>
                    </table>
                    <table class="table table-bordered" style=" width:100%;margin-bottom:15px;">
                        <thead>
                        <tr>
                            <th style="border:1px solid #ccc; text-align: center;" scope="col" width="20%">PROPERTY TYPE</th>
                             <th style="border:1px solid #ccc;" scope="col" width="16%">HABITABLE FLOOR(S)</th> 
                            <th style="border:1px solid #ccc;white-space: pre" scope="col">FLOOR AREA (Ft<sup>2</sup>)</th>
                            <th style="border:1px solid #ccc;" scope="col" width="20%">WALL MATERIAL</th>
                            <th style="border:1px solid #ccc;" scope="col">ROOF MATERIAL</th>
                            <th style="border:1px solid #ccc;" scope="col" width="16%">WINDOW TYPE</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td style="border:1px solid lightgray;">
                                @php
                                            $array_values = ["Flats/Apartment","Zinc House","Board House","Mud House","Mudcrete"];
                                            $index = 0;
                                            
                                            
                                            if(array_search($array_values[0], $assessment->categories->pluck('label')->toArray()) ){
                                                $index = array_search($array_values[0], $assessment->categories->pluck('label')->toArray() );
                                            }else if(array_search($array_values[1], $assessment->categories->pluck('label')->toArray()) ) {
                                                $index = array_search($array_values[1], $assessment->categories->pluck('label')->toArray() );
                                            }else if( array_search($array_values[2], $assessment->categories->pluck('label')->toArray()) ) {
                                                $index = array_search($array_values[2], $assessment->categories->pluck('label')->toArray());
                                            }else if( array_search($array_values[3], $assessment->categories->pluck('label')->toArray()) ) {
                                                $index = array_search($array_values[3], $assessment->categories->pluck('label')->toArray());
                                            }else if(array_search($array_values[4], $assessment->categories->pluck('label')->toArray()) ) {
                                                $index = array_search($array_values[4], $assessment->categories->pluck('label')->toArray());
                                            }
                                            
                                            $type  =  $assessment->categories->pluck('label')->count() > 0 ? strtoupper($assessment->categories->pluck('label')[$index]) : '';
                                            $type_val =  $assessment->categories->pluck('value')->count() > 0 ? $assessment->categories->pluck('value')[$index] : '';
                                        @endphp
                                        {{ $type }}
                            </td>
                             <td style="border:1px solid lightgray;">{{ strtoupper($assessment->types->pluck('label')->implode(', ')) }} 
                                {{-- {{$assessment->types->pluck('value')->sum()}} --}}
                            </td> 
                            <td style="border:1px solid lightgray;">{{ strtoupper(number_format(optional($assessment)->square_meter, 2,'.','')) }} {{ (optional($assessment)->square_meter) ? ' SQ FEET' : '' }}</td>
                            <td style="border:1px solid lightgray;">
                                {{strtoupper( optional(App\Models\PropertyWallMaterials::find($property->assessment->property_wall_materials))->label) . ' (' . optional($assessment)->wall_material_type . ')' }}
                            </td>
                            <td style="border:1px solid lightgray;">{{  strtoupper(optional(App\Models\PropertyRoofsMaterials::find($property->assessment->roofs_materials))->label) . ' (' . optional($assessment)->roof_material_type . ')' }}</td>
                            <td style="border:1px solid lightgray; ">{{ strtoupper(optional(optional($assessment)->windowType)->label) . ' (' . optional($assessment)->roof_material_type . ')' }}</td>
                        </tr>
                        </tbody>
                    </table>

                    <table class="table table-bordered" style=" width:100%;margin-bottom:15px;">
                        <thead>
                            <tr>
                                <th style="border:1px solid #ccc;text-align: left" scope="col">VALUE ADDED ASSESSMENT PARAMETERS</th>
                                <th style="border:1px solid #ccc; text-align: center" scope="col">Sanitation</th>
                                <th style="border:1px solid #ccc;" scope="col">PROPERTY USE</th>
                                
                       
                          
                            </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td style="border:1px solid lightgray;">{{ strtoupper($assessment->valuesAdded->pluck('label')->implode(', ')) }} </td>
                            <td style="border:1px solid lightgray; ">{{ strtoupper(optional(App\Models\PropertySanitationType::find($assessment->sanitation))->label) }}</td> 
                            <td style="border:1px solid lightgray; ">{{ strtoupper(optional(App\Models\PropertyUse::find($assessment->property_use))->label) }}</td>
                            
                            
                                            
                            <!-- {{ strtoupper(($assessment->value_added_type)? '('.strtoupper($assessment->value_added_type).')': '') }}           -->
                        </tr>
                        </tbody>
                    </table>

                    <table class="table table-bordered" style="width:100%;margin-bottom:15px;">
                        <thead>
                        <tr>
                           
                            <th style="border:1px solid #ccc;" scope="col">ASSESSED VALUE</th>
                            <th style="border:1px solid #ccc;" scope="col">COUNCIL ADJUSTMENTS</th>
                            <th style="border:1px solid #ccc;" scope="col">NET ASSESSED VALUE</th>
                            <th style="border:1px solid #ccc;" scope="col">COUNCIL CATEGORY</th>
                            {{--  <th style="border:1px solid #ccc;" scope="col">TAXABLE PROPERTY VALUE</th>  --}}
                            <th style="border:1px solid #ccc;" scope="col">MILL RATE</th>
                            <th style="border:1px solid #ccc;" scope="col">RATE PAYABLE 2024</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                           
                            <td style="border:1px solid lightgray;">{!! number_format($assessment->getCurrentYearAssessmentAmount()) !!}</td>
                            <td style="border:1px solid lightgray;">{!! number_format($assessment->getCouncilAdjustments()) !!}</td>
                            <td style="border:1px solid lightgray;">{!! number_format($assessment->getNetPropertyAssessedValue()) !!}</td>
                            <td style="border:1px solid lightgray;">{!! ($assessment->group_name) !!}</td>
                            {{--  <td style="border:1px solid lightgray;">{!! number_format($assessment->getNetPropertyAssessedValue()*12*27*1.18, 2, '.',',') !!}</td>  --}}
                            <td style="border:1px solid lightgray;">{!! $assessment->mill_rate > 0? $assessment->mill_rate: 0 !!}</td>
                            <td style="border:1px solid lightgray;">{!! number_format($assessment->getPropertyTaxPayable()) !!}</td>
                        </tr>
                        </tbody>
                    </table>

                    <table class="table table-bordered" style="width:100%;margin-bottom:15px;" cellspacing="0" cellpadding="0">
                        <thead>
                        <tr>
                            <th style="border:1px solid #ccc;" scope="col">PENSIONER DISCOUNT</th>
                            <th style="border:1px solid #ccc;" scope="col">DISABLITY DISCOUNT</th>
                            <th style="border:1px solid #ccc;" scope="col">DISCOUNTED RATE PAYABLE 2024</th>
                            {{--  <th style="border:1px solid #ccc;" scope="col">PROPERTY TAX PAYABLE {{ $assessment->created_at->year }}</th>  --}}
                            <th style="border:1px solid #ccc;" scope="col">ARREARS Past Year(s)</th>
                            <th style="border:1px solid #ccc;" scope="col">PENALTY</th>
                            <th style="border:1px solid #ccc;" scope="col">1st INSTALMENT<br/>DUE 30-06-{{ $assessment->created_at->year }}</th>
                            <th style="border:1px solid #ccc;" scope="col">2nd INSTALMENT<br/>DUE 31-12-{{ $assessment->created_at->year }}</th>
                            {{--  <th style="border:1px solid #ccc;" scope="col">3rd INSTALMENT<br/>DUE 30-09-{{ $assessment->created_at->year }}</th>
                            <th style="border:1px solid #ccc;" scope="col">4th INSTALMENT<br/>DUE 31-12-{{ $assessment->created_at->year }}</th>  --}}
                            <th style="border:1px solid #ccc;" scope="col">TOTAL AMOUNT DUE 31-12-{{ $assessment->created_at->year }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td style="border:1px solid lightgray;">{!! $assessment->pensioner_discount ? number_format($assessment->getPensionerDiscount(),0,'',',') : 0 !!}</td>
                            <td style="border:1px solid lightgray;">{!! $assessment->disability_discount ? number_format($assessment->getDisabilityDiscount(),0,'',',') : 0 !!}</td>
                            <td style="border:1px solid lightgray;">{!! $assessment->getPensionerDisabilityDiscountActual() ? number_format($assessment->getPensionerDisabilityDiscountActual(),0,'',',') : 0 !!}</td>
                            {{--  <td style="border:1px solid lightgray; color:#000000; background-color:#E0E0E0;">{!! number_format($assessment->getPropertyTaxPayable()) !!}</td>  --}}
                            <td style="border:1px solid lightgray;">{!! number_format($assessment->getPastPayableDue()) !!}</td>
                            <td style="border:1px solid lightgray;">{!! number_format($assessment->getPenalty()) !!}</td>
                            <td style="border:1px solid lightgray;">{!! isset($paymentInQuarter[1]) ?  number_format($paymentInQuarter[1]) : '-' !!}</td>
                            <td style="border:1px solid lightgray;">{!! isset($paymentInQuarter[2]) ?  number_format($paymentInQuarter[2]) : '-' !!}</td>
                            {{--  <td style="border:1px solid lightgray;">{!! isset($paymentInQuarter[3]) ?  number_format($paymentInQuarter[3]) : '-' !!}</td>
                            <td style="border:1px solid lightgray;">{!! isset($paymentInQuarter[4]) ?  number_format($paymentInQuarter[4]) : '-' !!}</td>  --}}
                            <td style="border:1px solid lightgray; color:#000000; background-color:#E0E0E0;">{{ number_format($assessment->getCurrentYearTotalDue()) }}</td>
                        </tr>
                        </tbody>
                    </table>

                    <table class="total" style="width: 100%;">
                        <tr>
                            <td style="text-align: left;padding-right: 20px">
                                <h6 class="font-weight-bold" style="white-space: pre">PLEASE DISREGARD ARREARS IF PAID</h6>
                            </td>
                            <td style="border:1px solid #ccc; width: 10%">
                                <h5  style="font-size: 12px;margin: 0; white-space: pre; color:#000000; background-color:#E0E0E0;" align="center">PROPERTY ID: {{ $property->getPrintableId() }}</h5>
                            </td>
                            <td style="border:1px solid #ccc;width: 10%">
                                <h5 style="font-size: 12px;margin: 0; white-space: pre" align="center">DIGITAL ADDRESS:</h5>
                            </td>
                            <td style="font-size: 1rem;border:1px solid #ccc;width: 10%">
                                <h5 style="font-size: 12px;margin: 0; white-space: pre" align="center">{{ $property->newDigitalAddress() }}</h5>
                            </td>
                        </tr>
                    </table>

                    <div class="clearfix"></div>
                    <p class="notice-text" style="font-size: 9px !important; margin-bottom: 5px; margin-top: 8px">{{$district->penalties_note}}</p>
                </div>
            </div>
        </div>
    </div>
</div>