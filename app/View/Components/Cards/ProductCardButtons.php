<?php

namespace App\View\Components\Cards;

use App\Models\Product;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductCardButtons extends Component
{
    protected Product $product;
    protected $popup;
    public function __construct($product, $popup = false)
    {
        $this->product = $product;
        $this->popup = $popup;
    }

    public function render(): View|Closure|string
    {
        return view('components.cards.product-card-buttons',[
            'product' => $this->product,
            'popup' => $this->popup
        ]);
    }
}
