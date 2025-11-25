<?php

namespace App\View\Components;

use App\Models\ProductCategory;
use App\Models\ProductType;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductList extends Component
{
    protected ?ProductCategory $category;
    protected ?ProductType $productType;
    protected ?array $filters;
    protected int $columnCount;
    
    public function __construct($category = null, $productType = null, $filters = null, $columnCount = 3)
    {
        $this->productType = $productType;
        $this->category = $category;
        $this->filters = $filters;
        $this->columnCount = $columnCount;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.product-list', [
            'productType' => $this->productType,
            'category' => $this->category,
            'filters' => $this->filters,
            'columnCount' => $this->columnCount,
        ]);
    }
}
