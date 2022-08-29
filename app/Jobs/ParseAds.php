<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ParseAds implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private array $adList;

    public const bggCondition = [
        'Unacceptable',
        'Acceptable',
        'Good',
        'Very Good',
        'Like New',
        'New',
    ];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $adList)
    {
        $this->adList = $adList;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        collect($this->adList)
            ->map(function ($item, $key) {
                $attributes = $item['@attributes'];
                $body = str($item['body'])
                    ->replaceMatches('/\[\/?(size|url|color)\]/i', '')
                    ->replaceMatches('/\[\/?(b|u|c)\]/i', '')
                    ->replaceMatches('/\[size=\d+\]/i', '')
                    ->replaceMatches('/\[COLOR=\#[\d\w]{6}\]/i', '')
                    ->replaceMatches('/\[url=[a-z0-9:\/\._-]+\]/i', '');

                $stars = $body->match("/condition:?\s?\n?(.*)/i")->substrCount(':star:');
                $nostars = $body->match("/condition:?\s?\n?(.*)/i")->substrCount(':nostar:');

                $condition = '';
                $conditionComment = '';

                if ($stars + $nostars === 5) {
                    $conditionComment = $body
                        ->replaceMatches('/(:star:|:nostar:)/i', '')
                        ->match("/condition:?\s?\n?[\w\s]*\((.*)\)/i")->value();
                    $condition = self::bggCondition[$stars];

                    $body = $body->replaceMatches('/(:star:|:nostar:)/i', '')->replaceMatches("/condition:?\s?\n?[\w\s]*\((.*)\)/i", '');
                } else {
                    $condition = $body
                        ->replaceMatches('/(:star:|:halfstar:|:nostar:)/i', '')
                        ->match("/condition\s?:?\s?\n?(.*)/i")
                        ->title();

                    $conditionComment = $condition->match("/\((.*)\)/");
                    $condition = $condition->replaceMatches("/\((.*)\)/", '');
                    $body = $body->replaceMatches("/condition:?\s?\n?(.*)/i", '');
                }

                $condition = str($condition)->trim(' ')->value();

                $version = $body->match("/version:?\s?\n?(.+)/i")->title()->value();
                $language = $body->match("/languages?:?\s?\n?(.+)/i")->title();

                if ($language->is('English')) {
                    $language = $language->prepend('🇬🇧 ');
                } elseif ($language->is('German')) {
                    $language = $language->prepend('🇩🇪 ');
                } elseif ($language->is('French')) {
                    $language = $language->prepend('🇫🇷 ');
                } elseif ($language->is('Dutch')) {
                    $language = $language->prepend('🇳🇱 ');
                } elseif ($language->is('Italian')) {
                    $language = $language->prepend('🇮🇹 ');
                } elseif ($language->is('Spanish')) {
                    $language = $language->prepend('🇪🇸 ');
                }

                $expansions = $body->match("/expansion.*(?:\[thing=\d+\])(.+)(?:\[\/thing\])/i")->title()->value();
                $softReserve = $body->match("/soft reserve.*€?(\d+)/i")->value();
                $bin = $body->match("/bin.*€(\d+)/i")->value();
                $startingBid = $body->match("/starting bid.*€?(\d+)/i")->value();
                $bin = $body->match("/bin.*€(\d+)/i")->value();
                $deleted = $body->lower()->contains(['sold to']) || $body->lower()->startsWith('[-]');

                return [
                    'attributes' => $item['@attributes'],
                    'body' => $item['body'],
                    'bgg_id' => $attributes['id'],
                    'index' => $key + 1,
                    'author' => $attributes['username'],
                    'title' => $attributes['objectname'],
                    'object_id' => $attributes['objectid'],
                    'expansions' => $expansions ?: 'None',
                    'condition' => $condition,
                    'condition_comment' => $conditionComment ?? null,
                    'version' => $version,
                    'language' => $language->value(),
                    'starting_bid' => $startingBid ?: '-',
                    'bin' => $bin ?: '-',
                    'deleted' => $deleted,
                    'soft_reserve' => $softReserve ?: '-',
                ];
            })
            ->each(fn ($item) => SaveAdInDatabase::dispatch($item));
    }
}
