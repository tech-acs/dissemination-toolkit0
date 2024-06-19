<?php

namespace App\Livewire;

use App\Models\Topic;
use Livewire\Component;

class TopicSelector extends Component
{
    public $topics;

    public function mount()
    {
        $this->topics = Topic::all();
    }
    public function render()
    {
        return view('livewire.topic-selector');
    }
}
