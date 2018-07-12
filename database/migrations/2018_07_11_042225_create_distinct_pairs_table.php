<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistinctPairsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distinct_pairs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('base_id')->unsigned();
            $table->integer('quote_id')->unsigned();
            $table->integer('priority');
			$table->integer('distinct_price');
            $table->datetime('date_completed')->default('2010-01-01 00:00:00');
            $table->integer('source_id')->unsigned()->default('1');
            
            $table->foreign('base_id')->references('id')->on('coins')->onDelete('cascade');
            $table->foreign('quote_id')->references('id')->on('coins')->onDelete('cascade');
            $table->foreign('source_id')->references('id')->on('sources')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('distinct_pairs', function(Blueprint $table) {
	    $table->dropforeign('distinct_pair_base_id_foreign');
            $table->dropforeign('distinct_pair_quote_id_foreign');
            $table->dropforeign('distinct_pair_source_id_foreign');
	});
        Schema::dropIfExists('distinct_pairs');
        
    }
}
