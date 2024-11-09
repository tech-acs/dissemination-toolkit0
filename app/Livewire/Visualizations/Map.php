<?php

namespace App\Livewire\Visualizations;

use App\Livewire\Visualization;
use App\Traits\AreaResolver;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;

class Map extends Visualization
{
    use AreaResolver;

    public const DEFAULT_CONFIG = [
        'responsive' => true,
        'displaylogo' => false,
        'modeBarButtonsToRemove' => ['select2d', 'lasso2d', 'autoScale2d', 'hoverClosestCartesian', 'hoverCompareCartesian'],
    ];
    public array $config;
    public string $filterPath = '';

    public function mount(): void
    {
        parent::mount();
        $this->config = $this->makeConfig();
    }

    public function makeTraces(Collection $rawData): array
    {
        $traces = $this->data;
        $data = toDataFrame($rawData);
        logger('dump', ['traces' => $traces, 'df' => $data]);
        if ($data->isNotEmpty()) {
            foreach ($traces as $index => $trace) {
                $columnNames = Arr::get($trace, 'meta.columnNames', []);
                /*if ($columnNames) {
                    $traces[$index]['x'] = $data[$columnNames['x']] ?? null;
                    $traces[$index]['y'] = $data[$columnNames['y']] ?? null;
                }*/
                foreach($columnNames as $key => $columnName) {
                    if (! is_array($columnName)) {
                        $traces[$index][$key] = $data[$columnName] ?? null;
                    }
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

    #[On('dataShaperEvent')]
    public function reactToChanges(array $rawData, string $indicatorName, array $dataParams): void
    {
        //$this->layout = array_replace_recursive($this::DEFAULT_OPTIONS, $this->layout, []);
        $this->preparePayload($rawData);
        $this->dispatch("updateChart.$this->htmlId", $this->data, $this->layout);
    }

    /*#[On(['filterChanged'])]
    public function update()
    {
        list($this->filterPath,) = $this->areaResolver();
        $this->data = $this->getTraces($data, $this->filterPath);
        $this->layout = $this->getLayout($this->filterPath);
    }*/

    public function render()
    {
        return view('livewire.visualizations.chart');
    }
}
