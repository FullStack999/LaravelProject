<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('goods')) {
            Schema::create('goods', function (Blueprint $table) {
                $table->increments('id')->unique();
                $table->string('name')->unique();
                $table->string('unit');
                $table->integer('subdivision_id');
                $table->foreign('subdivision_id')
                    ->references('id')
                    ->on('subdivisions');
                $table->integer('returnable')->default(0);
                $table->timestamps();
                $table->softDeletes();
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
        Schema::dropIfExists('goods');
    }
}
