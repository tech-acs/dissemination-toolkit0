<?php

namespace App\Livewire;

use App\Livewire\Visualizations\Table;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataExplorer extends Component
{
    public ?string $livewireComponent = Table::class;
    public string $indicatorName = '';
    public array $dataParams = [];
    public array $data = [];
    public bool $hasData = false;
    public array $dataShaperSelections = [];

    #[On('dataChanged')]
    public function dataShaperUpdated(array $data, string $indicatorName, array $dataParams)
    {
        $this->data = $data;
        $this->indicatorName = $indicatorName;
        $this->dataParams = $dataParams;
        $this->hasData = ! empty($this->data);
    }

    #[On('selectionMade')]
    public function dataShaperSelectionMade(array $selection)
    {
        $this->dataShaperSelections = $selection;
    }

    public function download(): StreamedResponse
    {
        $filename = str($this->indicatorName)->slug('-')->append('.xlsx')->toString();
        $writer = SimpleExcelWriter::streamDownload($filename)->addRows($this->data);
        return response()->streamDownload(fn() => $writer->close(), $filename);
    }
}
