<?php

namespace App\Console\Commands;

use App\Jobs\RetrieveAdList;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RetrieveList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'essen:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Essen No Shiping Auction list';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        RetrieveAdList::dispatch();

        return 0;
    }
}
