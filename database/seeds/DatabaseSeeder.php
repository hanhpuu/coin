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
        [   'name' => 'BTCUSDT',
            'base_currency_id' => 2,
            'quote_currency_id' => 1,
            'base_currency' => 'BTC',
            'quote_currency' => 'USDT',
            'priority' => 1
        ],
        [   'name' => 'ETHBTC',
            'base_currency_id' => 3,
            'quote_currency_id' => 2,
            'base_currency' => 'ETH',
            'quote_currency' => 'BTC',
            'priority' => 2
        ],
        [   'name' => 'ETHUSDT',
            'base_currency_id' => 3,
            'quote_currency_id' => 1,
            'base_currency' => 'ETH',
            'quote_currency' => 'USDT',
            'priority' => 1
        ]
        ]);
         
    }
    
    
}
