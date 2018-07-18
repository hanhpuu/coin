<?php

use Illuminate\Database\Seeder;

class CoinsTableSeeder extends Seeder
{

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('coins')->insert([
				['name' => 'USDT'
			],
				['name' => 'BTC'
			],
				['name' => 'ETH'
			],
				['name' => 'SALT'
			],
				['name' => 'DENT'
			]
		]);
	}

}
