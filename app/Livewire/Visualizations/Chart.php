<?php

namespace App\Livewire\Visualizations;

use App\Livewire\Visualization;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

class Chart extends Visualization
{
    public const DEFAULT_CONFIG = [
        'responsive' => true,
        'displaylogo' => false,
        'modeBarButtonsToRemove' => ['select2d', 'lasso2d', 'autoScale2d', 'hoverClosestCartesian', 'hoverCompareCartesian'],
    ];
    public array $traces = [];

    public function makeTraces(): array
    {

    }

    #[Computed]
    public function config(): array
    {
        $dynamicOptions = ['toImageButtonOptions' => ['filename' => $this->htmlId . ' (' . now()->toDayDateTimeString() . ')'], 'locale' => app()->getLocale(),];
        return array_merge(self::DEFAULT_CONFIG, $dynamicOptions);
    }

    #[On('changeOccurred')]
    public function reactToChanges($data, $indicatorName, $dataParams): void
    {
        $this->data = $data;
        $this->options = array_replace_recursive($this::DEFAULT_OPTIONS, $this->options, []);
        $this->preparePayload();
        $this->dispatch("updateChart.$this->htmlId", $this->traces, $this->options);
    }

    public function render()
    {
        return view('livewire.visualizations.chart');
    }

    public function preparePayload(): void
    {
        // TODO: Implement preparePayload() method.
    }
}
