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
          $table->integer('user_id')->default('0')->unique();
          $table->enum('hasVideo',array('0','1'))->default('0');
          $table->enum('hasMentor',array('0','1'))->default('0');
          $table->enum('hasInvestments',array('0','1'))->default('0');
          $table->enum('hasROI',array('0','1'))->default('0');
          $table->integer('summary_id')->nullable()->unique();
          $table->integer('proof_Concept_id')->nullable()->unique();
          $table->integer('backer_list_id')->nullable()->unique();
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
