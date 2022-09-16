<?php

namespace App\Http\Livewire;

use App\Models\Ads;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Component;

class AuctionList extends Component
{
    protected $queryString = ['searchQuery'];

    public $searchQuery;

    public function render()
    {
        if ($this->queryString) {
            $ads = Ads::where(
                'title',
                'like',
                '%'.Str::of($this->searchQuery)->title()->value().'%'
            )->select(
                'attributes',
                'body',
                'bgg_id',
                'index',
                'author',
                'title',
                'object_id',
                'expansions',
                'condition',
                'condition_comment',
                'version',
                'language',
                'starting_bid',
                'bin',
                'deleted',
                'soft_reserve',
            )->get();
        } else {
            $ads = Cache::remember('ads', Carbon::now()->addMinutes(60), function () {
                return Ads::select(
                    'attributes',
                    'body',
                    'bgg_id',
                    'index',
                    'author',
                    'title',
                    'object_id',
                    'expansions',
                    'condition',
                    'condition_comment',
                    'version',
                    'language',
                    'starting_bid',
                    'bin',
                    'deleted',
                    'soft_reserve',
                )->all();
            });
        }

        return view('livewire.auction-list', compact('ads'));
    }
}
