<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderNumberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::Create('orders_number', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id');
            $table->integer('exit_ware_status')->default(0);
            $table->integer('confirmed')->default(0);
            $table->integer('back_fill')->default(0);
            $table->string('not_enough')->nullable();
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
        Schema::table('orders_number', function () {
            Schema::drop('orders_number');
        });
    }
}
