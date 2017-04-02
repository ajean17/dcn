<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('profileId');
            $table->string('name')->default('Show Me Off');
            $table->string('category')->default('Other');
            $table->string('subCategory')->nullable();
            $table->text('elementOne')->nullable();
            $table->string('oneType')->nullable();
            $table->text('elementTwo')->nullable();
            $table->string('twoType')->nullable();
            $table->text('elementThree')->nullable();
            $table->string('threeType')->nullable();
            $table->text('elementFour')->nullable();
            $table->string('fourType')->nullable();
            $table->text('elementFive')->nullable();
            $table->string('fiveType')->nullable();
            //$table->enum('category', array('fill with info'))->default('naive');
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
        Schema::dropIfExists('projects');
    }
}
