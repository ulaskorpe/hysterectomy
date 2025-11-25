<?php

namespace App\View\Components\Cards;

use App\Models\Product;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductCard extends Component
{
    protected Product $product;
    protected $popup;
    public function __construct($product, $popup = false)
    {
        $this->product = $product;
        $this->popup = $popup;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cards.product-card',[
            'product' => $this->product,
            'popup' => $this->popup
        ]);
    }
}
