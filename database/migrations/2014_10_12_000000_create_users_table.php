<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name', 80);
            $table->string('last_name', 80);
            $table->string('email', 50)->unique()->nullable();
            $table->string('password', 255);
            $table->enum('group', ['user', 'admin'])->default('user');
            $table->boolean('active')->default(1);
            $table->string('fb_id', 32)->unique()->nullable();
            $table->string('api_token', 60)->unique()->nullable();
            $table->rememberToken();
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
        Schema::drop('users');
    }
}
