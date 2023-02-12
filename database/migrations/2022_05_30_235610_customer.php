<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Customer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer', function (Blueprint $table) {
            $table->id();            
            $table->string("full_name")->nullable();
            $table->string("prefix");           
            $table->engine = "InnoDB";
        });

        Schema::table('surcharge', function (Blueprint $table) {
            $table->dropColumn("customer-prefix");

            $table->decimal("percentaje",10,2);            
            $table->bigInteger('fk_customer')->unsigned();
            $table->foreign('fk_customer')->references('id')->on('customer')->onDelete('cascade');
            $table->index('fk_customer');
        });


        Schema::table('p_o_codes', function (Blueprint $table) {
            $table->bigInteger('fk_customer')->unsigned()->nullable();
            $table->foreign('fk_customer')->references('id')->on('customer')->onDelete('cascade');
            $table->index('fk_customer');
        });


        Schema::dropIfExists('surcharge_item');
        


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('surcharge_item', function (Blueprint $table) {
            $table->id();            
            $table->bigInteger('fk_surcharge')->unsigned();
            $table->foreign('fk_surcharge')->references('id')->on('surcharge')->onDelete('cascade');
            $table->index('fk_surcharge');
            $table->decimal("fuel_range_from",10,2);
            $table->decimal("fuel_range_to",10,2);
            $table->decimal("surcharge_per",10,2);
            $table->engine = "InnoDB";
        });

        Schema::table('surcharge', function (Blueprint $table) {
            $table->dropForeign('surcharge_fk_customer_foreign');
            $table->dropIndex('surcharge_fk_customer_index');
        });
        Schema::table('p_o_codes', function (Blueprint $table) {
            $table->dropForeign('p_o_codes_fk_customer_foreign');
            $table->dropIndex('p_o_codes_fk_customer_index');
        });

        Schema::dropIfExists('customer');        
    }
}
