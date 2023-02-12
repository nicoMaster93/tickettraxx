<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_states', function (Blueprint $table) {
            $table->id();
            $table->string("name");
        });

        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string("name");
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string("number");
            $table->date("date_gen");
            $table->date("date_pay");
            
            $table->string("pickup")->nullable();
            $table->string("deliver")->nullable();
            $table->string("file")->nullable();
            $table->decimal("tonage");
            $table->decimal("rate",10,2);            
            $table->decimal("total",10,2);

            $table->string("return_message")->nullable();

            $table->bigInteger('fk_vehicle')->unsigned();
            $table->foreign('fk_vehicle')->references('id')->on('vehicles')->onDelete('cascade');
            $table->index('fk_vehicle');

            $table->bigInteger('fk_material')->unsigned()->nullable();
            $table->foreign('fk_material')->references('id')->on('materials')->onDelete('cascade');
            $table->index('fk_material');

            $table->bigInteger('fk_ticket_state')->unsigned();
            $table->foreign('fk_ticket_state')->references('id')->on('ticket_states')->onDelete('cascade');
            $table->index('fk_ticket_state');           

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', function(Blueprint $table)
        {
            $table->dropForeign('tickets_fk_vehicle_foreign');
            $table->dropIndex('tickets_fk_vehicle_index');

            $table->dropForeign('tickets_fk_material_foreign');
            $table->dropIndex('tickets_fk_material_index');

            $table->dropForeign('tickets_fk_ticket_state_foreign');
            $table->dropIndex('tickets_fk_ticket_state_index');
        });
        
        

        Schema::dropIfExists('materials');
        Schema::dropIfExists('ticket_states');
        Schema::dropIfExists('tickets');
    }
}
