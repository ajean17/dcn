<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePortfoliosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('portfolios', function (Blueprint $table)
        {
            $table->increments('id');
            $table->string('username');
            /*
            $table->enum('hasStuff',array('0','1'))->default('0');
            $table->enum('to',array('0','1'))->default('0');
            $table->enum('search',array('0','1'))->default('0');
            $table->enum('by',array('0','1'))->default('0');
            */
            $table->integer('projectID')->nullable()->unique();
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
        Schema::dropIfExists('portfolios');
    }
}
