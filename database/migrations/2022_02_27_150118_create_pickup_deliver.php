<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePickupDeliver extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pickup_deliver', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger("type");
            $table->string("place");
            $table->engine = "InnoDB";
        });

        Schema::table('p_o_codes', function (Blueprint $table) {
            $table->bigInteger('fk_pickup')->unsigned();
            $table->foreign('fk_pickup')->references('id')->on('pickup_deliver')->onDelete('cascade');
            $table->index('fk_pickup');

            $table->bigInteger('fk_deliver')->unsigned();
            $table->foreign('fk_deliver')->references('id')->on('pickup_deliver')->onDelete('cascade');
            $table->index('fk_deliver');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('p_o_codes', function (Blueprint $table) {
            $table->dropForeign('p_o_codes_fk_pickup_foreign');
            $table->dropIndex('p_o_codes_fk_pickup_index');

            $table->dropForeign('p_o_codes_fk_deliver_foreign');
            $table->dropIndex('p_o_codes_fk_deliver_index');
            
            $table->dropColumn("fk_pickup");
            $table->dropColumn("fk_deliver");
            
        });

        Schema::dropIfExists('pickup_deliver');
    }
}
