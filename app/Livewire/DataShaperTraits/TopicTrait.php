<?php

namespace App\Livewire\DataShaperTraits;

use App\Models\Topic;

trait TopicTrait {
    public array $topics = [];
    public int $selectedTopic = 0;

    public function mountTopicTrait()
    {
        $this->topics = Topic::has('indicators')->pluck('name', 'id')->all();
    }

    public function updatedSelectedTopic(int $topicId): void
    {
        $this->reset('selectedIndicator', 'selectedDataset', 'datasets', 'selectedGeographyLevel',
            'geographyLevels', 'geographies', 'selectedGeographies', 'years', 'selectedYears',
            'dimensions', 'selectedDimensions', 'selectedDimensionValues', 'pivotableDimensions', 'pivotColumn', 'pivotRow', 'nestingPivotColumn');
        $topic = Topic::find($topicId);
        $this->indicators = $topic?->indicators->pluck('name', 'id')->all() ?? [];
        $this->nextSelection = 'indicator';

        $this->dispatch('dataShaperSelectionMade', $this->makeReadableDataParams('topic', $topic->name));
    }
}
