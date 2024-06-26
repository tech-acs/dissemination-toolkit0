<?php

namespace App\Livewire;

use App\Livewire\Visualizations\Placeholder;
use App\Livewire\VisualizationTraits\WizardStepActionTrait;
use App\Models\Dataset;
use App\Models\Dimension;
use App\Models\Topic;
use App\Service\DataParam;
use App\Service\QueryBuilder;
use App\Service\Sorter;
use App\Service\TemplateStores\VisualizationTemplateStore;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use App\Models\Area;
use App\Services\AreaTree;

class VisualizationDeriver extends Component
{
    use WizardStepActionTrait;

    public array $steps = [
        1 => ['name' => 'step1', 'label' => 'Select template'],
        2 => ['name' => 'step2', 'label' => 'Plug data'],
        3 => ['name' => 'step3', 'label' => 'Review & save'],
    ];
    public int $currentStep = 1;
    // Step 1
    public Collection $templates;
    public array $requirements = [];
    public bool $showRequirements = false;
    public array $datasets = [];
    // Step 2
    public ?string $selectedTemplate = null;
    public string $type;
    public array $dataParams = [];
    public array $data = [];
    public array $options = [];
    public ?string $livewireComponent = Placeholder::class;
    public ?int $selectedDataset = null;
    public array $geographyLevels = [];
    public int $selectedGeographyLevel = 0;
    public array $geographies = [];
    public array $selectedGeographies = [];
    public array $years = [];
    public array $selectedYears = [];
    // Step 3
    public Collection $topics;
    public ?string $title = null;
    public ?string $description = null;
    public ?int $topicId = null;
    public $tags;
    public bool $showSaveNotification = false;

    public function mount()
    {
        $this->templates = (new VisualizationTemplateStore)->getAll()
            ->map(function ($template) {
                return [
                    'id' => $template->id,
                    'type' => $template->type,
                    'name' => $template->name,
                    'description' => $template->description,
                    'thumbnail' => $template->thumbnailPath,
                    'dataRequirement' => $template->dataRequirement,
                    'livewireComponent' => $template->livewireComponent,
                    'options' => $template->options,
                ];
            });
        $this->topics = Topic::all();
    }

    public function checkRequirements(string $templateId): void
    {
        $template = $this->templates->firstWhere('id', $templateId);
        $this->requirements['template_id'] = $templateId;
        $this->requirements['indicators'] = $template['dataRequirement']['indicators'];
        $this->requirements['dimensions'] = array_keys($template['dataRequirement']['dimensions']);
        $this->datasets = $this->getQualifyingDatasets(
            current($template['dataRequirement']['indicators']),
            array_keys($template['dataRequirement']['dimensions'])
        );
        $this->showRequirements = true;
    }

    private function getQualifyingDatasets(string $indicator, array $dimensions): array
    {
        $locale = app()->getLocale();
        $candidateDatasets = Dataset::whereRelation('indicator', "name->{$locale}", 'ilike', $indicator)->get();
        return $candidateDatasets->filter(function ($dataset) use ($dimensions) {
                return collect($dimensions)->reduce(function (bool $carry, $dimension) use($dataset) {
                    return $carry && $dataset->dimensions->pluck('name')->contains($dimension);
                }, true);
            })
            ->pluck('name', 'id')->all();
    }

    public function selectTemplate(string $templateId): void
    {
        $this->selectedTemplate = $templateId;
        $this->showRequirements = false;
        $this->nextStep();
    }

    public function updatedSelectedDataset($datasetId): void
    {
        $dataset = Dataset::with('dimensions')->find($datasetId);
        if ($dataset) {
            $allLevels = (new AreaTree())->hierarchies;
            $this->geographyLevels = $dataset ? array_slice($allLevels, 0,$dataset->max_area_level + 1) : $allLevels;
            $this->geographies = Area::ofLevel(0)->pluck('name', 'id')->all();
            $this->years = $dataset->years->pluck('name', 'id')->all();
            $this->resetValidation('selectedDataset');
        }
    }

    public function updatedSelectedGeographyLevel(int $level): void
    {
        $this->geographies = Area::ofLevel($level)->pluck('name', 'id')->all();
    }

