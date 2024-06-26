<div class="shadow sm:rounded-md sm:overflow-hidden">
    <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
        <div class="grid grid-cols-1 gap-6">
            <div>
                <x-label for="name" value="{{ __('Name') }} *" />

                <x-multi-lang-input id="name" name="name" type="text"
                                              value="{{ old('name', $dataset->name ?? null) }}"/>
                <x-input-error for="name" class="mt-2" />
            </div>
            <div>
                <x-label for="description" value="{{ __('Description') }}" class="inline" /><x-locale-display />
                <x-textarea name="description" rows="3">{{ old('description', $dataset->description ?? null) }}</x-textarea>
                <x-input-error for="description" class="mt-2" />
            </div>
            <div>
                <x-label for="indicator_id" value="{{ __('Indicator') }} *" />
                <select id="indicator_id" name="indicator_id" class="mt-1 pr-10 space-y-1 text-base p-1 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                    <option value="">{{ __('Select indicator') }}</option>
                    @foreach($indicators ?? [] as $indicator)
                        <option class="p-2 rounded-md" value="{{ $indicator?->id }}"
                            @selected($indicator->id == old('indicator_id', $dataset->indicator->id ?? null))
                        >
                            {{ $indicator->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error for="indicator_id" class="mt-2" />
            </div>
            <div>
                <x-label for="fact_table" value="{{ __('Fact table') }} *" />
                <select id="fact_table" name="fact_table" class="mt-1 pr-10 space-y-1 text-base p-1 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                    <option value="">{{ __('Select fact table') }}</option>
                    @foreach($factTables ?? [] as $factTable => $name)
                        <option class="p-2 rounded-md" value="{{ $factTable }}"
                            @selected($factTable == old('fact_table', $dataset->fact_table ?? null))
                        >
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error for="fact_table" class="mt-2" />
            </div>
            <div>
                <x-label for="max_area_level" value="{{ __('Data geographic granularity') }} *" />
                <select id="max_area_level" name="max_area_level" class="mt-1 pr-10 space-y-1 text-base p-1 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                    <option value="">{{ __('Select geographic granularity') }}</option>
                    @foreach($areaLevels ?? [] as $level => $name)
                        <option class="p-2 rounded-md" value="{{ $level }}"
                            @selected($level == old('max_area_level', $dataset->max_area_level ?? -1))
                        >
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error for="max_area_level" class="mt-2" />
            </div>
            <div>
                <x-label for="years" value="{{ __('Data years') }} *" />
                <select id="years" name="years[]" multiple class="mt-1 pr-10 space-y-1 text-base p-1 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                    <option value="">{{ __('Select years') }}</option>
                    @foreach($years ?? [] as $code => $name)
                        <option class="p-2 rounded-md" value="{{ $code }}"
                            @if($dataset ?? false) @selected(in_array($code, $dataset->years->pluck('id')->all() ?? [])) @endif
                        >
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error for="years" class="mt-2" />
            </div>
        </div>
    </div>
    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
        <x-secondary-button class="mr-2"><a href="{{ route('manage.dataset.index') }}">{{ __('Cancel') }}</a></x-secondary-button>
        <x-button>
            {{ __('Submit') }}
        </x-button>
    </div>
</div>
