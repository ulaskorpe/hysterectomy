<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Jackiedo\Cart\Details as CartDetails;
use Illuminate\View\Component;

class CartCouponGiftVoucher extends Component
{
    protected $cart;

    public function __construct(CartDetails $cart)
    {
        $this->cart = $cart;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cart-coupon-gift-voucher', [
            'cart' => $this->cart,
        ]);
    }
}
