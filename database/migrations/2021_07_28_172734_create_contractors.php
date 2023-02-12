<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('type_ids', function (Blueprint $table) {
            $table->id();
            $table->string("type_name",100);
        });
        
        Schema::create('location', function (Blueprint $table) {
            $table->id();
            $table->string("location_name",100);
            $table->string("location_type",100);
            
            $table->bigInteger('fk_location')->unsigned()->nullable();
            $table->foreign('fk_location')->references('id')->on('location')->onDelete('cascade');
            $table->index('fk_location');
        });

        
        Schema::create('contractor_states', function (Blueprint $table) {
            $table->id();
            $table->string("name");
        });

        

        Schema::create('contractors', function (Blueprint $table) {
            $table->id();
            $table->string("name_contact",100)->nullable();
            $table->string("address",100)->nullable();
            $table->string("zip_code",20)->nullable();
            $table->string("id_number",100)->nullable();
            $table->string("company_name")->nullable();
            $table->string("email",100)->nullable();
            $table->decimal("percentage",10,2);

            $table->bigInteger('fk_type_ids')->unsigned();
            $table->foreign('fk_type_ids')->references('id')->on('type_ids')->onDelete('cascade');
            $table->index('fk_type_ids');

            $table->bigInteger('fk_location_city')->unsigned();
            $table->foreign('fk_location_city')->references('id')->on('location')->onDelete('cascade');
            $table->index('fk_location_city');

            $table->bigInteger('fk_user')->unsigned();
            $table->foreign('fk_user')->references('id')->on('users')->onDelete('cascade');
            $table->index('fk_user');

            $table->bigInteger('fk_contractor_state')->unsigned();
            $table->foreign('fk_contractor_state')->references('id')->on('contractor_states')->onDelete('cascade');
            $table->index('fk_contractor_state');

            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('location', function(Blueprint $table)
        {
            $table->dropForeign('location_fk_location_foreign');
            $table->dropIndex('location_fk_location_index');

        });
        Schema::table('contractors', function(Blueprint $table)
        {
            $table->dropForeign('contractors_fk_type_ids_foreign');
            $table->dropIndex('contractors_fk_type_ids_index');

            $table->dropForeign('contractors_fk_location_city_foreign');
            $table->dropIndex('contractors_fk_location_city_index');

            $table->dropForeign('contractors_fk_user_foreign');
            $table->dropIndex('contractors_fk_user_index');

            $table->dropForeign('contractors_fk_contractor_state_foreign');
            $table->dropIndex('contractors_fk_contractor_state_index');

        });
        

        Schema::dropIfExists('type_ids');
        Schema::dropIfExists('location');
        Schema::dropIfExists('contractor_states');
        Schema::dropIfExists('contractors');

    }
}
