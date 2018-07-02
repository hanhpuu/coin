<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\CurrencyPair;
use App\Price;
use Log;

class SaveDataInPast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'saveDataInPast';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cron Job to save all the data in the past';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
            Price::saveAveragePriceFromAPICall('KEY','BTC');
        
        
    }
}
