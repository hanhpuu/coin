<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('users')->insert([
				['name' => 'Cua đẹp trai',
				 'email' => 'kiemt95@gmail.com',
				 'password'	=> Hash::make('Dungmatdam123')		
			]
		]);
	}

}
