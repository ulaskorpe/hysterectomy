<?php

namespace App\View\Components\ProductLayout;

use App\Models\Form;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductLayoutContact extends Component
{
    
    protected ?Form $contactForm;

    public function __construct($contactForm = null)
    {
        $this->contactForm = $contactForm;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.product-layout.product-layout-contact',[
            'contactForm' => $this->contactForm
        ]);
    }
}
