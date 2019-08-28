<?php

use App\Subdivision;
use Illuminate\Database\Seeder;

class SubdivisionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Schema::hasTable('subdivisions')) {
            $sub = new Subdivision;
            $sub->name = 'Փաթեթավորում';
            $sub->place()->associate(2);
            $sub->save();

            $sub = new Subdivision;
            $sub->name = 'Պարարտանյութեր';
            $sub->place()->associate(1);
            $sub->save();

            $sub = new Subdivision;
            $sub->name = 'Թունաքիմիկատներ';
            $sub->place()->associate(1);
            $sub->save();
        }
    }
}
