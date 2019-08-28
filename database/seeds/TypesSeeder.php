<?php

use Illuminate\Database\Seeder;
use App\Type;

class TypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Schema::hasTable('types')) {
            Type::create([
                "name" => 'Կառավարիչ',
            ]);
            Type::create([
                "name" => 'Փաթեթավորման պահեստ',
            ]);
            Type::create([
                "name" => 'Թունաքիմիկատների պահեստ',
            ]);
            Type::create([
                "name" => 'Պարարտանյութերի պահեստ',
            ]);
            Type::create([
                "name" => 'Արտ. պահեստ',
            ]);
            Type::create([
                "name" => 'Տնօրեն',
            ]);
            Type::create([
                "name" => 'Պատվեր գր.',
            ]);
            Type::create([
                "name" => 'Հաշվապահ',
            ]);
        }
    }
}
