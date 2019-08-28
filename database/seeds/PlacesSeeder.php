<?php

use Illuminate\Database\Seeder;
use App\Place;
class PlacesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Schema::hasTable('places')) {

            $place = new Place;
            $place->name = 'ջերմոց';
            $place->save();

            $place = new Place;
            $place->name = 'փաթեթավորման';
            $place->save();
        }
    }
}
