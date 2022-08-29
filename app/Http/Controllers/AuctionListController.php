<?php

namespace App\Http\Controllers;

class AuctionListController extends Controller
{
    public function index()
    {
        return view('auction-list');
    }
}
