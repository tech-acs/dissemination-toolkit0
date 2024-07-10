<div x-data="{ nextSelection: @entangle('nextSelection') }">
    <div class="pt-4">
        <div x-data="{ active: '{{ $nextSelection }}' }" class="mx-auto max-w-3xl w-full space-y-4">
            @include('data-explorer.partials.topic')
            @include('data-explorer.partials.datasets')
            @include('data-explorer.partials.indicators')
            @include('data-explorer.partials.geography')
            {{--@include('data-explorer.partials.years')--}}
            @include('data-explorer.partials.dimensions')
            @include('data-explorer.partials.pivoting')
        </div>
        <div class="flex justify-end mt-6">
            {{--@dump($selectedGeographyLevels, $selectedGeographies)--}}
            <x-animation.bouncing-right-pointer :class="$nextSelection === 'apply' ? '' : 'hidden'" />
            <x-button wire:click="apply()" wire:loading.attr="disabled">Fetch</x-button>
            <x-secondary-button class="ml-4" wire:click="resetFilter()">Reset</x-secondary-button>
        </div>
    </div>
</div>

