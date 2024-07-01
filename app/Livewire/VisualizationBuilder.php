<?php

namespace App\Livewire;

use App\Livewire\Visualizations\Placeholder;
use App\Livewire\VisualizationTraits\WizardStepActionTrait;
use App\Models\Topic;
use App\Enums\VisualizationTypeEnum;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\On;
use Livewire\Component;

class VisualizationBuilder extends Component
{
    use WizardStepActionTrait;

    public array $steps = [
        1 => ['name' => 'step1', 'label' => 'Prepare data'],
        2 => ['name' => 'step2', 'label' => 'Visualize'],
        3 => ['name' => 'step3', 'label' => 'Review & save'],
    ];
    public int $currentStep = 1;
    // Step 1
    public ?array $dataParams = null;
    public array $data = [];
    public bool $hasData = false;
    // Step 2
    public ?string $type = 'Chart';
    public ?string $selectedViz = '';
    public ?string $livewireComponent = Placeholder::class;
    public array $layoutParams = [];
    public array $layoutForm = [];
    // Step 3
    public Collection $topics;
    public ?string $title = null;
    public ?string $description = null;
    public ?int $topicId = null;
    public $tags;
    public bool $showSaveNotification = false;
    public array $dataShaperSelections = [];

    public function mount()
    {
        $this->topics = Topic::all();
        $this->hydrateDefaultLayoutFormValues($this->livewireComponent);
        $this->updateLayout();
    }

    #[On('dataShaperSelectionMade')]
    public function dataShaperSelectionMade(array $selection)
    {
        $this->dataShaperSelections = $selection;
    }

    private function castToType(string $type, $value): int|float|string|bool
    {
        return match ($type) {
            'integer' => (integer) $value,
            'float' => (float) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            default => (string) $value
        };
    }

    private function hydrateDefaultLayoutFormValues(string $component): void
    {
        $this->layoutForm = collect($component::EDITABLE_OPTIONS)
            ->map(function ($editable) use ($component) {
                return Arr::get($component::DEFAULT_OPTIONS, $editable['key'], '');
            })
            ->all();
    }

    public function updatedSelectedViz($value): void
    {
        $viz = VisualizationTypeEnum::all()->where('name', $value)->first();
        $this->livewireComponent = $viz['component'] ?? null;
        $this->reset('layoutParams', 'layoutForm');
        $this->type = $viz['type'] ?? null;
        $this->hydrateDefaultLayoutFormValues($viz['component'] ?? null);
        $this->updateLayout();
    }

    #[On('changeOccurred')]
    public function dataParamsUpdated(array $rawData, array $dataParams): void
    {
        $this->data = $rawData;
        $this->hasData = ! empty($data);
        $this->dataParams = $dataParams;
    }

    public function updateLayout(): void
    {
        //dump($this->livewireComponent::EDITABLE_OPTIONS, $this->layoutForm);
        $updated = Arr::undot(
            collect($this->livewireComponent::EDITABLE_OPTIONS)
                ->mapWithKeys(function ($editable, $index) {
                    return [$editable['key'] => $this->castToType($editable['data_type'], $this->layoutForm[$index])];
                })
                ->all()
        );
        $this->dispatch('optionsChanged', $updated);
        $this->layoutParams = $updated;
    }

    private function makeValidator(int $step): \Illuminate\Validation\Validator
    {
        return match ($step) {
            1 => Validator::make(
                ['data' => $this->data, 'dataParams' => $this->dataParams],
                ['data' => 'required', 'dataParams' => 'required'],
                [
                    'data.required' => 'Make sure there is some data',
                    'dataParams.required' => 'You have not set valid data parameters'
                ],
            )->stopOnFirstFailure(),
            2 => Validator::make(
                ['selectedViz' => $this->selectedViz, 'layoutParams' => $this->layoutParams],
                ['selectedViz' => 'required', ],//'layoutParams' => 'required'
                [
                    'selectedViz.required' => 'You must select a visualization type',
                    //'layoutParams.required' => 'Layout parameters have not been specified'
                ],
            )->stopOnFirstFailure(),
            3 => Validator::make(
                ['title' => $this->title, 'topicId' => $this->topicId],
                ['title' => 'required', 'topicId' => 'required'],
                ['topicId.required' => 'The topic field is required'],
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
                'slug' => str($this->title)->slug()->toString(),
                'description' => $this->description,
                'topic_id' => $this->topicId,
                'type' => $this->type,
                'data_params' => $this->dataParams,
                'options' => $this->layoutParams,
                'livewire_component' => $this->livewireComponent
            ]);
            //$this->dispatch("thumbnailCaptureRequested", name:$viz->id);
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
        return view('livewire.visualization-builder.wizard');
    }
}
