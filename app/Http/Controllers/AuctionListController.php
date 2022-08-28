<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use NumberFormatter;

class AuctionListController extends Controller
{
    public function index()
    {
        $currencyFormatter = new NumberFormatter(app()->getLocale(), NumberFormatter::CURRENCY);
        $bggCondition = ['Unacceptable', 'Acceptable', 'Good', 'Very Good', 'Like New', 'New'];

        $geekListItems = collect(xml_to_array(Storage::get('list.xml'))['item']);

        $formattedGameItem = $geekListItems
            ->map(function ($item, $key) use ($currencyFormatter, $bggCondition) {
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
                    $condition = $bggCondition[$stars];

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
                    'bggId' => $attributes['id'],
                    'index' => $key + 1,
                    'author' => $attributes['username'],
                    'title' => $attributes['objectname'],
                    'bggLink' => "https://boardgamegeek.com/boardgame/{$attributes['objectid']}/{$attributes['objectname']}",
                    'expansions' => $expansions ?: 'None',
                    'condition' => $condition,
                    'conditionComment' => $conditionComment ?? null,
                    'version' => $version,
                    'language' => $language,
                    'startingBid' => $startingBid ? $currencyFormatter->formatCurrency($startingBid, 'EUR') : '-',
                    'bin' => $bin ? $currencyFormatter->formatCurrency($bin, 'EUR') : '-',
                    'deleted' => $deleted,
                    'softReserve' => $softReserve ? $currencyFormatter->formatCurrency($softReserve, 'EUR') : '-',
                ];
            });

        return view('auction-list', ['list' => $formattedGameItem]);
    }
}
