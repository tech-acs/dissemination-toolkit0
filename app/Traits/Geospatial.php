<?php

namespace App\Traits;

use App\Models\Area;

trait Geospatial
{
    private static function findContainingGeometry($level, $geom)
    {
        return Area::ofLevel($level)
            ->whereRaw("ST_Area(ST_Intersection(geom::geometry, ?)) > 0.60 * ST_Area(?)", [$geom, $geom])
            ->first();
    }
}
