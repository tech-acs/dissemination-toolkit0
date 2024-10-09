<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class StateRecorder extends Component
{
    #[On('dataShaperEvent')]
    public function recordDataShape(array $rawData, string $indicatorName, array $dataParams)
    {
        $resource = session()->get('viz-wizard-resource');
        $resource->dataSources = toDataFrame(collect($rawData))->toArray();
        $resource->indicatorTitle = $indicatorName;
        $resource->dataParams = $dataParams;
        $resource->rawData = $rawData;
        session()->put('viz-wizard-resource', $resource);
    }

    /*#[On('chartDesignerStateChanged')]
    public function recordChartDesign(array $data, array $layout)
    {
        $resource = session()->get('viz-wizard-resource');
        $resource->data = $data;
        $resource->layout = $layout;
        logger('state change', ['layout' => $layout]);
        session()->put('viz-wizard-resource', $resource);
    }*/

    #[On('thumbnailCaptured')]
    public function recordThumbnail(string $imageData)
    {
        $resource = session()->get('viz-wizard-resource');
        $resource->thumbnail = $imageData;
        session()->put('viz-wizard-resource', $resource);
    }

    #[On('tableOptionsShaperEvent')]
    public function recordOptions(array $options): void
    {
        $resource = session()->get('viz-wizard-resource');
        $resource->options = array_replace_recursive($resource->options, $options);
        session()->put('viz-wizard-resource', $resource);
    }

    public function render()
    {
        return <<<'blade'
            <div></div>
        blade;
    }
}
