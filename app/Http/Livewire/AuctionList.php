<?php

namespace App\Http\Livewire;

use Livewire\Component;

class AuctionList extends Component
{
    public $list;

    protected $queryString = ['searchQuery'];

    public $searchQuery;

    public function render()
    {
        return view('livewire.auction-list');
    }

    public function getFilteredListProperty()
    {
        if (!$this->searchQuery) {
            return $this->list;
        }

        return collect($this->list)->filter(function ($game) {
            return str($game['title'])->lower()->contains(str($this->searchQuery)->lower()->value());
        })->all();
    }

    public function mount($list)
    {
        $this->list = $list;
    }
}
