<?php

namespace App\View\Components;

use Closure;
use Jackiedo\Cart\Cart;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Jackiedo\Cart\Details as CartDetails;

class CartTotals extends Component
{
    protected $cart;
    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cart-totals',[
            'cart' => $this->cart->getDetails()
        ]);
    }
}
