<?php

namespace App\Http\Livewire;

use Illuminate\Support\Collection;
use Livewire\Component;

class AuctionList extends Component
{
    public Collection $list;

    protected $queryString = ['searchQuery'];

    public $searchQuery;

    public function render()
    {
        return view('livewire.auction-list');
    }

    public function getFilteredListProperty()
    {
        if (!$this->searchQuery) {
            return  $this->list;
        }

        return $this->list->filter(function ($game) {
            return str($game['title'])->lower()->contains(str($this->searchQuery)->lower()->value());
        });
    }

    public function mount(Collection $list)
    {
        $this->list = $list;
    }
}
