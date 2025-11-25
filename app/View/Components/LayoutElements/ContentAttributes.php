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

class ContentAttributes extends Component
{
    protected Content|ContentCategory|Campaign|Product|ProductCategory|ContentPreview|null $content;
    protected array $element;
    public function __construct($content = null,$element)
    {
        $this->content = $content;
        $this->element = $element;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.layout-elements.content-attributes',[
            'content' => $this->content,
            'element' => $this->element
        ]);
    }
}