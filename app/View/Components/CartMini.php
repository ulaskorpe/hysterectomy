<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Jackiedo\Cart\Cart;
use Illuminate\Contracts\View\View;

class CartMini extends Component
{
    protected $cart;
    protected $removeButtons;

    public function __construct(Cart $cart,bool $removeButtons = true)
    {
        $this->cart = $cart;
        $this->removeButtons = $removeButtons;

    }

    public function render(): View|Closure|string
    {
        return view('components.cart-mini',[
            'cart' => $this->cart->getDetails(),
            'removeButtons' => $this->removeButtons
        ]);
    }
}
