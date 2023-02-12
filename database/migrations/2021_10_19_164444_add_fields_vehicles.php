<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsVehicles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('photo_truck_dot_inspection')->nullable();
            $table->string('photo_truck_registration')->nullable();
            $table->string('photo_trailer_dot_inspection')->nullable();
            $table->string('photo_trailer_registration')->nullable();
            $table->string('photo_trailer_over')->nullable();
        });
    
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('photo_truck_dot_inspection');
            $table->dropColumn('photo_truck_registration');
            $table->dropColumn('photo_trailer_dot_inspection');
            $table->dropColumn('photo_trailer_registration');
            $table->dropColumn('photo_trailer_over');

        });
    }
}
