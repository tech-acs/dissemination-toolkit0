<?php

namespace App\Livewire\DataShaperTraits;

use Uneca\Scaffold\Models\Area;
use Uneca\Scaffold\Services\AreaTree;
use Livewire\Attributes\Computed;

trait GeographyTrait
{
    public int $max_area_level = 0;
    public array $geographyLevels = [];
    public int $selectedGeographyLevel = 0;

    public array $geographies = [];
    public array $selectedGeographies = [];

    public bool $fetchGeographicalChildren = false;

    #[Computed]
    public function showFetchGeographicalChildren(): bool
    {
        return $this->selectedGeographyLevel < $this->max_area_level && ((count($this->geographies) == 1) || (count($this->selectedGeographies) == 1));
    }
    public function resetFetchGeographicalChildren(): void
    {
        unset($this->fetchGeographicalChildren);
    }
    public function updatedSelectedGeographyLevel(int $level): void
    {
        $this->geographies = Area::ofLevel($level)->pluck('name', 'id')->all();
        $this->reset('selectedGeographies');
        $this->nextSelection = 'year';
        unset($this->showfetchGeographicalChildren);
        $this->dispatch('selectionMade', $this->makeReadableDataParams('geography', (new AreaTree)->hierarchies[$level]));
    }

    public function updatedSelectedGeographies($geographyId): void
    {
        $this->nextSelection = 'year';
    }
}
