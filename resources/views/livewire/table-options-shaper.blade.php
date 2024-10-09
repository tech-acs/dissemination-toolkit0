<div>
    <h3 class="text-2xl pb-4">Table options</h3>
    <div class="space-y-8 mb-8">
        @foreach($options as $name => $value)
            <div class="relative flex items-start">
                <div class="flex h-6 items-center">
                    <input wire:model="options.{{ $name }}" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                </div>
                <div class="ml-3 text-sm leading-6">
                    {{ $optionLabels[$name] }}
                </div>
            </div>
        @endforeach
    </div>

    <button wire:click="apply" class="cursor-pointer rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
        Apply
    </button>
</div>
