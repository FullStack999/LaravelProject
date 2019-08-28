<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id');
            $table->foreign('client_id')
                ->references('id')
                ->on('clients');
            $table->integer('good_id')->nullable();
            $table->integer('good_amt')->default(0);
            $table->integer('debt')->default(0);
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
        Schema::dropIfExists('clients_history');
    }
}
