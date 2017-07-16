<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSummariesTable extends Migration
{
    public function up()
    {
        Schema::create('summaries', function (Blueprint $table)
        {   //For the executive summary
            $table->increments('id');
            $table->integer('user_id')->unique();
            $table->string('product_name');
            $table->string('market');//aka Category
            $table->string('age_range');
            $table->string('region')->nullable();
            $table->text('market_other')->nullable();//Other info related to the market
            $table->text('competitor1')->nullable();//Competitor info
            $table->text('competitor2')->nullable();
            $table->text('competitor3')->nullable();
            $table->text('risks')->nullable();//Risk report
            $table->text('exit_strategy')->nullable();
            $table->text('ROI')->nullable();//Return on investment
            $table->text('liquidity')->nullable();//Business liquidity aka immediate cash
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('summaries');
    }
}
