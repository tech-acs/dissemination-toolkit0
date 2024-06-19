<?php

namespace App\Livewire\DataShaperTraits;

trait PivotingTrait {
    public array $pivotableDimensions = [];
    public int|null $pivotColumn = null;
    public int|null $pivotRow = null;
    public int|null $nestingPivotColumn = null;
}
