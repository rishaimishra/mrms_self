<div class="receipt-second policy-content">
    <div class="container">
        <div class="receipt-content receipt-description" style="color: #888888;">
           

                <div style="text-align: center;width: 100%;">
                    <strong style="color: #888888;">MUNICIPAL RATE MANAGEMENT SYSTEM (MRMS)</strong>
                    <br>
                    <br>
                    <strong style="color: #888888;">RATE CALCULATION</strong>
                </div>

            
            <p>
                <strong> </strong>
                <strong></strong>
            </p>
            
            
            <table class="table table-bordered" style="width:100%; margin-left: -5px;" cellspacing="0" cellpadding="0">
            <tbody>
                            <tr>
                                <td>
                                    <th style="border-top:1px solid  #606060; border-left:1px solid  #606060; text-align: right; font-size: 10px; width:15%; color:#303030;" scope="col"> ASSESSMENT</th>
                                </td>
                                <td style="border-top:1px solid  #606060; border-right:1px solid  #606060; font-weight: bold;text-align: left; font-size: 10px; width:15%; color:#303030;">PARAMETERS</td>
                                <td>
                                    <th style="border-top:1px solid  #606060; border-left:1px solid  #606060;text-align: right; font-size: 10px; width:20%; color:#303030;" scope="col">COUNCIL</th>
                                </td>
                                <td style="border-top:1px solid  #606060; border-right:1px solid  #606060; font-weight: bold;text-align: left; font-size: 10px; width:20%; color:#303030;">ADJUSTMENTS</td>
                                <td>
                                    <th style="border-top:1px solid  #606060; border-left:1px solid  #606060; border-right:1px solid  #606060;  font-size: 10px;width:30%; color:#303030;" colspan="2" scope="col">TAXABLE PROPERTY VALUE (LE)</th>
                                </td>
                                <!-- <td style="border-top:1px solid  #606060; border-right:1px solid  #606060; font-weight: bold;text-align: left; font-size: 10px;width:15%; color:#303030;">VALUE (LE)</td> -->
                            </tr>
                            
                            <tr>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left;color:#303030;" scope="col">PARAMETERS</th>
                                </td>
                                <td style="border:1px solid  #606060; font-weight: bold;color:#303030;">VALUE</td>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left;color:#303030;" scope="col">PARAMETERS</th>
                                </td>
                                <td style="border:1px solid  #606060; font-weight: bold;color:#303030;">DEDUCTIONS</td>
                                <td>
                                    <th style="border-top:1px solid  #606060; border-left:1px solid  #606060; border-right:1px solid  #606060; border-bottom:1px solid  #606060;" colspan="2"  scope="col">LE {!! number_format($assessment->geTaxablePropertyValue()) !!}</th>
                                    <!-- <th style="border-top:1px solid  #606060; border-right:1px solid  #606060; border-bottom:1px solid  #606060; text-align: left;"  scope="col">{!! number_format($assessment->geTaxablePropertyValue()) !!}</th> -->
                                </td>
                                <!-- <td style="border-top:1px solid  #606060; border-right:1px solid  #606060; border-bottom:1px solid  #606060; font-weight: bold;text-align: left;"></td> -->
                            </tr>
                            <tr>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left;" scope="col">Property Type</th>
                                </td>
                                <td style="border:1px solid #606060; color: #484848;">
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
                                    {{ $type }} {{ $type_val }}
                                </td>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left;" scope="col">Water Supply</th>
                                </td>
                                <td style="border:1px solid #606060; color: #484848;">{{ $assessment->water_percentage.'%' }}</td>
                                <td>
                                    <th style="border:0px solid #606060;text-align: left;" scope="col"></th>
                                </td>
                                <td style="border:0px solid #606060;"></td>
                            </tr>
                            <tr>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left;" scope="col">Dimension</th>
                                </td>
                                <td style="border:1px solid #606060; color: #484848;">{{ strtoupper(number_format(optional($assessment)->square_meter, 2,'.','')) }} {{ (optional($assessment)->square_meter) ? ' SQ METERS' : '' }}</td>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left;" scope="col">Electricity</th>
                                </td>
                                <td style="border:1px solid #606060; color: #484848;">{{ $assessment->electricity_percentage.'%' }}</td>
                                <td>
                                    <th style="border-top:1px solid #606060; border-left:1px solid #606060;border-right:1px solid #606060; border-bottom:1px solid #606060; font-weight: bold; color:#303030;" colspan="2" scope="col">POLICY</th>
                                </td>
                                <!-- <td style="border-top:1px solid #606060; border-right:1px solid #606060; border-bottom:1px solid #606060; font-weight: bold;text-align: left; color:#303030;" ></td> -->
                            </tr>
                            <tr>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left;" scope="col">Rate Per Sq M</th>
                                </td>
                                <td style="border:1px solid #606060; color: #484848;">LE {{ number_format($district->sq_meter_value) }}</td>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left;" scope="col">Waste Managment</th>
                                </td>
                                <td style="border:1px solid #606060; color: #484848;">{{ $assessment->waste_management_percentage.'%' }}</td>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left;" scope="col">Council Category</th>
                                </td>
                                <td style="border:1px solid #606060; color: #484848;">{{ $assessment->group_name }}</td>
                                <!-- <td style="border:1px solid #606060; color: #484848;">{{ isset(App\Models\MillRate::where('rate', '=', $assessment->mill_rate)->pluck('group_name')[0]) ? App\Models\MillRate::where('rate', '=', $assessment->mill_rate)->pluck('group_name')[0] : '-'}}</td> -->
                            </tr>
                            <tr>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left;" scope="col">Habitable Floor</th>
                                </td>
                                <td style="border:1px solid #606060; color: #484848;">{{ strtoupper($assessment->types->pluck('label')->implode(', ')) }}, {{ $assessment->types->pluck('value')->sum() }}</td>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left;" scope="col">Market</th>
                                </td>
                                <td style="border:1px solid #606060;color: #484848;">{{ $assessment->market_percentage.'%' }}</td>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left;" scope="col">Mill Rate</th>
                                </td>
                                <!-- <td style="border:1px solid #606060; color: #484848;">{!! $assessment->mill_rate>0? $assessment->mill_rate: 0 !!}</td> -->
                                <td style="border:1px solid #606060; color: #484848;">{!! App\Models\MillRate::where('group_name', '=', $assessment->group_name)->pluck('rate')->count() > 0 ? App\Models\MillRate::where('group_name', '=', $assessment->group_name)->pluck('rate')[0] : '' !!}</td>
                                
                            </tr>
                            <tr>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left;" scope="col">Wall Material</th>
                                </td>
                                <td style="border:1px solid #606060; color: #484848;">LE {{ strtoupper(number_format(optional(optional($assessment)->wallMaterial)->value)) }} {{($assessment->wall_material_type)? '('.strtoupper($assessment->wall_material_type).')': ''}}</td>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left;" scope="col">Hazardous Location</th>
                                </td>
                                <td style="border:1px solid #606060;color: #484848;">{{ $assessment->hazardous_precentage.'%' }}</td>
                                <td>
                                    <th style="border:0px solid #606060;text-align: left;" scope="col"></th>
                                </td>
                                <td style="border:0px solid #606060;"></td>

                            </tr>
                            <tr>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left;" scope="col">Roof Type</th>
                                </td>
                                <td style="border:1px solid #606060; color: #484848;">LE {{ strtoupper(number_format(optional(optional($assessment)->roofMaterial)->value)) }} {{($assessment->roof_material_type)? '('.strtoupper($assessment->roof_material_type).')': ''}}</td>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left;" scope="col">INF. Settlement</th>
                                </td>
                                <td style="border:1px solid #606060;color: #484848;">{{ $assessment->informal_settlement_percentage.'%' }}</td>
                                <td>
                                    <th style="border-top:1px solid #606060; border-left:1px solid #606060;border-right:1px solid #606060; border-bottom:1px solid #606060; color:#303030;" colspan="2" scope="col">RATE PAYABLE 2021</th>
                                </td>
                                <!-- <td style="border-top:1px solid #606060; border-right:1px solid #606060; border-bottom:1px solid #606060; font-weight: bold;text-align: left; color:#303030;" >2021</td> -->
                            </tr>
                            <tr>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left;" scope="col">Window Type</th>
                                </td>
                                <td style="border:1px solid #606060; color: #484848;">LE {{ strtoupper(number_format(optional(optional($assessment)->windowType)->value)) }} {{($assessment->roof_material_type)? '('.strtoupper($assessment->roof_material_type).')': ''}}</td>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left;" scope="col">No Street Access</th>
                                </td>
                                <td style="border:1px solid #606060;color: #484848;">{{ $assessment->easy_street_access_percentage.'%' }}</td>
                                <td>
                                    <th style="border-top:1px solid  #606060; border-left:1px solid  #606060; border-right:1px solid  #606060; border-bottom:1px solid  #606060;" colspan="2"  scope="col">LE {!! number_format($assessment->getPropertyTaxPayable()) !!}</th>
                                    <!-- <th style="border-top:1px solid #606060; border-left:1px solid #606060; border-bottom:1px solid #606060; text-align: right;" scope="col">LE {!! number_format($assessment->getPropertyTaxPayable()) !!}</th> -->
                                </td>
                                <!-- <td style="border-top:1px solid #606060; border-right:1px solid #606060; border-bottom:1px solid #606060; font-weight: bold;text-align: left;"></td> -->
                            </tr>
                            <tr>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left;" scope="col">Value Added</th>
                                </td>
                                <td style="border:1px solid #606060; color: #484848;">LE {{ number_format( array_sum( explode( ',', $assessment->valuesAdded->pluck('value')->implode(', ') ) ) ) }} {{ strtoupper(($assessment->value_added_type)? '('.strtoupper($assessment->value_added_type).')': '') }}</td>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left;" scope="col">Unpaved Stree/Road</th>
                                </td>
                                <td style="border:1px solid #606060;color: #484848;">{{ $assessment->paved_tarred_street_percentage.'%' }}</td>
                                <td>
                                    <th style="border-top:1px solid #606060; border-left:1px solid #606060;border-right:1px solid #606060; border-bottom:1px solid #606060; color:#303030;" colspan="2" scope="col">COUNCIL DISCOUNT</th>
                                </td>
                                <!-- <td style="border-top:1px solid #606060; border-right:1px solid #606060; border-bottom:1px solid #606060; font-weight: bold;text-align: left;color:#303030;">DISCOUNT</td> -->
                            </tr>
                            <tr>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left;" scope="col">Swimming Pool</th>
                                </td>
                                <td style="border:1px solid #606060; color: #484848;"> {!! strtoupper(optional(optional($assessment)->swimming)->label ? optional(optional($assessment)->swimming)->label : 'NO' ) !!} {!! strtoupper(optional(optional($assessment)->swimming)->value ? optional(optional($assessment)->swimming)->value: 0 ) !!} </td>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left;" scope="col">Drainage</th>
                                </td>
                                <td style="border:1px solid #606060;color: #484848;">{{ $assessment->drainage_percentage.'%' }}</td>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left;" scope="col">Disability (10%)</th>
                                </td>
                                <td style="border:1px solid #606060; color: #484848;">{!! strtoupper($assessment->disability_discount ? 'Yes' : 'No') !!}</td>
                            </tr>
                            <tr>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left;" scope="col">Zone</th>
                                </td>
                                <td style="border:1px solid #606060; color: #484848;">{{ strtoupper(optional(App\Models\PropertyZones::find($assessment->zone))->label) }} {{ strtoupper(optional(App\Models\PropertyZones::find($assessment->zone))->value) }}</td>
                                <td>
                                    <th style="border:0px solid #606060;text-align: left;" scope="col"></th>
                                </td>
                                <td style="border:0px solid #606060;"></td>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left;" scope="col">Pensioners (10%)</th>
                                </td>
                                <td style="border:1px solid #606060; color: #484848;">{!! strtoupper($assessment->pensioner_discount ? 'Yes' : 'No') !!}</td>
                            </tr>
                            <tr>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left;" scope="col">Property Use</th>
                                </td>
                                <td style="border:1px solid #606060; color: #484848;">{{ strtoupper(optional(App\Models\PropertyUse::find($assessment->property_use))->label) }} {{ strtoupper(optional(App\Models\PropertyUse::find($assessment->property_use))->value) }}</td>
                                <td>
                                    <th style="border:0px solid #606060;text-align: left;" scope="col"></th>
                                </td>
                                <td style="border:0px solid #606060; color: #484848;"></td>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left;" scope="col">80 Years + (10%)</th>
                                </td>
                                <td style="border:1px solid #606060; color: #484848;">NO</td>
                            </tr>
                            <tr>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left;" scope="col">Gated Community</th>
                                </td>
                                <td style="border:1px solid #606060; color: #484848;">{{ strtoupper(optional($assessment)->gated_community ? 'Yes' : 'No') }}</td>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left; color:#303030;" scope="col">TOTAL DEDUCTABLE</th>
                                </td>
                                <td style="border:1px solid #606060; color:#303030;">LE {!! number_format($assessment->getCouncilAdjustments()) !!}</td>
                                <td>
                                    <th style="border:0px solid #606060;text-align: left;" scope="col"></th>
                                </td>
                                <td style="border:0px solid #606060;">-</td>
                            </tr>
                            <tr>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left;" scope="col">Dilapidated</th>
                                </td>
                                <!-- <td style="border:1px solid #606060; color: #484848;">{{ strtoupper(optional(App\Models\PropertyUse::find($assessment->property_use))->label) }}</td> -->
                                <td style="border:1px solid #606060; color: #484848;">{{ str_contains(strtoupper($assessment->categories->pluck('label')->implode(', ')), 'Dilapidated House') ? 'YES' : 'NO' }}</td>
                                <td>
                                    <th style="border:0px solid #606060;text-align: left;" scope="col"></th>
                                </td>
                                <td style="border:0px solid #606060;"></td>
                                <td>
                                    <th style="border:0px solid #606060;text-align: left;" scope="col"></th>
                                </td>
                                <td style="border:0px solid #606060;"></td>
                            </tr>
                            <tr>
                            <td>
                                <th style="border:1px solid #606060;text-align: left;" scope="col">Sanitation</th>
                                </td>
                                <td style="border:1px solid #606060; color: #484848;">{{ strtoupper(    optional(App\Models\PropertySanitationType::find($assessment->sanitation))->label == 'Not Applicable' ? 'NA' : optional(App\Models\PropertySanitationType::find($assessment->sanitation))->label  ) }} {{ strtoupper(optional(App\Models\PropertySanitationType::find($assessment->sanitation))->value) }}</td>
                                <td>
                                    <th style="border:0px solid #606060;text-align: left;" scope="col"></th>
                                </td>
                                <td style="border:0px solid #606060;"></td>
                                <td>
                                    <th style="border:0px solid #606060;text-align: left;" scope="col"></th>
                                </td>
                                <td style="border:0px solid #606060;">-</td>
                            </tr>
                            <tr>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left; color:#303030;" scope="col">ASSESSED VALUE</th>
                                </td>
                                <td style="border:1px solid #606060; color:#303030;">LE {!! number_format($assessment->getCurrentYearAssessmentAmount()) !!}</td>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left; color:#303030;" scope="col">NET ASSESSED VALUE</th>
                                </td>
                                <td style="border:1px solid #606060; color:#303030;">LE {!! number_format($assessment->getNetPropertyAssessedValue()) !!}</td>
                                <td>
                                    <th style="border:1px solid #606060;text-align: left; color:#303030;" scope="col">DISCOUNT ADJUSTED RATE PAYABLE 2021</th>
                                </td>
                                <td style="border:1px solid #606060; color:#303030;">LE {!! number_format($assessment->getPensionerDisabilityDiscountActual())!!}</td>
                            </tr>
                            
                        </tbody>
            </table>


            <table class="table table-bordered" style="width:100%; margin-top:20px;" cellspacing="0" cellpadding="0">
                <tbody>
                    <tr>
                                    
                        <th style="border:1px solid #ccc; text-align: center;background-color:#ccc; color: #000;font-size: 12px;" scope="col">RATE PAYABLE = (TAXABLE PROPERTY VALUE * MILL RATE)/1000</th>
                                    
                    </tr>
                                
                </tbody>
            </table>
    

    
            <hr>
                <div style="width: 100%;justify-content: center;">
                <div style="width: 100%;">
            <p>
                <strong style="color: #888888; font-size: 10px;">PRIVACY POLICY - PERSONAL INFORMATION:  </strong>
            </p>
            
            <p align="justify" style="color: #888888; font-size: 8px; align: justify;">
            <strong>1. </strong>
                Types of information collected: We may collect and hold personal information 
                about you including information that can identify you, and is relevant to obligatory
                service(s) provided by the council e.g. Municipal Property Rate Collection. 
                This personal information may include details such as your name, gender, address, 
                contact information, etc.
            </p>
            <p align="justify" style="color: #888888; font-size: 8px; align: justify;">
                <strong>2. </strong>
                Personal information collected directly from you or tenants through our enumerators. We may also collect personal information about you from third parties either residing or whom we met at your property/premises at the time of assessment or delivery.
            </p>
            <p align="justify" style="color: #888888; font-size: 8px; align: justify;">
                <strong>3. </strong>
                Purpose of collection: We collect, use and hold your personal information for the purposes of * Administration for the Property Rate Assessment, Calculation, Bill generation, and Demand Note Distribution. * Providing you with information and other communications. * Our internal business operations, including the fulfilment of any legal requirements; and analyzing our services and customer needs with a view to improving those services
            </p>
            <p align="justify" style="color: #888888; font-size: 8px; align: justify;">
                <strong>4. </strong>
                Failure to provide accurate and complete information: If the personal information you provide to us is incomplete or inaccurate, we will record the provided details as given on any formal document or communication relating to you. 
            </p>
            <p align="justify" style="color: #888888; font-size: 10px; align: justify;">
                <strong>5. </strong>
                Correction of personal information: If upon receiving your Demand Note and you believe the ownership and or property information we hold for that property is incorrect, incomplete or out of date, please advise us. We will take reasonable steps to correct the information so that it is accurate, complete and up to date.
            </p>
            <p align="justify" style="color: #888888; font-size: 8px; align: justify;">
                <strong>6. </strong>
                How do we use and disclose Personal Information: Generally, we only use or disclose personal information about you as set out above. In some circumstances, the law may permit or require us to use or disclose personal information for other purposes (for instance, where you would reasonably expect us to). 
            </p>
            <p align="justify" style="color: #888888; font-size: 8px; align: justify;">
                <strong>7. </strong>
                Security of your information: We store your personal information in electronic format. We take reasonable steps to ensure the security of all information we collect from risks, such as loss or unauthorized access, destruction, use, modification or disclosure of data. Authorized personnel only maintain your personal information in an accessible and secure environment. 
            </p>
            <p align="justify" style="color: #888888; font-size: 8px; align: justify;">
                <strong>8. </strong>
                Changes to this Privacy Policy This privacy policy may change from time to time particularly as new rules, regulations and industry requirements are introduced. 
            </p>
            <p align="justify" style="color: #888888; font-size: 8px; align: justify;">
                <strong>9. </strong>
                Complaints and feedback if you wish to make a complaint about a breach of your privacy that applies to us, please contact us as set out below and we will take reasonable steps to investigate the complaint and respond to you.
            </p>
            <p>
            If you have any queries or concerns about this privacy policy or the way we handle your personal information, please contact us at <a style="background-color: #FFF;">{{  $district->enquiries_email }}</a> 
            </p>
            <ul style="list-style-type: square; margin-left: 1%; font-size: 10px; align: justify;">
                <li><strong>DISCLAIMER:</strong> This methodology and estimate used by council is for Municipal Property Rate calculation only, and might not be representative of the value as would be calculated for other purposes by a professional individual or entity with quantity surveyor and or property valuation expertise.</li>
                <li>Taxable Property Value is an algorithm-generated value estimated as a percentage of Actual Property Value (Minimum value inclusive of land acquisition value).</li>
            </ul>
</div>
</div>
           
        </div>
    </div>
</div>
