<?php

namespace App\Livewire;

use Livewire\Component;

class TableOptionsShaper extends Component
{
    public array $options = [];

    public array $optionLabels = [
        'pagination' => 'Enable pagination',
        'suppressMovableColumns' => 'Disable movable columns',
        'unSortIcon' => 'Show unsort icon',
        'columnHoverHighlight' => 'Highlight columns on hover'
    ];

    public function apply()
    {
        $options = $this->options;
        unset($options['rowData']);
        $this->dispatch('tableOptionsShaperEvent', options: $options);
    }

    public function render()
    {
        return view('livewire.table-options-shaper');
    }
}
