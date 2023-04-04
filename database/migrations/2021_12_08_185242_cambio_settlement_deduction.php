<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CambioSettlementDeduction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settlements_deduction', function (Blueprint $table) {
            $table->bigInteger('fk_vehicle')->unsigned()->nullable();
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
        Schema::table('settlements_deduction', function (Blueprint $table) {
            $table->dropForeign('settlements_deduction_fk_vehicle_foreign');
            $table->dropIndex('settlements_deduction_fk_vehicle_index');

            $table->dropColumn('fk_vehicle');       
        });
    }
}
