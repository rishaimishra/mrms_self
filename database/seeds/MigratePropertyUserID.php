<?php

use Illuminate\Database\Seeder;

class MigratePropertyUserID extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $properties = \App\Models\Property::whereNULL('user_id')->get();

        foreach ($properties as $property)
        {
            $backup = \DB::connection('mysql2')->selectOne(\DB::raw("SELECT * FROM properties where id={$property->id}"));

            if($backup)
            {
                $property->user_id = isset($backup->user_id) ? $backup->user_id : '';
                $property->save();
            }
        }

    }
}
