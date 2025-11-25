<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PageHeaderStatic extends Component
{
    protected ?string $title;
    protected ?string $subTitle;
    protected ?string $summary;
        
    public function __construct($title = null, $subTitle = null, $summary = null)
    {
        $this->title = $title;
        $this->subTitle = $subTitle;
        $this->summary = $summary;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.page-header-static',[
            'title' => $this->title,
            'subTitle' => $this->subTitle,
            'summary' => $this->summary,
        ]);
    }
}
