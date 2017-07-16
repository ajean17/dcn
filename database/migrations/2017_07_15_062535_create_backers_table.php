<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBackersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   //For the list of backers for every CREATOR
        Schema::create('backers', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('backer_id')->nullable();//ID of the user being backed
            $table->integer('backing_id')->default('0');//ID of the user backing them if they have an account/ might not
            $table->string('backer_name')->default('nobody real');//Name of the backer->required
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
        Schema::dropIfExists('backers');
    }
}
