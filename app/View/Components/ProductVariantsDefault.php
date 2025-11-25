<?php

namespace App\View\Components;

use App\Models\Product;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductVariantsDefault extends Component
{
    protected Product $product;
    public function __construct($product)
    {
        $this->product = $product;
    }

    public function render(): View|Closure|string
    {
        return view('components.product-variants-default',[
            'product' => $this->product,
        ]);
    }
}
