<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$this->call(SourcesTableSeeder::class);
		$this->call(CoinsTableSeeder::class);
		$this->call(CurrencyPairTableSeeder::class);
		$this->call(DistinctPairTableSeeder::class);
	}

}
