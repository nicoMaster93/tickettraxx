<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OtherPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('other_payment_states', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->engine = "InnoDB";
        });

        Schema::create('other_payments', function (Blueprint $table) {
            $table->id();
            $table->date("date_pay");
            $table->string("description",100)->nullable();
            $table->decimal("total",10,2);
            
            $table->bigInteger('fk_contractor')->unsigned();
            $table->foreign('fk_contractor')->references('id')->on('contractors')->onDelete('cascade');
            $table->index('fk_contractor');

            $table->bigInteger('fk_other_payment_state')->unsigned();
            $table->foreign('fk_other_payment_state')->references('id')->on('other_payment_states')->onDelete('cascade');
            $table->index('fk_other_payment_state');

            $table->engine = "InnoDB";
        });

        Schema::create('settlements_other_payments', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('fk_other_payments')->unsigned();
            $table->foreign('fk_other_payments')->references('id')->on('other_payments')->onDelete('cascade');
            $table->index('fk_other_payments');

            $table->bigInteger('fk_settlement')->unsigned();
            $table->foreign('fk_settlement')->references('id')->on('settlements')->onDelete('cascade');
            $table->index('fk_settlement');
            $table->engine = "InnoDB";
        });

        Schema::table('settlements', function (Blueprint $table) {
            $table->decimal("other_payments",10,2)->after('deduction')->nullable();
        });
        



    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('other_payments', function(Blueprint $table)
        {
            $table->dropForeign('other_payments_fk_contractor_foreign');
            $table->dropIndex('other_payments_fk_contractor_index');

            $table->dropForeign('other_payments_fk_other_payment_state_foreign');
            $table->dropIndex('other_payments_fk_other_payment_state_index');
           
        });

        Schema::table('settlements_other_payments', function(Blueprint $table)
        {
            $table->dropForeign('settlements_other_payments_fk_other_payments_foreign');
            $table->dropIndex('settlements_other_payments_fk_other_payments_index');

            $table->dropForeign('settlements_other_payments_fk_settlement_foreign');
            $table->dropIndex('settlements_other_payments_fk_settlement_index');
        });

        Schema::table('settlements', function (Blueprint $table) {
            $table->dropColumn('other_payments');       
        });

        Schema::dropIfExists('other_payments');
        Schema::dropIfExists('settlements_other_payments');
        Schema::dropIfExists('other_payment_states');
    }
}
