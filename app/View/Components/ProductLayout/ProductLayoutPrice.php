<?php

namespace App\View\Components\ProductLayout;

use App\Models\Product;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductLayoutPrice extends Component
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
        return view('components.product-layout.product-layout-price',[
            'product' => $this->product
        ]);
    }
}
