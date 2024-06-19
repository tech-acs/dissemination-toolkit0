<div class="shadow sm:rounded-md sm:overflow-hidden">
    <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
        <div class="grid grid-cols-1 gap-6">
            <div>
                <x-label for="name" value="{{ __('Name') }} *" />
                <x-input id="name" name="name" type="text" value="{{ old('name', $entry->name ?? null) }}" />
                <x-input-error for="name" class="mt-2" />
            </div>
            <div>
                <x-label for="table_name" value="{{ __('Code') }} *" />
                <x-input id="code" name="code" type="text" value="{{ old('code', $entry->code ?? null) }}" />
                <x-input-error for="code" class="mt-2" />
            </div>
        </div>
    </div>
    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
        <x-secondary-button class="mr-2"><a href="{{ route('manage.dimension.index') }}">{{ __('Cancel') }}</a></x-secondary-button>
        <x-button>{{ __('Submit') }}</x-button>
    </div>
</div>
