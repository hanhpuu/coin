<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('currency_pair_id')->unsigned();
            $table->string('name');
            $table->bigInteger('openning_date_in_unix');
            $table->datetime('openning_date');
            $table->float('open',18,10)->nullable();
            $table->float('high',18,10)->nullable();
            $table->float('low',18,10)->nullable();
            $table->float('close',18,10)->nullable();
            $table->float('quote_open',18,10);
            $table->float('quote_high',18,10);
            $table->float('quote_low',18,10);
            $table->float('quote_close',18,10);
            $table->datetime('closing_date');
            $table->float('average',30,10);
            
            $table->index('name');
            $table->index('openning_date');
            $table->index('openning_date_in_unix');
            $table->foreign('currency_pair_id')->references('id')->on('currency_pair')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prices', function(Blueprint $table) {
	    $table->dropforeign('prices_currency_pair_id_foreign');
	});
        Schema::dropIfExists('prices');
    }
}
