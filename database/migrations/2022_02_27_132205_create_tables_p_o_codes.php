<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablesPOCodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p_o_codes', function (Blueprint $table) {
            $table->id();
            $table->string("code");
            $table->decimal("rate",10,2);
            $table->engine = "InnoDB";
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->bigInteger('fk_p_o_code')->unsigned()->nullable();
            $table->foreign('fk_p_o_code')->references('id')->on('p_o_codes')->onDelete('cascade');
            $table->index('fk_p_o_code');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign('tickets_fk_p_o_code_foreign');
            $table->dropIndex('tickets_fk_p_o_code_index');
        });
        Schema::dropIfExists('p_o_codes');
    }
}
