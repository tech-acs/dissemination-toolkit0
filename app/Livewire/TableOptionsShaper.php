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
    public ?int $sortColumn = null;
    public string $sortDirection = 'asc';

    public function mount()
    {
        $this->sortColumn = collect($this->options['columnDefs'])->reduce(function ($carry, $columnDef, $index) {
            //dump($columnDef, $index);
            return property_exists($columnDef, 'sort') ? $index : $carry;
        });
    }

    public function apply()
    {
        if ($this->sortColumn) {
            $this->options['columnDefs'][$this->sortColumn]->sort = $this->sortDirection;
        }
        $options = $this->options;
        unset($options['rowData']);
        $this->dispatch('tableOptionsShaperEvent', options: $options);
    }

    public function render()
    {
        //dump($this->options, $this->sortColumn);
        return view('livewire.table-options-shaper');
    }
}
