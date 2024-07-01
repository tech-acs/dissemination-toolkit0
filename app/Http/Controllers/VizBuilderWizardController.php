<?php

namespace App\Http\Controllers;

use App\Enums\VisualizationTypeEnum;
use App\Livewire\Visualizations\Table;
use App\Models\Tag;
use App\Models\Topic;
use App\Traits\PlotlyDefaults;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class VizBuilderWizardController extends Controller
{
    use PlotlyDefaults;

    private function canShowStep(int $step): bool
    {
        return true;
    }

    private function getConfig(): array
    {
        return [
            ...self::DEFAULT_CONFIG,
            //'toImageButtonOptions' => ['filename' => $this->graphDiv . ' (' . now()->toDayDateTimeString() . ')'],
            'locale' => app()->getLocale(),
        ];
    }

    private function reset()
    {
        session()->forget([
            'step1.data', 'step1.indicatorName', 'step1.dataParams', 'step1.dataSources',
            'step2.data', 'step2.layout', 'step2.options', 'step2.vizType'
        ]);
    }

    public function show(int $currentStep)
    {
        $steps = [
            1 => 'Prepare data',
            2 => 'Visualize',
            3 => 'Review & save',
        ];
        $topics = Topic::all();
        if ($this->canShowStep($currentStep)) {
            return view("manage.viz-builder.$currentStep", compact('steps', 'topics', 'currentStep'));
        } else {
            return redirect()->back();
        }
    }

    public function update(int $currentStep, Request $request)
    {
        if ($currentStep === 1) {
            if ($request->has('reset')) {
                $this->reset();
                return back();
            }
            // 'data', 'indicatorName' and 'dataParams' already sent via the StateRecorder
            session()->put('step1.dataSources', toDataFrame(collect(session('step1.data', []))));
            return redirect()->route('manage.viz-builder-wizard.show.{currentStep}', 2);

        } elseif ($currentStep === 2) {
            session()->put('step2.vizType', request('viz', session('step2.vizType',Table::class)));
            session()->put('step2.data', json_decode(request('chart-data', '[]'), true));
            session()->put('step2.layout', json_decode(request('chart-layout', '{}'), true));
            if (session('step2.vizType') === Table::class) {
                session()->put('step2.options', array_replace_recursive(Table::DEFAULT_OPTIONS, []));
            } else {
                session()->put('step2.options', []);
            }
            return redirect()->route('manage.viz-builder-wizard.show.{currentStep}', 3);

        } elseif ($currentStep === 3) {
            $title = $request->get('title');
            $description = $request->get('description');
            $topicId = $request->get('topicId');
            $dataParams = session('step1.dataParams');
            $data = collect(session('step2.data'))
                ->map(function ($trace) {
                    unset($trace['x'], $trace['y']);
                    return $trace;
                });
            $layout = session('step2.layout', []);
            $options = session('step2.options', []);

            $viz = Auth::user()->visualizations()->create([
                'name' => str($title)->slug()->toString(),
                'title' => $title,
                'slug' => str($title)->slug()->toString(),
                'description' => $description,
                'topic_id' => $topicId,
                'type' => 'No longer needed. Drop this column',
                'data_params' => $dataParams,
                'data' => $data,
                'layout' => $layout,
                'options' => $options,
                'livewire_component' => session('step2.vizType')
            ]);
            if ($viz->exists()) {
                $updatedTags = Tag::prepareForSync($request->get('tags', ''));
                $viz->tags()->sync($updatedTags->pluck('id'));

                $this->reset();
                return redirect()->route('manage.visualization.index')->withMessage('Visualization successfully created');
            }
        } else {
            //
        }
    }

    public function ajaxSaveChart(Request $request)
    {
        $traces = collect($request->json('data'));
        $layout = $request->get('layout');

        logger('Save', ['request' => $request->all()]);

        session()->put('step2.layout', $layout);
        session()->put('step2.data', $traces);
    }

    public function ajaxGetChart(Request $request)
    {
        $dataSources = session('step1.dataSources');
        $data = session('step2.data');
        $layout = session('step2.layout');
        return [
            'dataSources' => $dataSources,
            'data' => $data,
            'layout' => $layout,
            'config' => [...$this->getConfig(), 'editable' => true],
            'title' => '',
        ];
    }
}
