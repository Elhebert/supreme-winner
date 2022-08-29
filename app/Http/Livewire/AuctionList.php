<?php

namespace App\Http\Livewire;

use App\Models\Ads;
use Livewire\Component;
use Illuminate\Support\Str;

class AuctionList extends Component
{
    protected $queryString = ['searchQuery'];

    public $searchQuery;

    public function render()
    {
        $ads = Ads::where(
            'title',
            'like',
            '%' . Str::of($this->searchQuery)->title()->value() . '%'
        )->get();

        return view('livewire.auction-list', compact('ads'));
    }
}
