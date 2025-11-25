<?php

namespace App\View\Components\ProductLayout;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductLayoutName extends Component
{
    protected string $name;
    protected ?string $sku;
    protected ?int $id;
    public function __construct($name, $sku = "", $id = null)
    {
        $this->name = $name;
        $this->sku = $sku;
        $this->id = $id;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.product-layout.product-layout-name',[
            'name' => $this->name,
            'sku' => $this->sku,
            'id' => $this->id,
        ]);
    }
}
