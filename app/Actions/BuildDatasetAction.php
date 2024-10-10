<?php

namespace App\Actions;

use App\Models\Dataset;

class BuildDatasetAction
{
    public function __construct(public Dataset $dataset) {}

    private function prepare()
    {
        $dataset = Dataset::find(1)
            ->observations()
            //->map(fn($item) => (array) $item)
            ->map(function ($row) {
                $key = collect((array) $row)
                    ->except(['value', 'indicator_id'])
                    ->join('.');
                $row->key = $key;
                return $row;
            })
            ->groupBy('indicator_id');
    }

    public function handle()
    {
        return collect([['Population' => 190]]);
    }
}
