<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Surcharge extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->decimal("surcharge",10,2)->after('total')->nullable();
        });
        Schema::table('settlements', function (Blueprint $table) {
            $table->decimal("surcharge",10,2)->after('total')->nullable();
        });

        Schema::create('surcharge', function (Blueprint $table) {
            $table->id();
            $table->date("from");
            $table->date("to");
            $table->string('customer-prefix');

            $table->engine = "InnoDB";
        });

        Schema::create('surcharge_item', function (Blueprint $table) {
            $table->id();            
            $table->bigInteger('fk_surcharge')->unsigned();
            $table->foreign('fk_surcharge')->references('id')->on('surcharge')->onDelete('cascade');
            $table->index('fk_surcharge');
            $table->decimal("fuel_range_from",10,2);
            $table->decimal("fuel_range_to",10,2);
            $table->decimal("surcharge_per",10,2);
            $table->engine = "InnoDB";
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
            $table->dropColumn("surcharge");
        });

        Schema::table('settlements', function (Blueprint $table) {
            $table->dropColumn("surcharge");
        });

        Schema::table('surcharge_item', function (Blueprint $table) {
            $table->dropForeign('surcharge_item_fk_surcharge_foreign');
            $table->dropIndex('surcharge_item_fk_surcharge_index');
        });
        Schema::dropIfExists('surcharge');
        Schema::dropIfExists('surcharge_item');
    }
}
