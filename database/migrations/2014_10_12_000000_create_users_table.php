<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table)
        {
            $table->increments('id');
            $table->string('name')->unique();//Company name
            $table->string('email')->unique();
            $table->string('password');
            $table->string('first');
            $table->string('last');
            $table->string('city')->nullable();
            $table->string('state');
            $table->string('avatar')->nullable();
            $table->enum('role', array('none','Creator','Investor'))->default('none');
            $table->enum('activated', array('0', '1'))->default('0');
            $table->dateTime('lastlogin')->nullable();
            $table->dateTime('notescheck')->nullable();
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
        Schema::dropIfExists('users');
    }
}
