<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class DisplayLayout extends Component
{
    public string $title = 'Coffee Shop';

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.display');
    }
}
