<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id');
            $table->string('path');
            $table->string('name');
            $table->string('redirect')->default('');
            $table->string('title');
            $table->unsignedInteger('_lft');
            $table->unsignedInteger('_rgt');
            $table->string('icon')->default('');
            $table->string('component');
            $table->string('permission');
            $table->unsignedInteger('parent_id')->nullable();
            $table->unsignedTinyInteger('sort');
            $table->unsignedTinyInteger('status')->default(0);
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
        Schema::dropIfExists('menus');
    }
};
