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
        [    'name' => 'USDT'
        ],
        [    'name' => 'BTC'
        ],
        [    'name' => 'ETH'
        ]
        ]);
         DB::table('currency_pair')->insert([
        [   'base_currency_id' => 2,
            'quote_currency_id' => 1,
            'name' => 'BTCUSDT'
        ],
        [   'base_currency_id' => 3,
            'quote_currency_id' => 2,
            'name' => 'BTCUSDT'
        ]
        ]);
         
    }
    
    
}
