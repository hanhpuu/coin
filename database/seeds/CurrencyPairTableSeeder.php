<?php

use Illuminate\Database\Seeder;

class CurrencyPairTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('currency_pair')->insert([
                [
                'base_id' => 2,
                'quote_id' => 1,
                'priority' => 1,
                'source_id' => 1
            ],
                [
                'base_id' => 3,
                'quote_id' => 2,
                'priority' => 2,
                'source_id' => 1
            ],
                [
                'base_id' => 3,
                'quote_id' => 1,
                'priority' => 1,
                'source_id' => 1
            ]
        ]);
    }
}
