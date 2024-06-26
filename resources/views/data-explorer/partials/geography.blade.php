<div x-data="{
        id: 'geography',
        get expanded() {
            return this.active === this.id
        },
        set expanded(value) {
            this.active = value ? this.id : null
        }
    }" class="rounded-md bg-white shadow-sm border"
>
    <h2>
        <button
            x-on:click="expanded = !expanded"
            class="flex w-full items-center justify-between px-6 py-2 text-xl font-bold"
        >
            <label class="block text-lg font-medium leading-6 text-gray-900">{{ __('Geography') }}</label>
            <x-animation.bouncing-left-pointer :class="$nextSelection === 'geography' ? '' : 'hidden'" />
            <span x-show="expanded" aria-hidden="true" class="ml-4" x-cloak>&minus;</span>
            <span x-show="!expanded" aria-hidden="true" class="ml-4">&plus;</span>
        </button>
    </h2>

    <div x-show="expanded" x-collapse x-cloak>
        <div class="px-6 pb-4">

            @if($geographyLevels)
                <div class="flex flex-col" x-data="{ showValues: false }">
                    <div class="flex justify-between">
                        <div class="flex items-start">
                            <select wire:model.live="selectedGeographyLevel"
                                    class="mt-1 rounded-md border border-gray-300 bg-white px-3 pr-10 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                @foreach($geographyLevels as $id => $geographyLevel)
                                <option value="{{ $id }}" class="p-2 rounded-md">By {{ $geographyLevel }}</option>
                                @endforeach
                            </select>

                            {{--@if(count($selectedGeographies ?? []) > 0)
                                <div title="Number of selected values" class="text-indigo-600 text-xs mt-1 ml-1 tracking-widest">
                                    [{{ count(array_filter($selectedGeographies)) }}]
                                </div>
                            @endif--}}
                        </div>
                        <div class="flex items-center">
                            <button @click="showValues = !showValues" type="button" class="text-gray-400 hover:text-gray-700 cursor-pointer">
                                <!-- Expand icon -->
                                <svg x-show="!(showValues)" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                                </svg>
                                <!-- Collapse icon -->
                                <svg x-show="showValues" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M4 10a.75.75 0 01.75-.75h10.5a.75.75 0 010 1.5H4.75A.75.75 0 014 10z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div x-show="showValues" class="text-xs mt-2 border rounded-md p-4 max-h-64 overflow-y-auto scrollbar">
                        @forelse($geographies ?? [] as $id => $name)
                            <div class="flex items-start">
                                <input
                                    id="geography-{{ $loop->index }}"
                                    wire:model="selectedGeographies"

                                    value="{{ $id }}"
                                    type="checkbox"
                                    class="h-4 w-4 mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                >
                                <label for="geography-{{ $loop->index }}" class="text-gray-900 ml-2 leading-6">{{ $name }}</label>
                            </div>
                        @empty
                            There are no values to display
                        @endforelse
                    </div>
                    {{--<div class="flex justify-end mt-6">
                        @if($this->showFetchGeographicalChildren)
                            <div class="flex items-start">
                                <input
                                    id="fetchGeographicalChildren",
                                    wire:model="fetchGeographicalChildren"
                                    type="checkbox"
                                    class="h-4 w-4 mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                >
                                <label for="fetchGeographicalChildren" class="text-gray-900 ml-2 leading-6">{{ __('Fetch geographical heirachical area children') }}</label>
                            </div>
                        @endif--}}
                </div>
            @else
                <div class="text-gray-500 mt-2 border rounded-md p-4 py-2">{{ __('Select dataset to see available geographies') }}</div>
            @endif
        </div>
    </div>
</div>
