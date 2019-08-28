<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWarehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('warehouses')) {
            Schema::create('warehouses', function (Blueprint $table) {
                $table->increments('id')->unique();
                $table->integer('good_id')->nullable();
                $table->foreign('good_id')->references('id')->on('goods');
                $table->integer('place_id')->nullable();
                $table->foreign('place_id')->references('id')->on('places');
                $table->integer('client_id')->nullable();
                $table->foreign('client_id')->references('id')->on('clients');
                $table->integer('supplier_id')->nullable();
//                $table->foreign('supplier_id')->references('id')->on('suppliers');
                $table->float('amt',4,3);
                $table->float('balance', 5,3)->default(0);
                $table->float('price')->nullable();
                $table->float('cost')->nullable();
                $table->integer('paid')->default(0);
                $table->text('comment')->nullable();
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
        Schema::dropIfExists('warehouses');
    }
}
