<?php

namespace App\Livewire;

use App\Livewire\Visualizations\Table;
use Livewire\Component;

class Visualizer extends Component
{
    public array $data = [];
    public array $options = [];
    public string $designatedComponent = Table::class;

    public function render()
    {
        return view('livewire.visualizer');
    }
}
