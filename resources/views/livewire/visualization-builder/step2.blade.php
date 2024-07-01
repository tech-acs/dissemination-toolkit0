<div class="mx-auto flex flex-col items-baseline" x-show="step == 2" x-cloak>

    {{-- Top menu, visualization selector --}}
    <div class="flex w-full space-x-4 p-3 px-2 bg-gray-50 border rounded-md" x-show="step == 2" x-data="{ selected: @entangle('selectedViz').live }">
        @foreach(\App\Enums\VisualizationTypeEnum::class::all()->groupBy('type') as $category => $group)
            <div class="h-16">&nbsp;</div>
            @foreach($group->sortBy('rank') as $vizType)
                <div
                    title="{{ $vizType['name'] }}"
                    class="border border-gray-200 h-16 w-16 shadow-sm rounded-md bg-white hover:ring-blue-400 hover:ring-2"
                    x-on:click="$wire.set('selectedViz', '{{ $vizType['name'] }}')"
                    :class="selected == '{{ $vizType['name'] }}' ? 'ring-2 ring-blue-700' : ''"
                >
                    {!! $vizType['icon'] !!}
                </div>
            @endforeach
        @endforeach
    </div>

    <div class="flex w-full">
        @if ($livewireComponent === App\Livewire\Visualizations\Chart::class)

            <div class="relative pr-3" style="height: calc(100vh - 210px);">
                <div wire:ignore id="chart-editor" indicator="{{ $currentStep }}" default-layout="'{}'"></div>
            </div>

        @else
            {{-- Live visualizer --}}
            <div class="w-1/2 border-r pr-2 py-8">
                <livewire:visualizer :data="$data" :options="$layoutParams" :designatedComponent="$livewireComponent" key="{{ $livewireComponent }}" />
            </div>

            {{-- Layout editor form --}}
            <div class="w-1/2 pr-12 pt-6">
                @if(empty($livewireComponent::EDITABLE_OPTIONS))
                    <div class="w-2/3 mx-auto space-y-4 py-6">
                        <div class="text-center py-36">
                            <p class="mt-10 text-lg text-gray-400">
                                When available, customizable options will be displayed here
                                which you can use to alter the visual aspects of the visualization.</p>
                        </div>
                    </div>
                @else
                    <form class="w-10/12 mx-auto space-y-4 py-6">
                        @foreach($livewireComponent::EDITABLE_OPTIONS as $key => $field)
                            <div class="flex flex-col">
                                <label>{{ $field['label'] }}</label>
                                <x-layout-form-control wire:model.blur="layoutForm.{{ $key }}" :directives="$field" />
                            </div>
                        @endforeach
                        <div>
                            <x-button wire:click.prevent="updateLayout" class="inline">Apply</x-button>
                        </div>
                    </form>
                @endif
                {{--@dump($layoutForm, $this->layoutParams)--}}
            </div>
        @endif

    </div>

</div>

@script

    @viteReactRefresh
    @vite('resources/js/ChartEditor/index.jsx')

@endscript
