<?php

namespace App\Livewire;

use App\Services\AreaTree;
use App\Traits\AreaResolver;
use App\Traits\ChecksumSafetyTrait;
use Livewire\Component;

class AreaFilter extends Component
{
    use ChecksumSafetyTrait;
    use AreaResolver;

    public array $dropdowns;

    public function mount()
    {
        $areaTree = new AreaTree();
        $selectionsFromSession = session()->get('area-filter', []);
        $restrictions = [];
        $subject = null;
        $this->dropdowns = array_map(function ($level) use ($selectionsFromSession, $restrictions, $areaTree, &$subject) {
            $dropdown = ['list' => [], 'selected' => null, 'restricted' => null];
            $levelName = $areaTree->hierarchies[$level];
            if ($level === 0) {
                $dropdown['list'] = $areaTree->areas(checksumSafe: true)->pluck('name', 'path')->all();
            }
            if ($subject) {
                $dropdown['list'] = $areaTree->areas($subject, checksumSafe: true)->pluck('name', 'path')->all();
                $subject = null;
            }
            if (array_key_exists($levelName, $selectionsFromSession)) {
                $subject = $selectionsFromSession[$levelName];
                $dropdown['selected'] = $this->addChecksumSafety($subject);
            }
            if (array_key_exists($levelName, $restrictions)) {
                $subject = $restrictions[$levelName];
                $dropdown['restricted'] = $this->addChecksumSafety($subject);
            }
            return $dropdown;
        }, array_flip($areaTree->hierarchies));
    }

    public function changeHandler($changedLevelName, $selectedPath)
    {
        $areaTree = new AreaTree(removeLastNLevels: 1);
        $shouldUpdate = false;
        $resetDownstream = false;
        foreach ($this->dropdowns as $levelName => $dropdown) {
            if ($resetDownstream) {
                $this->dropdowns[$levelName]['list'] = [];
                $this->dropdowns[$levelName]['selected'] = null;
                continue;
            }
            if ($shouldUpdate) {
                $this->dropdowns[$levelName]['list'] = $areaTree
                    ->areas($this->removeChecksumSafety($selectedPath), checksumSafe: true)
                    ->pluck('name', 'path')
                    ->all();
                $this->dropdowns[$levelName]['selected'] = null;
                $shouldUpdate = false;
                $resetDownstream = true;
            }
            if ($changedLevelName === $levelName) {
                $this->dropdowns[$changedLevelName]['selected'] = $selectedPath;
                $shouldUpdate = true;
            }
        }
    }

    public function filter()
    {
        $filter = array_map(
            fn ($dropdown) => $this->removeChecksumSafety($dropdown['selected']),
            array_filter($this->dropdowns, fn ($dropdown) => $dropdown['selected'])
        );
        session()->put('area-filter', $filter);
        list($filterPath,) = $this->areaResolver();
        $this->dispatch('filterChanged', $filterPath);
    }

    public function clear()
    {
        session()->forget('area-filter');
        $this->mount();
        $this->dispatch('filterChanged');
    }

    public function render()
    {
        return view('livewire.area-filter');
    }
}
