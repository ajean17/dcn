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
            $table->integer('profile_id');
            $table->string('name')->default('Show Me Off');
            $table->string('category')->default('Other');
            $table->string('subCategory')->nullable();
            $table->text('elementOne')->nullable();
            $table->string('oneType')->nullable();
            $table->string('oneName')->default('Element One');
            $table->text('elementTwo')->nullable();
            $table->string('twoType')->nullable();
            $table->string('twoName')->default('Element Two');
            $table->text('elementThree')->nullable();
            $table->string('threeType')->nullable();
            $table->string('threeName')->default('Element Three');
            $table->text('elementFour')->nullable();
            $table->string('fourType')->nullable();
            $table->string('fourName')->default('Element Four');
            $table->text('elementFive')->nullable();
            $table->string('fiveType')->nullable();
            $table->string('fiveName')->default('Element Five');
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
