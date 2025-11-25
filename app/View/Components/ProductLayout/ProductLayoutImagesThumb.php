<?php

namespace App\View\Components\ProductLayout;

use Closure;
use App\Models\Product;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class ProductLayoutImagesThumb extends Component
{
    protected array $images;
    protected string $name;
    protected bool $useMainImage;

    public function __construct($images,$name,$useMainImage = false)
    {
        $this->images = $images;
        $this->name = $name;
        $this->useMainImage = $useMainImage;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.product-layout.product-layout-images-thumb',[
            'images' => $this->images,
            'name' => $this->name,
            'useMainImage' => $this->useMainImage,
        ]);
    }
}
