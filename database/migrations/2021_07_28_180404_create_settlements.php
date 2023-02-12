<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettlements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('settlement_states', function (Blueprint $table) {
            $table->id();
            $table->string("name");
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->date("date_pay");
            $table->decimal("total",10,2);
        });
        


        Schema::create('settlements', function (Blueprint $table) {
            $table->id();
            $table->date("start_date");
            $table->date("end_date");
            $table->decimal("subtotal",10,2);
            $table->decimal("deduction",10,2);
            $table->decimal("total",10,2);
            $table->decimal("for_contractor",10,2);


            $table->bigInteger('fk_contractor')->unsigned();
            $table->foreign('fk_contractor')->references('id')->on('contractors')->onDelete('cascade');
            $table->index('fk_contractor');

            $table->bigInteger('fk_settlement_state')->unsigned();
            $table->foreign('fk_settlement_state')->references('id')->on('settlement_states')->onDelete('cascade');
            $table->index('fk_settlement_state');

            $table->bigInteger('fk_payment')->unsigned()->nullable();
            $table->foreign('fk_payment')->references('id')->on('payments')->onDelete('cascade');
            $table->index('fk_payment');
            
            
        });

        Schema::create('settlements_deduction', function (Blueprint $table) {
            $table->id();
            $table->decimal("value",10,2);
            $table->bigInteger('fk_deduction')->unsigned();
            $table->foreign('fk_deduction')->references('id')->on('deductions')->onDelete('cascade');
            $table->index('fk_deduction');

            $table->bigInteger('fk_settlement')->unsigned();
            $table->foreign('fk_settlement')->references('id')->on('settlements')->onDelete('cascade');
            $table->index('fk_settlement');
        });

        Schema::create('settlements_tickets', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('fk_ticket')->unsigned();
            $table->foreign('fk_ticket')->references('id')->on('tickets')->onDelete('cascade');
            $table->index('fk_ticket');

            $table->bigInteger('fk_settlement')->unsigned();
            $table->foreign('fk_settlement')->references('id')->on('settlements')->onDelete('cascade');
            $table->index('fk_settlement');
        });
        

        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        Schema::table('settlements', function(Blueprint $table)
        {
            $table->dropForeign('settlements_fk_contractor_foreign');
            $table->dropIndex('settlements_fk_contractor_index');

            $table->dropForeign('settlements_fk_settlement_state_foreign');
            $table->dropIndex('settlements_fk_settlement_state_index');

            $table->dropForeign('settlements_fk_payment_foreign');
            $table->dropIndex('settlements_fk_payment_index');
            
            
        });

        Schema::table('settlements_deduction', function(Blueprint $table)
        {
            $table->dropForeign('settlements_deduction_fk_deduction_foreign');
            $table->dropIndex('settlements_deduction_fk_deduction_index');

            $table->dropForeign('settlements_deduction_fk_settlement_foreign');
            $table->dropIndex('settlements_deduction_fk_settlement_index');
            
        });
        Schema::table('settlements_tickets', function(Blueprint $table)
        {

            $table->dropForeign('settlements_tickets_fk_ticket_foreign');
            $table->dropIndex('settlements_tickets_fk_ticket_index');

            $table->dropForeign('settlements_tickets_fk_settlement_foreign');
            $table->dropIndex('settlements_tickets_fk_settlement_index');
            
            
        });
        
        
        
        Schema::dropIfExists('settlements_tickets');
        Schema::dropIfExists('settlements_deduction');
        Schema::dropIfExists('settlements');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('settlement_states');
        
    }
}
