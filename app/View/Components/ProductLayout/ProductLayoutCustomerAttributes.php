<?php

namespace App\View\Components\ProductLayout;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class ProductLayoutCustomerAttributes extends Component
{
    protected Collection $productCustomerAttributes;
    protected ?bool $withLabel;

    public function __construct($productCustomerAttributes, $withLabel = true)
    {
        $this->productCustomerAttributes = $productCustomerAttributes;
        $this->withLabel = $withLabel;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.product-layout.product-layout-customer-attributes',[
            'productCustomerAttributes' => $this->productCustomerAttributes,
            'withLabel' => $this->withLabel,
        ]);
    }
}
