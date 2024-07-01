<?php

namespace App\Livewire\Visualizations;

use App\Livewire\Visualization;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;

class Chart extends Visualization
{
    public const DEFAULT_CONFIG = [
        'responsive' => true,
        'displaylogo' => false,
        'modeBarButtonsToRemove' => ['select2d', 'lasso2d', 'autoScale2d', 'hoverClosestCartesian', 'hoverCompareCartesian'],
    ];
    public array $config;

    public function mount(): void
    {
        parent::mount();
        $this->config = $this->makeConfig();
    }

    public function makeTraces(Collection $rawData): array
    {
        $traces = $this->data;
        $data = toDataFrame($rawData);
        //logger('dump', ['traces' => $traces, 'df' => $data]);
        if ($data->isNotEmpty()) {
            foreach ($traces as $index => $trace) {
                $columnNames = Arr::get($trace, 'meta.columnNames', null);
                if ($columnNames) {
                    $traces[$index]['x'] = $data[$columnNames['x']] ?? null;
                    $traces[$index]['y'] = $data[$columnNames['y']] ?? null;
                }
            }
        } else {
            $traces = [];
        }
        return $traces;
    }

    public function makeConfig(): array
    {
        $dynamicOptions = ['toImageButtonOptions' => ['filename' => $this->htmlId . ' (' . now()->toDayDateTimeString() . ')'], 'locale' => app()->getLocale()];
        return array_merge(self::DEFAULT_CONFIG, $dynamicOptions);
    }

    public function preparePayload(array $rawData = []): void
    {
        $this->data = $this->makeTraces(collect($rawData));
    }

    #[On('changeOccurred')]
    public function reactToChanges(array $rawData, string $indicatorName, array $dataParams): void
    {
        //$this->layout = array_replace_recursive($this::DEFAULT_OPTIONS, $this->layout, []);
        $this->preparePayload($rawData);
        $this->dispatch("updateChart.$this->htmlId", $this->data, $this->layout);
    }

    public function render()
    {
        return view('livewire.visualizations.chart');
    }
}