    private function dataRequirementToDataParamSliceMapper(array $dataRequirement): array
    {
        $locale = app()->getLocale();
        $dimensions = collect($dataRequirement['dimensions'])
            ->mapWithKeys(function ($dimensionValueCodes, $dimensionName) use ($locale) {
                $dimension = Dimension::where("name->$locale", $dimensionName)->first();
                $dimensionValues = $dimension->values();
                return [$dimension->id => $dimensionValues->filter(fn ($value) => in_array($value->name, $dimensionValueCodes))->pluck('id')->all()];
            })
            ->all();
        $pivotColumn = is_numeric($dataRequirement['pivotColumn'])?$dataRequirement['pivotColumn']:Dimension::where("name->$locale", $dataRequirement['pivotColumn'])->first()->id;
        $pivotRow = is_numeric($dataRequirement['pivotRow'])?$dataRequirement['pivotRow']:Dimension::where("name->$locale", $dataRequirement['pivotRow'])->first()->id;
        return [$dimensions, $pivotColumn, $pivotRow];
    }

    public function getData(): void
    {
        $validator = Validator::make(
            ['dataset' => $this->selectedDataset],
            ['dataset' => 'required'],
            ['dataset.required' => 'You must select a dataset'],
        );
        if ($validator->passes()) {
            $template = $this->templates->firstWhere('id', $this->selectedTemplate);
            [$dimensions, $pivotColumn, $pivotRow] = $this->dataRequirementToDataParamSliceMapper(
                $template['dataRequirement']
            );
            $queryParameters = new DataParam(
                dataset: $this->selectedDataset,
                geographies: [$this->selectedGeographyLevel => $this->selectedGeographies],
                years: $this->selectedYears,
                dimensions: $dimensions,
                pivotColumn: $pivotColumn,
                pivotRow: $pivotRow
            );
            $query = new QueryBuilder($queryParameters);
            $this->type = $template['type'];
            $this->livewireComponent = $template['livewireComponent'];
            $this->options = $template['options'];
            $this->dataParams = $queryParameters->toArray();
            $this->data = Sorter::sort($query->get())->all();
            $this->dispatch(
                "dataChanged",
                data: Sorter::sort($query->get()),
                dataParams: $queryParameters->toArray()
            );
        } else {
            $errors = $validator->errors();
            foreach ($errors->all() as $error) {
                $this->dispatch('notify', content: $error, type: 'error');
            }
        }
    }

    private function makeValidator(int $step): \Illuminate\Validation\Validator
    {
        return match ($step) {
            1 => Validator::make(
                ['selectedTemplate' => $this->selectedTemplate, 'datasets' => $this->datasets],
                ['selectedTemplate' => 'required', 'datasets' => 'min:1'],
                [
                    'selectedTemplate.required' => 'First, you must choose a template',
                    'datasets.min' => "There are no datasets that fulfil the template's requirements"
                ],
            )->stopOnFirstFailure(),
            2 => Validator::make(
                ['dataset' => $this->selectedDataset, 'data' => $this->data, 'options' => $this->options],
                ['dataset' => 'required', 'data' => 'required', 'options' => 'required'],
                [
                    'dataset.required' => 'You must select a dataset',
                    'data.required' => 'Press the apply button and preview the visualization'
                ],
            )->stopOnFirstFailure(),
            3 => Validator::make(
                ['title' => $this->title, 'topicId' => $this->topicId],
                ['title' => 'required', 'topicId' => 'required'],
                ['required' => 'The :attribute field is required'],
            )
        };
    }

    public function saveViz(): void
    {
        $validator = $this->makeValidator($this->currentStep);
        if ($validator->passes()) {
            $viz = Auth::user()->visualizations()->create([
                'name' => str($this->title)->slug()->toString(),
                'title' => $this->title,
                'description' => $this->description,
                'topic_id' => $this->topicId,
                'type' => $this->type,
                'data_params' => $this->dataParams,
                'options' => $this->options,
                'livewire_component' => $this->livewireComponent
            ]);
            if ($viz->exists()) {
                $this->showSaveNotification = true;
            } else {
                $this->dispatch('notify', content: 'There was a problem saving the visualization', type: 'error');
            }
        } else {
            $errors = $validator->errors();
            foreach ($errors->all() as $error) {
                $this->dispatch('notify', content: $error, type: 'error');
            }
        }
    }

    public function complete()
    {
        $this->redirectRoute('manage.visualization.index');
    }

    public function render()
    {
        return view('livewire.visualization-deriver.wizard');
    }
}
