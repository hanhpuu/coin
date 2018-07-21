<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Price;

class fetchAndSaveCurrencyPairDataInPast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetchAndSaveCurrencyPairDataInPast';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cron Job to save all the data in the past every 30mins';

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
        Price::fetchAndSaveAllData();   
    }
}
