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
        DB::table('coins')->insert([
                ['name' => 'USDT'
            ],
                ['name' => 'BTC'
            ],
                ['name' => 'ETH'
            ]
        ]);
        DB::table('sources')->insert([
                ['name' => 'Binance'
            ]
        ]);
        DB::table('currency_pair')->insert([
                [
                'base_currency_id' => 2,
                'quote_currency_id' => 1,
                'priority' => 1,
                'source_id' => 1
            ],
                [
                'base_currency_id' => 3,
                'quote_currency_id' => 2,
                'priority' => 2,
                'source_id' => 1
            ],
                [
                'base_currency_id' => 3,
                'quote_currency_id' => 1,
                'priority' => 1,
                'source_id' => 1
            ]
        ]);
    }
}
