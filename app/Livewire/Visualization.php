<?php

namespace App\Livewire;

use App\Services\QueryBuilder;
use App\Services\Sorter;
use Livewire\Component;

abstract class Visualization extends Component
{
    public const DEFAULT_OPTIONS = [];
    public const EDITABLE_OPTIONS = [];

    public string $htmlId;
    public ?int $vizId;
    public array $data = [];
    public array $options = [];

    /*protected function getListeners(): array
    {
        return [
            'dataChanged' => 'updateData',
            'optionsChanged' => 'updateOptions',
        ];
    }*/

    public function mount(int $vizId = null): void
    {
        $this->htmlId = 'viz-' . str()->random(5);

        if ($vizId) {
            $visualization = \App\Models\Visualization::find($vizId);
            $this->options = $visualization->options;
            $query = new QueryBuilder($visualization->data_params);
            $this->data = Sorter::sort($query->get())->all();
        }
        $this->options = array_replace_recursive($this::DEFAULT_OPTIONS, $this->options);
        $this->preparePayload();
        //dump('At mount', $this->data, $this->options);
    }

    /*#[On('thumbnailCaptureRequested')]
    public function captureThumbnail($name):void
    {
        $this->dispatch("captureThumbnail.$this->visualizationId", $name);
    }*/

    abstract public function reactToChanges($data, $indicatorName, $dataParams);

    abstract public function preparePayload(): void;
}
