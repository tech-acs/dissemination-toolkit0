<?php

namespace App\Services;

use App\Models\Dataset;
use App\Models\Dimension;
use App\Models\Indicator;
use App\Models\Year;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class QueryBuilder
{
    public const VALUE_COLUMN_INVISIBLE_MARKER = '­';
    private Dataset $dataset;
    private Collection $indicators;
    private Collection $geographies;
    //private Collection $years;
    private Collection $dimensions;
    private string $valueColumnInFactTable = 'value';
    private string $valueColumnInResult = 'result';
    private string $sql = '';
    private string $schema;

    private ?Pivoting $pivoting = null;

    public function __construct(array $queryParameter, ?string $schema = null)
    {
        $this->schema = is_null($schema) ? '' : "$schema.";
        $this->dataset = Dataset::find($queryParameter['dataset']);
        $this->dataset->load('dimensions', 'indicators');
        $this->indicators = Indicator::findMany($queryParameter['indicators']); // ToDo
        $this->geographies = collect($queryParameter['geographies']);
        //$this->years = Year::find($queryParameter['years']);
        $this->dimensions = collect($queryParameter['dimensions'])->map(function ($dimensionValueIds, $dimensionId) {
            return [
                'model' => Dimension::find($dimensionId),
                'valueIds' => $dimensionValueIds,
            ];
        });
        if (! is_null($queryParameter['pivotColumn']) && ! is_null($queryParameter['pivotRow'])) {
            $this->pivoting = new Pivoting($queryParameter['pivotColumn'], $queryParameter['pivotRow'] ?? 0, $queryParameter['nestingPivotColumn']);
        }
        $this->buildSql();
    }

    private function buildSql(): void
    {
        if ($this->pivoting) {
            $select = $this->pivoting->select();
            $from = $this->makeFrom($this->dataset->dimensions);
            $where = $this->makeWhere($this->dimensions);
            $orderBy = $this->makeOrderBy($this->dimensions);
            $this->sql = vsprintf(
                "SELECT %s, %s.%s::text AS %s FROM %s WHERE %s ORDER BY %s",
                [$select, $this->dataset->fact_table, $this->valueColumnInFactTable, $this->valueColumnInResult, $from, $where, $orderBy]
            );
            $this->sql = vsprintf(
                "SELECT * FROM crosstab('%s') AS (%s)",
                [str($this->sql)->replace("'", "''")->toString(), $this->pivoting->crosstabCategories($this->get())]
            );
        } else {
            $select = $this->makeSelect($this->dimensions);
            $from = $this->makeFrom($this->dataset->dimensions);
            $where = $this->makeWhere($this->dimensions);
            $orderBy = "areas.path";
            $this->sql = vsprintf(
                "SELECT %s, %s.%s::text AS \"%s%s\" FROM %s WHERE %s ORDER BY %s",
                [$select, $this->dataset->fact_table, $this->valueColumnInFactTable, $this->indicators->first()->name, self::VALUE_COLUMN_INVISIBLE_MARKER, $from, $where, $orderBy]
            );
        }
    }

    private function makeSelect(Collection $dimensions): string
    {
        return $dimensions
            ->pluck('model')
            ->map(fn ($dimension) => "{$dimension->table_name}.name::text AS {$dimension->table_name}")
            /*->unless($forCrosstab, function (Collection $collection) {
                return $collection->prepend("years.name AS year");
            })*/
            ->prepend("areas.name->>'" . app()->getLocale() . "'::text AS geography")
            //->prepend("years.name::text AS year")
            ->join(', ');
    }

    private function makeFrom(Collection $dimensions): string
    {
        $factTable = $this->dataset->fact_table;
        return $dimensions
            ->map(function ($dimension) use ($factTable) {
                $table = $dimension->table_name;
                return "INNER JOIN {$this->schema}$table ON {$this->schema}{$factTable}.{$table}_id = $table.id";
            })
            //->prepend("INNER JOIN years ON {$this->schema}{$factTable}.year_id = years.id")
            ->prepend("INNER JOIN areas ON {$this->schema}{$factTable}.area_id = areas.id")
            ->prepend($this->schema . $factTable)
            ->join(' ');
    }

    private function makeWhere(Collection $dimensions): string
    {
        $factTable = $this->dataset->fact_table;
        $excludedDimensions = $this->dataset->dimensions->pluck('id')->diff($dimensions->keys());

        $whereClauseForDimensions = $this->dataset->dimensions
            ->map(function ($dimension) use ($dimensions, $excludedDimensions) {
                if ($excludedDimensions->contains($dimension->id)) {
                    return "{$dimension->table_name}.code = '_T'";
                } else {
                    $filter = collect($dimensions[$dimension->id]['valueIds'])
                        ->map(fn ($v) => "$dimension->table_name.id = '{$v}'")
                        ->join(' OR ');
                    return empty($filter) ? null : str($filter)->wrap('(', ')')->toString();
                }
            });

        $whereClauseForGeography = $this->geographies
            ->map(function ($areaCodes, $level) {
                if (! empty($areaCodes)) {
                    /*return "( areas.level = '$level' )";
                } else {*/
                    $inClause = collect($areaCodes)->map(fn ($code) => str($code)->wrap("'"))->join(', ');
                    return "( areas.level = '$level' AND areas.id IN ($inClause) )";
                }
            })->filter()->join(' OR ');

        /*$whereClauseForYear = $this->years
            ->map(function ($year) use ($factTable) {
                return "$factTable.year_id = $year->id";
            })->join(' OR ');*/

        return $whereClauseForDimensions
            ->concat([str($whereClauseForGeography)->wrap('(', ')')->toString()])
            //->when(! empty($whereClauseForYear), fn (Collection $c) => $c->concat([str($whereClauseForYear)->wrap('(', ')')->toString()]))
            ->prepend("$factTable.dataset_id = {$this->dataset->id}")
            ->prepend("$factTable.indicator_id IN ({$this->indicators->pluck('id')->map(fn ($id) => str($id)->wrap("'"))->join(', ')})")
            ->filter()
            ->join(' AND ');
    }

    private function makeOrderBy(Collection $dimensions): string
    {
        return '1, 2';
        $excludedDimensions = $this->dataset->dimensions->pluck('id')->diff($dimensions->keys());

        $orderByDimensions = $this->dataset->dimensions
            ->map(function ($dimension) use ($dimensions, $excludedDimensions) {
                if ($excludedDimensions->contains($dimension->id)) {
                    return "{$dimension->table_name}.code = '_T'";
                } else {
                    $filter = collect($dimensions[$dimension->id]['valueIds'])
                        ->map(fn ($v) => "$dimension->table_name.id = '{$v}'")
                        ->join(' OR ');
                    return empty($filter) ? null : str($filter)->wrap('(', ')')->toString();
                }
            })
            ->join(', ');
        return $orderByDimensions;
    }

    public function get(?string $sql = null) : Collection
    {
        //dump($this->sql);
        try {
            $result = DB::select($sql ?? $this->sql);
            return collect(json_decode(json_encode($result), true));
        } catch (\Exception $exception) {
            logger('In QueryBuilder', ['Exception' => $exception->getMessage()]);
            return collect();
        }
    }

    public function toSql() : string
    {
        return $this->sql;
    }
}
