<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSummariesTable extends Migration
{
    public function up()
    {
        Schema::create('summaries', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('user_id')->unique();
            $table->string('product_name');
            $table->string('market');
            $table->string('age_range');
            $table->string('region')->nullable();
            $table->text('market_other')->nullable();
            $table->text('competitor1')->nullable();
            $table->text('competitor2')->nullable();
            $table->text('competitor3')->nullable();
            $table->text('risks')->nullable();
            $table->text('exit_strategy');
            $table->text('ROI');
            $table->text('liquidity')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('summaries');
    }
}
