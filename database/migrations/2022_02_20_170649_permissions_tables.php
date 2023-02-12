<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PermissionsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("link")->nullable();
            $table->integer("type", false, true);
            $table->bigInteger('fk_menu')->unsigned()->nullable();
            $table->foreign('fk_menu')->references('id')->on('menu')->onDelete('cascade');
            $table->index('fk_menu');
            $table->engine = "InnoDB";
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('fk_menu')->unsigned();
            $table->foreign('fk_menu')->references('id')->on('menu')->onDelete('cascade');
            $table->index('fk_menu');
            
            $table->bigInteger('fk_user')->unsigned();
            $table->foreign('fk_user')->references('id')->on('users')->onDelete('cascade');
            $table->index('fk_user');
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
        Schema::table('menu', function(Blueprint $table)
        {
            $table->dropForeign('menu_fk_menu_foreign');
            $table->dropIndex('menu_fk_menu_index');

        });

        Schema::table('permissions', function(Blueprint $table)
        {
            $table->dropForeign('permissions_fk_menu_foreign');
            $table->dropIndex('permissions_fk_menu_index');

            $table->dropForeign('permissions_fk_user_foreign');
            $table->dropIndex('permissions_fk_user_index');

        });

        Schema::dropIfExists('permissions');
        Schema::dropIfExists('menu');

    }
}
