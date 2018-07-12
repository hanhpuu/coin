<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\DistinctPrice;

class SaveDataInPresentEveryFifteen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SaveDataInPresentEveryFifteen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cron Job to save all the data every 15mins';

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
        DistinctPrice::fetchAndSaveDataInPresent();   
    }
}
