<?php

namespace App\Models;

use Folklore\Image\Facades\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;

class PropertyAssessmentDetail extends Model
{
    use LogsActivity;
    protected $table = 'property_assessment_details';

    protected $currentYearTotalDue;
    protected $currentYearTotalPayment;
    protected $totalPaid;
    protected $pastPayableDue;

    protected static $logAttributes = ['*', 'valuesAdded'];
    protected static $logOnlyDirty = true;
    protected static $logName = 'property-assessment';

    protected $fillable = [
        'property_types',
        'property_wall_materials',
        'roofs_materials',
        'property_window_type',
        'property_dimension',
        'length',
        'breadth',
        'square_meter',
        'value_added',
        'property_rate_without_gst',
        'property_gst',
        'property_rate_with_gst',
        'property_image',
        'property_use',
        'zone',
        'no_of_mast',
        'no_of_shop',
        'no_of_compound_house',
        'compound_name',
        'assessment_images_2',
        'assessment_images_1',
        'gated_community',
        'demand_note_delivered_at',
        'demand_note_recipient_name',
        'demand_note_recipient_mobile',
        'demand_note_recipient_photo',
        'total_adjustment_percent',
        'group_name',
        'mill_rate',
        'wall_material_percentage',
        'wall_material_type',
        'roof_material_percentage',
        'roof_material_type',
        'value_added_percentage',
        'value_added_type',
        'window_type_percentage',
        'window_type_type',
        'is_map_set',
        'water_percentage',
        'electricity_percentage',
        'waste_management_percentage',
        'market_percentage',
        'hazardous_precentage',
        'informal_settlement_percentage',
        'easy_street_access_percentage',
        'paved_tarred_street_percentage',
        'drainage_percentage',
        'pensioner_discount',
        'disability_discount',
        'council_group_name',
        'sanitation',
        'is_rejected_pensioner',
        'is_rejected_disability',
        'net_property_assessed_value',
        'taxable_property_value',
        'property_assessed_value',
        'property_tax_payable_2024',
        'discounted_rate_payable',
        'council_adjustments'
    ];

    protected $appends = [
        'original_one',
        'small_preview_one',
        'large_preview_one',
        'original_two',
        'small_preview_two',
        'large_preview_two',
        'swimming_pool',
        'is_demand_note_delivered',
        'demand_note_recipient_photo_url',
        'current_year_assessment_amount',
        'current_installment_due_amount',
        'arrear_due',
        'penalty',
        'amount_paid',
        'balance',
        'assessment_year',
        'assessment_length',
        'assessment_breadth',
        'assessment_square_meter',
        'wall_material',
        'roof_material',
        'window_material',
    ];

    protected $dates = [
        'last_printed_at',
        'demand_note_delivered_at'
    ];
    public function getCostofOneTownAttribute(){
        return 250000;
    }
    public function getOneTownLotAttribute(){
        return 3750;
    }
    public function getFloorAreaPlottedOnMapAttribute(){
        return 1722;
    }
    public function getValuePerSquareFeetAttribute(){
        return $this->getCostofOneTownAttribute() / $this->getOneTownLotAttribute();
    }
    public function getfloorAreaValueAttribute(){
        return $this->getValuePerSquareFeetAttribute() * $this->getFloorAreaPlottedOnMapAttribute();
    }
    public function getWallMaterialAttribute()
    {
            // Extract the three columns and create an object
            return (object) [
                'property_wall_materials' => $this->property_wall_materials,
                'wall_material_percentage' => $this->wall_material_percentage,
                'wall_material_type' => $this->wall_material_type,
            ];
         // Return null if no related data is found
    }
    public function getRoofMaterialAttribute()
    {
            // Extract the three columns and create an object
            return (object) [
                'roofs_materials' => $this->roofs_materials,
                'roof_material_percentage' => $this->roof_material_percentage,
                'roof_material_type' => $this->roof_material_type,
            ];
         // Return null if no related data is found
    }
    public function getWindowMaterialAttribute()
    {
            // Extract the three columns and create an object
            return (object) [
                'property_window_type' => $this->property_window_type,
                'window_type_percentage' => $this->window_type_percentage,
                'window_type_type' => $this->window_type_type,
            ];
         // Return null if no related data is found
    }
    public function getIsDemandNoteDeliveredAttribute()
    {
        return !is_null($this->demand_note_delivered_at);
    }
    public function getAssessmentLengthAttribute()
    {
        return ($this->length);
    }
    public function getAssessmentBreadthAttribute()
    {
        return ($this->breadth);
    }
    public function getAssessmentSquareMeterAttribute()
    {
        return ($this->square_meter);
    }

