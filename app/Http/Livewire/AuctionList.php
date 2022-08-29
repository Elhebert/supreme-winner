<?php

namespace App\Http\Livewire;

use App\Models\Ads;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Illuminate\Support\Str;

class AuctionList extends Component
{
    protected $queryString = ['searchQuery'];

    public $searchQuery;

    public function render()
    {
        $ads = Cache::remember('ads', Carbon::now()->addMinutes(30), function () {
            return Ads::where(
                'title',
                'like',
                '%' . Str::of($this->searchQuery)->title()->value() . '%'
            )->get();
        });

        return view('livewire.auction-list', compact('ads'));
    }
}
