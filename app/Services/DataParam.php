<?php

namespace App\Services;

use Illuminate\Contracts\Support\Arrayable;

/**
 * int $datasetId
 * array $geographies [ level => [ areaIds ], ... ]
 * array $years [ yearId1, yearId2, ... ]
 * array $dimensions [ dimensionId => [ dimensionValueIds ]]
 * array $pivotColumn dimensionId
 * int $pivotRow dimensionId
 * int $nestingPivotColumn dimensionId
 */

class DataParam implements Arrayable
{
    public function __construct(
        public int $dataset,
        public array $geographies = [],
        //public bool $fetchGeographicalChildren = false,
        public array $years = [],
        public array $dimensions = [],
        public int|null $pivotColumn = null,
        public int|null $pivotRow = null,
        public int|null $nestingPivotColumn = null
    ) {
    }

    public function toArray(): array
    {
        return [
            'dataset' => $this->dataset,
            'geographies' => $this->geographies,
            //'fetchGeographicalChildren'=> $this->fetchGeographicalChildren,
            'years' => $this->years,
            'dimensions' => $this->dimensions,
            'pivotColumn' => $this->pivotColumn,
            'pivotRow' => $this->pivotRow,
            'nestingPivotColumn' => $this->nestingPivotColumn,
        ];
    }
}
