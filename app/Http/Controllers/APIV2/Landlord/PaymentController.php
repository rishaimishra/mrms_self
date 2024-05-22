<?php

namespace App\Http\Controllers\APIV2\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyGeoRegistry;
use App\Models\PropertyPayment;
use App\Models\LandlordDetail;
use App\Models\District;
use App\Models\UserTitleTypes;
use App\Notifications\PaymentSMSNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use PayPal\Rest\ApiContext;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use Folklore\Image\Facades\Image;

/** All Paypal Details class **/

use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{


    public function show(Request $request)
    {
        $property = [];
        $last_payment = null;
        $paymentInQuarter = [];
        $history = [];
        $assessmentValues = [];

        $landlord = $request->user('landlord-api');


        $property = Property::with([
            'landlord',
            'landlord.titles',
            'occupancy',
            'occupancy.titles',
            'occupancies',
            'assessment.categories',
            'assessment.types',
            'assessment.wallMaterial',
            'assessment.roofMaterial',
            'assessment.valuesAdded',
            'assessment.dimension',
            'assessment.propertyUse',
            'assessment.zone',
            'assessment.swimming',
            'assessment.windowType',
            'assessment.sanitationType',
            'geoRegistry',
            'registryMeters',
            'payments.admin',
            'assessmentHistory'
        ])->whereHas('landlord', function ($query) use ($landlord) {
            return $query->where('mobile_1', 'like', '%' . $landlord->mobile . '%')->orWhere('mobile_2', 'like', '%' . $landlord->mobile . '%');
        })->get();
        // dd($property[0]->assessmentHistory);
        $properties_discount_pensioner_images = [];
        foreach( $property as $pr)
        {
                $propertyId = $pr->id; 
                $discounted_value = 0;
                $pensioner_discount = 0;
                $disability_discount = 0;
                $property_tax_payable = (float)$pr->assessment->getPropertyTaxPayable();
                if($pr->assessment->pensioner_discount && $pr->assessment->disability_discount)
                {
                    $discounted_value = $property_tax_payable * ((20)/100);
                    $pensioner_discount = $property_tax_payable * (10/100);
                    $disability_discount = $property_tax_payable * (10/100);
                }else if( $pr->assessment->pensioner_discount && $pr->assessment->disability_discount != 1)
                {
                    $discounted_value = $property_tax_payable * ((10)/100);
                    $pensioner_discount = $property_tax_payable * (10/100);

                }else if ($pr->assessment->pensioner_discount != 1 && $pr->assessment->disability_discount)
                {
                    $discounted_value = $property_tax_payable * ((10)/100);   
                    $disability_discount = $property_tax_payable * (10/100);
                }else
                {
                    $discounted_value = 0; 
                }
                // dd($pr->assessment->discounted_value);
                // $pr->assessment->property_rate_without_gst = number_format((float)$pr->assessment->getPropertyTaxPayable(), 2, '.', '');
                $pr->assessment->property_rate_without_gst = number_format((float)$pr->assessment->property_rate_without_gst, 2, '.', '');
                $pr->assessment->{"discounted_value"} = number_format($discounted_value,2,'.','');
                $pr->assessment->{"pensioner_discount"} = number_format($pensioner_discount,2,'.','');
                $pr->assessment->{"disability_discount"} = number_format($disability_discount,2,'.','');
                $pr->assessment->{"rate_payable"} = number_format((float)$pr->assessment->getPropertyTaxPayable(),2,'.','');
                $pr->assessment->{"property_net_assessed_vaue"} = number_format($pr->assessment->getNetPropertyAssessedValue(),0,'',',');
                $council_adjusment_labels = array();
        
                // dd($pr->assessment->water_percentage);
                if($pr->assessment->water_percentage != 0 )
                {
                    array_push($council_adjusment_labels,'Water Supply');
                    
                }
                if($pr->assessment->electricity_percentage != 0 )
                {
                    array_push($council_adjusment_labels,'Electricity');
                    
                }
                if($pr->assessment->waste_management_percentage != 0 )
                {
                   
                    array_push($council_adjusment_labels,'Waste Management Services/Points/Locations');
                    
                }
                if($pr->assessment->market_percentage != 0 )
                {
                  
                    array_push($council_adjusment_labels,'Market');
                   
                }
                if($pr->assessment->hazardous_precentage != 0 )
                {
                
                    array_push($council_adjusment_labels,'Hazardous Location/Environment');
                    
                }
                if($pr->assessment->informal_settlement_percentage != 0 )
                {
                    
                    array_push($council_adjusment_labels,'Informal settlement');
                   
                }
                if($pr->assessment->easy_street_access_percentage != 0 )
                {
                    
                    array_push($council_adjusment_labels,'Easy Street Access');
                    
                }
                if($pr->assessment->paved_tarred_street_percentage != 0 )
                {
                   
                    array_push($council_adjusment_labels,'Paved/Tarred Road/Street');
                    
                }
                if($pr->assessment->drainage_percentage != 0 )
                {
                   
                    array_push($council_adjusment_labels,'Drainage');
                   
                }
                
                // $pr->assessment->{"council_adjustments_parameters"} = implode(', ',$council_adjusment_labels);
                $pr->assessment->{"council_adjustments_parameters"} = $pr->assessment->water_percentage + 
                                                                        $pr->assessment->electricity_percentage +
                                                                        $pr->assessment->waste_management_percentage+
                                                                        $pr->assessment->market_percentage+
                                                                        $pr->assessment->hazardous_precentage+
                                                                        $pr->assessment->informal_settlement_percentage +
                                                                        $pr->assessment->easy_street_access_percentage+
                                                                        $pr->assessment->paved_tarred_street_percentage+
                                                                        $pr->assessment->drainage_percentage;
                
                $pr->assessment->{"council_adjustments_parameters"} = ($pr->assessment->property_rate_without_gst * $pr->assessment->{"council_adjustments_parameters"})/100;
                $pr->assessment->{"council_adjustments_parameters"} = number_format($pr->assessment->{"council_adjustments_parameters"},0,'',',');
                $pensioner_image_path = PropertyPayment::where('property_id','=',$propertyId)->whereNotNull('pensioner_discount_image')->orderBy('created_at','desc')->first();
                $disability_image_path = PropertyPayment::where('property_id','=',$propertyId)->whereNotNull('disability_discount_image')->orderBy('created_at','desc')->first();
                $data = [
                    'property_id' => $pr->id,
                    'property_rate_without_gst' => number_format($pr->assessment->getPropertyTaxPayable(),0,'',','),
                    'pensioner_image_path' => $pensioner_image_path,
                    'disability_image_path' => $disability_image_path,
                    'property_taxable_value' => number_format($pr->assessment->geTaxablePropertyValue(),0,'',',')
                ];

            array_push($properties_discount_pensioner_images, $data);

        }
        
        // return response()->json(compact('property'));
        return response()->json(compact('property', 'paymentInQuarter', 'history','landlord','properties_discount_pensioner_images'));
    }

    public function storeLandLord($id, Request $request)
    {
        $property = Property::with('landlord')->findOrFail($id);
        $landlord_data = $property->landlord()->firstOrNew([]);

        $verification_document = null;
        $address_document = null;
        $conveyance_document = null;

        if ($request->hasFile('verification_document')) {
            $verification_document = $request->verification_document->store(LandlordDetail::DOCUMENT_IMAGE);
        }


        if ($request->hasFile('address_document')) {
            $address_document = $request->address_document->store(LandlordDetail::DOCUMENT_IMAGE);
        }

        if ($request->hasFile('conveyance_proof')) {
            $conveyance_document = $request->conveyance_proof->store(LandlordDetail::DOCUMENT_IMAGE);
        }


        $landlord_data->fill([
            'temp_first_name' => $request->landlord_first_name,
            'temp_middle_name' => $request->landlord_middle_name,
            'temp_surname' => $request->landlord_surname,
            'temp_street_number' => $request->old_street_number,
            'temp_street_numbernew' => $request->landlord_street_number,
            'temp_street_name' => $request->landlord_street_name,
            'temp_email' => $request->landlord_email,
            'temp_mobile_1' => $request->landlord_mobile_1,
            'document_image' => $verification_document,
            'address_image' => $address_document,
            'conveyance_image' => $conveyance_document,
            'verified' => 0,
            'requested_by' => $request->requested_by
        ]);

        $landlord_data->save();
        return response()->json(['status' => 'success','image'=>$verification_document, 'address'=>$address_document],201);
    }

    public function storeProperty($id, Request $request)
    {
        $property = Property::where('id',$id)->first();
        
        $address_document = null;
        $conveyance_document = null;

        if ($request->hasFile('address_document')) {
            $address_document = $request->address_document->store(Property::DOCUMENT_IMAGE);
        }

        if ($request->hasFile('conveyance_proof')) {
            $conveyance_document = $request->conveyance_proof->store(Property::DOCUMENT_IMAGE);
        }


        
        $property->temp_street_number = $request->old_street_number;
        $property->temp_street_numbernew = $request->landlord_street_number;
        $property->temp_street_name = $request->landlord_street_name;
        $property->address_image = $address_document;
        $property->conveyance_image = $conveyance_document;
        $property->verified = 0;
        $property->requested_by = $request->requested_by;

        $property->save();
        
        return response()->json(['status' => 'success','image'=>$conveyance_document, 'address'=>$address_document],201);



    }

    public function getReceipt($id, $year = null)
    {
        $year = !$year ? date('Y') : $year;

        $property = Property::with('assessment', 'occupancy', 'types', 'geoRegistry', 'user')->findOrFail($id);
        // dd($property);
        $assessment = $property->assessments()->whereYear('created_at', $year)->firstOrFail();

        // $assessment->setPrinted();

        //        $pdf = \PDF::loadView('admin.payments.receipt');
        //        return $pdf->download('invoice.pdf');

        @$paymentInQuarter = $property->getPaymentsInQuarter($year);
        $district = District::where('name', $property->district)->first();
        
        
        $pdf = \PDF::loadView('admin.payments.receipt', compact('property', 'paymentInQuarter', 'assessment', 'district'));
        // return view('admin.payments.receipt', compact('property', 'paymentInQuarter', 'assessment', 'district'));

        // Save PDF to temporary location
        $pdfile = (Carbon::now()->format('Y-m-d-H-i-s') . '.pdf');
        $pdfPath = public_path($pdfile);
        $pdf->save($pdfPath);
        
        //  $pdfPath = Image::url($pdfPath);
        $imageUrl =  url($pdfile);
        //  $imageUrl = url($pdfPath);
        // Return path to the PDF file in JSON response



        return response()->json(['pdf_path' => $imageUrl]);
        


    }
    
    
    public function getPayReceipt()
    {
        $id = '41839';
        $year = '2024';
        $year = !$year ? date('Y') : $year;

        
        $property = Property::with('assessment', 'occupancy', 'types', 'geoRegistry', 'user')->findOrFail($id);
        // dd($property);
        $assessment = $property->assessments()->whereYear('created_at', $year)->firstOrFail();

        // $assessment->setPrinted();

        //        $pdf = \PDF::loadView('admin.payments.receipt');
        //        return $pdf->download('invoice.pdf');

        @$paymentInQuarter = $property->getPaymentsInQuarter($year);
        $district = District::where('name', $property->district)->first();
        
  
        $pdf = \PDF::loadView('admin.payments.receipt', compact('property', 'paymentInQuarter', 'assessment', 'district'));
        // return view('admin.payments.receipt', compact('property', 'paymentInQuarter', 'assessment', 'district'));

        // Save PDF to temporary location
        $pdfPath = storage_path('app/public/current-receipt.pdf');
        $pdf->save($pdfPath);

        //  $pdfPath = Image::url($pdfPath);
         $imageUrl = url($pdfPath);
        // Return path to the PDF file in JSON response
        return response()->json(['pdf_path' => $imageUrl]);
        


    }

}
