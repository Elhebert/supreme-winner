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
            )->get();
        } else {
            $ads = Cache::remember('ads', Carbon::now()->addMinutes(60), function () {
                return Ads::all();
            });
        }

        return view('livewire.auction-list', compact('ads'));
    }
}