    public function getDemandNoteRecipientPhotoUrlAttribute()
    {
        return $this->attributes['demand_note_recipient_photo'] ? url(Image::url($this->attributes['demand_note_recipient_photo'], 500, 500)) : null;
    }


    public function categories()
    {
        return $this->belongsToMany(
            PropertyCategory::class,
            'property_property_category',
            'assessment_id',
            'property_category_id'
        )->withPivot(['property_id']);
    }

    public function types()
    {
        return $this->belongsToMany(
            PropertyType::class,
            'property_property_type',
            'assessment_id',
            'property_type_id'
        )->withPivot(['property_id']);
    }

    public function typesTotal()
    {
        return $this->belongsToMany(
            PropertyType::class,
            'property_property_types_total',
            'assessment_id',
            'property_type_id'
        )->withPivot(['property_id']);
    }

    public function valuesAdded()
    {
        return $this->belongsToMany(
            PropertyValueAdded::class,
            'property_property_value_added',
            'assessment_id',
            'property_value_added_id'
        )->withPivot(['property_id']);
    }

    public function getSwimmingPoolAttribute()
    {
        return $this->swimming_id;
    }

    public function property()
    {
        return $this->belongsTo('App\Models\Property');
    }

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class, 'property_types_id');
    }

    public function propertyCategory()
    {
        return $this->belongsTo(PropertyCategory::class, 'property_categories_id');
    }

    public function dimension()
    {
        return $this->belongsTo(PropertyDimension::class, 'property_dimension');
    }

    public function wallMaterial()
    {
        return $this->belongsTo(PropertyWallMaterials::class, 'property_wall_materials');
    }

    public function roofMaterial()
    {
        return $this->belongsTo(PropertyRoofsMaterials::class, 'roofs_materials');
    }

    public function windowType()
    {
        return $this->belongsTo(PropertyWindowType::class, 'property_window_type');
    }

    public function zone()
    {
        return $this->belongsTo(PropertyZones::class, 'zone');
    }

    public function propertyUse()
    {
        return $this->belongsTo(PropertyUse::class, 'property_use');
    }

    public function swimming()
    {
        return $this->belongsTo(Swimming::class, 'swimming_id');
    }

    public function sanitationType()
    {
        return $this->belongsTo(PropertySanitationType::class, 'sanitation');
    }

    // for assessment_image_1

    public function getOriginalOneAttribute()
    {
        return $this->hasImageOne() ? url(Image::url($this->assessment_images_1)) : null;
    }

    public function getSmallPreviewOneAttribute()
    {
        return $this->getImageOneUrl(100, 100);
    }

    public function getLargePreviewOneAttribute()
    {
        return $this->getImageOneUrl(800, 800);
    }

    public function hasImageOne()
    {
        return $this->assessment_images_1 && file_exists($this->getImageOne());
    }

    public function getImageOne()
    {
        return storage_path('app/' . $this->assessment_images_1);
    }

    public function getImageOneUrl($width = 100, $height = 100)
    {
        return $this->hasImageOne() ? url(Image::url($this->assessment_images_1, $width, $height, ['crop'])) : '';
    }

    public function getAdminImageOneUrl($width = 100, $height = 100)
    {
        return $this->hasImageOne() ? url(Image::url($this->assessment_images_1, $width, $height, ['crop'])) : asset('/images/No_Image_Available.jpg');
    }

    //assessment_image_2

    public function getOriginalTwoAttribute()
    {
        return $this->hasImageTwo() ? url(Image::url($this->assessment_images_2)) : null;
    }

    public function getSmallPreviewTwoAttribute()
    {
        return $this->getImageTwoUrl(100, 100);
    }

    public function getLargePreviewTwoAttribute()
    {
        return $this->getImageTwoUrl(800, 800);
    }

    public function hasImageTwo()
    {
        return $this->assessment_images_2 && file_exists($this->getImageTwo());
    }

    public function getImageTwo()
    {
        return storage_path('app/' . $this->assessment_images_2);
    }

    public function getRecipientPhoto($width = 100, $height = 100, $options = ['crop'])
    {
        return Storage::has($this->demand_note_recipient_photo) ? url(Image::url($this->demand_note_recipient_photo, $width, $height, $options)) : asset('images/person-placer.png');
    }

    public function getImageTwoUrl($width = 100, $height = 100)
    {
        return $this->hasImageTwo() ? url(Image::url($this->assessment_images_2, $width, $height, ['crop'])) : '';
    }

    public function getImageAnyUrl($width = 100, $height = 100, $resize = false)
    {
        if($this->hasImageOne()){
            return url(Image::url($this->assessment_images_1, $width, $height, $resize ? [] : ['crop']));
        }elseif($this->hasImageTwo()){
            return url(Image::url($this->assessment_images_2, $width, $height, $resize ? [] : ['crop']));
        }else{
                return url(Image::url("District/council_logo.jpg", $width, $height, ['crop']));
        }
    }

    public function getAdminImageTwoUrl($width = 100, $height = 100)
    {
        return $this->hasImageTwo() ? url(Image::url($this->assessment_images_2, $width, $height, ['crop'])) : asset('/images/No_Image_Available.jpg');
    }

    public function getCurrentYearAssessmentAmount()
    {
        return $this->property_rate_without_gst;
    }    

    public function getCouncilAdjustments()
    {
        return $this->getCurrentYearAssessmentAmount() * ($this->total_adjustment_percent/100);
    }

    public function getNetPropertyAssessedValue()
    {
        return $this->getCurrentYearAssessmentAmount() - $this->getCouncilAdjustments();
        //return $this->getCurrentYearAssessmentAmount() * ((100-$this->total_adjustment_percent)/100);
    }

    public function geTaxablePropertyValue()
    {
        //return $this->getNetPropertyAssessedValue() * 12 * $this->mill_rate * 1.425;
        return $this->getNetPropertyAssessedValue() * 12 * 27 * 1.18;
    }     

    public function getPropertyTaxPayable()
    {
        return ($this->mill_rate * $this->getNetPropertyAssessedValue()) / 1000;
    }

    public function getPensionerDiscount()
    {
        return $this->getPropertyTaxPayable() * ((10)/100);
    }    

    public function getDisabilityDiscount()
    {
        return $this->getPropertyTaxPayable() * ((10)/100);
    }  

    public function getPensionerDiscountActual()
    {
        return $this->getPropertyTaxPayable() * ((10)/100);
    }    

    public function getDisabilityDiscountActual()
    {
        return $this->getPropertyTaxPayable() * ((10)/100);
    }

    public function getPensionerNDisabilityDiscount()
    {
        return $this->getPropertyTaxPayable() * ((100-20)/100);
    }  
    
    
    public function getPensionerDisabilityDiscountActual()
    {
        if($this->pensioner_discount && $this->disability_discount)
        {
            return $this->getPropertyTaxPayable() * ((100-20)/100);
        }else if( $this->pensioner_discount && $this->disability_discount != 1)
        {
            return $this->getPropertyTaxPayable() * ((100-10)/100);
        }else if ($this->pensioner_discount != 1 && $this->disability_discount)
        {
            return $this->getPropertyTaxPayable() * ((100-10)/100);   
        }else
        {
            return $this->getPropertyTaxPayable(); 
        }
    }

    public function getPensionerDisabilityDiscountValue()
    {
        if($this->pensioner_discount && $this->disability_discount)
        {
            return $this->getPropertyTaxPayable() * ((100-20)/100);
        }else if( $this->pensioner_discount && $this->disability_discount != 1)
        {
            return $this->getPropertyTaxPayable() * ((100-10)/100);
        }else if ($this->pensioner_discount != 1 && $this->disability_discount)
        {
            return $this->getPropertyTaxPayable() * ((100-10)/100);   
        }else
        {
            return $this->getPropertyTaxPayable(); 
        }
    }


    public function getPastPenaltyFunc()
    {

        if($this->created_at->format('Y') == 2021)
        {
            $pastTotalDue = PropertyAssessmentDetail::where('property_id', $this->property_id)
            ->whereYear('created_at', 2020)
            ->sum('property_rate_without_gst');
        }else{
            $pastTotalDue = PropertyAssessmentDetail::where('property_id', $this->property_id)
            ->where('created_at', '<', $this->created_at->startOfYear())
            ->sum('property_rate_without_gst');
        
            $pastTotalDue = $pastTotalDue - $this->getCurrentYearAssessmentAmount();
        }

        
        if($pastTotalDue == 0)
        {
            return 0;
        }else{
            return $pastTotalDue*0.25;
        }
    }
    public function getPastPayableDue()
    {
        
        $pastTotalPayments = PropertyPayment::where('property_id', $this->property_id)
                ->where('created_at', '<', $this->created_at->startOfYear())
                ->sum('amount');

        

        $pastTotalDue = PropertyAssessmentDetail::where('property_id', $this->property_id)
            ->where('created_at', '<', $this->created_at->startOfYear())
            ->sum('property_rate_without_gst');

        
        $this->pastPayableDue = $pastTotalDue - $pastTotalPayments;

        if($this->pastPayableDue < 0 && $this->created_at->format('Y') == 2021)
        {
            $temppastTotalPayments = PropertyPayment::where('property_id', $this->property_id)
                ->where('created_at', '<', $this->created_at->startOfYear()->addYear(-1))
                ->sum('amount');

            $temppastTotalPaymentsOne = PropertyPayment::where('property_id', $this->property_id)
                ->where('created_at', '>', $this->created_at->startOfYear()->addYear(-1))
                ->sum('amount');
            
            $temppastTotalPaymentsTwo = PropertyPayment::where('property_id', $this->property_id)
                ->where('created_at', '>', $this->created_at->startOfYear())
                ->sum('amount');
            
            $temppastTotalDue = PropertyAssessmentDetail::where('property_id', $this->property_id)
                ->where('created_at', '<', $this->created_at->startOfYear()->addYear(-1))
                ->sum('property_rate_without_gst');
            
            $lastYearAssessmentValue = PropertyAssessmentDetail::where('property_id', $this->property_id)
                ->where('created_at', '<', $this->created_at->startOfYear()->addYear(-1))
                ->value('property_rate_without_gst');

            $val = $lastYearAssessmentValue+$temppastTotalDue-$temppastTotalPayments-$temppastTotalPaymentsOne+max(($temppastTotalDue-$temppastTotalPayments)*.25,0)+$temppastTotalPaymentsTwo;
            return  $val ;
        }
       
        if($this->created_at->format('Y') == 2021 && $this->pastPayableDue == null)
        {
            $this->pastPayableDue = 0;
            return $this->pastPayableDue;
        }
        
        if($this->created_at->format('Y') == 2021 && $pastTotalPayments != 0)
        {
            //$this->pastPayableDue = $pastTotalDue - $pastTotalPayments + $this->getPastPenaltyFunc();
            return $this->pastPayableDue;
        }

        if($this->created_at->format('Y') == 2021)
        {
            $this->pastPayableDue = $pastTotalDue - $pastTotalPayments + $this->getPastPenaltyFunc();
            return $this->pastPayableDue;
        }

        

        return $this->pastPayableDue;
    }

    public function getCurrentQuarter()
    {
        return getQuarter($this->created_at);
    }

    public function payments()
    {
        return $this->hasMany(PropertyPayment::class, 'property_id', 'property_id');
    }

    public function getPenalty()
    {
        return max($this->getPastPayableDue() * .25, 0);
    }

    public function getCurrentYearTotalPayment()
    {
        if ($this->currentYearTotalPayment !== null) {
            return $this->currentYearTotalPayment;
        }

        return $this->currentYearTotalPayment = $this->payments()->whereYear('created_at', $this->created_at->year)->sum('amount');
    }

    public function getCurrentYearTotalDue()
    {
        if($this->created_at->format('Y') == 2021)
        {
            return $this->getPastPayableDue() + $this->getPenalty() - $this->getCurrentYearTotalPayment() + $this->getCurrentYearAssessmentAmount();
        }

        if($this->getPastPayableDue())
        {
            if($this->getTotalPayable() - $this->getCurrentYearTotalPayment() == 0)
            {
                return $this->getCurrentYearAssessmentAmount();
            }
            return $this->getTotalPayable() - $this->getCurrentYearTotalPayment() + $this->getCurrentYearAssessmentAmount();
        }else {
            
            if($this->pensioner_discount == 0 && $this->disability_discount == 0){
                $discounted_rate_payable = $this->getPropertyTaxPayable() -  $this->pensioner_discount - $this->disability_discount ;
            }else{
                $discounted_rate_payable = $this->getPensionerDisabilityDiscountActual() -  $this->pensioner_discount - $this->disability_discount ;
            }
            // amount due modification


            return $discounted_rate_payable - $this->getCurrentYearTotalPayment();
            
        }
        
    }

    public function getTotalPayable()
    {
        return $this->getPastPayableDue() + $this->getPropertyTaxPayable() + $this->getPenalty();
    }

    public function getTotalPaid()
    {
        if ($this->totalPaid !== null) {
            return $this->totalPaid;
        }

        $this->totalPaid = $this->payments()->sum('amount');
        return $this->totalPaid;
    }

    public function getEachInstallmentAmount()
    {
        return $this->getTotalPayable() / 4;
    }

    public function getQuarter($date = null)
    {
        return getQuarter($this->created_at, $date);
    }

    public function getCurrentInstallmentDueAmount($date = null)
    {
        $totalPaidThisYear = $this->getCurrentYearTotalPayment();
        $currentQuarter = $this->getQuarter($date);

        $paymentShouldPaid = $currentQuarter * $this->getEachInstallmentAmount();

        return max(0, $paymentShouldPaid - $totalPaidThisYear);
    }

    public function setPrinted()
    {
        $this->forceFill([
            'last_printed_at' => $this->freshTimestamp()
        ])->save();
    }

    public function isPrinted()
    {
        return !is_null($this->last_printed_at);
    }

    public function isDelivered()
    {
        return !is_null($this->demand_note_delivered_at);
    }

    public function installmentDates($key = null)
    {
        $year = $this->created_at->format('Y');

        $dates = [
            "0" => "31-03-{$year}",
            "1" => "30-06-{$year}",
            "2" => "30-09-{$year}",
            "3" => "31-12-{$year}",
        ];

        return (isset($dates[$key])) ? $dates[$key] : $dates;
    }

    public function paymentCompleted()
    {
        return $this->getTotalPayable() <= $this->getCurrentYearTotalPayment();
    }

    public function getCurrentYearAssessmentAmountAttribute()
    {
        return $this->getCurrentYearAssessmentAmount();
    }
    public function getArrearDueAttribute()
    {
        return $this->getPastPayableDue();
    }
    public function getPenaltyAttribute()
    {
        return $this->getPenalty();
    }
    public function getAmountPaidAttribute()
    {
        return $this->getCurrentYearTotalPayment();
    }
    public function getBalanceAttribute()
    {
        return $this->getCurrentYearTotalDue();
    }
    public function getAssessmentYearAttribute()
    {
        return $this->created_at->format('Y');
    }
    public function getCurrentInstallmentDueAmountAttribute()
    {
        return $this->getCurrentInstallmentDueAmount();
    }
}
