<div class="mx-auto flex flex-col items-baseline" x-show="step == 3" x-cloak>

    <div class="flex w-full">
        {{-- Common to step 2 & 3 --}}
        {{--<div class="w-1/2 border-r pr-2 py-8">
            <livewire:visualizer :data="$data" :options="$layoutParams" :designatedComponent="$livewireComponent" key="{{ $livewireComponent }}" />
        </div>--}}

        {{-- Visualization save form (Step 3) --}}
        <div class="w-1/2 pr-12">
            <form class="w-10/12 mx-auto space-y-4 py-6">
                <div>
                    <x-label for="title" value="{{ __('Title') }} *" />
                    {{--<x-multi-lang-input wire:model="title" type="text" />--}}
                    <input type="text" wire:model="title" class="min-w-0 mt-1 flex-1 rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block w-full px-3" />
                    <x-input-error for="title" class="mt-2" />
                </div>
                <div>
                    <x-label for="description" value="{{ __('Description') }}" class="inline" /><x-locale-display />
                    <x-textarea wire:model="description" rows="3"></x-textarea>
                    <x-input-error for="description" class="mt-2" />
                </div>
                <div>
                    <x-label for="topic" value="{{ __('Topic') }} *" />
                    <select wire:model="topicId" class="mt-1 space-y-1 text-base p-1 pr-10 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                        <option value="">{{ __('Select topic') }}</option>
                        @foreach($topics ?? [] as $topic)
                            <option class="p-2 rounded-md" value="{{ $topic?->id }}" @selected($topic->id == ($visualization->topic->id ?? null))>
                                {{ $topic->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error for="topic_id" class="mt-2" />
                </div>
                <div>
                    <x-label for="tags" value="{{ __('Tags') }}" />
                    <x-tags value="[]" class="mt-1" wire:model="tags" />
                    <x-input-error for="tags" class="mt-2" />
                </div>
            </form>
        </div>
    </div>

</div>
