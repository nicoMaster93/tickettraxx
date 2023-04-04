<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrivers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_states', function (Blueprint $table) {
            $table->id();
            $table->string("name");
        });


        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string("name")->nullable();
            $table->string("phone")->nullable();
            $table->string("email")->nullable();
            $table->string("address")->nullable();

            $table->bigInteger('fk_contractor')->unsigned();
            $table->foreign('fk_contractor')->references('id')->on('contractors')->onDelete('cascade');
            $table->index('fk_contractor');

            $table->bigInteger('fk_driver_state')->unsigned();
            $table->foreign('fk_driver_state')->references('id')->on('driver_states')->onDelete('cascade');
            $table->index('fk_driver_state');

            $table->bigInteger('fk_location_city')->unsigned();
            $table->foreign('fk_location_city')->references('id')->on('location')->onDelete('cascade');
            $table->index('fk_location_city');
        });

        Schema::create('vehicles_drivers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('fk_vehicle')->unsigned();
            $table->foreign('fk_vehicle')->references('id')->on('vehicles')->onDelete('cascade');
            $table->index('fk_vehicle');
            $table->bigInteger('fk_driver')->unsigned();
            $table->foreign('fk_driver')->references('id')->on('drivers')->onDelete('cascade');
            $table->index('fk_driver');           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivers', function(Blueprint $table)
        {
            $table->dropForeign('drivers_fk_contractor_foreign');
            $table->dropIndex('drivers_fk_contractor_index');

            $table->dropForeign('drivers_fk_driver_state_foreign');
            $table->dropIndex('drivers_fk_driver_state_index');
            
            $table->dropForeign('drivers_fk_location_city_foreign');
            $table->dropIndex('drivers_fk_location_city_index');
            
            
        });
        Schema::table('vehicles_drivers', function(Blueprint $table)
        {
            $table->dropForeign('vehicles_drivers_fk_vehicle_foreign');
            $table->dropIndex('vehicles_drivers_fk_vehicle_index');

            $table->dropForeign('vehicles_drivers_fk_driver_foreign');
            $table->dropIndex('vehicles_drivers_fk_driver_index');
        });

        

        Schema::dropIfExists('drivers');
        Schema::dropIfExists('driver_states');
        Schema::dropIfExists('vehicles_drivers');
        
    }
}
