<?php

namespace App\View\Components\ProductLayout;

use Closure;
use App\Models\Product;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class ProductLayoutBasketButtonsBasic extends Component
{
    protected Product $product;
    
    public function __construct($product)
    {
        $this->product = $product;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.product-layout.product-layout-basket-buttons-basic',[
            'product' => $this->product,
        ]);
    }
}
