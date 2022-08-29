<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Str;
use NumberFormatter;

class CurrencyFormatter extends Component
{
    private NumberFormatter $currencyFormatter;
    public $price;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($price)
    {
        $this->currencyFormatter = new NumberFormatter(
            app()->getLocale(),
            NumberFormatter::CURRENCY
        );

        $this->price = $price;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $amount = '-';

        if ($this->price !== '-') {
            $amount = $this->currencyFormatter->formatCurrency((int)$this->price, 'EUR');
        }

        return view('components.currency-formatter', compact('amount'));
    }
}
