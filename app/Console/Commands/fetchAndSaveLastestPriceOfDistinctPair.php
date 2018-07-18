<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\DistinctPair;

class fetchAndSaveLastestPriceOfDistinctPair extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetchAndSaveLastestPriceOfDistinctPair';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cron Job to save latest price of dictinct pairs every 15mins';

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
        DistinctPair::SaveLatestPrice();   
    }
}
