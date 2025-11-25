<?php

namespace App\View\Components\ProductLayout;

use Closure;
use App\Models\ProductVariant;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class ProductLayoutBasketButtonsVariant extends Component
{
    protected ?ProductVariant $productVariant;
    
    public function __construct($productVariant = null)
    {
        $this->productVariant = $productVariant;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.product-layout.product-layout-basket-buttons-variant',[
            'productVariant' => $this->productVariant,
        ]);
    }
}
