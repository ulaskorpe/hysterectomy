<?php

namespace App\View\Components\Cards;

use App\Models\ProductCategory;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductCardCategory extends Component
{
    protected ProductCategory $category;
    public function __construct($category)
    {
        $this->category = $category;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cards.product-category-card',[
            'category' => $this->category
        ]);
    }
}
