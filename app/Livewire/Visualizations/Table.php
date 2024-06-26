<?php

namespace App\Livewire\Visualizations;

use App\Livewire\Visualization;
use App\Services\QueryBuilder;
use Livewire\Attributes\On;

class Table extends Visualization
{
    public const DEFAULT_OPTIONS = [
        'columnTypes' => [
            'rangeColumn' => [
                'width' => 150
            ]
        ],
        'columnDefs' => [],
        'rowData' => [],
        'autoSizeStrategy' => [
            'type' => 'fitGridWidth'
        ],
    ];

    public function preparePayload(): void
    {
        $this->options['rowData'] = $this->data;
        $this->options['columnDefs'] = $this->makeColumnDefs(collect($this->data));
    }

    private function makeColumnDefs($data): array
    {
        if ($data->isNotEmpty()) {
            [$toNest, $flat] = collect(array_keys((array)$data->first()))
                ->partition(fn ($header) => str($header)->contains('|'));

            $nested = $toNest
                ->map(function ($joined, $index) {
                    list($parent, $child) = explode('|', $joined);
                    return ['parent' => $parent, 'child' => $child, 'field' => $joined, 'order' => $index];
                })
                ->groupBy('parent')
                ->map(function ($children, $parent) {
                    return (object)[
                        'headerName' => $parent,
                        //'headerHozAlign' => 'center',
                        'order' => min($children->pluck('order')->all()),
                        'children' => $children
                            ->map(function ($c) {
                                $colDef = [
                                    'headerName' => $c['child'],
                                    'field' => $c['field'],
                                ];
                                if (str($c['child'])->endsWith(QueryBuilder::VALUE_COLUMN_INVISIBLE_MARKER)){
                                    /*$colDef['hozAlign'] = 'right';
                                    $colDef['headerHozAlign'] = 'right';
                                    $colDef['sorter'] = 'number';
                                    $colDef['formatter'] = 'money';*/
                                    $colDef['type'] = 'numericColumn';
                                }
                                return (object)$colDef;
                            })
                            ->values()
                            ->all(),
                    ];
                })->values();

            $notNested = $flat
                ->map(function ($column) {
                    $colDef = [
                        'headerName' => str($column)->replace('_', ' ')->ucfirst()->toString(),
                        'field' => $column,
                        'filter' => true
                        //'sorter' => 'string',
                        //'frozen' => true
                    ];
                    if (str($column)->endsWith(QueryBuilder::VALUE_COLUMN_INVISIBLE_MARKER)){
                        //$colDef['hozAlign'] = 'right';
                        //$colDef['headerHozAlign'] = 'right';
                        //$colDef['sorter'] = 'number';
                        //$colDef['formatter'] = 'money';
                        $colDef['type'] = 'numericColumn';
                        unset($colDef['filter']);
                    }
                    if (str($column)->contains('age_group')){
                        //$colDef['sorter'] = 'number';
                        $colDef['type'] = 'rangeColumn';
                    }
                    return $colDef;
                });

            return $nested
                ->concat($notNested)
                ->sortBy('order')
                ->map(fn ($header) => (object)$header)
                ->values()
                ->all();
        }
        return [];
    }

    #[On('changeOccurred')]
    public function reactToChanges($data, $indicatorName, $dataParams): void
    {
        $this->data = $data;
        $this->options = array_replace_recursive($this::DEFAULT_OPTIONS, $this->options, []);
        $this->preparePayload();
        $this->dispatch("updateTable.$this->htmlId", $this->options);
    }

    public function render()
    {
        return view('livewire.visualizations.table');
    }
}
