<?php

namespace App\View\Components\ProductLayout;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductLayoutAttributes extends Component
{
    protected array $productAttributes;
    protected ?bool $withLabel;

    public function __construct($productAttributes, $withLabel = true)
    {
        $this->productAttributes = $productAttributes;
        $this->withLabel = $withLabel;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.product-layout.product-layout-attributes',[
            'productAttributes' => $this->productAttributes,
            'withLabel' => $this->withLabel,
        ]);
    }
}
