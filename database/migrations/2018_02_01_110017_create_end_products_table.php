<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEndProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('end_products')) {
            Schema::create('end_products', function (Blueprint $table) {
                $table->increments('id')->unique();
                $table->integer('product_id');
                $table->foreign('product_id')
                    ->references('id')
                    ->on('products')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
                $table->integer('orders_number_id')->nullable();
                $table->foreign('orders_number_id')
                    ->references('id')
                    ->on('orders_number')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
                $table->integer('amt');
                $table->integer('balance')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('end_products');
    }
}
