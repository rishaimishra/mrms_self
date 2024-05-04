<?php

use Illuminate\Database\Seeder;

class PropertyMigration extends Seeder
{
    protected $oldProperty;
    protected $property;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $properties = \App\Models\Dpm\Property::whereRaw('id BETWEEN ' . 10081 . ' AND ' . 10488 . '')->get()->toArray();

        foreach ($properties as $oldProperty)
        {

            DB::beginTransaction();
            $this->oldProperty = $oldProperty;

            $this->createProperty();
            $this->createPropertyInaccessable();
            $this->createLandloadDetails();
            $this->occupancyCreate();
            $this->occupancyDetails();
            $this->assessmentDetails();
            $this->geoRegistoryDetails();
            $this->createRegistryMeter();
            $this->getPaymentDetails();

            DB::commit();
        }

    }

    protected function createProperty()
    {
        $property = new \App\Models\Property();
        $property->fill($this->oldProperty);
        $property->created_from = $this->oldProperty['id'];
        unset($property->delivered_image_path);
        unset($property->id);
        $property->save();

        $this->property = $property;
    }

    protected function createPropertyInaccessable()
    {
        $inaccessables = DB::connection('mysql2')->table('property_property_inaccessibles')
        ->where('property_id', $this->oldProperty['id'])->pluck('property_inaccessible_id')->toArray();

        return $this->property->propertyInaccessible()->sync($inaccessables);
    }

    protected function createLandloadDetails()
    {
        $landload = DB::connection('mysql2')->table('landlord_details')
            ->where('property_id', $this->oldProperty['id'])->first();

        if($landload)
        {
           $landload = (array) $landload;
            unset($landload['id']);
            unset($landload['property_id']);
            $newLandload = $this->property->landlord()->firstOrNew([]);
            $newLandload->fill($landload);
            $newLandload->property()->associate($this->property->id);
            $newLandload->save();
        }
    }

    public function occupancyCreate()
    {
        $occupances = DB::connection('mysql2')->table('property_occupancies')
            ->where('property_id', $this->oldProperty['id'])->get()->toArray();

        if(count($occupances))
        {
            foreach ($occupances as $oldOccupancy)
            {
                $oldOccupancy = (array) $oldOccupancy;
                unset($oldOccupancy['id']);
                unset($oldOccupancy['property_id']);

                $newOccupancy = new \App\Models\PropertyOccupancy();
                $newOccupancy->fill($oldOccupancy);
                $newOccupancy->property()->associate($this->property->id);
                $newOccupancy->save();
            }
        }
    }

    public function occupancyDetails()
    {
        $oldOccupancy = DB::connection('mysql2')->table('occupancy_details')
            ->where('property_id', $this->oldProperty['id'])->first();

        if($oldOccupancy)
        {
            $oldOccupancy = (array) $oldOccupancy;
            unset($oldOccupancy['id']);
            unset($oldOccupancy['property_id']);

            $newOccupancy = new \App\Models\OccupancyDetail();
            $newOccupancy->fill($oldOccupancy);
            $newOccupancy->property()->associate($this->property->id);
            $newOccupancy->save();
        }
    }

    protected function assessmentDetails()
    {
        $assessmentss = DB::connection('mysql2')->table('property_assessment_details')
            ->where('property_id', $this->oldProperty['id'])->get()->toArray();

        if(count($assessmentss))
        {
            foreach ($assessmentss as $oldAssessment)
            {
                $oldAssessment = (array) $oldAssessment;
                unset($oldAssessment['id']);
                unset($oldAssessment['property_id']);

                $newAssessment = new \App\Models\PropertyAssessmentDetail();
                $newAssessment->fill($oldAssessment);
                $newAssessment->property()->associate($this->property->id);
                $newAssessment->save();
                $this->propertyAssessmentCategoriesCreate($newAssessment);
                $this->PropertyAssessmentTypesCreate($newAssessment);
                $this->propertyAssessmentTypeTotalCreate($newAssessment);
                $this->propertyAssessmentValueAdded($newAssessment);
            }
        }
    }

    protected function propertyAssessmentCategoriesCreate($assessment)
    {
        $oldCategories = DB::connection('mysql2')->table('property_property_category')
            ->where('property_id', $this->oldProperty['id'])->pluck('property_category_id')->toArray();
            $categories = getSyncArray($oldCategories, ['property_id' => $this->property->id]);
            $assessment->categories()->sync($categories);
    }

    protected function PropertyAssessmentTypesCreate($assessment)
    {
        $oldTypes = DB::connection('mysql2')->table('property_property_type')
            ->where('property_id', $this->oldProperty['id'])->pluck('property_type_id')->toArray();

        $types = getSyncArray($oldTypes, ['property_id' => $this->property->id]);
        $assessment->types()->sync($types);
    }

    protected function propertyAssessmentTypeTotalCreate($assessment)
    {
        $oldTypes = DB::connection('mysql2')->table('property_property_types_total')
            ->where('property_id', $this->oldProperty['id'])->pluck('property_type_id')->toArray();

        $types = getSyncArray($oldTypes, ['property_id' => $this->property->id]);
        $assessment->typesTotal()->sync($types);
    }

    protected function propertyAssessmentValueAdded($assessment)
    {
        $oldValueAdded = DB::connection('mysql2')->table('property_property_value_added')
            ->where('property_id', $this->oldProperty['id'])->pluck('property_value_added_id')->toArray();

        $valueAdded = getSyncArray($oldValueAdded, ['property_id' => $this->property->id]);
        $assessment->valuesAdded()->sync($valueAdded);
    }

    protected function geoRegistoryDetails()
    {
        $oldGeoRegistory = DB::connection('mysql2')->table('property_geo_registry')
            ->where('property_id', $this->oldProperty['id'])->first();

        if($oldGeoRegistory)
        {
            $oldGeoRegistory = (array) $oldGeoRegistory;
            unset($oldGeoRegistory['id']);
            unset($oldGeoRegistory['property_id']);

            $newGeoRegistory = new \App\Models\PropertyGeoRegistry();
            $newGeoRegistory->fill($oldGeoRegistory);
            $newGeoRegistory->property()->associate($this->property->id);
            $newGeoRegistory->save();
        }
    }

    public function createRegistryMeter()
    {
        $oldRegistryMeters = DB::connection('mysql2')->table('registry_meters')
            ->where('property_id', $this->oldProperty['id'])->get()->toArray();

        if(count($oldRegistryMeters))
        {
            foreach ($oldRegistryMeters as $oldRegisterMeter)
            {
                $oldRegisterMeter = (array) $oldRegisterMeter;
                unset($oldRegisterMeter['id']);
                unset($oldRegisterMeter['property_id']);

                $newRegisterMeter = new \App\Models\RegistryMeter();
                $newRegisterMeter->fill($oldRegisterMeter);
                $newRegisterMeter->property()->associate($this->property->id);
                $newRegisterMeter->save();
            }
        }
    }

    private function getPaymentDetails()
    {
        $oldpayments = DB::connection('mysql2')->table('property_payments')
            ->where('property_id', $this->oldProperty['id'])->get()->toArray();

        if(count($oldpayments))
        {
            foreach ($oldpayments as $oldpayment)
            {
                $oldpayment = (array) $oldpayment;
                unset($oldpayment['id']);
                unset($oldpayment['property_id']);

                $newPayments = new \App\Models\PropertyPayment();
                $newPayments->fill($oldpayment);
                $newPayments->property()->associate($this->property->id);
                $newPayments->save();
            }
        }
    }
}
