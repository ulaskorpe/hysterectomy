<?php

namespace App\View\Components\ProductLayout;

use App\Models\Product;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductLayoutReadMoreButton extends Component
{
    protected Product $product;
    protected array $element;
    protected bool $popup;
    public function __construct($product,$element,$popup = false)
    {
        $this->product = $product;
        $this->element = $element;
        $this->popup = $popup;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.product-layout.product-layout-read-more-button',[
            'product' => $this->product,
            'element' => $this->element,
            'popup' => $this->popup,
        ]);
    }
}
