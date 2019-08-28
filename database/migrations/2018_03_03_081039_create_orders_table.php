<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('orders_number_id');
            $table->foreign('orders_number_id')
                ->references('id')
                ->on('orders_number')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->integer('product_id');
            $table->integer('amt');
            $table->integer('price');
            $table->integer('discount_price')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('orders');
    }
}
