<?php


use Illuminate\Database\Seeder;
use App\Good;

class GoodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        if (Schema::hasTable('goods')) {

            $good_name = [
                'Արկղ',
                'Ռեզին',
                'Պարարտանյութ',
                'Թուղթ',
                'Փաթեթ',
                'Ժապավեն',
                'Դույլ',
                'Կրիշկա',
            ];
            $good_unit = [
                'հատ',
                'տուփ',
                'կգ',
                'տուփ',
                'հատ',
                'մետր',
                'հատ',
                'հատ',

            ];
            $id = [1,2,3,4,5];
            $sub = [1,2,3];

            for($i = 0; $i < count($good_name); $i++){
                $good = new Good;
                $good->name = $good_name[$i];
                $good->unit = $good_unit[$i];
                $good->subdivision()->associate($sub[rand(1, count($sub))-1]);
                $good->save();
                $good->supplier()->attach($id[rand(1, count($id))-1]);
            }

        }
    }
}
