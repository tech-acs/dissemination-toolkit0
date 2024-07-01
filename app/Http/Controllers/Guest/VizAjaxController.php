<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Livewire\Visualizations\Chart;
use App\Livewire\Visualizations\Table;
use App\Models\Visualization;

class VizAjaxController extends Controller
{
    /*public function index()
    {
        return Visualization::orderBy('livewire_component')
            ->published()
            ->get()
            ->map(function (Visualization $visualization) {
                return [
                    'id' => $visualization->id,
                    'name' => $visualization->title,
                ];
            })->all();
    }*/

    public function show(Visualization $visualization)
    {
        if ($visualization->type === 'Chart') {
            $instance = new Chart();
            $instance->vizId = $visualization->id;
            $instance->mount();
            return [
                'data' => $instance->data,
                'layout' => $instance->options,
                'config' => $instance->config,
            ];

        } elseif ($visualization->type === 'Table') {
            $instance = new Table();
            $instance->vizId = $visualization->id;
            $instance->mount();
            return [
                'options' => $instance->options,
            ];
        } else {
            //
        }

    }
}
