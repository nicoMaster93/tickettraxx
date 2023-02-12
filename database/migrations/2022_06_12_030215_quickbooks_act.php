<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class QuickbooksAct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->bigInteger('id_quickbooks')->nullable()->unsigned()->unique();
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->bigInteger('fk_customer')->unsigned()->nullable();
            $table->foreign('fk_customer')->references('id')->on('customer')->onDelete('cascade');
            $table->index('fk_customer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->dropColumn("id_quickbooks");
        });
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign('ticket_fk_customer_foreign');
            $table->dropIndex('ticket_fk_customer_index');
        });
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn("fk_customer");
        });
    }
}
