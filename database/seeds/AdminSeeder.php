<?php

use Illuminate\Database\Seeder;
use App\User;
use Carbon\Carbon;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Schema::hasTable('users')) {
            $user = new User;
            $user->name = 'admin';
            $user->last_name = 'adminovich';
            $user->login = 'admin';
            $user->email = 'admin@admin.net';
            $user->password = bcrypt('secret');
            $user->type()->associate(1);
            $user->save();
        }
    }
}
