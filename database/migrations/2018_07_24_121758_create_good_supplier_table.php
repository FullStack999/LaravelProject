<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodSupplierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('good_supplier', function (Blueprint $table) {

            $table->increments('id');

            $table->integer('good_id')->unsigned();
            $table->foreign('good_id')
                ->references('id')
                ->on('goods')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->integer('supplier_id')->unsigned();
            $table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers')
                ->onUpdate('cascade')
                ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('good_supplier');
    }
}
