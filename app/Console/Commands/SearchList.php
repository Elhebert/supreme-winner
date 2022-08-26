<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SearchList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'essen:search {title?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Search the Essen No Shiping Auction list';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $geekListItems = collect(xml_to_array(Storage::get('list.xml'))['item']);
        $gameTitles = $geekListItems->pluck('@attributes')->pluck('objectname')->sort()->all();

        $formattedGameItem = $geekListItems
            ->map(function ($item, $key) {
                $attributes = $item['@attributes'];
                $body = $item['body'];

                $condition = str($body)->match('/condition.*(Very Good|Good|New|Like New|Acceptable|Unacceptable)/i')->value();
                $version = str($body)->match("/version.*(?:\[u\])(.+)(?:\[\/u\])/i")->value();
                $expansions = str($body)->match("/expansion.*(?:\[thing=\d+\])(.+)(?:\[\/thing\])/i")->value();
                $startingBid = str($body)->match("/Starting bid.*€(\d+)/i")->value();
                $bin = str($body)->match("/bin.*€(\d+)/i")->value();

                return [
                    'index' => $key + 1,
                    'author' => $attributes['username'],
                    'title' => $attributes['objectname'],
                    'expansions' => $expansions ?: 'None',
                    'condition' => $condition,
                    'version' => $version,
                    'starting bid' => "{$startingBid}€",
                    'bin' => $bin ? "{$bin}€" : 'None',
                ];
            });

        if (! $this->argument('title')) {
            dump($gameTitles);

            return 0;
        }

        $searchResults = collect($gameTitles)
            ->filter(fn ($game) => str($game)
            ->lower()
            ->contains($this->argument('title')));

        if (! $searchResults) {
            return 'No game found with that name';
        }

        $searchResults = $searchResults
            ->keys()
            ->map(function ($resultIndex) use ($formattedGameItem) {
                return $formattedGameItem[$resultIndex];
            });

        dump($searchResults);

        return 0;
    }
}
