<?php

use Illuminate\Database\Seeder;

class DistinctPairTableSeeder extends Seeder
{

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('distinct_pairs')->insert([
				[
				'base_id' => 2,
				'quote_id' => 1,
				'priority' => 1,
				'initial_price' => 6500,
				'latest_price' => 6500,
				'source_id' => 1,
				'potential_group_id' => 1,
			],
				[
				'base_id' => 4,
				'quote_id' => 2,
				'priority' => 2,
				'initial_price' => 1,
				'latest_price' => 1,
				'source_id' => 1,
				'potential_group_id' => 2,
			],
				[
				'base_id' => 5,
				'quote_id' => 2,
				'priority' => 2,
				'initial_price' => 1,
				'latest_price' => 1,
				'source_id' => 1,
				'potential_group_id' => 2,
			],
		]);
	}

}
