<?php

namespace App\Livewire;

use App\Livewire\Visualizations\Table;
use Livewire\Component;

class Visualizer extends Component
{
    public string $designatedComponent = Table::class;
    public array $rawData = [];
    public array $data = [];
    public array $layout = [];
    public array $options = [];
    public int $vizId;

    public function render()
    {
        return view('livewire.visualizer');
    }
}
