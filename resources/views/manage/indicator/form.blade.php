<div class="shadow sm:rounded-md sm:overflow-hidden">
    <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
        <div class="grid grid-cols-1 gap-6">
            <div>
                <x-label for="name" value="{{ __('Name') }} *" />
                <x-multi-lang-input id="name" name="name" type="text" value="{{old('name', $indicator->name ?? null)}}" />
                <x-input-error for="name" class="mt-2" />
            </div>
            <div>
                <x-label for="description" value="{{ __('Description') }}" class="inline" /><x-locale-display />
                <x-textarea name="description" rows="3">{{old('description', $indicator->description ?? null)}}</x-textarea>
                <x-input-error for="description" class="mt-2" />
            </div>
            {{--<div>
                <x-label for="rank" value="{{ __('Rank') }}" />
                <x-input id="rank" name="rank" type="number" class="w-20 mt-1" value="{{ old('rank', $indicator->rank ?? null) }}" />
                <x-input-error for="rank" class="mt-2" />
            </div>--}}
            <div>
                <x-label for="topic" value="{{ __('Topic') }} *" />
                <select id="topic" name="topic_id" class="mt-1 pr-10 space-y-1 text-base p-1 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                    <option value="">{{ __('Select topic') }}</option>
                    @foreach($topics ?? [] as $topic)
                        <option class="p-2 rounded-md" value="{{ $topic?->id }}"
                            @if($indicator->topic ?? null) @selected($topic?->id == $indicator->topic->id) @endif
                        >
                            {{ $topic->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error for="topic_id" class="mt-2" />
            </div>

            {{--<div>
                <x-label for="published" value="{{ __('Status') }}" />
                <div class="flex items-center mt-3 ml-3" x-data="{enabled: @json($page->published ?? false) }" x-cloak>
                    <label for="status">
                        <span class="text-sm text-gray-500">Draft</span>
                    </label>
                    <input type="hidden" name="published" :value="enabled">
                    <button
                        x-on:click="enabled = ! enabled"
                        :class="enabled ? 'bg-indigo-600' : 'bg-gray-200'"
                        type="button"
                        class="ml-3  relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        role="switch"
                        id="status"
                    >
                        <span aria-hidden="true" :class="enabled ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"></span>
                    </button>
                    <label for="status" class="ml-3">
                        <span class="text-sm text-gray-900">{{ __('Published') }}</span>
                    </label>
                </div>
            </div>--}}
        </div>
    </div>
    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
        <x-secondary-button class="mr-2"><a href="{{ route('manage.indicator.index') }}">{{ __('Cancel') }}</a></x-secondary-button>
        <x-button>
            {{ __('Submit') }}
        </x-button>
    </div>
</div>
