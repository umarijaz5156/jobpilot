<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

class SwitchInput extends Component
{
    public $checked;

    public $name;

    public $value;

    public $button;

    public $oldvalue;

    public $onText;

    public $offText;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($checked, $name, $value, $onText, $offText, $button, $oldvalue)
    {
        $this->checked = $checked;
        $this->name = $name;
        $this->value = $value;
        $this->button = $button;
        $this->oldvalue = $oldvalue;
        $this->onText = $onText;
        $this->offText = $offText;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.forms.switch-input');
    }
}
