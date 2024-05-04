<?php

use Illuminate\Database\Seeder;

class PropertyPaymentMigration extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $properties = \App\Models\Property::whereRaw('id BETWEEN ' . 0 . ' AND ' . 10080 . '')
            ->whereNull('payment_migrate_from')
            ->get();

        if(count($properties)) {
            foreach ($properties as $property) {
                $property->payments()->delete();
                $this->migratePayment($property);
            }
        }
    }

    protected function migratePayment($property) {
        $oldpayments = DB::connection('mysql2')->table('property_payments')
            ->where('property_id', $property->id)->get()->toArray();

        if(count($oldpayments))
        {
            $paymentMigrateFrom = '';

            foreach ($oldpayments as $oldpayment)
            {
                $oldpayment = (array) $oldpayment;

                $paymentMigrateFrom .= ', ' .$oldpayment['id'];

                unset($oldpayment['id']);
                unset($oldpayment['property_id']);

                $newPayments = new \App\Models\PropertyPayment();
                $newPayments->fill($oldpayment);
                $newPayments->property()->associate($property->id);
                $newPayments->migrate_at = date('Y-m-d H:i:s');
                $newPayments->save();

            }

            $property->payment_migrate_from = ltrim($paymentMigrateFrom, ', ');
            $property->save();
        }
    }
}
