<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('profiles', function (Blueprint $table)
      {
          $table->increments('id');
          $table->string('username');
          $table->enum('hasVideo',array('0','1'))->default('0');
          $table->enum('hasMentor',array('0','1'))->default('0');
          $table->enum('hasInvestments',array('0','1'))->default('0');
          $table->enum('hasROI',array('0','1'))->default('0');
          $table->integer('projectOne')->nullable()->unique();
          $table->integer('projectTwo')->nullable()->unique();
          //$table->enum('type', array('inventor', 'investor', 'naive'))->default('naive');
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
        Schema::dropIfExists('profiles');
    }
}
