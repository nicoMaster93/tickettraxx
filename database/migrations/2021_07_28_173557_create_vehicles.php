<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_states', function (Blueprint $table) {
            $table->id();
            $table->string("name");
        });
    
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string("unit_number")->nullable();
            $table->string("truck_model_brand")->nullable();
            $table->string("truck_year")->nullable();
            $table->string("truck_vin_number")->nullable();
            $table->string("trailer_model_brand")->nullable();
            $table->string("trailer_year")->nullable();
            $table->string("trailer_vin_number")->nullable();

            $table->bigInteger('fk_contractor')->unsigned();
            $table->foreign('fk_contractor')->references('id')->on('contractors')->onDelete('cascade');
            $table->index('fk_contractor');

            $table->bigInteger('fk_vehicle_state')->unsigned();
            $table->foreign('fk_vehicle_state')->references('id')->on('vehicle_states')->onDelete('cascade');
            $table->index('fk_vehicle_state');
        });

        Schema::create('vehicles_alias', function (Blueprint $table) {
            $table->id();
            $table->string("alias")->nullable();

            $table->bigInteger('fk_vehicle')->unsigned();
            $table->foreign('fk_vehicle')->references('id')->on('vehicles')->onDelete('cascade');
            $table->index('fk_vehicle');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehicles_alias', function(Blueprint $table)
        {
            $table->dropForeign('vehicles_alias_fk_vehicle_foreign');
            $table->dropIndex('vehicles_alias_fk_vehicle_index');

        });
        Schema::table('vehicles', function(Blueprint $table)
        {

            
            $table->dropForeign('vehicles_fk_contractor_foreign');
            $table->dropIndex('vehicles_fk_contractor_index');

            $table->dropForeign('vehicles_fk_vehicle_state_foreign');
            $table->dropIndex('vehicles_fk_vehicle_state_index');
        });

        
        Schema::dropIfExists('vehicles_alias');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('vehicle_states');
    }
}
