<?php

use Illuminate\Database\Seeder;
use App\Client;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Schema::hasTable('clients')) {

            Client::create([
                "name" => 'Գառնիկ',
                "address" => 'Երևան',
                "phone" => '+374-55-66-22-33',

            ]);
            Client::create([
                "name" => 'Արտակ',
                "address" => 'Եվրոպա',
                "phone" => '+374-93-62-72-33',
                "status" => 1

            ]);
            Client::create([
                "name" => 'Վարդան',
                "address" => 'Ռուսաստան',
                "phone" => '+374-99-10-50-30',
                "status" => 1

            ]);
            Client::create([
                "name" => 'Վազգեն',
                "address" => 'Աշտարակ',
                "phone" => '+374-95-10-00-30',

            ]);

        }
    }
}
