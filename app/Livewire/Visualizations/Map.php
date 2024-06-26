<?php

namespace App\Livewire\Visualizations;

use App\Livewire\Visualization;
use App\Livewire\VisualizationTraits\ChartTrait;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Uneca\Scaffold\Models\Area;

abstract class Map extends Visualization
{
    use ChartTrait;
    public const DEFAULT_OPTIONS = [
        'plot_bgcolor' => '#cccccc',
    ];
    public const EDITABLE_OPTIONS = [

    ];

    public const DEFAULT_CONFIG = ['responsive' => true, 'displaylogo' => false, 'modeBarButtonsToRemove' => ['select2d', 'lasso2d', 'autoScale2d', 'hoverClosestCartesian', 'hoverCompareCartesian'],];

    public string $visualizationId = 'map';

    public array $traces = [];

    abstract protected function makeTraces(): array;
    protected function getGeoJson($locations)
    {
        $locationList = implode("','", $locations);
        $localeNameStatement =
            "case when name->>'".app()->currentLocale()."' is not null then name->>'".app()->currentLocale()."' else
         name->>'".app()->getFallbackLocale()."' end";

        $sql = "
        SELECT
            CASE
                WHEN COUNT(filtered_areas.*) = 0 THEN NULL
                ELSE json_build_object(
                    'type', 'FeatureCollection',
                    'features', json_agg(
                        json_build_object(
                            'type',       'Feature',
                            'id',         lower(replace(${localeNameStatement},' ','_')),
                            'geometry',   ST_AsGeoJSON(ST_ForcePolygonCW(filtered_areas.geom))::json,
                            'properties', json_build_object(
                                'code', code,
                                'name', ${localeNameStatement},
                                'id', lower(replace(${localeNameStatement},' ','_')),
                                'path', path
                            )
                        )
                    )
                )
            END
        AS feature_collection
        FROM
        (
            SELECT areas.name, areas.code, areas.level, areas.path, ST_SimplifyPreserveTopology(ST_GeomFromWKB(areas.geom), area_hierarchies.simplification_tolerance) AS geom
            FROM areas
            join area_hierarchies on area_hierarchies.index = areas.level
            WHERE areas.name->>'en' IN ('$locationList') AND areas.geom IS NOT NULL
        ) AS filtered_areas
    ";
        try {
            // dump($sql);
            $result = DB::select($sql);
        } catch (Exception $exception) {
            logger('Query error in getGeoJson()', ['exception' => $exception->getMessage()]);
            return '';
        }
        return $result[0]?->feature_collection ?? '';
    }

    #[Computed]
    public function config(): array
    {
        $dynamicOptions = ['toImageButtonOptions' => ['filename' => $this->visualizationId . ' (' . now()->toDayDateTimeString() . ')'], 'locale' => app()->getLocale(),];
        return array_merge(self::DEFAULT_CONFIG, $dynamicOptions);
    }

    protected function prepareViewPayload(): void
    {
        $this->traces = $this->tryMakeTraceUsingOptions() ?? $this->makeTraces();
    }

    public function updateOptions(array $options): void
    {
        $this->options = array_replace_recursive($this::DEFAULT_OPTIONS, $this->options, $options);
        $this->dispatchViewPayload();
    }

    public function updateData(array $data): void
    {
        $this->data = $data;
        $this->traces = $this->makeTraces();
        //dump("About to send ", $this->options);
        $this->dispatchViewPayload();
    }

    #[On('readyForViewPayload.{visualizationId}')]
    public function dispatchViewPayload(): void
    {

        //dump("Got asked by {$this->visualizationId}", $this->options);
        $this->dispatch("updateMap.$this->visualizationId", $this->traces, $this->options);
    }

    public function render()
    {
        //dump('At render ' . get_called_class(), $this->data, $this->options);
        return view('livewire.visualizations.map');
    }
}
