<?php

namespace App\View\Components\ProductLayout;

use Closure;
use App\Models\Product;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class ProductLayoutBasket extends Component
{
    protected Product $product;
    protected ?bool $useQuantity;
    protected int $maxQuantity;
    
    public function __construct($product,$useQuantity = true, $maxQuantity = 5)
    {
        $this->product = $product;
        $this->useQuantity = $useQuantity;
        $this->maxQuantity = $maxQuantity;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.product-layout.product-layout-basket',[
            'product' => $this->product,
            'useQuantity' => $this->useQuantity,
            'maxQuantity' => $this->maxQuantity,
        ]);
    }
}
