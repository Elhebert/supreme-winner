<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class RetrieveAdList implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $response = Http::accept('application/xml')
            ->connectTimeout(60)
            ->get('https://boardgamegeek.com/xmlapi/geeklist/301669', [
                'comments' => 1,
            ]);

        if (
            Str::of($response->body())
                ->contains('Your request for this geeklist has been accepted and will be processed.')
        ) {
            self::dispatch()->delay(Carbon::now()->addSeconds(30));
            return;
        }

        ParseAds::dispatch(xml_to_array($response->body())['item']);
    }
}
