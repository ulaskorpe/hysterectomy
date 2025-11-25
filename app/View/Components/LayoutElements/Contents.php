<?php

namespace App\View\Components\LayoutElements;

use Closure;
use App\Models\Content;
use App\Models\Product;
use App\Models\Campaign;
use App\Models\ContentPreview;
use Illuminate\View\Component;
use App\Models\ContentCategory;
use App\Models\ProductCategory;
use Illuminate\Contracts\View\View;

class Contents extends Component
{
    protected Content|ContentCategory|Campaign|Product|ProductCategory|ContentPreview|null $content;
    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.layout-elements.contents',[
            'content' => $this->content
        ]);
    }
}
