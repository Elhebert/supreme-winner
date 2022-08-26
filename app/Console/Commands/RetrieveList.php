<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

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
        $response = Http::accept('application/xml')
            ->connectTimeout(60)
            ->get('https://boardgamegeek.com/xmlapi/geeklist/301669', [
                'comments' => 1,
            ]);

        Storage::put('list.xml', $response->body());

        return 0;
    }
}
