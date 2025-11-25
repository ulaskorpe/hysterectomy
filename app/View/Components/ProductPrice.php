<?php

namespace App\View\Components;

use App\Models\Product;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductPrice extends Component
{
    protected $product;
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function render(): View|Closure|string
    {
        return view('components.product-price',[
            'product' => $this->product,
        ]);
    }
}
