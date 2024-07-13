<?php

namespace App\Traits;

use App\Services\AreaTree;

trait AreaResolver
{
    public function shouldIgnoreFilterInSession(): bool
    {
        return false;
    }

    public function areaResolver(): array
    {
        $filtersToApply = [
            ...($this->shouldIgnoreFilterInSession() ? [] : session()->get('area-filter', []))
        ];
        $path = AreaTree::getFinestResolutionFilterPath($filtersToApply);
        $expandedPath = AreaTree::pathAsFilter($path);
        return [$path, $expandedPath];
    }
}
