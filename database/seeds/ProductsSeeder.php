<?php

use App\Product;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Schema::hasTable('products')) {

            $product_name = [
                'Վարդ',
                'Գիացինտ',
                'Ռուդբեկիա',
                'Գորտենզիա',
                'Ստրելիցիա',
                'Կաննա',
                'Կալա',
                'Սակուրա'
                ];
            $local_price = [
                1000,
                1000,
                1000,
                1000,
                1000,
                1000,
                1000,
                1000,
                1000,
            ];
            $export_price = [
                1500,
                1500,
                1500,
                1500,
                1500,
                1500,
                1500,
                1500,
            ];
            $product_height = [
                '50',
                '50',
                '50',
                '50',
                '50',
                '50',
                '50',
                '50',
            ];
            for($i = 0; $i < count($product_name); $i++){
                $product = new Product;
                $product->name = $product_name[$i];
                $product->height = $product_height[$i];
                $product->local_price = $local_price[$i];
                $product->export_price = $export_price[$i];
                $product->save();

            }
        }
    }
}
