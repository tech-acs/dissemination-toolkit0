@pushonce('scripts')
    @viteReactRefresh
    @vite('resources/js/ChartEditor/index.jsx')
@endpushonce

<x-app-layout>

    <div class="flex flex-col max-w-full mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <x-message-display />

        <section class="shadow sm:rounded-md sm:overflow-hidden py-5 bg-white sm:p-6">
            @include('manage.viz-builder.nav')

            <div class="flex justify-center space-x-4 px-2 py-4">
                @foreach(\App\Enums\VisualizationTypeEnum::all()->groupBy('type') as $category => $group)
                    @foreach($group->sortBy('rank') as $vizType)
                        <a
                            title="{{ $vizType['name'] }}"
                            {{--class="border border-gray-200 h-16 w-16 shadow-sm rounded-md bg-white hover:ring-blue-400 hover:ring-2 @if(request('viz', 'Table') == $vizType['name']) ring-2 ring-blue-700 @endif"--}}
                            href="{{ route('manage.viz-builder-wizard.show.{currentStep}', $currentStep) }}?viz={{ $vizType['component'] }}"
                        >
                            <label class="flex cursor-pointer items-center justify-center rounded-md px-6 py-2 text-sm font-semibold uppercase focus:outline-none sm:flex-1
                                        @if(request('viz', session('step2.vizType', \App\Livewire\Visualizations\Table::class)) == $vizType['component']) bg-indigo-600 text-white hover:bg-indigo-500 @else ring-1 ring-inset ring-gray-300 bg-white text-gray-900 hover:bg-gray-50 @endif">
                                {{ $vizType['name'] }}
                            </label>
                            {{--{!! $vizType['icon'] !!}--}}
                        </a>
                    @endforeach
                @endforeach
            </div>

            <div class="flex w-full">

                @if (request('viz', session('step2.vizType', \App\Livewire\Visualizations\Table::class)) === \App\Livewire\Visualizations\Table::class)

                    <div class="w-full mb-4">
                        <livewire:visualizer :raw-data="session()->get('step1.data', [])" />
                    </div>

                @elseif(request('viz', session('step2.vizType', \App\Livewire\Visualizations\Table::class)) === \App\Livewire\Visualizations\Chart::class)

                    <div class="relative pr-3 w-full" style="height: calc(100vh - 450px);">
                        <div id="chart-editor" data-sources="{{ json_encode(session()->get('step1.dataSources', [])) }}"></div>
                    </div>

                @elseif(request('viz', session('step2.vizType', \App\Livewire\Visualizations\Table::class)) === 'Map')

                    <div>
                        Coming soon
                    </div>

                @endif

            </div>

            @include('manage.viz-builder.footer')
            <x-toast />
        </section>

    </div>
</x-app-layout>
