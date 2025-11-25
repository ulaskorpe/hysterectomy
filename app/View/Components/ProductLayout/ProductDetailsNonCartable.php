<?php

namespace App\View\Components\ProductLayout;

use App\Models\Product;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductDetailsNonCartable extends Component
{
    protected Product $product;
    protected bool $popup;
    public function __construct($product, $popup = false)
    {
        $this->product = $product;
        $this->popup = $popup;
    }

    public function render(): View|Closure|string
    {
        return view('components.product-layout.product-details-noncartable',[
            'product' => $this->product,
            'popup' => $this->popup,
        ]);
    }
}
