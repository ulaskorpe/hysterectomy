<?php

namespace App\View\Components;

use Closure;
use App\Models\ProductType;
use Illuminate\View\Component;
use App\Models\ProductCategory;
use Illuminate\Contracts\View\View;

class ProductFilters extends Component
{

    protected ?ProductCategory $category;
    protected ?ProductType $productType;
    protected ?array $filters;
    protected bool $useActiveFilters;

    public function __construct($category = null, $useActiveFilters = false, $productType = null, $filters = [])
    {

        if (empty($filters)) {
            $filters = [
                "price" => [
                    "label" => __('Ücret'),
                    "active" => false
                ],
                "attributes" => [
                    "label" => __('Özellikler'),
                    "active" => true
                ],
                "categories" => [
                    "label" => __('Kategoriler'),
                    "active" => true
                ],
                "product_types" => [
                    "label" => __('Sertifikalar'),
                    "active" => true
                ]
            ];
        }

        $this->useActiveFilters = $useActiveFilters;
        $this->productType = $productType;
        $this->category = $category;
        $this->filters = $filters;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.product-filters', [
            'useActiveFilters' => $this->useActiveFilters,
            'productType' => $this->productType,
            'category' => $this->category,
            'filters' => $this->filters,
        ]);
    }
}
