<main class="mx-auto" x-data="{ hasData: @entangle('hasData') }">

    {{-- Top bar --}}
    <div class="flex items-center justify-between py-3 pb-2">
        <div class="inline-flex items-center">
            <h1 class="text-2xl font-semibold tracking-tight text-gray-600">{{ __('Data Explorer') }}</h1>
        </div>
        <div class="flex items-center">
            <div class="h-6 text-lg font-medium leading-6 text-gray-900">{{ $indicatorName }}</div>
        </div>
        <div class="flex gap-x-2">
            @if($hasData)
                <div>
                    {{--ToDo: Change this to a disabled download button if there's no data--}}
                    <x-viz-button wire:click="download()" icon="download-xls">{{ __('Download') }}</x-viz-button>
                </div>
            @endif
        </div>
    </div>
    <div class="flex flex-wrap space-x-2 border-b border-gray-200 pb-3">
        @forelse($dataShaperSelections as $key => $value)
            <x-simple-badge><span class="font-bold">{{ ucfirst($key) }}:</span>&nbsp;{{ $value }}</x-simple-badge>
        @empty
            Please select your desired parameters and press the fetch button
        @endforelse
    </div>

    <main class="pb-24">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-x-6">

            <livewire:data-shaper />

            <!-- Visualization -->
            <div class="lg:col-span-3 pl-6 border-l" x-data="{ hasData: @entangle('hasData') }">
                <div x-show="hasData" class="pr-2">
                    {{--<livewire:visualizer :data="$data" :designatedComponent="$livewireComponent" />--}}
                </div>

                <div x-show="! hasData" type="button" class="relative block w-full mt-16 rounded-lg border-2 border-dashed border-gray-300 p-12 py-32 text-center focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <svg class="mx-auto h-24 w-24 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0112 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 1.5v-1.5m0 0c0-.621.504-1.125 1.125-1.125m0 0h7.5"></path>
                    </svg>
                    <span class="mt-2 block text-sm font-semibold text-gray-900 leading-7">
                        When available, data will be displayed here
                    </span>
                </div>
            </div>
        </div>
        <x-toast />
    </main>

</main>
