<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TicketSurcharge extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->bigInteger('fk_surcharge')->unsigned()->nullable();
            $table->foreign('fk_surcharge')->references('id')->on('surcharge')->onDelete('cascade');
            $table->index('fk_surcharge');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign('ticket_fk_surcharge_foreign');
            $table->dropIndex('ticket_fk_surcharge_index');
        });
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn("fk_surcharge");
        });
    }
}
