<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeductions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deduction_states', function (Blueprint $table) {
            $table->id();
            $table->string("name");
        });
        Schema::create('deduction_types', function (Blueprint $table) {
            $table->id();
            $table->string("name");
        });        
        Schema::create('deductions', function (Blueprint $table) {
            $table->id();
            $table->decimal("total_value",10,2);
            $table->decimal("balance_due",10,2);
            $table->date("date_loan")->nullable();
            $table->date("date_pay")->nullable();
            $table->integer("number_installments")->nullable();
            $table->decimal("fixed_value",10,2)->nullable();
            $table->integer("days")->nullable();

            $table->bigInteger('fk_deduction_type')->unsigned();
            $table->foreign('fk_deduction_type')->references('id')->on('deduction_types')->onDelete('cascade');
            $table->index('fk_deduction_type');

            $table->bigInteger('fk_deduction_state')->unsigned();
            $table->foreign('fk_deduction_state')->references('id')->on('deduction_states')->onDelete('cascade');
            $table->index('fk_deduction_state');

            $table->bigInteger('fk_contractor')->unsigned()->nullable();
            $table->foreign('fk_contractor')->references('id')->on('contractors')->onDelete('cascade');
            $table->index('fk_contractor');

        });

        Schema::create('deduction_vehicles', function (Blueprint $table) {
            $table->id();
            $table->date("date");
            
            $table->bigInteger('fk_deduction')->unsigned()->nullable();
            $table->foreign('fk_deduction')->references('id')->on('deductions')->onDelete('cascade');
            $table->index('fk_deduction');

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
        Schema::table('deductions', function(Blueprint $table)
        {
            $table->dropForeign('deductions_fk_deduction_type_foreign');
            $table->dropIndex('deductions_fk_deduction_type_index');

            $table->dropForeign('deductions_fk_deduction_state_foreign');
            $table->dropIndex('deductions_fk_deduction_state_index');

            $table->dropForeign('deductions_fk_contractor_foreign');
            $table->dropIndex('deductions_fk_contractor_index');
        });
        
        Schema::table('deduction_vehicles', function(Blueprint $table)
        {
            $table->dropForeign('deduction_vehicles_fk_deduction_type_foreign');
            $table->dropIndex('deduction_vehicles_fk_deduction_type_index');

            $table->dropForeign('deduction_vehicles_fk_vehicle_type_foreign');
            $table->dropIndex('deduction_vehicles_fk_vehicle_type_index');

        });

        Schema::dropIfExists('deduction_types');
        Schema::dropIfExists('deduction_states');
        Schema::dropIfExists('deduction_vehicles');
        Schema::dropIfExists('deductions');
        

    }
}
