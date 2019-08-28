<?php

use App\Month;
use Illuminate\Database\Seeder;

class MonthsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Month::create([
            "name" => 'Հունվար',
        ]);
        Month::create([
            "name" => 'Փետրվար',
        ]);
        Month::create([
            "name" => 'Մարտ',
        ]);
        Month::create([
            "name" => 'Ապրիլ',
        ]);
        Month::create([
            "name" => 'Մայիս',
        ]);
        Month::create([
            "name" => 'Հունիս',
        ]);
        Month::create([
            "name" => 'Հուլիս',
        ]);
        Month::create([
            "name" => 'Օգոստոս',
        ]);
        Month::create([
            "name" => 'Սեպտեմբեր',
        ]);
        Month::create([
            "name" => 'Հոկտեմբեր',
        ]);
        Month::create([
            "name" => 'Նոյեմբեր',
        ]);
        Month::create([
            "name" => 'Դեկտեմբեր',
        ]);
    }
}
