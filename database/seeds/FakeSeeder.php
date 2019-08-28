<?php

use Illuminate\Database\Seeder;
use App\User;
use Carbon\Carbon;

class FakeSeeder extends Seeder
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
            $user->name = 'Փաթեթավորման';
            $user->last_name = 'Պատասխանատու';
            $user->login = 'account_1';
            $user->password = bcrypt('123456');
            $user->type()->associate(2);
            $user->save();

            $user = new User;
            $user->name = 'Պարարտանյութերի';
            $user->last_name = 'Պատասխանատու';
            $user->login = 'account_2';
            $user->password = bcrypt('123456');
            $user->type()->associate(3);
            $user->save();

            $user = new User;
            $user->name = 'Թունաքիմիկատների';
            $user->last_name = 'Պատասխանատու';
            $user->login = 'account_3';
            $user->password = bcrypt('123456');
            $user->type()->associate(4);
            $user->save();

            $user = new User;
            $user->name = 'Արման';
            $user->last_name = 'Սարգսյան';
            $user->login = 'account_4';
            $user->password = bcrypt('123456');
            $user->type()->associate(5);
            $user->save();

            $user = new User;
            $user->name = 'Տնօրեն';
            $user->last_name = 'Վարդանյան';
            $user->login = 'account_5';
            $user->password = bcrypt('123456');
            $user->type()->associate(6);
            $user->save();

            $user = new User;
            $user->name = 'Միհրան';
            $user->last_name = 'Գևորգյան';
            $user->login = 'account_6';
            $user->password = bcrypt('123456');
            $user->type()->associate(7);
            $user->save();

            $user = new User;
            $user->name = 'Հաշվապահ';
            $user->last_name = 'Ջանիբեկյան';
            $user->login = 'account_7';
            $user->password = bcrypt('123456');
            $user->type()->associate(8);
            $user->save();





        }
    }
}
