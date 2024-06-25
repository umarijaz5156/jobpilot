<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

class Option extends Component
{
    public $selected;

    public $value;

    public $label;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($selected, $value, $label)
    {
        $this->selected = $selected;
        $this->value = $value;
        $this->label = $label;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.forms.option');
    }
}
