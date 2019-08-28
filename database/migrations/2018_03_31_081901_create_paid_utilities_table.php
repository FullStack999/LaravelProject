<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaidUtilitiesTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('paid_utilities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('expense_id');
            $table->foreign('expense_id')
                ->references('id')
                ->on('expenses')
                ->onUpdate('cascade')
                ->onDelete('cascade');;
            $table->integer('amt');
            $table->integer('balance');
            $table->integer('month_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paid_utilities');
    }
}
