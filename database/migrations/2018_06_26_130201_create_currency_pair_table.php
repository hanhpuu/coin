<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrencyPairTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currency_pair', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->integer('base_currency_id')->unsigned();
            $table->integer('quote_currency_id')->unsigned();
            $table->string('base_currency');
            $table->string('quote_currency');
            $table->integer('priority');
            $table->boolean('cron_past_completed')->default(FALSE);
            $table->boolean('cron_present_completed')->default(FALSE);
            
            $table->foreign('base_currency_id')->references('id')->on('coins')->onDelete('cascade');
            $table->foreign('quote_currency_id')->references('id')->on('coins')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('currency_pair', function(Blueprint $table) {
	    $table->dropforeign('currency_pair_base_currency_id_foreign');
            $table->dropforeign('currency_pair_quote_currency_id_foreign');
	});
        Schema::dropIfExists('currency_pair');
        
    }
}
