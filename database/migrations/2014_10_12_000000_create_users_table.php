<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rol', function (Blueprint $table) {
            $table->id();
            $table->string("name",100);
            $table->engine = "InnoDB";
        });
        

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            $table->bigInteger('fk_rol')->unsigned();
            $table->foreign('fk_rol')->references('id')->on('rol')->onDelete('cascade');
            $table->index('fk_rol');
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
        
        Schema::table('users', function(Blueprint $table)
        {
            $table->dropForeign('users_fk_rol_foreign');
            $table->dropIndex('users_fk_rol_index');

        });
        Schema::dropIfExists('users');
        Schema::dropIfExists('rol');
    }
}
