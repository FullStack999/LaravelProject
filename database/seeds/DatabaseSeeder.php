<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(TypesSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(FakeSeeder::class);
        $this->call(PlacesSeeder::class);
        $this->call(ProductsSeeder::class);
        $this->call(SubdivisionsSeeder::class);
        $this->call(ClientSeeder::class);
        $this->call(expensesSeeder::class);
        $this->call(MonthsSeeder::class);
        $this->call(SuppliersSeeder::class);
        $this->call(GoodsSeeder::class);
    }
}
