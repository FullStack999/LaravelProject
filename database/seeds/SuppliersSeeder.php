<?php

use App\Supplier;
use Illuminate\Database\Seeder;

class SuppliersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Supplier::create([
            'name' => 'Մատակարար 1'
        ]);
        Supplier::create([
            'name' => 'Մատակարար 2'
        ]);
        Supplier::create([
            'name' => 'Մատակարար 3'
        ]);
        Supplier::create([
            'name' => 'Մատակարար 4'
        ]);
        Supplier::create([
            'name' => 'Մատակարար 5'
        ]);

    }
}
