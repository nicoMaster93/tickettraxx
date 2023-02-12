<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeductionVehiclesNewFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deduction_vehicles', function (Blueprint $table) {
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->decimal("gallons",10,2)->nullable();
            $table->decimal("total",10,2)->nullable();           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settlements_deduction', function (Blueprint $table) {
            $table->dropColumn('city');       
            $table->dropColumn('state');       
            $table->dropColumn('gallons');       
            $table->dropColumn('total');       
        });
    }
}
