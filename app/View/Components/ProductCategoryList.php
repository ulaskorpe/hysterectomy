<?php

namespace App\View\Components;

use App\Models\ProductSegment;
use App\Models\ProductType;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductCategoryList extends Component
{
    protected ?ProductSegment $segment;
    protected ?ProductType $productType;

    public function __construct($segment = null, $productType = null)
    {
        $this->segment = $segment;
        $this->productType = $productType;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.product-category-list', [
            'segment' => $this->segment,
            'productType' => $this->productType,
        ]);
    }
}
